<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

use \App\Models\EventTicket as model;

class EventTicketRepository extends AbstractRepository
{
    private $request;

    private static $_types = [
        'checkin',
        'billing'
    ];

    private static $_key = 'EB01010';

    public function __construct(Request $request, Model $model)
    {
        $this->request = $request;
        $this->model = $model;
    }

    /**
     * item sold tickets
     * @param int
     *
     */

    public function soldTickets($billing_item_id)
    {
        $billingItem = \App\Models\BillingItem::where('id', $billing_item_id)->first();
        if (!$billingItem || $billingItem->is_ticket != '1') {
            return false;
        }
        $tickets = $this->model->whereHas('billing_item', function ($q) use ($billing_item_id) {
            $q->where('id', '=', $billing_item_id);
        })->count();
        return $tickets;
    }

    /**
     * @param mixed $ids
     * @param mixed $order
     * 
     * @return [type]
     */
    public static function generatePDF($ids, $order)
    {
        $ticketIds = [];
        
        if(!is_array($ids)) { $ticketIds[0] = $ids; }
        else { $ticketIds = $ids; }

        if(count($ticketIds) < 1) return false;

        $language_id = $order->getUtility()->getLangaugeId();

        $event_id = $order->getUtility()->getEventId();

        $html = '';

        $css = false;

        $num_elements =  count($ids);

        $last_key = $num_elements - 1;

        foreach($ticketIds as $key => $ticketID)
        {
            $ticket = \App\Models\EventTicket::where('id', $ticketID)
                ->with(['validity', 'addon.attendee', 'addon', 'ticket_item', 'ticket_item.info'])
                ->first();
                
            $description = '';

            foreach($ticket->ticket_item->info as $info)
            {
                if($info['languages_id'] == $language_id && $info['name'] == 'description')
                {
                    $description = $info['value'];
                }
            }
            
            $eventTicketSetting = \App\Models\EventTicketSetting::where('event_id','=',$ticket->event_id)->first();

            if(!$ticket) {return false;}

            $event_setting = EventSettingRepository::getEventSetting(["event_id" => $event_id, "language_id" => $language_id]);
            $event = EventRepository::getEventDetail(["event_id" => $event_id, "language_id" => $language_id]);
            $eventInfo = readArrayKey($event, [], 'info');
            $event_start_date = $event['start_date'];
            $event_end_date = $event['end_date'];
            $paymentSetting = EventSiteSettingRepository::getPaymentSetting(["event_id" => $ticket->event_id, "language_id" => $language_id]);
            $ticket->price = self::_getTicketPrice($ticket->addon, $ticket);
            $ticket->qrString = ($ticket->qr_string == '') ? generateQRHash($ticket->id, "ebticket=", 'EB01010') : $ticket->qr_string;
            $currency = getCurrencyArray()[$paymentSetting->eventsite_currency];
            $labels = eventsite_labels('tickets', ["event_id" => $ticket->event_id, "language_id" => $language_id]);
            $html .= \View::make('admin.order.order_history.ticket.order-item-ticket',compact('event', 'eventInfo', 'ticket','event_setting','currency','eventTicketSetting','description','css','labels','event_start_date','event_end_date'))->render();
            
            if ($key != $last_key) {

                $html .= '<div style="page-break-after: always;"></div>';
            }

            $css = true;
        }

        $file_to_save = config('cdn.cdn_upload_path').DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'ticket_'.time().'_'.rand(0,1000).'.pdf';
        if(file_exists($file_to_save)) { 
            \File::delete( $file_to_save ); 
        }
        \PDF::loadHTML($html)->setPaper('a4')->save($file_to_save);
        return $file_to_save;
    }

    /**
     * @param mixed $addon
     * @param mixed $ticket
     * 
     * @return [type]
     */
    private static function _getTicketPrice($addon, $ticket)
    {
        $price = $addon->price - $addon->discount;
        if ($ticket->vat > 0) {
            $price = $price + (($ticket->vat * $price) / 100);
        }

        return $price;
    }

    /**
     * @param mixed $type
     * 
     * @return [type]
     */
    private static function _is_valid_type($type)
    {
        return !(array_search($type,self::$_types) === false);
    }

    /**
     * @param mixed $addon_id
     * @param mixed $type
     * 
     * @return [type]
     */
    public static function createTicket($addon_id, $type)
    {
        if(!self::_is_valid_type($type)) {
            throw new \Exception('Invalid ticket type.');
        }

        if($type == 'billing') {
            $addon = \App\Models\BillingOrderAddon::where('id', '=', $addon_id)->with('ticket_item')->first();
            $type = '\App\Models\BillingOrderAddon';
        } else {
            $addon = \App\Models\EventCheckInTicketOrderAddon::where('id', '=', $addon_id)->with('ticket_item')->first();
            $type = '\App\Models\EventCheckInTicketOrderAddon';
        }

        $ticket_item = $addon->ticket_item;

        if($ticket_item->status != '1') { return false; } //disallow ticket creation if ticket item is inactive

        $ticket_item_id = $ticket_item->id;

        $eventId = $ticket_item->event_id;

        $created_tickets = [];

        for($i = 1; $i <= $addon->qty; $i++)
        {
            $obj = new \App\Models\EventTicket();
            $obj->addon_id = $addon_id;
            $obj->ticket_item_id = $ticket_item_id;
            $obj->addon_type = $type;
            $obj->serial = self::_generateSerial($eventId,$ticket_item_id, $type);
            $obj->event_id = $eventId;
            $obj->save();
            $obj->qr_string = self::_generateQRString($obj->id);
            $obj->save();
            $validity = self::_getValidity($ticket_item_id);
            foreach($validity as $vd)
            {
                $obj->validity()->create([
                    'valid_from' => $vd['valid_from'],
                    'valid_to' => $vd['valid_to'],
                    'usage_limit' => $vd['usage_limit']
                ]);
            }
            $created_tickets[] = $obj->id;
        }

        return $created_tickets;
    }

    /**
     * @param mixed $eventId
     * @param mixed $ticket_item_id
     * @param mixed $type
     * 
     * @return [type]
     */
    private static function _generateSerial($eventId, $ticket_item_id, $type)
    {
        $lastSr = \App\Models\EventTicket::where('event_id','=',$eventId)->where('ticket_item_id','=',$ticket_item_id)->orderBy('id','DESC')->value('serial');
        if($lastSr)
        {
            $tmp = explode('-',$lastSr);
            //Add padding to serial
            $l = strlen($tmp[1]);
            $intVal = (int)$tmp[1];
            $intVal = $intVal + 1;
            $intLen = strlen((string)$intVal);
            $diff = $l - $intLen;
            if($diff > 0)
            {
                $str = str_pad($intVal,$l,'0',STR_PAD_LEFT);
            } else {
                $str = $intVal;
            }
            return $tmp[0].'-'.$str;
        } else {

            if($type == 'billing')
            {
                $item = \App\Models\BillingItem::with('ticket_config')->where('id','=',$ticket_item_id)->first();
            } else {
                $item = \App\Models\EventCheckinTicketItem::with('ticket_config')->where('id', '=', $ticket_item_id)->first();
            }

            $ticketConfig = $item->ticket_config;

            return $ticketConfig->prefix .'-'.$ticketConfig->serial_start;
        }
    }

    /**
     * @param mixed $ticketID
     * 
     * @return [type]
     */
    private static function _generateQRString($ticketID)
    {
        $prefix = 'ebticket=';
        $hash = hash_hmac('md5',$ticketID,self::$_key);
        return $prefix.$hash;
    }

    /**
     * @param mixed $ticket_item_id
     * 
     * @return [type]
     */
    private static function _getValidity($ticket_item_id)
    {
        $arr = \App\Models\EventCheckinTicketItem::where('id', '=', $ticket_item_id)->with('ticket_validity')->first();
        if(count($arr['ticket_validity']) < 1) { return false; }
        return $arr['ticket_validity'];
    }
}
