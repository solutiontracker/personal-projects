<?php

namespace App\Eventbuizz\Repositories;

use App\Models\BillingOrder;
use App\Models\BillingOrderAttendee;
use App\Models\EventAttendee;
use App\Models\EventEmailTemplate;
use App\Models\Events;
use App\Models\EventSetting;
use App\Models\EventsiteSetting;
use App\Models\EventWaitingListSetting;
use App\Models\Timezone;
use App\Models\WaitingListAttendee;
use Illuminate\Http\Request;
use App\Mail\Email;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;
use App\Events\RegistrationFlow\Event;

class EventsiteBillingOrderRepository extends AbstractRepository
{
    protected $request;

    protected $eventsiteBillingItemRepository;
    protected $eventSettingRepository;

    public function __construct(Request $request, EventsiteBillingItemRepository $eventsiteBillingItemRepository, EventSettingRepository $eventSettingRepository)
    {
        $this->request = $request;
        $this->eventsiteBillingItemRepository = $eventsiteBillingItemRepository;
        $this->eventSettingRepository = $eventSettingRepository;
    }

    public function attendeeOrder($attendee_id, $event_id)
    {
        $order = \App\Models\BillingOrder::with('order_attendees', 'child_orders')
            ->where('event_id', '=', $event_id)
            ->where('status', '<>', 'cancelled')
            ->where('status', '<>', 'rejected')
            ->where('is_archive', '=', '0')
            ->whereHas('order_attendees', function ($q) use ($attendee_id) {
                $q->where('attendee_id', '=', $attendee_id);
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if (!$order) {
            return false;
        } else if ((count($order['child_orders'] ?? []) > 0)) {
            //if a parent order with children return false
            return false;
        } else if (($order['parent_id'] != 0) && $this->_hasNewerVersion($order['id'], $order['parent_id'], $event_id)) {
            //if a child order and newer version of order exists then return false. This is an older version.
            return false;
        } else {
            return $order;
        }
    }

    private function _hasNewerVersion($order_id, $parent_id, $event_id)
    {
        $ord = \App\Models\BillingOrder::where('event_id', '=', $event_id)->where('parent_id', '=', $parent_id)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();
        if ($ord['id'] != $order_id) {
            return true;
        }
        return false;
    }

    public function isOrderWithoutItems($order_id)
    {
        $count = \App\Models\BillingOrder::where('id', $order_id)->with('order_addons')->count();
        return ($count < 1);
    }

    static public function cancelledOrders($event_id, $count = false)
    {
        //Cancelled orders
        $query = \App\Models\BillingOrder::where('event_id', $event_id)->where('is_archive', 0)->where('status', "cancelled")->where('is_waitinglist', '=', '0')->currentOrder();

        if ($count == true) {
            return $query->count();
        } else {
            return $query->get();
        }
    }

    static public function waitingListOrders($event_id, $count = false)
    {
        //Total orders
        $query = \App\Models\BillingOrder::where('event_id', $event_id)->where('is_waitinglist', '1')->where('is_archive', 0)->currentOrder();

        if ($count == true) {
            return $query->count();
        } else {
            return $query->get();
        }
    }

    static public function totalOrders($event_id, $count = false)
    {
        //Total orders
        $query = \App\Models\BillingOrder::where('event_id', $event_id)->where('is_archive', 0)->currentOrder();

        if ($count == true) {
            return $query->count();
        } else {
            return $query->get();
        }
    }

    /**
     * activeOrders
     *
     * @param mixed $formInput
     * @param mixed $count
     * @param mixed $ids
     * @return void
     */
    static public function activeOrders($formInput, $count = false, $ids = false)
    {
        //Total orders
        $query = \App\Models\BillingOrder::where('event_id', $formInput["event_id"])->where('is_archive', '=', '0')->currentOrder();

        if (isset($formInput['status']) && is_array($formInput['status'])) {
            $query->whereIn('status', $formInput['status']);
        } else if (isset($formInput['status']) && !is_array($formInput['status'])) {
            $query->where('status', $formInput['status']);
        }

        if(isset($formInput['waiting_list'])){
            $query->where('is_waitinglist', $formInput['waiting_list']);
        }

        if ($count) {
            return $query->count();
        } else if ($ids) {
            return $query->pluck('id');
        } else {
            return $query->get();
        }
    }

    /**
     * getOrderAssignedAttendees
     *
     * @param mixed $formInput
     * @param mixed $count
     * @return void
     */
    static public function getOrderAssignedAttendees($formInput, $count = false)
    {
        $query = \App\Models\BillingOrderAttendee::whereIn('order_id', $formInput['order_ids']);

        if (isset($formInput['registration_form_id']) && $formInput['registration_form_id']) {
            $query->where('registration_form_id', $formInput['registration_form_id']);
        }

        if ($count) {
            return $query->count();
        } else {
            return $query->get();
        }
    }

    /**
     * @param $formInput
     * @param $is_archived
     * @return array
     */
    static public function getOrders($formInput, $is_archived = 0)
    {
        $setting = \App\Models\EventsitePaymentSetting::where('event_id', $formInput['event_id'])
            ->where('registration_form_id', 0)
            ->first();

        $isWaitingList = $formInput["is_waiting_list"] === true ? 1 : 0;

        $is_free = $setting->eventsite_billing == 1 ? 0 : 1;

        $searchOperator = '<>';

        $searchField = 'status';

        $searchValue = 'null';

        $searchKey = $formInput['query'] != '' ? $formInput['query'] : '';

        if ($formInput['type'] != 'all') {
            if ($formInput['type'] == 'completed') {
                $searchOperator = '=';
                $searchValue = 'completed';
            } elseif ($formInput['type'] == 'cancelled' || $formInput['type'] == 'cancelled_without_creditnote') {
                $searchOperator = '=';
                $searchValue = $formInput['type'];
            } elseif ($formInput['type'] == 'pending') {
                $searchOperator = '=';
                $searchValue = 'pending';
            }
        }

        $valid_order_ids = BillingOrder::where('event_id', $formInput['event_id'])
            ->where('is_free', $is_free)
            ->where('is_archive', $is_archived)
            ->currentOrder()
            ->pluck('id');

        if(isset($formInput['registration_form_id']) && $formInput['registration_form_id'] > 0) {
            $valid_order_ids = \App\Models\BillingOrderAttendee::whereIn('order_id', $valid_order_ids)->where('registration_form_id', $formInput['registration_form_id'])->pluck('order_id');
        }

        if (trim($searchKey) != '') {

            if (!is_numeric(trim($formInput['query']))) {

                $result = BillingOrder::join('conf_attendees', function ($join) {
                    $join->on('conf_attendees.id', '=', 'conf_billing_orders.attendee_id');
                })
                    ->join('conf_attendees_info AS b_title', function ($join) use ($formInput) {
                        $join->on('conf_attendees.id', '=', 'b_title.attendee_id')
                            ->where('b_title.languages_id', '=', $formInput['language_id']);
                    })
                    ->join('conf_attendees_info AS b_company', function ($join) use ($formInput) {
                        $join->on('conf_attendees.id', '=', 'b_company.attendee_id')
                            ->where('b_company.languages_id', '=', $formInput['language_id']);
                    })->leftJoin('conf_billing_order_attendees', function ($join) {
                        $join->on('conf_billing_orders.id', '=', 'conf_billing_order_attendees.order_id');
                    })
                    ->join('conf_attendees AS additional', function ($join) {
                        $join->on('additional.id', '=', 'conf_billing_order_attendees.attendee_id');
                    })->where(function ($query) use ($searchKey) {
                        $query->where(\DB::raw('CONCAT(conf_attendees.first_name, " ", conf_attendees.last_name)'), 'LIKE', '%' . trim($searchKey) . '%')
                            ->orWhere('conf_attendees.email', 'LIKE', '%' . trim($searchKey) . '%')
                            ->orWhere('b_title.value', 'LIKE', '%' . trim($searchKey) . '%')
                            ->orWhere('b_company.value', 'LIKE', '%' . trim($searchKey) . '%')
                            ->orWhere('additional.email', 'LIKE', '%' . trim($searchKey) . '%')
                            ->orWhere(\DB::raw('CONCAT(additional.first_name, " ", additional.last_name)'), 'LIKE', '%' . trim($searchKey) . '%');
                    })
                    ->where('conf_billing_orders.event_id', '=', $formInput['event_id'])
                    ->where('conf_billing_orders.is_free', '=', $is_free)
                    ->whereIn('conf_billing_orders.id', $valid_order_ids)
                    ->where('conf_billing_orders.is_waitinglist', $isWaitingList)
                    ->where('conf_billing_orders.is_archive', '=', $is_archived)
                    ->where('conf_billing_orders.language_id', '=', $formInput['language_id'])
                    ->where('b_title.name', '=', 'title')
                    ->where('b_company.name', '=', 'company_name')
                    ->select(array('conf_billing_orders.id as billing_order_id', 'conf_attendees.*', 'b_title.value as title', 'b_company.value as company', 'conf_billing_order_attendees.*'))
                    ->get();

                $temp_order_id = array();

                foreach ($result as $row) {
                    $temp_order_id[] = $row->billing_order_id;
                }

            } else {
                $temp_order_id = $valid_order_ids;
            }
        }

        $query = BillingOrder::join("conf_attendees", "conf_attendees.id", "=", "conf_billing_orders.attendee_id")
            ->where('conf_billing_orders.event_id', '=', $formInput['event_id'])
            ->where('conf_billing_orders.is_archive', '=', $is_archived)
            ->where('conf_billing_orders.is_free', '=', $is_free)
            ->with([
                'order_attendee.info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', '=', $formInput['language_id']);
                },
                'order_attendees',
                'order_attendees.attendee_detail'
            ]);

        //get waiting list orders
        if ($formInput["is_waiting_list"] === true) {
            $query->where('conf_billing_orders.is_waitinglist', $isWaitingList);
        } else {
            //get normal orders
            $query->where('conf_billing_orders.is_waitinglist', $isWaitingList);
        }

        //Filter date range
        if (isset($formInput['fromDate']) && $formInput['fromDate']) {
            $query->whereDate('conf_billing_orders.created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
        }
        if (isset($formInput['toDate']) && $formInput['toDate']) {
            $query->whereDate('conf_billing_orders.created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
        }

        if (trim($searchKey) != '') {
            if (is_numeric(trim($formInput['query']))) {
                $query->whereIn('conf_billing_orders.id', $valid_order_ids)
                    ->where('conf_billing_orders.order_number', '=', $formInput['query']);
            } else {
                if ($searchValue == 'cancelled_without_creditnote') {
                    $query->whereIn('conf_billing_orders.id', $temp_order_id)
                        ->where('conf_billing_orders.is_cancelled_wcn', 1)
                        ->where("conf_billing_orders." . $searchField, $searchOperator, 'cancelled');
                } else {
                    $query->whereIn('conf_billing_orders.id', $temp_order_id)
                        ->where("conf_billing_orders." . $searchField, $searchOperator, $searchValue);
                }
            }
        } else {
            if ($searchValue == 'cancelled_without_creditnote') {
                $query->whereIn('conf_billing_orders.id', $valid_order_ids)
                    ->where('conf_billing_orders.is_cancelled_wcn', 1)
                    ->where("conf_billing_orders." . $searchField, $searchOperator, 'cancelled');
            } else {
                $query->whereIn('conf_billing_orders.id', $valid_order_ids)
                    ->where("conf_billing_orders." . $searchField, $searchOperator, $searchValue);
            }
        }

        if ($formInput['payment_status'] == 'payment_received') {
            $query->where("conf_billing_orders.is_payment_received", "=", '1');
        } elseif ($formInput['payment_status'] == 'payment_pending') {
            $query->where("conf_billing_orders.is_payment_received", "=", '0');
        }
        if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && in_array($formInput['sort_by'], ['first_name', 'email']))) {
            $query->orderBy("conf_attendees." . $formInput['sort_by'], $formInput['order_by']);
        } else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && in_array($formInput['sort_by'], ['order_number', 'order_date', 'grand_total', 'status']))) {
            $query->orderBy("conf_billing_orders." . $formInput['sort_by'], $formInput['order_by']);
        } else {
            $query->orderBy('conf_billing_orders.order_number', 'DESC');
        }

        $result = $query->select('conf_attendees.*', 'conf_billing_orders.*')->paginate($formInput['limit'])->toArray();

        foreach ($result['data'] as $key => $row) {
            //get the company name
            $attendeeInfoArray = $row['order_attendee']['info'];

            //loop through all rows to find the row
            //with company name then get its value
            foreach ($attendeeInfoArray as $attendeeInfo) {
                if ($attendeeInfo["name"] === "company_name") {
                    $row['company_name'] = $attendeeInfo["value"];
                }
            }

            //get waiting list attendees
            $waitingListAttendee = WaitingListAttendee::where("event_id", $formInput['event_id'])
                ->where("attendee_id", $row["attendee_id"])
                ->whereNull("deleted_at")->first();

            //get order attendee status
            if($waitingListAttendee) {
                $waitingListAttendee = $waitingListAttendee->toArray();

                $row["order_attendee_status"] = WaitingListAttendee::getOrderAttendeeStatus($waitingListAttendee['status'],
                    [
                        "event_id" => $formInput["event_id"],
                        "date_sent" => $waitingListAttendee['date_sent'],
                        "attendee_id" => $row['attendee_id']
                    ]
                );
            }

            $row['first_name'] = $row['order_attendee']['first_name'];
            $row['last_name'] = $row['order_attendee']['last_name'];
            $row['email'] = $row['order_attendee']['email'];
            $row['phone'] = $row['order_attendee']['phone'];
            $row['order_tickets'] = count($row['order_attendees']);
            $result['data'][$key] = $row;
        }

        //sort by status col that is in conf_wiatinglist_attendees table
        //and we are not joining that table in our query already but
        //getting the status manually
        //only applicable when the orders type is waiting list
        if(!empty($formInput["sort_by"]) && !empty($formInput['order_by']) && $formInput["sort_by"] == "order_attendee_status" && $isWaitingList === 1){
            $sortedByStatus = collect($result["data"])->sortBy([
                ["order_attendee_status", Str::lower($formInput["order_by"])]
            ]);
            $result["data"] = $sortedByStatus->values()->all();
        }

        return $result;
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function generatePdfForTicketItems($order)
    {
        $ticketsIds = $order->getGeneratedTicketsIds();
        if (count($ticketsIds) > 0) {
            $ticketsPdf = EventTicketRepository::generatePDF($ticketsIds, $order);
            $order->setTicketsPDFFile($ticketsPdf);
        }
    }

    /**
     * @param mixed $order
     * @param mixed $addon
     *
     * @return [type]
     */
    public function attachAttendee($order, $addon)
    {
        if ($addon->getModel()->link_to_id > 0 || ($addon->getModel()->link_to != 'attendee_group' && $addon->getModel()->link_to_id != '0')) {
            $link_to = $addon->getModel()->link_to;
            $link_to_id = $addon->getModel()->link_to_id;
            $attendee_id = $addon->getModel()->attendee_id;
            $programs = [];
            if ($link_to == 'workshop') {
                $programs = $this->getPrograms('workshop_id', $link_to_id, $order->getUtility()->getEventId(), $order->getUtility()->getLangaugeId());
            } elseif ($link_to == 'track') {
                $programs = $this->getPrograms('track_id', $link_to_id, $order->getUtility()->getEventId(), $order->getUtility()->getLangaugeId());
            } elseif ($link_to == 'program') {
                $programs = $this->getPrograms('id', $link_to_id, $order->getUtility()->getEventId(), $order->getUtility()->getLangaugeId());
            } elseif ($link_to == 'attendee_group') {
                $groups = $this->getEventGroups($link_to_id, $order->getUtility()->getEventId(), $order->getUtility()->getLangaugeId());
                if (count((array)$groups) > 0) {
                    $assigned = [];
                    foreach ($groups as $group) {
                        if ($group['parent']['allow_multiple'] == 1) {
                            if (!in_array($group['parent_id'], $assigned)) {
                                $group_ids = \App\Models\EventGroup::where('event_id', $order->getUtility()->getEventId())->where('parent_id', $group['parent_id'])->pluck('id')->toArray();
                                $result = \App\Models\EventAttendeeGroup::where('attendee_id', $attendee_id)->whereIn('group_id', $group_ids)->get();
                                if (count($result) > 0) {
                                    $assigned[] = $group['parent_id'];
                                } else {
                                    $assigned[] = $group['parent_id'];
                                    $values_array = array('group_id' => $group['id'], 'attendee_id' => $attendee_id);
                                    $match_array = array('group_id' => $group['id'], 'attendee_id' => $attendee_id);
                                    \App\Models\EventAttendeeGroup::updateOrCreate($match_array, $values_array);
                                }
                            }
                        } else {
                            $values_array = array('group_id' => $group['id'], 'attendee_id' => $attendee_id, 'linked_from' => 'billing_item');
                            $match_array = array('group_id' => $group['id'], 'attendee_id' => $attendee_id);
                            \App\Models\EventAttendeeGroup::updateOrCreate($match_array, $values_array);
                        }
                    }
                }
            }
            if (count((array)$programs) > 0) {
                foreach ($programs as $rec) {
                    $values_array = array('agenda_id' => $rec['id'], 'attendee_id' => $addon->getModel()->attendee_id, 'linked_from' => 'billing_item', 'link_id' => $link_to_id);
                    $match_array = array('agenda_id' => $rec['id'], 'attendee_id' => $addon->getModel()->attendee_id);
                    \App\Models\EventAgendaAttendeeAttached::updateOrCreate($match_array, $values_array);
                }
            }
        }
    }

    /**
     * @param mixed $id_type
     * @param mixed $link_to_id
     * @param string $event_id
     * @param string $language_id
     *
     * @return [type]
     */
    public function getPrograms($id_type, $link_to_id, $event_id, $language_id)
    {
        if ($id_type == 'track_id') {
            $data = \App\Models\EventAgenda::where('event_id', $event_id)
                ->with(['info' => function ($query) use ($language_id) {
                    return $query->where('languages_id', $language_id);
                },])
                ->whereHas('tracks.info', function ($q) use ($link_to_id) {
                    $q->where('track_id', $link_to_id);
                })
                ->orderBy('start_date', 'desc')
                ->get()
                ->toArray();
        } else {
            $data = \App\Models\EventAgenda::where('event_id', $event_id)
                ->where($id_type, $link_to_id)
                ->with(['info' => function ($query) use ($language_id) {
                    return $query->where('languages_id', $language_id);
                },
                ])
                ->orderBy('start_date', 'desc')
                ->get()
                ->toArray();
        }

        $rec_workshop_info = array();

        foreach ($data as $tracks) {
            $rec_workshop_info['id'] = $tracks['id'];
            foreach ($tracks['info'] as $track_info) {
                $rec_workshop_info[$track_info['name']] = $track_info['value'];
            }
            unset($tracks['info']);
            $rec_tracks[] = $rec_workshop_info;
        }

        return $rec_tracks;
    }

    /**
     * @param mixed $link_to_id
     * @param mixed $event_id
     * @param mixed $language_id
     *
     * @return [type]
     */
    public function getEventGroups($link_to_id, $event_id, $language_id)
    {
        $groups = \App\Models\EventGroup::where('event_id', $event_id)->whereIn('id', explode(',', $link_to_id))
            ->with(['info' => function ($query) use ($language_id) {
                return $query->where('languages_id', $language_id);
            }])
            ->with(['parent' => function ($r) {
                return $r->whereNull('deleted_at')
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('id', 'asc');
            }, 'parentInfo' => function ($r) use ($language_id) {
                return $r->whereNull('deleted_at')
                    ->where('languages_id', $language_id);
            }])
            ->whereNull('deleted_at')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return $groups;
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function addWaitingList($order)
    {
        if ($order->getModelAttribute('is_waitinglist') == '0') {
            //Do not proceed, this is not a waiting list order.
            return;
        }

        $event_id = $order->getOrderEventId();

        $main_attendee_id = $order->getMainAttendee()->getModel()->id;

        $type = '1';

        if ($order->getUtility()->getData('is_tango') == '1') {
            $type = '2';
        }

        $waiting_record = \App\Models\WaitingListAttendee::withTrashed()->where('event_id', '=', $event_id)->where('attendee_id', '=', $main_attendee_id)->first();
        if ($waiting_record) {
            $waiting_record->order_data = serialize($order->getUtility()->getAllData());
            $waiting_record->status = 0;
            $waiting_record->deleted_at = null;
        } else {
            $waiting_record = new \App\Models\WaitingListAttendee();
            $waiting_record->attendee_id = $main_attendee_id;
            $waiting_record->order_data = serialize($order->getUtility()->getAllData());
            $waiting_record->event_id = $event_id;
            $waiting_record->status = 0;
            $waiting_record->type = $type;
        }

        $waiting_record->save();

        //send email
        $this->sendWaitingListEmail($order, $order->getMainAttendee()->getModel()->toArray());
    }

    /**
     * @param mixed $attendee
     * @param mixed $order_id
     *
     * @return [type]
     */
    public function sendWaitingListEmail($order, $attendee)
    {
        $event = \App\Models\Event::where('id', $order->getOrderEventId())->first();

        $language_id = $order->getUtility()->getLangaugeId();

        $order_id = $order->getModel()->id;

        $registration_form = $order->getRegistrationForm($attendee['id']);

        $registration_form_id = $registration_form ? $registration_form->id : 0;
//get template
        $templateData = (object)TemplateRepository::getTemplateDataByAlias(['event_id' => $event->id, 'alias'=>'waiting_list_registration_confirmation' , 'registration_form_id' => $registration_form_id, 'language_id' => $language_id]);

        $alias = $templateData->alias;

        $template = $templateData->template;

        $subject = $templateData->subject;

        $template = getEmailTemplate($template, $order->getOrderEventId());

        $content = stripslashes($template);

        $event_setting = EventSettingRepository::getEventSetting(["event_id" => $order->getOrderEventId(), "language_id" => $order->getUtility()->getLangaugeId()]);

        if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
            $src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
        } else {
            $src = cdn('/_admin_assets/images/eventbuizz_logo.png');
        }

        $logo = '<img src="' . $src . '" width="150" />';

        if($event->registration_form_id == 1) {
            $content = str_replace("{cancel_link}", '<a style="color:#324c59 !important;" href="'.\Config::get('app.reg_flow_url') . '/' . $event['url'].'/attendee/cancel-waitinglist-order/'.$order_id.'">Waiting list cancel</a>', $content);
            $content = str_replace("{accept_link}",'<a style="color:#324c59 !important;" href="'.\Config::get('app.reg_flow_url') . '/' . $event['url'].'/attendee/order-summary/'.$order_id.'/1">Accept</a>', $content);
        } else {
            //accept link
            $orderCompletionUrl = cdn('/event/' . $event->url . '/detail/waitinglist/ordercompletion/' . $order);
            $waitingListAcceptLinkLabel = 'Accept';
            $orderCompletionUrl = "<a style=\"color:#324c59 !important;\" href=\"$orderCompletionUrl\">$waitingListAcceptLinkLabel</a>";

            //replace
            $content = str_replace("{accept_link}", $orderCompletionUrl, $content);

            //cancel link
            $orderCancellationUrl = cdn('/event/' . $event->url . '/detail/waitinglist/ordercancellation/' . $order_id);
            $waitingListCancelLinkLabel = 'Waiting list cancel';
            $orderCancellationUrl = "<a style=\"color:#324c59 !important;\" href=\"$orderCancellationUrl\">$waitingListCancelLinkLabel</a>";

            //replace
            $content = str_replace("{cancel_link}",$orderCancellationUrl, $content);
        }

        $subject = str_replace("{event_name}", stripslashes($event['name']), $subject);
        $content = str_replace("{event_logo}", $logo, $content);
        $content = str_replace("{event_name}", stripslashes($event['name']), $content);
        $content = str_replace("{attendee_name}", stripslashes($attendee['first_name']), $content);
        $content = str_replace("{first_name}", stripslashes($attendee['first_name']), $content);
        $content = str_replace("{last_name}", stripslashes($attendee['last_name']), $content);
        $content = str_replace("{email}", stripslashes($attendee['email']), $content);
        $content = str_replace("{event_organizer_name}", "" . $event['organizer_name'], $content);

        $recipientEmail = $attendee['email'];

        $data = array();
        $data['event_id'] = $event->id;
        $data['template'] = $alias;
        $data['subject'] = $subject;
        $data['content'] = $content;
        $data['view'] = 'email.plain-text';
        $data['from_name'] = $event['organizer_name'];
        if ($recipientEmail) \Mail::to($recipientEmail)->send(new Email($data));
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function addGdprLog($order)
    {
        $event_id = $order->getOrderEventId();
        $attendee_id = $order->getMainAttendee()->getModel()->id;
        $bit = $order->getMainAttendee()->getGdpr();
        $settings = \App\Models\EventGdprSetting::where('event_id', $event_id)->whereNull('deleted_at')->first();
        if ($settings->enable_gdpr) {
            if ($bit == NULL) $bit = 0;
            $event_gdpr = \App\Models\EventGdpr::where('event_id', '=', $event_id)->whereNull('deleted_at')->first();
            $gdpr_date['event_id'] = $event_id;
            $gdpr_date['attendee_id'] = $attendee_id;
            $gdpr_date['gdpr_accept'] = $bit;
            $gdpr_date['gdpr_description'] = $event_gdpr->description;
            \App\Models\GdprAttendeeLog::create($gdpr_date);
            if ($bit == 0) {
                GdprRepository::sendGdprEmail($event_id, $attendee_id);
            }
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function addFoodsAllergiesLog($order)
    {
        $event_id = $order->getOrderEventId();
        $foods_allergies = \App\Models\EventFoodAllergies::where('event_id', $event_id)->first();
        foreach ($order->getAllAttendees() as $attendee) {
            $order_attendee_model = $attendee->getOrderAttendee();
            if ((int)$order_attendee_model->accept_foods_allergies == 1) {
                $data['event_id'] = $event_id;
                $data['attendee_id'] = $attendee->getModel()->id;
                $data['food_accept'] = (int)$order_attendee_model->accept_foods_allergies;
                $data['food_description'] = $foods_allergies->description;
                \App\Models\FoodAllergiesAttendeeLog::create($data);
            }
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function addReportingRevenue($order)
    {
        $order_detail = $order->getModel();

        $reporting = \App\Models\ReportingRevenueTable::where('event_id', '=', $order_detail->event_id)->whereDate('date', '=', date('Y-m-d'))->first();

        $order_attendees = \App\Models\BillingOrderAttendee::where('order_id', '=', $order_detail->id)->count();

        if ($reporting) {
            
            if ($order_detail->is_waitinglist == '1') {
                $reporting->waiting_order_ids .= $order_detail->id . ',';
                $reporting->waiting_tickets += $order_attendees;
            } else {
                $reporting->order_ids .= $order_detail->id . ',';
                $reporting->total_tickets += $order_attendees;
                $reporting->total_revenue += $order_detail->reporting_panel_total;
            }

            $reporting->save();

        } else {

            $reporting = new \App\Models\ReportingRevenueTable();

            if ($order_detail->is_waitinglist == '1') {
                $reporting->waiting_order_ids .= $order_detail->id . ',';
                $reporting->waiting_tickets += $order_attendees;
            } else {
                $reporting->order_ids .= $order_detail->id . ',';
                $reporting->total_tickets += $order_attendees;
                $reporting->total_revenue += $order_detail->reporting_panel_total;
            }

            $reporting->event_id = $order_detail->event_id;

            $reporting->date = date('Y-m-d');

            $reporting->save();
            
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function updateReportingRevenue($order)
    {

        $order_detail = $order->getModel();

        if ($order->getPreviousVersion() instanceof \App\Eventbuizz\EBObject\EBOrder) {

            $previous_order = $order->getPreviousVersion()->getModel();

            if (is_object($order_detail) && is_a($order_detail, '\App\Models\BillingOrder')) {
                $order = $order_detail->toArray();
            } else {
                $order = object_to_array($order_detail);
            }

            if (is_object($previous_order) && is_a($previous_order, '\App\Models\BillingOrder')) {
                $previous_order = $previous_order->toArray();
            } else {
                $previous_order = object_to_array($previous_order);
            }

            $order = \App\Models\BillingOrder::where('id', $order_detail['id'])->first();

            $previous_order = \App\Models\BillingOrder::where('id', $previous_order['id'])->first();

            $order_attendees = \App\Models\BillingOrderAttendee::where('order_id', $order['id'])->count();

            $previous_order_attendees = \App\Models\BillingOrderAttendee::where('order_id', $previous_order['id'])->count();

            $reporting_object = \App\Models\ReportingRevenueTable::where('event_id', $order['event_id'])->whereDate('date', date('Y-m-d', strtotime($previous_order['order_date'])))->first();

            if ($reporting_object) {
                $order_ids = str_replace($previous_order['id'] . ',', '', $reporting_object->order_ids);
                $order_ids = str_replace($previous_order['clone_of'] . ',', '', $order_ids);
                $order_ids .= $order['id'] . ',';
                $reporting_object->order_ids = $order_ids;
                $reporting_object->total_tickets = ($reporting_object->total_tickets + $order_attendees) - $previous_order_attendees;
                $reporting_object->total_revenue = ($reporting_object->total_revenue + $order['reporting_panel_total']) - $previous_order['reporting_panel_total'];
                $reporting_object->save();
            } else {
                $reporting_object = new \App\Models\ReportingRevenueTable();
                $reporting_object->order_ids .= $order['id'] . ',';
                $reporting_object->total_tickets += $order_attendees;
                $reporting_object->total_revenue += $order['reporting_panel_total'];
                $reporting_object->event_id = $order['event_id'];
                $reporting_object->date = date('Y-m-d');
                $reporting_object->save();
            }

        }

    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function sendAttendeeVerificationEmail($order)
    {
        $isRegVerificationEnabled = $order->getAttendeeSettingsAttribute('attendee_reg_verification');

        //Do nothing because registration verification settings are off or order is a waitinglist order.
        if ($isRegVerificationEnabled == false || $order->getModelAttribute('is_waitinglist') == '1') {
            return;
        }

        //Event attendee model
        $event_attendee_model = $order->getMainAttendee()->getEventAttendeeModel();

        //Send email
        $event_id = $order->getOrderEventId();

        $event = \App\Models\Event::where('id', $order->getOrderEventId())->first();

        //labels
        $labels = eventsite_labels('eventsite', ["event_id" => $event_id, "language_id" => $event->language_id]);

        //Find out where to get inital and gender fields and pass into email function
        $attendee_model = $order->getMainAttendee()->getModel();

        $language_id = $order->getUtility()->getLangaugeId();

        $registration_form = $order->getRegistrationForm($attendee_model->id);

        $registration_form_id = $registration_form ? $registration_form->id : 0;

        //get template
        $templateData = (object)TemplateRepository::getTemplateDataByAlias(['event_id' => $event['id'], 'alias'=>'indentity_verification' , 'registration_form_id' => $registration_form_id, 'language_id' => $language_id]);

        $alias = $templateData->alias;

        $template = $templateData->template;

        $subject = $templateData->subject;

        $template = getEmailTemplate($template, $order->getOrderEventId());

        $content = stripslashes($template);

        $event_setting = EventSettingRepository::getEventSetting(["event_id" => $order->getOrderEventId(), "language_id" => $order->getUtility()->getLangaugeId()]);

        if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
            $src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
        } else {
            $src = cdn('/_admin_assets/images/eventbuizz_logo.png');
        }

        $logo = '<img src="' . $src . '" width="150" />';

        $gender = "";

        $content = str_replace("{body_content}", stripslashes($content), $content);
        $content = str_replace("{event_name}", stripslashes($event['name']), $content);
        $content = str_replace("{attendee_name}", stripslashes($attendee_model->first_name . ' ' . $attendee_model->last_name), $content);
        $content = str_replace("{initial}", "", $content);
        $content = str_replace("{first_name}", stripslashes($attendee_model->first_name), $content);
        $content = str_replace("{last_name}", stripslashes($attendee_model->last_name), $content);
        $content = str_replace("{gender}", stripslashes($gender), $content);
        $content = str_replace("{link}", '<a href="'.config('app.reg_site_url').'/'.$event->url.'?validateAttendee='.$attendee_model->id.'&verification_id='.$event_attendee_model->verification_id.'">' . $labels['EVENTSITE_ACTIVATE_ACCOUNT'] . '</a>', $content);
        $content = str_replace("{event_organizer_name}", stripslashes($event['organizer_name']), $content);
        $content = str_replace("{event_logo}", $logo, $content);

        $recipientEmail = $attendee_model->email;

        $data = array();
        $data['event_id'] = $event->id;
        $data['template'] = $alias;
        $data['subject'] = $subject;
        $data['content'] = $content;
        $data['view'] = 'email.plain-text';
        $data['from_name'] = $event['organizer_name'];
        if ($recipientEmail && !Str::contains($recipientEmail, '@mch.dk')) \Mail::to($recipientEmail)->send(new Email($data));
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function sendXml($order)
    {
        $event = $order->_getEvent();

        $language_id = $order->getUtility()->getLangaugeId();

        $event_id = $order->getOrderEventId();

        $payment_setting = $order->_getPaymentSetting();

        $eventSetting = $order->_getEventSetting();

        $order_detail = $order->getInvoiceSummary();

        $order_vat = ($order->getPaymentSettingAttribute('eventsite_apply_multi_vat') == 0) ? $order->getVatPercentage() : $order->getModelAttribute('vat');

        $organizer = $order->_getOrganizer();

        $attendee = $order->_getMainAttendeeDetail();

        $info = readArrayKey($attendee, [], 'info');

        $attendee = array_merge($info, $attendee);

        if (trim($order_detail['order']->order_number)) {
            $order_number = $order_detail['order']->order_number;
        } else {
            $order_number = $order_detail['order']->id;
        }

        $billing_fields = $order->_getEventbillingFields();

        $attendeeBilling = $order->getMainAttendee()->getBillingModel();

        $date = date("Y-m-d", strtotime($order_detail['order']->order_date));

        if ($payment_setting['bank_name'] == "0") {
            $payment_due_date = date('Y-m-d', strtotime($date . ' + 14 days'));
        } elseif ($payment_setting['bank_name'] == "1") {
            $payment_due_date = date('Y-m-d', strtotime($date . ' + 21 days'));
        } elseif ($payment_setting['bank_name'] == "2") {
            $payment_due_date = date('Y-m-d', strtotime($date . ' + 8 days'));

        } elseif ($payment_setting['bank_name'] == "3") {
            $payment_due_date = date('Y-m-d', strtotime($date . ' + 30 days'));
        } else {
            $payment_due_date = date("Y-m-d", strtotime($order_detail['order']->order_date));
        }

        if ($payment_setting['eventsite_invoice_prefix'] != '') {
            $order_prefix = $payment_setting['eventsite_invoice_prefix'] . '-';
        }

        $currencies = getCurrencyArray();

        foreach ($currencies as $key => $cur) {
            if ($order_detail['order']->eventsite_currency == $key) {
                $currency = $cur;
            }
        }

        $account_number = $payment_setting['account_number'];

        $voucher = $order->_getVoucher();

        $order_attendees = array();

        $orderAllAttendees = $order->_getOrderAttendees();

        foreach ($orderAllAttendees as $att) {
            if ($att['attendee_id'] != $order_detail['order']->attendee_id) {
                $order_attendees[] = $att;
            }
        }

        $invoice = new \SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><Invoice></Invoice>");
        $invoice->addAttribute('xmlns:xmlns:cbc', "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
        $invoice->addAttribute('xmlns:xmlns:cac', "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");
        $invoice->addAttribute('xmlns:xmlns:ccts', "urn:oasis:names:specification:ubl:schema:xsd:CoreComponentParameters-2");
        $invoice->addAttribute('xmlns:xmlns:udt', "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2");
        $invoice->addAttribute('xmlns:xmlns', "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2");
        $invoice->addChild("xmlns:cbc:UBLVersionID", "2.0");
        $invoice->addChild("xmlns:cbc:CustomizationID", "OIOUBL-2.02");
        $childProfile = $invoice->addChild("xmlns:cbc:ProfileID", "urn:www.nesubl.eu:profiles:profile5:ver2.0");
        $childProfile->addAttribute('xmlns:schemeID', "urn:oioubl:id:profileid-1.2");
        $childProfile->addAttribute('xmlns:schemeAgencyID', "320");
        $invoice->addChild("xmlns:cbc:ID", $order_prefix . $order_number);
        $invoice->addChild("xmlns:cbc:IssueDate", date("Y-m-d", strtotime($order_detail['order']->order_date)));
        $childInvoiceTypeCode = $invoice->addChild("xmlns:cbc:InvoiceTypeCode", "380");
        $childInvoiceTypeCode->addAttribute('xmlns:listID', "urn:oioubl:codelist:invoicetypecode-1.1");
        $childInvoiceTypeCode->addAttribute('xmlns:listAgencyID', "320");
        if ($attendeeBilling['billing_poNumber'] != "") {
            $invoice->addChild("xmlns:cbc:Note", $billing_fields['po_number'] . ": " . $attendeeBilling['billing_poNumber']);
        }
        $invoice->addChild("xmlns:cbc:DocumentCurrencyCode", "DKK");
        $invoice->addChild("xmlns:cbc:LineCountNumeric", "1");
        if($attendeeBilling['billing_poNumber']) {
            $order_ref = $invoice->addChild("xmlns:cac:OrderReference");
            $order_ref->addChild("xmlns:cbc:ID", $attendeeBilling['billing_poNumber']);
        }
        $AccountingSupplierParty = $invoice->addChild("xmlns:cac:AccountingSupplierParty");
        $Party = $AccountingSupplierParty->addChild("xmlns:cac:Party");
        $Party->addChild("xmlns:cbc:EndpointID", $organizer['vat_number'])->addAttribute('xmlns:schemeID', "DK:CVR");
        $Party->addChild("xmlns:cac:PartyName")->addChild("xmlns:cbc:Name", $organizer['first_name'] . ' ' . $organizer['last_name']);
        $PostalAddress = $Party->addChild("xmlns:cac:PostalAddress");
        $AddressFormatCode = $PostalAddress->addChild("xmlns:cbc:AddressFormatCode", "StructuredDK");
        $AddressFormatCode->addAttribute('xmlns:listID', "urn:oioubl:codelist:addressformatcode-1.1");
        $AddressFormatCode->addAttribute('xmlns:listAgencyID', "320");
        $PostalAddress->addChild("xmlns:cbc:StreetName", $organizer['address']);
        $PostalAddress->addChild("xmlns:cbc:BuildingNumber", $organizer['house_number']);
        $PostalAddress->addChild("xmlns:cbc:CityName", $organizer['city']);
        $PostalAddress->addChild("xmlns:cbc:PostalZone", $organizer['zip_code']);
        $Country = $PostalAddress->addChild("xmlns:cac:Country");
        $Country->addChild("xmlns:cbc:IdentificationCode", "DK");
        $Party->addChild("xmlns:cac:PartyLegalEntity")->addChild("xmlns:cbc:CompanyID", $organizer['vat_number'])->addAttribute('xmlns:schemeID', "DK:CVR");
        $contactChild = $Party->addChild("xmlns:cac:Contact");
        $contactChild->addChild("xmlns:cbc:Name", $event['name']);
        $contactChild->addChild("xmlns:cbc:ElectronicMail", $eventSetting['support_email']);
        $AccountingCustomerParty = $invoice->addChild("xmlns:cac:AccountingCustomerParty");
        $Party = $AccountingCustomerParty->addChild("xmlns:cac:Party");
        $EAN = $Party->addChild("xmlns:cbc:EndpointID", $attendeeBilling['billing_ean']);
        $EAN->addAttribute('xmlns:schemeID', "GLN");
        $EAN->addAttribute('xmlns:schemeAgencyID', "9");
        $Party->addChild("xmlns:cac:PartyName")->addChild("xmlns:cbc:Name", htmlentities($attendee['company_name'], ENT_XML1));
        $PostalAddress = $Party->addChild("xmlns:cac:PostalAddress");
        $AddressFormatCode = $PostalAddress->addChild("xmlns:cbc:AddressFormatCode", "StructuredDK");
        $AddressFormatCode->addAttribute('xmlns:listID', "urn:oioubl:codelist:addressformatcode-1.1");
        $AddressFormatCode->addAttribute('xmlns:listAgencyID', "320");
        $PostalAddress->addChild("xmlns:cbc:StreetName", $attendeeBilling['billing_company_street']);
        $PostalAddress->addChild("xmlns:cbc:BuildingNumber", $attendeeBilling['billing_company_house_number']);
        $PostalAddress->addChild("xmlns:cbc:CityName", $attendeeBilling['billing_company_city']);
        $PostalAddress->addChild("xmlns:cbc:PostalZone", $attendeeBilling['billing_company_post_code']);
        $Country = $PostalAddress->addChild("xmlns:cac:Country");
        $Country->addChild("xmlns:cbc:IdentificationCode", "DK");
        $billing_company_registration_number_local = str_replace(' ', '', $attendeeBilling['billing_company_registration_number']);
        $billing_company_registration_number_local = trim(str_replace('DK', '', $billing_company_registration_number_local));
        $Party->addChild("xmlns:cac:PartyLegalEntity")->addChild("xmlns:cbc:CompanyID", "DK" . $billing_company_registration_number_local)->addAttribute('xmlns:schemeID', "DK:CVR");
        $contactChild = $Party->addChild("xmlns:cac:Contact");
        if ($attendeeBilling['billing_bruger_id'] != '') {
            $contactChild->addChild("xmlns:cbc:ID", $attendeeBilling['billing_bruger_id']);
        } else {
            $contactChild->addChild("xmlns:cbc:ID", "n/a");
        }
        $contactChild->addChild("xmlns:cbc:Name", $attendee['first_name'] . ' ' . $attendee['last_name']);
        $contactChild->addChild("xmlns:cbc:Telephone", $attendee['phone']);
        $contactChild->addChild("xmlns:cbc:ElectronicMail", $attendee['email']);
        $PaymentMeans = $invoice->addChild("xmlns:cac:PaymentMeans");
        $PaymentMeans->addChild("xmlns:cbc:PaymentMeansCode", "42");
        $PaymentMeans->addChild("xmlns:cbc:PaymentDueDate", date("Y-m-d", strtotime($payment_due_date)));
        $PaymentChannelCode = $PaymentMeans->addChild("xmlns:cbc:PaymentChannelCode", "DK:BANK");
        $PaymentChannelCode->addAttribute('xmlns:listID', "urn:oioubl:codelist:paymentchannelcode-1.1");
        $PaymentChannelCode->addAttribute('xmlns:listAgencyID', "320");
        $PayeeFinancialAccount = $PaymentMeans->addChild("xmlns:cac:PayeeFinancialAccount");
        $PayeeFinancialAccount->addChild("xmlns:cbc:ID", substr($account_number, 4));
        $childBranch = $PayeeFinancialAccount->addChild("xmlns:cac:FinancialInstitutionBranch");
        $childBranch->addChild("xmlns:cbc:ID", substr($account_number, 0, 4));
        $childBranch->addChild("xmlns:cbc:Name", $payment_setting['bank_name']);
        $childBranch->addChild("xmlns:cac:Address");

        $TaxTotal = $invoice->addChild("xmlns:cac:TaxTotal");
        $TaxTotal->addChild("xmlns:cbc:TaxAmount", number_format((float) $order_detail['order']->vat_amount, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxSubtotal = $TaxTotal->addChild("xmlns:cac:TaxSubtotal");
        $TaxSubtotal->addChild("xmlns:cbc:TaxableAmount", "0.00")->addAttribute('xmlns:currencyID', "DKK");
        $TaxSubtotal->addChild("xmlns:cbc:TaxAmount", number_format((float) $order_detail['order']->vat_amount, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxCategory = $TaxSubtotal->addChild("xmlns:cac:TaxCategory");
        $ID = $TaxCategory->addChild("xmlns:cbc:ID", $order_detail['order']->vat > 0 ? "StandardRated" : "ZeroRated");
        $ID->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxcategoryid-1.1");
        $ID->addAttribute('xmlns:schemeAgencyID', "320");
        $TaxCategory->addChild("xmlns:cbc:Percent", number_format((float) $order_detail['order']->vat, 2, '.', ''));
        $TaxScheme = $TaxCategory->addChild("xmlns:cac:TaxScheme");
        $TaxScheme->addChild("xmlns:cbc:ID", "63")->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxschemeid-1.1");
        $TaxScheme->addChild("xmlns:cbc:Name", "Moms");
        $TaxScheme->addChild("xmlns:cbc:CurrencyCode", "DKK");

        //Extra Elements
        $LegalMonetaryTotal = $invoice->addChild("xmlns:cac:LegalMonetaryTotal");

        //Items
        $i = 0;

        foreach ($order_detail['order_summary_detail']['group_addons'] as $group) {
            foreach ($group['addons'] as $addon) {
                $this->sproomInvoiceItem($order_detail['order'], $invoice, $addon, $i, $order_vat);
                $i++;
            }
        }

        foreach ($order_detail['order_summary_detail']['single_addons'] as $addon) {
            $this->sproomInvoiceItem($order_detail['order'], $invoice, $addon, $i, $order_vat);
            $i++;
        }

        foreach ($order_detail['hotel'] as $hotel) {
            $this->sproomInvoiceHotel($invoice, $hotel, $i, $order_vat);
            $i++;
        }

        $LegalMonetaryTotal->addChild("xmlns:cbc:LineExtensionAmount", number_format((float)$order_detail['order']->grand_total - $order_detail['order']->vat_amount , 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $LegalMonetaryTotal->addChild("xmlns:cbc:TaxExclusiveAmount", number_format((float)$order_detail['order']->vat_amount, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $LegalMonetaryTotal->addChild("xmlns:cbc:TaxInclusiveAmount", number_format((float)$order_detail['order']->grand_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $LegalMonetaryTotal->addChild("xmlns:cbc:PayableAmount", number_format((float)$order_detail['order']->grand_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");

        return $invoice->asXML();
      
    }

    /**
     * sproomInvoiceItem
     *
     * @param mixed $invoice
     * @param mixed $addon
     * @param mixed $i
     * @return void
     */
    public function sproomInvoiceItem($order_modal, $invoice, $addon, $i, $order_level_vat)
    {

        //If voucher order applied [Just for sproom calculation]
        if ($order_modal->discount_type == 'order' && $order_modal->discount_amount > 0) {
            $discount_percentage = ($order_modal->discount_amount / ($order_modal->summary_sub_total + $order_modal->discount_amount)) * 100;
            $addon['discount'] = ($discount_percentage / 100) * $addon['grand_total'];
            $addon['grand_total'] = $addon['grand_total'] - $addon['discount'];
        }

        $invoiceLine = $invoice->addChild("xmlns:cac:InvoiceLine");
        $invoiceLine->addChild("xmlns:cbc:ID", $i);
        $invoiceLine->addChild("xmlns:cbc:InvoicedQuantity", number_format((float)$addon['qty'], 2, '.', ''))->addAttribute('xmlns:unitCode', "EA");
        $invoiceLine->addChild("xmlns:cbc:LineExtensionAmount", number_format((float)$addon['grand_total'], 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");

        $vat = $order_level_vat ? $order_level_vat : $addon['vat'];

        //Discount
        if ($addon['discount'] > 0) {
            $AllowanceChange = $invoiceLine->addChild("xmlns:cac:AllowanceCharge");
            $AllowanceChange->addChild("xmlns:cbc:ID", 1);
            $AllowanceChange->addChild("xmlns:cbc:ChargeIndicator", "false");
            $AllowanceChange->addChild("xmlns:cbc:AllowanceChargeReason", "Voucher Applied");
            $AllowanceChange->addChild("xmlns:cbc:MultiplierFactorNumeric", 1);
            $allowanceChildAmount = $AllowanceChange->addChild("xmlns:cbc:Amount", number_format((float)$addon['discount'], 2, '.', ''));
            $allowanceChildAmount->addAttribute("currencyID", "DKK");
            $allowanceChildBaseAmount = $AllowanceChange->addChild("xmlns:cbc:BaseAmount", number_format((float)$addon['discount'], 2, '.', ''));
            $allowanceChildBaseAmount->addAttribute("currencyID", "DKK");
            $taxAllowanceCharge = $AllowanceChange->addChild("xmlns:cac:TaxCategory");
            $IDAllowance = $taxAllowanceCharge->addChild("xmlns:cbc:ID", $vat > 0 ? "StandardRated" : "ZeroRated");
            $IDAllowance->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxcategoryid-1.1");
            $IDAllowance->addAttribute('xmlns:schemeAgencyID', "320");
            $taxAllowanceCharge->addChild("xmlns:cbc:Percent", number_format((float)$vat, 2, '.', ''));
            $TaxScheme = $taxAllowanceCharge->addChild("xmlns:cac:TaxScheme");
            $taxSchemeID = $TaxScheme->addChild("xmlns:cbc:ID", "63");
            $taxSchemeID->addAttribute("xmlns:schemeID", "urn:oioubl:id:taxschemeid-1.1");
            $TaxScheme->addChild("xmlns:cbc:Name", "Moms");
        }

        $TaxTotal = $invoiceLine->addChild("xmlns:cac:TaxTotal");
        $TaxTotal->addChild("xmlns:cbc:TaxAmount", number_format((float)($addon['grand_total'] * $vat) / 100, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxSubtotal = $TaxTotal->addChild("xmlns:cac:TaxSubtotal");
        $TaxSubtotal->addChild("xmlns:cbc:TaxableAmount", number_format((float)$addon['grand_total'], 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxSubtotal->addChild("xmlns:cbc:TaxAmount", number_format((float)($addon['grand_total'] * $vat) / 100, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxCategory = $TaxSubtotal->addChild("xmlns:cac:TaxCategory");
        $ID = $TaxCategory->addChild("xmlns:cbc:ID", $vat > 0 ? "StandardRated" : "ZeroRated");
        $ID->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxcategoryid-1.1");
        $ID->addAttribute('xmlns:schemeAgencyID', "320");
        $TaxCategory->addChild("xmlns:cbc:Percent", number_format((float)$vat, 2, '.', ''));
        $TaxScheme = $TaxCategory->addChild("xmlns:cac:TaxScheme");
        $TaxScheme->addChild("xmlns:cbc:ID", "63")->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxschemeid-1.1");
        $TaxScheme->addChild("xmlns:cbc:Name", "Moms");
        $TaxScheme->addChild("xmlns:cbc:CurrencyCode", "DKK");

        $Item = $invoiceLine->addChild("xmlns:cac:Item");
        $addon_name = $addon['name'];
        $Item->addChild("xmlns:cbc:Description", stripslashes(strip_tags($addon_name)));
        $Item->addChild("xmlns:cbc:PackQuantity", $addon['qty']);
        $Item->addChild("xmlns:cbc:PackSizeNumeric", number_format((float)$addon['subtotal'], 2, '.', ''));
        $Item->addChild("xmlns:cbc:Name", stripslashes(strip_tags($addon_name)));
        $sellerID = $Item->addChild("xmlns:cac:SellersItemIdentification");
        $sellerID->addChild("xmlns:cbc:ID", $addon['item_number']);
        $Price = $invoiceLine->addChild("xmlns:cac:Price");
        $Price->addChild("xmlns:cbc:PriceAmount", number_format((float)($addon['grand_total'] / $addon['qty']), 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");

        return $invoice;
    }

    /**
     * sproomInvoiceHotel
     *
     * @param mixed $invoice
     * @param mixed $hotel
     * @param mixed $i
     * @return void
     */
    public function sproomInvoiceHotel($invoice, $hotel, $i, $order_level_vat)
    {
        $vat = $order_level_vat ? $order_level_vat : $hotel['vat_rate'];

        if($hotel['price_type'] == 'fixed') {
            $hotel_quantity = $hotel['rooms'];
            $hotel_total = $hotel['price'] * $hotel_quantity;
        } else {
            $hotel_quantity = $hotel['rooms'] * $hotel['nights'];
            $hotel_total = $hotel['price']*$hotel_quantity;
        }

        $hotel_item_vat = ($hotel_total * $vat) / 100;

        $invoiceLine = $invoice->addChild("xmlns:cac:InvoiceLine");
        $invoiceLine->addChild("xmlns:cbc:ID", $i);
        $invoiceLine->addChild("xmlns:cbc:InvoicedQuantity", number_format((float)$hotel_quantity, 2, '.', ''))->addAttribute('xmlns:unitCode', "EA");
        $invoiceLine->addChild("xmlns:cbc:LineExtensionAmount", number_format((float)$hotel_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");

        $TaxTotal = $invoiceLine->addChild("xmlns:cac:TaxTotal");
        $TaxTotal->addChild("xmlns:cbc:TaxAmount", number_format((float)$hotel_item_vat, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxSubtotal = $TaxTotal->addChild("xmlns:cac:TaxSubtotal");
        $TaxSubtotal->addChild("xmlns:cbc:TaxableAmount", number_format((float)$hotel_total, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxSubtotal->addChild("xmlns:cbc:TaxAmount", number_format((float)$hotel_item_vat, 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");
        $TaxCategory = $TaxSubtotal->addChild("xmlns:cac:TaxCategory");
        $ID = $TaxCategory->addChild("xmlns:cbc:ID", $vat > 0 ? "StandardRated" : "ZeroRated");
        $ID->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxcategoryid-1.1");
        $ID->addAttribute('xmlns:schemeAgencyID', "320");
        $TaxCategory->addChild("xmlns:cbc:Percent", number_format((float)($vat), 2, '.', ''));
        $TaxScheme = $TaxCategory->addChild("xmlns:cac:TaxScheme");
        $TaxScheme->addChild("xmlns:cbc:ID", "63")->addAttribute('xmlns:schemeID', "urn:oioubl:id:taxschemeid-1.1");
        $TaxScheme->addChild("xmlns:cbc:Name", "Moms");
        $TaxScheme->addChild("xmlns:cbc:CurrencyCode", "DKK");

        $Item = $invoiceLine->addChild("xmlns:cac:Item");
        $Item->addChild("xmlns:cbc:Description", stripslashes(strip_tags($hotel['name'])));
        $Item->addChild("xmlns:cbc:PackQuantity", $hotel_quantity);
        $Item->addChild("xmlns:cbc:PackSizeNumeric", number_format((float)$hotel_total, 2, '.', ''));
        $Item->addChild("xmlns:cbc:Name", stripslashes(strip_tags($hotel['name'])));
        $sellerID = $Item->addChild("xmlns:cac:SellersItemIdentification");
        $sellerID->addChild("xmlns:cbc:ID", $hotel['id']);
        $Price = $invoiceLine->addChild("xmlns:cac:Price");
        $Price->addChild("xmlns:cbc:PriceAmount", number_format((float)$hotel['price'], 2, '.', ''))->addAttribute('xmlns:currencyID', "DKK");

        return $invoice;
    }

    /**
     * @param mixed $order
     * @param mixed $xml
     *
     * @return [type]
     */
    public function sendXmlEmail($order, $xml)
    {
        $errors = '';

        $event = $order->_getEvent();

        $event_info = $order->_getEventInfo();

        $words_url = preg_replace('/[0-9]+/', '', $event['url']);

        $file_path = config('cdn.cdn_upload_path') . 'assets' . DIRECTORY_SEPARATOR . 'eInvoice' . DIRECTORY_SEPARATOR . $words_url . '-' . $order->getModelAttribute('id') . '.xml';
        
        if(file_exists($file_path)) unlink($file_path);

        $result = \File::put($file_path, $xml);
       
        $options = [
            'file_path' => $file_path,
        ];

        $result = \App\Libraries\Sproom\SproomApi::sproomAPI($options);

        $support_email = $event_info['support_email'];

        if (isset($result->errors)) {
            foreach ($result->errors as $error) {
                $errors .= $error->text . '<br/>';
            }
            $subject = $event['name'] . ' - Order #' . $order->getModelAttribute('order_number') . ' - EAN invoice sending failed';
            $body = 'Invoice for order# ' . $order->getModelAttribute('order_number') . ' failed to send due to following errors: <br/>';
            $body .= $errors;

            $data = array();
            $data['event_id'] = $event->id;
            $data['subject'] = $subject;
            $data['content'] = $body;
            $data['bcc'] = ['ki@eventbuizz.com', 'ida@eventbuizz.com'];
            $data['view'] = 'email.plain-text';
            $data['from_name'] = $event['organizer_name'];
            if ($support_email) \Mail::to($support_email)->send(new Email($data));
            return [
                'success' => false,
                'message' => $errors
            ];
        }

        //Update E-invoice
        \App\Models\BillingOrder::where('id', (int)$order->getModelAttribute('id'))->where('order_type', 'invoice')->update([
            'e_invoice' => 1,
            'e_invoice_date' => date('Y-m-d H:i:s')
        ]);

        //Xml log
        \App\Models\XmlLog::insert([
            "order_id" => $order->getModelAttribute('id'),
            "xml_send_date" => date('Y-m-d H:i:s')
        ]);

        // Ean log
        $ean_log = new \App\Models\EanLog();

        $ean_log->event_id = $event['id'];

        $ean_log->organizer_id = $event['organizer_id'];

        $ean_log->order_id = $order->getModelAttribute('id');

        $ean_log->type = 'order';

        $ean_log->save();

        unlink($file_path);
        
        return [
            'success' => true,
            'message' => 'Xml send successfully!'
        ];
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function printCard($order)
    {
        $event = $order->_getEvent();
        $organizer_id = $event->organizer_id;
        $orderDetail = $order->_getOrderDetail();
        $attendee_id = $orderDetail['attendee_id'];
        $event_id = $orderDetail['event_id'];
        $terminal_cookie_id = request()->cookie('TerminalId_' . $event_id);
        if ($terminal_cookie_id) {
            $print = \App\Models\PrintDropDown::where('id', $terminal_cookie_id)->where('type', 'terminal')->first();
            $terminal_name = $print->value;
            $posted_data = array(
                "type" => '',
                "attendee_type" => 0,
                "badge_id" => 0,
            );

            //Event attendee
            $eventAttendee = \App\Models\EventAttendee::where('event_id', $event_id)->where('attendee_id', $attendee_id)->first();

            //Event attendee type
            $event_attendee_type = \App\Models\EventAttendeeType::find($eventAttendee->attendee_type);

            if ($eventAttendee->attendee_type > 0) {
                $eventBadge = \App\Models\EventBadgeDesign::where('event_id', $event_id)->where('attendee_type', $eventAttendee->attendee_type)->first();

                if ($eventBadge) {
                    $posted_data['type'] = $eventBadge['type'];
                    $posted_data['attendee_type'] = $eventBadge['attendee_type'];
                    $posted_data['badge_id'] = $eventBadge['id'];

                } else {
                    $eventBadge = \App\Models\EventBadgeDesign::where('event_id', $event_id)->where('is_default', 1)->orderBy('id', 'DESC')->first();
                    if ($eventBadge) {
                        $posted_data['type'] = $eventBadge['type'];
                        $posted_data['attendee_type'] = $eventBadge['attendee_type'];
                        $posted_data['badge_id'] = $eventBadge['id'];
                    }
                }
            } else {
                $eventBadge = \App\Models\EventBadgeDesign::where('event_id', $event_id)->where('is_default', 1)->orderBy('id', 'DESC')->first();

                if ($eventBadge) {
                    $posted_data['type'] = $eventBadge['type'];
                    $posted_data['attendee_type'] = $eventBadge['attendee_type'];
                    $posted_data['badge_id'] = $eventBadge['id'];
                }
            }

            $attendee = $order->_getMainAttendeeDetail();

            $info = readArrayKey($attendee, [], 'info');

            $attendee = array_merge($info, $attendee);

            $language_id = $order->getUtility()->getLangaugeId();

            $eventSetting = $order->_getEventSetting();

            if (isset($eventSetting['header_logo']) && $eventSetting['header_logo']) {
                $logo = cdn('/assets/event/branding/' . $eventSetting['header_logo']);
            } else {
                $logo = cdn('/_eventsite_assets/images/eventbuizz_logo-1.png');
            }

            $checkinoutURL = CheckInOutRepository::generateURlShortner([
                'attendee_id' => $attendee_id,
                'event_id' => $event_id,
                'organizer_id' => $organizer_id,
                'event_url' => cdn('/event/' . $event->url),
            ]);

            $attendeeBilling = $order->getMainAttendee()->getBillingModel();

            if ($attendeeBilling) {
                $posted_data['pStreet'] = $attendeeBilling['billing_private_street'] . " " . $attendeeBilling['billing_private_house_number'];
                $posted_data['pZip'] = $attendeeBilling['billing_private_post_code'];
                $posted_data['pCity'] = $attendeeBilling['billing_private_city'];
                $posted_data['pCountry'] = getCountryName($attendeeBilling['billing_private_country']);
                $posted_data['cStreet'] = $attendeeBilling['billing_company_street'] . " " . $attendeeBilling['billing_company_house_number'];
                $posted_data['cZip'] = $attendeeBilling['billing_company_post_code'];
                $posted_data['cCity'] = $attendeeBilling['billing_company_city'];
                $posted_data['cCountry'] = getCountryName($attendeeBilling['billing_company_country']);
            }

            // Custom Fields
            $custom_field_ids = $attendee['custom_field_id'];
            $custom_fields_array = explode(',', $custom_field_ids);

            $companyAddress = $posted_data['cStreet'] . ' ' . $posted_data['cZip'] . ' ' . $posted_data['cCity'] . ' ' . $posted_data['cCountry'];
            $privateAddress = $posted_data['pStreet'] . ' ' . $posted_data['pZip'] . ' ' . $posted_data['pCity'] . ' ' . $posted_data['pCountry'];
            $badge_queue = new \App\Models\BadgePrinterQueue();
            $badge_queue->event_id = $orderDetail['event_id'];
            $badge_queue->type = $posted_data['type'];
            $badge_queue->attendee_type = $posted_data['attendee_type'];
            $badge_queue->badge_id = $posted_data['badge_id'];
            $badge_queue->name = $attendee['first_name'] . ' ' . $attendee['last_name'];
            $badge_queue->name_1 = $attendee['first_name'] . ' ' . $attendee['last_name'];
            $badge_queue->firstname = is_null($attendee['first_name']) ? '' : $attendee['first_name'];
            $badge_queue->firstname_1 = is_null($attendee['first_name']) ? '' : $attendee['first_name'];
            $badge_queue->lastname = is_null($attendee['last_name']) ? '' : $attendee['last_name'];
            $badge_queue->lastname_1 = is_null($attendee['last_name']) ? '' : $attendee['last_name'];
            $badge_queue->companyName = is_null($attendee['company_name']) ? '' : $attendee['company_name'];
            $badge_queue->companyName_1 = is_null($attendee['company_name']) ? '' : $attendee['company_name'];
            $badge_queue->title = is_null($attendee['title']) ? '' : $attendee['title'];
            $badge_queue->title_1 = is_null($attendee['title']) ? '' : $attendee['title'];
            $badge_queue->companyAddress = is_null($companyAddress) ? '' : $companyAddress;
            $badge_queue->privateAddress = is_null($privateAddress) ? '' : $privateAddress;
            $badge_queue->telephone = is_null($attendee['phone']) ? '' : $attendee['phone'];
            $badge_queue->interests = is_null($attendee['interests']) ? '' : $attendee['interests'];
            $badge_queue->logo = is_null($logo) ? '' : $logo;
            $badge_queue->image = BadgeRepository::getBadgeDesignURL($event_id, 'IsImage') || '';
            $badge_queue->bg_image = BadgeRepository::getBadgeDesignURL($event_id, 'IsBgImage') || '';
            $badge_queue->textfield = 'Dimittendår';
            $badge_queue->email = is_null($attendee['email']) ? '' : $attendee['email'];
            $badge_queue->productArea = '';
            $badge_queue->department = is_null($attendee['department']) ? '' : $attendee['department'];
            $badge_queue->barcode = is_null($checkinoutURL) ? '' : $checkinoutURL;
            $badge_queue->barcode_1 = is_null($checkinoutURL) ? '' : $checkinoutURL;
            $badge_queue->country = getCountryName($attendee['country']);
            if (is_null($badge_queue->country)) $badge_queue->country = '';
            $badge_queue->organization = is_null($attendee['organization']) ? '' : $attendee['organization'];
            $badge_queue->delegateNumber = is_null($attendee['delegate_number']) ? '' : $attendee['delegate_number'];
            $badge_queue->networkGroup = is_null($attendee['network_group']) ? '' : $attendee['network_group'];
            $badge_queue->jobTask = is_null($attendee['jobs']) ? '' : $attendee['jobs'];
            $badge_queue->initial = is_null($attendee['initial']) ? '' : $attendee['initial'];
            $badge_queue->age = is_null($attendee['age']) ? '' : $attendee['age'];
            $badge_queue->gender = is_null($attendee['gender']) ? '' : $attendee['gender'];
            $badge_queue->birthDate = is_null($attendee['BIRTHDAY_YEAR']) ? '' : $attendee['BIRTHDAY_YEAR'];
            $badge_queue->department1 = is_null($attendee['department']) ? '' : $attendee['department'];
            $badge_queue->employmentDate = is_null($attendee['EMPLOYMENT_DATE']) ? '' : $attendee['EMPLOYMENT_DATE'];
            $badge_queue->industry = is_null($attendee['industry']) ? '' : $attendee['industry'];
            $badge_queue->about = is_null($attendee['about']) ? '' : $attendee['about'];
            $badge_queue->attendeeType = is_null($event_attendee_type['attendee_type']) ? '' : $event_attendee_type['attendee_type'];

            $attendeeGroups = AttendeeRepository::getAttendeeGroups(["event_id" => $event_id, "attendee_id" => $attendee_id, "language_id" => $language_id]);
            $groups = array();
            foreach ($attendeeGroups as $key => $group) {
                $i = 0;
                foreach ($group['child'] as $child) {
                    if ($child['present'] == 1) {
                        if ($i == 0) {
                            $groups[] = $attendeeGroups[$key]['name'];
                            $i++;
                        }
                        $groups[] = $child['info']['value'];
                    }
                }
            }
            $groups = implode(',', $groups);
            $badge_queue->attendeeGroups = empty($groups) ? '' : $groups;
            // $badge_queue->printer_group = is_null($print) ? '' : $print;
            if (is_numeric($terminal_cookie_id)) {
                $terminal = \App\Models\PrintDropDown::where('id', $terminal_cookie_id)->first();
                $terminal_cookie_id = $terminal->value;
            }

            $badge_queue->printer_group = $terminal_cookie_id;
            $badge_queue->printed = '0';

            $i = 0;

            foreach ($custom_fields_array as $id) {
                $i++;
                if ($i > 10) break;
                //mobile
                if ($i == 1) {
                    $badge_queue->mobile = getCustomFieldValue($id, $language_id);
                } else {
                    $mobile_name = 'mobile' . '_' . $i;
                    $badge_queue->$mobile_name = getCustomFieldValue($id, $language_id);
                }
            }

            $badge_queue->save();

            self::dispatchPrintJobToRedis($badge_queue->event_id, $badge_queue->printer_group, $badge_queue->type, $badge_queue->id);

            // Call thirdpart call to update socket for event center
            $orgainzer = self::fetchOrganizer(["organizer_id" => $organizer_id]);
            $client = new \GuzzleHttp\Client(['base_uri' => config('app.eventcenter_url')]);
            $response = $client->request('POST', '/_admin/checkinout/push_check_in_out_history_to_socket/' . $event_id, [
                "headers" => [
                    "Authorization" => $orgainzer->api_key
                ]
            ]);
            $response = json_decode($response->getBody());
            //End

            //Checking In Attendee
            $log = new \App\Models\CheckInLog();
            $log->checkin = date('Y-m-d H:i:s');
            $log->event_id = $event_id;
            $log->organizer_id = $organizer_id;
            $log->attendee_id = $attendee_id;
            $log->status = '1';
            $log->type_id = $event_id;
            $log->save();
            //Checking In Attendee
        }

        return;
    }

    /**
     * @param mixed $event_id
     * @param mixed $terminal
     * @param mixed $category
     * @param mixed $job_id
     *
     * @return [type]
     */
    static public function dispatchPrintJobToRedis($event_id, $terminal, $category, $job_id)
    {
        $setting = \App\Models\PrintSetting::where('event_id', $event_id)->first();

        if ($setting and $setting->active == '1') {
            $queue_name = "print_queue::event_" . $event_id . ":terminal_" . $terminal . ":category_";
            \Redis::lpush($queue_name, $job_id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    static public function fetchOrganizer($formInput)
    {
        $organizer = \App\Models\Organizer::where('id', $formInput['organizer_id'])->first();
        return $organizer;
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function sendEmailWhenSellingItemQtyBecomeZero($order)
    {
        $event = $order->_getEvent();
        $organizer_id = $event->organizer_id;
        $orderDetail = $order->_getOrderDetail();
        $attendee_id = $orderDetail['attendee_id'];
        $event_id = $orderDetail['event_id'];
        $language_id = $order->getUtility()->getLangaugeId();
        $eventSetting = $order->_getEventSetting();

        $items = $this->eventsiteBillingItemRepository->getBillingItems(["event_id" => $event_id, "language_id" => $language_id]);

        if ($order->getModelAttribute('is_waitinglist') == '1' || ($order->isFree() && count($items) < 1)) return;

        $orderItems = [];

        foreach ($order->getAllItems() as $item) {
            if (!array_key_exists($item->getModel()->addon_id, $orderItems)) {
                $orderItems[$item->getModel()->addon_id] = $item->getQuantity();
            } else {
                $orderItems[$item->getModel()->addon_id] += $item->getQuantity();
            }
        }

        $order_item_ids = array_keys($orderItems);

        $items = \App\Models\BillingItem::whereIn('id', $order_item_ids)
            ->where('event_id', $event_id)
            ->with(['info' => function ($query) use ($language_id) {
                return $query->where('languages_id', $language_id)->where('name', 'item_name');
            }])
            ->get();

        $item_names = '';

        foreach ($items as $i) {
            if ($i->total_tickets != 0) {
                $response  = EventsiteBillingItemRepository::getItemRemainingTickets($i->id, $i->total_tickets);
                if ($response['remaining_tickets'] <= 0) {
                    $item_names = $i->info[0]->value . ',';
                }
            }
        }

        $item_names = rtrim($item_names, ',');

        if (!empty($item_names)) {
            $to_email = $eventSetting['support_email'];
            $body = 'Dear Organizer, <br><br>';
            $body .= 'Your item ' . $item_names . ' has 0 remaining Tickets.<br><br><br>';
            $body .= 'Go to the Eventbuizz Center and check out your Billing item list for more information.';

            $data = array();
            $data['event_id'] = $event->id;
            $data['subject'] = 'Billing item for ' . $eventSetting['name'] . ' is sold out!';
            $data['content'] = $body;
            $data['view'] = 'email.plain-text';
            $data['from_name'] = $event['organizer_name'];
            if ($to_email) \Mail::to($event['support_email'])->send(new Email($data));
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function sendConfirmationEmail($order)
    {
        $isRegVerificationEnabled = $order->getAttendeeSettingsAttribute('attendee_reg_verification');
        $isAttendeeVerificationPending = $order->getMainAttendee()->isVerificationPending();
        if (($isRegVerificationEnabled && $isAttendeeVerificationPending) || $order->getModelAttribute('is_waitinglist') == '1') {
            return;
        }
        $this->registerConfirmationEmail($order);
    }

    /**
     * @param mixed $order
     * @param string $is_credit
     *
     * @return [type]
     */
    public function registerConfirmationEmail($order, $is_credit = '0', $template_alias = 'registration_verification')
    {
        $event = $order->_getEvent();

        $event_info = $order->_getEventInfo();

        $organizer_id = $event->organizer_id;

        $organizer = $order->_getOrganizer();

        $orderDetail = $order->_getOrderDetail();

        $attendee_id = $orderDetail['attendee_id'];

        $event_id = $orderDetail['event_id'];

        $language_id = $order->getUtility()->getLangaugeId();

        //Merge arrays because this $eventSetting is using below on multile places
        $eventSetting = array_merge($event->toArray(), $order->_getEventSetting(), $event_info);

        $payment_setting = $order->_getPaymentSetting();

        $eventsite_setting = $order->_getEventSiteSetting();

        $ticketsPdfFile = $order->getTicketsPDFFile();

        //labels
        $labels = eventsite_labels(['eventsite', 'exportlabels'], ["event_id" => $event_id, "language_id" => $event->language_id]);

        $billing_currency = $payment_setting['eventsite_currency'];

        $sections = EventSiteSettingRepository::getAllSectionFields(["event_id" => $event_id, "language_id" => $event->language_id]);

        foreach ($sections as $section) {
            foreach ($section as $key => $field) {
                $billing_fields[$key] = $field['name'];
            }
        }

        // Order detail summary
        $order_detail = $order->getInvoiceSummary();

        // Generate invoice email if attach invoice email flag is on
        $file_to_save = $attachment_name = $file_to_save_calendar = $attachment_calendar_name = $attachment_hotel_name = $file_to_save_hotel = '';

        /* Invoice html*/
        $invoiceHtml = $this->getOrderDetailInvoice("html", $order, $labels, $language_id, $event_id, $billing_currency, $order_detail['order']['id'], 1, 1, true, false, $is_credit);

        //PDF
        $pdf = 1;

        $export_file_name = sanitizeLabel($labels['EXPORT_ORDER_INVOICE']) . '_' . $order_detail['order']['order_number'] . '_' . time() . '.pdf';
        
        $file_to_save = config('cdn.cdn_upload_path') . 'assets' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $export_file_name;
        
        $snappy = \PDF::loadHTML($invoiceHtml)->setPaper('a4');
        
        $snappy->setOption('header-html', \View::make('admin.order.order_history.invoice.header', compact('eventSetting', 'pdf', 'billing_fields', 'order_detail'))->render());
        
        if (strlen(trim(stripslashes($payment_setting['footer_text']))) > 0) {
            $snappy->setOption('footer-html', \View::make('admin.order.order_history.invoice.footer', compact('eventSetting', 'pdf', 'payment_setting', 'order_detail'))->render());
        }

        
        $snappy->setOption('print-media-type', true);
        
        $snappy->setOption('margin-right', 0);
        
        $snappy->setOption('margin-left', 0);;
        
        $snappy->save($file_to_save);

        if (trim($order_detail['order']['order_number'])) {
            $order_number = $order_detail['order']['order_number'];
        } else {
            $order_number = $order_detail['order']['id'];
        }

        if ($order_detail['order']['order_type'] == 'invoice') {
            $attachment_name = sanitizeLabel($labels['EXPORT_ORDER_INVOICE']) . '_' . $order_number . '.pdf';
        } else {
            $attachment_name = sanitizeLabel($labels['EXPORT_ORDER']) . '_' . $order_number . '.pdf';
        }

        // Attach calendar ics file on Registration Email
        $calender_data = $this->addToCalender($order, $labels, true);

        $export_calendar_file_name = 'calendar' . $order_detail['order']['order_number'] . '_' . time() . '.ics';

        $file_to_save_calendar = config('cdn.cdn_upload_path') . 'ical' . DIRECTORY_SEPARATOR . $export_calendar_file_name;

        file_put_contents($file_to_save_calendar, $calender_data);

        $attachment_calendar_name = $eventSetting['name'] . '.ics';

        $cc = [];

        if ($payment_setting['eventsite_send_email_order_creator'] == 1 && isset($order_detail['order_billing_detail']['billing_contact_person_email']) && $order_detail['order_billing_detail']['billing_contact_person_email'] != '') {
            $cc[] = $order_detail['order_billing_detail']['billing_contact_person_email'];
        }

        $bcc = [];

        if (trim($payment_setting['bcc_emails'])) {
            $arr_emails = explode(',', $payment_setting['bcc_emails']);
            foreach ($arr_emails as $email) {
                $bcc[] = rtrim($email, ';');
            }
        }
            
        foreach ($order->getAllAttendees() as $attendee) {

            $order_attendee = $attendee->getOrderAttendee();

            $eventsite_setting = $order->_getEventsiteFormSetting($order_attendee->registration_form_id);

            $payment_setting = $order->_getPaymentFormSetting($order_attendee->registration_form_id);

            if ($eventsite_setting['send_invoice_email']) {

                $attendee_type = $order_attendee->attendee_type;

                $registration_form = $order->getRegistrationForm($attendee->getModel()->id);

                $registration_form_id = $registration_form ? $registration_form->id : 0;

                $templateData = (object)TemplateRepository::getTemplateDataByAlias(['event_id' => $event['id'], 'alias'=> $template_alias , 'registration_form_id' => $registration_form_id, 'language_id' => $language_id]);

                $subject = str_replace("{event_name}", stripslashes($event['name']), $templateData->subject);

                $template = getEmailTemplate($templateData->template, $event['id']);

                //Stand sale link
                $stand_sale_link = \App\Models\StandSaleRegistrationLink::where('order_id', $order->getModel()->id)->where('attendee_id', $attendee->getModel()->id)->first();

                if ($stand_sale_link) {
                    $template = str_replace("{stand_sale_register}", cdn('/_hub/' . $organizer->user_name . '/registration/' . $stand_sale_link->token), $template);
                }

                $content = stripslashes($template);

                $email_subject = str_replace('{event_name}', $eventSetting['name'], $subject);

                $attachmentFile = [];

                if ($eventsite_setting['attach_invoice_email'] == 1) {

                    //Sub registration email
                    $sub_registration_attachment = $this->getSubregistrationAttachment($order, $attendee->getModel(), $labels, $language_id, $event_id, $order_detail, $payment_setting, $billing_fields);

                    if(is_array($sub_registration_attachment) && !empty($sub_registration_attachment)) {
                        $attachmentFile[] = $sub_registration_attachment;
                    }

                }
               
                if ($attendee->isMainAttendee()) {

                    if ($eventsite_setting['attach_invoice_email'] == 1) {

                        // Order attachment
                        if ($file_to_save) {
                            $attachmentFile[] = ['path' => $file_to_save, 'name' => $attachment_name];
                        }

                    }

                    // Calender attachment
                    if ($file_to_save_calendar && $eventsite_setting['attach_calendar_to_email'] == 1) {
                        $attachmentFile[] = ['path' => $file_to_save_calendar, 'name' => $attachment_calendar_name];
                    }
                    
                    // Ticket attachment
                    if ($ticketsPdfFile != '') {
                        $attachmentFile[] = ['path' => $ticketsPdfFile, 'name' => basename($ticketsPdfFile)];
                    }

                    $content = $this->_getTemplateContent($event, $attendee, $content, $event_id, $organizer_id, $eventSetting, $labels, $order_detail);
                    
                    if($payment_setting['eventsite_send_email_order_creator'] == 2 && isset($order_detail['order_billing_detail']['billing_contact_person_email']) && $order_detail['order_billing_detail']['billing_contact_person_email'] != '')
                    {
                        $data = array();
                        $data['event_id'] = $event_id;
                        $data['subject'] = $email_subject;
                        $data['content'] = $content;
                        $data['view'] = 'email.plain-text';
                        $data['from_name'] = $event['organizer_name'];
                        $data['bcc'] = $bcc;
                        $data['attachment'] = $attachmentFile;

                        if ($order_detail['order_billing_detail']['billing_contact_person_email']) {
                            \Mail::to($order_detail['order_billing_detail']['billing_contact_person_email'], $attendee->getModel()->first_name . ' ' . $attendee->getModel()->last_name)->send(new Email($data));
                        }

                        // In case we set only contact person then we send registration email to main attendee as well with out invoice
                        unset($data['attachment']);
                        if ($attendee->getModel()->email) \Mail::to($attendee->getModel()->email, $attendee->getModel()->first_name . ' ' . $attendee->getModel()->last_name)->send(new Email($data));
                    
                    } elseif ($payment_setting['eventsite_send_email_order_creator'] == 1) {

                        $data = array();
                        $data['event_id'] = $event_id;
                        $data['subject'] = $email_subject;
                        $data['content'] = $content;
                        $data['view'] = 'email.plain-text';
                        $data['from_name'] = $event['organizer_name'];
                        $data['bcc'] = $bcc;
                        $data['cc'] = $cc;
                        $data['attachment'] = $attachmentFile;

                        if ($attendee->getModel()->email) \Mail::to($attendee->getModel()->email, $attendee->getModel()->first_name . ' ' . $attendee->getModel()->last_name)->send(new Email($data));
                    
                    } else {

                        $data = array();
                        $data['event_id'] = $event_id;
                        $data['subject'] = $email_subject;
                        $data['content'] = $content;
                        $data['view'] = 'email.plain-text';
                        $data['from_name'] = $event['organizer_name'];
                        $data['bcc'] = $bcc;
                        $data['attachment'] = $attachmentFile;

                        if ($attendee->getModel()->email) \Mail::to($attendee->getModel()->email, $attendee->getModel()->first_name . ' ' . $attendee->getModel()->last_name)->send(new Email($data));
                    
                    }

                } else if(in_array($template_alias, ['registration_verification'])) {

                    $attendeeIsExist = \App\Models\EventHotelPerson::where('order_id', $order_detail['order']['id'])->where('attendee_id', $attendee->getModel()->id)->first();

                    // Hotels attachment
                    if ($order_detail['is_hotel_attached'] && $attendeeIsExist) {

                        $html = $this->getOrderHotelDetail($order, $labels, $language_id, $event_id, $order_detail, $attendee->getModel()->id);
                        $filename = 'HotelDetail_' . $order_detail['order']['order_number'] . '_' . time() . '.pdf';
                        $file_to_save_hotel = config('cdn.cdn_upload_path') . 'assets' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $filename;
                        $pdf = 1;
                        $snappy = \PDF::loadHTML($html)->setPaper('a4');
                        $snappy->setOption('header-html', \View::make('admin.order.order_history.invoice.header', compact('eventSetting', 'pdf', 'billing_fields', 'order_detail'))->render());
                        $snappy->setOption('print-media-type', true);
                        $snappy->setOption('margin-right', 0);
                        $snappy->setOption('margin-left', 0);
                        $snappy->save($file_to_save_hotel);

                        if (trim($order_detail['order']['order_number'])) {
                            $order_number = $order_detail['order']['order_number'];
                        } else {
                            $order_number = $order_detail['order']['id'];
                        }

                        $attachment_hotel_name = sanitizeLabel($labels['EXPORT_HOTEL']) . '_' . $order_number . '.pdf';

                        $attachmentFile[] = ['path' => $file_to_save_hotel, 'name' => $attachment_hotel_name];
                        
                    }

                    // Calender attachment
                    if ($eventsite_setting['attach_calendar_to_email'] == 1) {
                        $attachmentFile[] = ['path' => $file_to_save_calendar, 'name' => $attachment_calendar_name];
                    }

                    //Send additional attendees
                    $content = $this->_getTemplateContent($event, $attendee, $content, $event_id, $organizer_id, $eventSetting, $labels, $order_detail);

                    $data = array();
                    $data['event_id'] = $event_id;
                    $data['subject'] = $email_subject;
                    $data['content'] = $content;
                    $data['view'] = 'email.plain-text';
                    $data['from_name'] = $event['organizer_name'];
                    $data['attachment'] = $attachmentFile;

                    if ($attendee->getModel()->email) \Mail::to($attendee->getModel()->email, $attendee->getModel()->first_name . ' ' . $attendee->getModel()->last_name)->send(new Email($data));
                }
                
            }
        }
    }
    
    /**
     * getSubregistrationAttachment
     *
     * @param  mixed $order
     * @param  mixed $attendee
     * @param  mixed $labels
     * @param  mixed $language_id
     * @param  mixed $event_id
     * @param  mixed $order_detail
     * @param  mixed $payment_setting
     * @param  mixed $billing_fields
     * @return void
     */
    public function getSubregistrationAttachment($order, $attendee, $labels, $language_id, $event_id, $order_detail, $payment_setting, $billing_fields) {

        if($payment_setting->attach_sub_registration == 1) {

            $event = $order->_getEvent();
    
            $order_attendee = $order->_getAttendeeByID($attendee->id)->getOrderAttendee();
          
            $registration_form = $order->getRegistrationForm($attendee->id);
    
            $registration_form_id = $registration_form ? $registration_form->id : 0;
    
            $eventSetting = $order->_getEventSetting();
    
            $sub_registration = \App\Models\EventSubRegistration::where('event_id', '=', $event_id)->where('registration_form_id', $registration_form_id)->with(['question' => function ($query) {
                return $query->where('status', '=', '1')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
            }, 'question.info' => function ($q) use ($language_id) {
                return $q->where('languages_id', '=', $language_id);
            }, 'question.answer' => function ($r) {
                return $r->orderBy('sort_order')->orderBy('id', 'ASC');
            }, 'question.answer.info' => function ($r) use ($language_id) {
                return $r->where('languages_id', '=', $language_id);
            }, 'question.result' => function ($s) use ($attendee) {
                return $s->where('attendee_id', $attendee->id)->orderBy('id', 'DESC');
            }])->whereNull('deleted_at')->first();
    
            if($sub_registration) {
    
                $html = \View::make('admin.order.order_history.sub-registration-result', compact('sub_registration', 'eventSetting', 'labels'))->render();
    
                $filename = 'SubResgistration_' . $attendee->id.$order_detail['order']['order_number'] . '_' . time() . '.pdf';
    
                $file = config('cdn.cdn_upload_path') . 'assets' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $filename;
    
                $pdf = 1;
                //PDF
                $snappy = \PDF::loadHTML($html)->setPaper('a4');
    
                $snappy->setOption('header-html', \View::make('admin.order.order_history.invoice.header', compact('eventSetting', 'pdf', 'billing_fields', 'order_detail'))->render());
    
                $snappy->setOption('print-media-type', true);
    
                $snappy->setOption('margin-right', 0);
    
                $snappy->setOption('margin-left', 0);
                
                $snappy->save($file);
    
                $name = $labels['EXPORT_SUB_REGISTRATION'] . '_' . $order_number . '.pdf';
    
                return ['path' => $file, 'name' => $name];
    
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param mixed $order
     * @param string $is_credit
     * @param string $action
     *
     * @return [type]
     */
    public function orderAction($order, $is_credit = '0', $action)
    {
        $event = $order->_getEvent();

        $event_info = $order->_getEventInfo();

        $organizer_id = $event->organizer_id;

        $organizer = $order->_getOrganizer();

        $orderDetail = $order->_getOrderDetail();

        $attendee_id = $orderDetail['attendee_id'];

        $event_id = $orderDetail['event_id'];

        $language_id = $order->getUtility()->getLangaugeId();

        //Merge arrays because this $eventSetting is using below on multile places
        $eventSetting = array_merge($event->toArray(), $order->_getEventSetting(), $event_info);

        $payment_setting = $order->_getPaymentSetting();

        $eventsite_setting = $order->_getEventSiteSetting();

        $subRegSetting = $order->_getEventSubregistrationSetting();

        $ticketsPdfFile = $order->getTicketsPDFFile();

        //labels
        $labels = eventsite_labels(['eventsite', 'exportlabels'], ["event_id" => $event_id, "language_id" => $event->language_id]);

        $billing_currency = $payment_setting['eventsite_currency'];

        $sections = EventSiteSettingRepository::getAllSectionFields(["event_id" => $event_id, "language_id" => $event->language_id]);

        foreach ($sections as $section) {
            foreach ($section as $key => $field) {
                $billing_fields[$key] = $field['name'];
            }
        }

        // Order detail summary
        $order_detail = $order->getInvoiceSummary();

        //PDF
        $pdf = $action == 'download-pdf' ? 1 : 0;

        $print = $action == 'download-pdf' ? 0 : 1;
        
        /* Invoice html*/
        $invoiceHtml = $this->getOrderDetailInvoice("html", $order, $labels, $language_id, $event_id, $billing_currency, $order_detail['order']['id'], $pdf, 1, true, false, $is_credit);

        if($action == 'download-pdf') {

            $export_file_name = sanitizeLabel($labels['EXPORT_ORDER_INVOICE']) . '_' . $order_detail['order']['order_number'] . '_' . time() . '.pdf';
            
            $snappy = \PDF::loadHTML($invoiceHtml)->setPaper('a4');
            
            $snappy->setOption('header-html', \View::make('admin.order.order_history.invoice.header', compact('eventSetting', 'pdf', 'billing_fields', 'order_detail'))->render());
            
            if (strlen(trim(stripslashes($payment_setting['footer_text']))) > 0) {
                $snappy->setOption('footer-html', \View::make('admin.order.order_history.invoice.footer', compact('eventSetting', 'pdf', 'payment_setting', 'order_detail'))->render());
            }
            
            $snappy->setOption('print-media-type', true);
            
            $snappy->setOption('margin-right', 0);
            
            $snappy->setOption('margin-left', 0);
    
            return $snappy->download($export_file_name);

        } else if($action == 'html') {
            return $invoiceHtml;
        }
    }

    /**
     * @param mixed $order
     * @param mixed $labels
     * @param mixed $language_id
     * @param mixed $event_id
     * @param mixed $billing_currency
     * @param mixed $order_id
     * @param bool $print
     * @param bool $pdf
     * @param mixed $is_free
     * @param bool $is_pdf
     * @param int $is_credit
     * @param int $is_archive
     * @param bool $show_order_no
     *
     * @return [type]
     */
    static public function getOrderDetailInvoice($response_type, $order, $labels, $language_id, $event_id, $billing_currency, $order_id, $print = false, $pdf = false, $is_free, $is_pdf = true, $is_credit = 0, $is_archive = 0, $show_order_no = true)
    {
        global $order_detail;

        $event_date_format = \App\Models\EventDateFormat::where('event_id', '=', $event_id)->where('language_id', '=', $language_id)->first();

        $date_format_id = 0;

        if ($event_date_format) {
            $event_date_format = $event_date_format->toArray();
            $date_format_id = $event_date_format['date_format_id'];
            if ($event_date_format['date_format_id'] == 2) {
                setlocale(LC_TIME, "da_DK.utf8");
            } elseif ($event_date_format['date_format_id'] == 3) {
                setlocale(LC_TIME, "nb_NO.utf8");
            } elseif ($event_date_format['date_format_id'] == 4) {
                setlocale(LC_TIME, "de_DE.utf8");
            } elseif ($event_date_format['date_format_id'] == 5) {
                setlocale(LC_TIME, "lt_LT.utf8");
            } elseif ($event_date_format['date_format_id'] == 6) {
                setlocale(LC_TIME, "fi_FI.utf8");
            } elseif ($event_date_format['date_format_id'] == 7) {
                setlocale(LC_TIME, "sv_SE.utf8");
            }
        }

        $date_format_id = 8;

        $payment_setting = $order->_getPaymentSetting();

        $eventsite_setting = $order->_getEventSiteSetting();

        $eventSetting = $order->_getEventSetting();

        $order_detail = $order->getInvoiceSummary();

        $reference_credit_note_no = '';

        if ($order->getModel()->is_credit_note) {
            $original_order = \App\Models\BillingOrder::where('id', $order->getModel()->clone_of)->first();
            $reference_credit_note_no = $original_order->order_number;
        }

        $billing_currency = getCurrencyArray();

        $currency = isset($billing_currency[$order_detail['order']['eventsite_currency']]) ? $billing_currency[$order_detail['order']['eventsite_currency']] : '';

        $sections = EventSiteSettingRepository::getAllSections(["event_id" => $event_id, "language_id" => $language_id, "status" => 1]);

        foreach ($sections as $section) {
            foreach ($section['fields'] as $field) {
                $billing_fields[$field['field_alias']] = $field['detail']['name'];
            }
        }

        $history_logs = \App\Models\BillingOrderLog::where('order_id', $order_id)->groupBy('update_date')->get();

        //discount_coupon
        $coupon_detail = \App\Models\BillingVoucher::find($order_detail['order']['coupon_id']);

        $coupon_detail = $coupon_detail ? $coupon_detail->toArray() : [];

        $is_credit = $order->getModel()->is_credit_note;

        $attendees = $order->getAllAttendees();

        if($response_type == "html") {
            return \View::make($eventsite_setting->payment_type == 1 ? 'admin.order.order_history.invoice.detail' : 'admin.order.order_history.invoice.free.detail', compact('reference_credit_note_no','history_logs', 'coupon_detail', 'payment_setting','is_credit','language_id', 'billing_fields','date_format_id','eventSetting', 'print', 'order_id', 'pdf', 'attendees', 'order_detail', 'currency', 'eventsite_setting', 'labels'))->render();
        } else if($response_type == "render") {
            return \View::make($eventsite_setting->payment_type == 1 ? 'admin.order.order_history.invoice.detail' : 'admin.order.order_history.invoice.free.detail', compact('reference_credit_note_no','history_logs', 'coupon_detail', 'payment_setting','is_credit','language_id', 'billing_fields','date_format_id','eventSetting', 'print', 'order_id', 'pdf', 'attendees', 'order_detail', 'currency', 'eventsite_setting', 'labels'));
        } else {
            return [
                "reference_credit_note_no" => $reference_credit_note_no,
                "history_logs" => $history_logs,
                "coupon_detail" => $coupon_detail,
                "payment_setting" => $payment_setting,
                "is_credit" => $is_credit,
                "language_id" => $language_id,
                "billing_fields" => $billing_fields,
                "date_format_id" => $date_format_id,
                "eventSetting" => $eventSetting,
                "print" => $print,
                "order_id" => $order_id,
                "pdf" => $pdf,
                "order_detail" => $order_detail,
                "currency" => $currency,
                "eventsite_setting" => $eventsite_setting,
                "labels" => $labels
            ];
        }
    }

    /**
     * @param mixed $order
     * @param mixed $labels
     * @param bool $returnData
     *
     * @return [type]
     */
    public function addToCalender($order, $labels, $returnData = false)
    {
        $event = $order->_getEvent();
        $eventInfo = readArrayKey($event, [], 'info');
        $event_id = $event->id;
        $startDate = $event['start_date'] . " " . $event['start_time'];
        $endDate = $event['end_date'] . " " . $event['end_time'];
        $subject = $event['name'];

        $calendar_description = '';
        $event_description = \App\Models\EventDescription::where('event_id', $event->id)->with(['Info' => function ($query) use ($event) {
            return $query->where('languages_id', $event->language_id);
        }])->first()->toArray();

        foreach($event_description['info'] AS $info) {

            if($info['name'] == 'calender_description') {
                $calendar_description = $info['value'];
            }

        }
        $calendar_description = preg_replace("/[\r\n]*/","",$calendar_description);
        $desc = str_replace("{event_link}", config('app.eventcenter_url') . '/event/' . $event['url'], $calendar_description);

        $location = $eventInfo['location_address'];

        //Will use for specific events
        if (false) {
            $location = $eventInfo['location_name'] . ', ' . $eventInfo['location_address'];
        }

        if ($returnData) {
            return $this->generateICS($startDate, $endDate, $subject, $desc, $location, $event_id, $returnData, $event->timezone_id, $event['organizer_name'], $eventInfo['support_email']);
        }

        $this->generateICS($startDate, $endDate, $subject, $desc, $location, $event_id, false, $event->timezone_id, $event['organizer_name'], $eventInfo['support_email']);
    }

    /**
     * @param mixed $start
     * @param mixed $end
     * @param mixed $name
     * @param mixed $description
     * @param mixed $location
     * @param mixed $event_id
     * @param mixed $returnData
     *
     * @return [type]
     */
    function generateICS($start, $end, $name, $description, $location, $event_id, $returnData, $timezone_id, $organizer, $support_email)
    {
        $_name = 'eventbuizz';
        $UID = $event_id . time();
        $_data = "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:Eventbuizz.com\nBEGIN:VEVENT\nUID:" . $UID . "\nDTSTART:" . convertEventTimezoneToUtc($timezone_id, $start, "Ymd\THis\Z") . "\nDTEND:" . convertEventTimezoneToUtc($timezone_id, $end, "Ymd\THis\Z") . "\nSEQUENCE:0\nTRANSP:OPAQUE\nLOCATION:" . $location . "\nSUMMARY:" . $name . "\nDESCRIPTION:" . $description."\nX-ALT-DESC;FMTTYPE=text/html:".$description . "\nORGANIZER;CN=".$organizer.":mailto:".$support_email."\nEND:VEVENT\nEND:VCALENDAR\n";
        if ($returnData) {
            return $_data;
        }
        header("Content-type:text/calendar");
        header('Content-Disposition: attachment; filename="' . $_name . '.ics"');
        header('Connection: close');
        echo $_data;
        exit;
    }

    /**
     * @param mixed $event
     * @param mixed $attendee
     * @param mixed $template
     * @param mixed $event_id
     * @param mixed $organizer_id
     * @param mixed $eventSetting
     * @param mixed $labels
     *
     * @return [type]
     */
    private function _getTemplateContent($event, $attendee, $template, $event_id, $organizer_id, $eventSetting, $labels, $order_detail = array())
    {
        $attendee_detail = $attendee->getModel()->toArray();

        $info = readArrayKey($attendee_detail, [], 'info');

        $attendee_detail['info'] = $info;

        $gender = $attendee_detail['info']['gender'];

        $checkinoutURL = CheckInOutRepository::generateURlShortner([
            'attendee_id' => $attendee_detail['id'],
            'event_id' => $event_id,
            'organizer_id' => $organizer_id,
            'event_url' => cdn('/event/' . $event->url),
        ]);

        // Template background color
        $template = getEmailTemplate($template, $event_id);

        $content = stripslashes($template);

        if ($eventSetting['header_logo'] != '' && $eventSetting['header_logo'] != 'NULL') {
            $src = cdn('/assets/event/branding/' . $eventSetting['header_logo']);
        } else {
            $src = cdn("/_admin_assets/images/eventbuizz_logo.png");
        }

        $logo = '<img src="' . $src . '" width="150" />';

        $base_url = cdn('/event/' . $event->url);
        $event_site_url =  config('app.reg_site_url').'/'. $event->url;

        $unsubscribe_attendee_url = $base_url . '/detail/attendee/unsubscribe_attendee/' . $event_id . '/' . $attendee_detail['id'];

        if ($event->registration_form_id == 1) {
            $event_url = $event_site_url;
            $unsubscribe_attendee_url = $event_site_url . '/unsubscribe_attendee/?id=' . $attendee_detail['id'] . '&event_id=' . $event_id . '&email=' . $attendee_detail['email'];
        } else {
            $event_url = $base_url . '/detail';
        }

        $content = str_replace("{event_name}", stripslashes($event['name']), $content);

        $content = str_replace("{event_url}", '<a href="' . $event_url .'">' . $event_url . '</a>', $content);

        $content = str_replace("{attendee_name}", stripslashes($attendee_detail['first_name'] . ' ' . $attendee_detail['last_name']), $content);

        $content = str_replace("{attendee_email}", stripslashes($attendee_detail['email']), $content);

        $content = str_replace("{initial}", stripslashes($attendee_detail['info']['initial']), $content);

        $content = str_replace("{first_name}", stripslashes($attendee_detail['first_name']), $content);

        $content = str_replace("{last_name}", stripslashes($attendee_detail['last_name']), $content);

        $content = str_replace("{gender}", stripslashes($gender), $content);

        $content = str_replace("{unsubscribe_attendee}", '<a target="#" href="' . $unsubscribe_attendee_url . '">' . $labels['ATTENDEE_UNSUBSCRIBE_TEXT'] . '</a>', $content);

        $content = str_replace("{unsubscribe_attendee_link}", $unsubscribe_attendee_url, $content);

        $content = str_replace("{unsubscribe_attendee_label}", $labels['ATTENDEE_UNSUBSCRIBE_TEXT'], $content);

        $content = str_replace("{qr_code}", '<a target="#" href="' . cdn("api/QrCode?chs=200x200&chl=" . urlencode($checkinoutURL)) . '">'
            . '<img src="' . cdn("api/QrCode?chs=200x200&chl=" . urlencode($checkinoutURL)) . '" />'
            . '</a>', $content);

        $content = str_replace("{app_link}", '<a href="' . cdn('/event/' . $event->url) . '">' . cdn('/event/' . $event->url) . '</a>', $content);

        $content = str_replace("{event_organizer_name}", stripslashes($event['organizer_name']), $content);

        $badge_URL = cdn('/_badges/printEmailBadges/' . $attendee_detail['id'] . '/' . $event_id);

        $content = str_replace("{badge_template}", "" . $badge_URL, $content);

        $content = str_replace("{event_logo}", $logo, $content);

        if(!empty($order_detail)) {

            $content = str_replace("{invoice_number}", stripslashes($order_detail['order']['order_number']), $content);

            $content = str_replace("{invoice_date}", stripslashes($order_detail['order']['order_date']), $content);

        }
        

        return $content;
    }

    /**
     * @param mixed $order
     * @param mixed $labels
     * @param mixed $language_id
     * @param mixed $event_id
     * @param mixed $order_detail
     * @param mixed $attendee_id
     *
     * @return [type]
     */
    public function getOrderHotelDetail($order, $labels, $language_id, $event_id, $order_detail, $attendee_id)
    {
        $eventSetting = $order->_getEventSetting();

        return \View::make('admin.order.order_history.hotel-detail', compact('order_detail', 'attendee_id', 'eventSetting', 'labels'))->render();
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function addNewsSubscriber($order)
    {
        $event = $order->_getEvent();

        foreach ($order->getAllAttendees() as $attendee) {
            $attendee_id = $attendee->getModel()->id;
            $attendee_model = \App\Models\Attendee::find($attendee_id);
            if ($attendee_model) {
                $order_attendee = $attendee->getOrderAttendee();
                if ($order_attendee && $order_attendee->subscriber_ids) {
                    if ($order_attendee->subscriber_ids) {
                        $subscriber_ids = (array) $order_attendee->subscriber_ids;
                        if (count($subscriber_ids) > 0) {
                            foreach ($subscriber_ids as $sub) {
                                $subscriber = \App\Models\MailingListSubscriber::where('organizer_id', $order->getUtility()->getOrganizerID())->where('mailing_list_id', $sub)->where('email', $email)->first();
                                if ($subscriber) {
                                    $subscriber->unsubscribed = null;
                                    $subscriber->first_name = $attendee_model->first_name;
                                    $subscriber->last_name = $attendee_model->last_name;
                                    $subscriber->event_id = $order->getOrderEventId();
                                    $subscriber->save();
                                } else {
                                    $subscriber = new \App\Models\MailingListSubscriber();
                                    $subscriber->organizer_id = $order->getUtility()->getOrganizerID();
                                    $subscriber->mailing_list_id = $sub;
                                    $subscriber->first_name = $attendee_model->first_name;
                                    $subscriber->last_name = $attendee_model->last_name;
                                    $subscriber->email = $attendee_model->email;
                                    $subscriber->event_id = $order->getOrderEventId();
                                    $subscriber->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function resetVoucherSessions($order)
    {
        \DB::table('conf_billing_coupons_sessions')->where('session_id', $order->getUtility()->getSessionID())->delete();
        \DB::table('conf_billing_coupon_items_sessions')->where('session_id', $order->getUtility()->getSessionID())->delete();
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function sendCreditNote($prev_order, $order)
    {
        $isRegVerificationEnabled = $order->getAttendeeSettingsAttribute('attendee_reg_verification');

        $isAttendeeVerificationPending = $order->getMainAttendee()->isVerificationPending();

        $order_attendee = $order->getMainAttendee()->getOrderAttendee();

        $eventsite_setting = $order->_getEventsiteFormSetting($order_attendee->registration_form_id);

        if (($isRegVerificationEnabled == '1' && $isAttendeeVerificationPending) || $order->getModelAttribute('is_waitinglist') == '1' || $order->isFree()) return; //do nothing because registration verification settings are on or order is waiting list order or send invoice email is disabled.

        if ($prev_order) {
            $invoice_file = $order->getOrderInvoicePDF();
            $credit_note_file = $prev_order->getOrderInvoicePDF();
            if($order->getModelAttribute('status') == "cancelled") {
                $this->sendAttendeCreditNoteAndNewInvoiceEmail($order, $order->getOrderEventId(), $order->getUtility()->getOrganizerID(), $order->getUtility()->getLangaugeId(), $order->getModelAttribute('id'), $prev_order->getModelAttribute('id'), $invoice_file, $credit_note_file);
            } else {
                if ($order->getModelAttribute('grand_total') > $prev_order->getModelAttribute('grand_total')) {
                    $this->sendAttendeCreditNoteAndNewInvoiceEmail($order, $order->getOrderEventId(), $order->getUtility()->getOrganizerID(), $order->getUtility()->getLangaugeId(), $order->getModelAttribute('id'), $prev_order->getModelAttribute('id'), $invoice_file, $credit_note_file);
                    $this->sendInvoiceDiffEmailToBookKeeper($order, $order->getOrderEventId(), $order->getUtility()->getOrganizerID(), $order->getUtility()->getLangaugeId(), $order->getModelAttribute('id'), $prev_order->getModelAttribute('id'), $invoice_file, $credit_note_file);
                } else {
                    $this->sendAttendeCreditNoteAndNewInvoiceEmail($order, $order->getOrderEventId(), $order->getUtility()->getOrganizerID(), $order->getUtility()->getLangaugeId(), $order->getModelAttribute('id'), $prev_order->getModelAttribute('id'), $invoice_file, $credit_note_file, 0);
                    $this->sendInvoiceDiffEmailToBookKeeper($order, $order->getOrderEventId(), $order->getUtility()->getOrganizerID(), $order->getUtility()->getLangaugeId(), $order->getModelAttribute('id'), $prev_order->getModelAttribute('id'), $invoice_file, $credit_note_file);
                }
            }
        }
    }

    /**
     * @param mixed $order
     * @param mixed $event_id
     * @param mixed $organizer_id
     * @param mixed $language_id
     * @param mixed $order_id
     * @param mixed $credit_note_id
     * @param mixed $invoice_pdf_file
     * @param mixed $credit_note_pdf_file
     * @param int $sent_with_diff
     *
     * @return [type]
     */
    function sendAttendeCreditNoteAndNewInvoiceEmail($eb_order, $event_id, $organizer_id, $language_id, $order_id, $credit_note_id, $invoice_pdf_file, $credit_note_pdf_file, $sent_with_diff = 1)
    {
        $event = $eb_order->_getEvent();

        $toemail = $eb_order->getMainAttendee()->getModel()->email;

        $first_name = $eb_order->getMainAttendee()->getModel()->first_name;

        $last_name = $eb_order->getMainAttendee()->getModel()->last_name;

        $eventSetting = $eb_order->_getEventSetting();

        $payment_setting = $eb_order->_getPaymentSetting();

        $order_attendee = $eb_order->getMainAttendee()->getOrderAttendee();

        $eventsite_setting = $eb_order->_getEventsiteFormSetting($order_attendee->registration_form_id);

        //labels
        $labels = eventsite_labels(['eventsite', 'exportlabels', 'generallabels'], ["event_id" => $event_id, "language_id" => $event->language_id]);

        if ($eventsite_setting['send_invoice_email'] == '0') {
            return;
        }

        // New invoice 
        $order = \App\Models\BillingOrder::where('id', addslashes($order_id))->first();

        if (trim($order['order_number'])) {
            $order_number = $order['order_number'];
        } else {
            $order_number = $order['id'];
        }

        $attendee_id = $order['attendee_id'];

        // Credit note
        $credit_note = \App\Models\BillingOrder::where('id', $credit_note_id)->first();
        
        if (trim($credit_note->order_number)) {
            $credit_order_number = $credit_note->order_number;
        } else {
            $credit_order_number = $credit_note->id;
        }

        // New order billing detail
        $billing_address = \App\Models\AttendeeBilling::where('attendee_id', $attendee_id)->where('event_id', $event_id)->where('order_id', $order_id)->first();

        $diff_in_amount = $order['grand_total'] - $credit_note->grand_total;

        $billing_currency = getCurrencyArray();

        foreach ($billing_currency as $key => $cur) {
            if ($order['eventsite_currency'] == $key) {
                $currency = $cur;
            }
        }

        if($order->status !== "cancelled") {
            
            if ($sent_with_diff) {

                $template = getTemplate('email', 'attendee_invoice_update', $event_id, $language_id);
    
                $subject = $template->info[0]['value'];
    
                $content = getEmailTemplate($template->info[1]['value'], $event['id']);
    
                $content = stripslashes($content);
    
                $content = (string)$this->_getTemplateContent($event, $eb_order->getMainAttendee(), $content, $event_id, $organizer_id, $eventSetting, $labels);
    
                $content = str_replace("{total_difference_amount}", stripslashes(getCurrency($diff_in_amount, $currency)), $content);
    
                $content = str_replace("{currency}", stripslashes($currency), $content);
    
                $content = str_replace("{credit_note_number}", stripslashes($credit_order_number), $content);
    
                $content = str_replace("{credit_note_amount}", stripslashes(getCurrency($credit_note['grand_total'], $currency)), $content);
    
                $content = str_replace("{new_invoice_number}", stripslashes($order_number), $content);
        
                $content = str_replace("{new_invoice_amount}", stripslashes(getCurrency($order['grand_total'], $currency)), $content);
    
                $subject = str_replace("{event_name}", stripslashes($event['name']), $subject);
    
            } else {
    
                $template = getTemplate('email', 'attendee_credit_note_update', $event_id, $language_id);
    
                $subject = $template->info[0]['value'];
    
                $content = getEmailTemplate($template->info[1]['value'], $event['id']);
    
                $content = stripslashes($content);
    
                $content = (string)$this->_getTemplateContent($event, $eb_order->getMainAttendee(), $content, $event_id, $organizer_id, $eventSetting, $labels);
    
                $content = str_replace("{total_difference_amount}", stripslashes(getCurrency($diff_in_amount, $currency)), $content);
    
                $content = str_replace("{currency}", stripslashes($currency), $content);
    
                $content = str_replace("{credit_note_number}", stripslashes($credit_order_number), $content);
    
                $content = str_replace("{credit_note_amount}", stripslashes(getCurrency($credit_note['grand_total'], $currency)), $content);
    
                $content = str_replace("{new_invoice_number}", stripslashes($order_number), $content);
    
                $content = str_replace("{new_invoice_amount}", stripslashes(getCurrency($order['grand_total'], $currency)), $content);
    
                $subject = str_replace("{event_name}", stripslashes($event['name']), $subject);
                
            }
    
            $subject = str_replace('{attendee_email}', $toemail, $subject);
    
            $subject = str_replace('{attendee_name}', trim($first_name . ' ' . $last_name), $subject);
    
            $subject = str_replace('{total_difference_amount}', getCurrency($diff_in_amount, $currency), $subject);

        } else {

            $template = getTemplate('email', 'attendee_cancel_order', $event_id, $language_id);
    
            $subject = $template->info[0]['value'];

            $content = getEmailTemplate($template->info[1]['value'], $event['id']);

            $content = stripslashes($content);

            $content = (string)$this->_getTemplateContent($event, $eb_order->getMainAttendee(), $content, $event_id, $organizer_id, $eventSetting, $labels);

            $content = str_replace("{currency}", stripslashes($currency), $content);

            $content = str_replace("{credit_note_number}", stripslashes($credit_order_number), $content);

            $content = str_replace("{credit_note_amount}", stripslashes(getCurrency($credit_note->grand_total, $currency)), $content);
            
            $content = str_replace("{old_invoice_number}", stripslashes($order_number), $content);

        }
       
        $cc = [];

        if ($payment_setting['eventsite_send_email_order_creator'] == 1 && isset($billing_address['billing_contact_person_email']) && $billing_address['billing_contact_person_email'] != '') {
            $cc[] = $billing_address['billing_contact_person_email'];
        }

        $bcc = [];

        if ($eventsite_setting['send_invoice_email']) {

            $export_file_name = sanitizeLabel($labels['EXPORT_ORDER_INVOICE']);

            $export_credit_note_file_name = sanitizeLabel($labels['EXPORT_CREDIT_NOTES']);

            $attachments = [];

            if ($sent_with_diff) {
                if ($eventsite_setting['attach_invoice_email'] == 1) {
                    $attachments[] = ['path' => $invoice_pdf_file, 'name' => $export_file_name . $order_number . '.pdf'];
                    $attachments[] = ['path' => $credit_note_pdf_file, 'name' => $export_credit_note_file_name . $credit_order_number . '.pdf'];
                }
            } else {
                if ($eventsite_setting['attach_invoice_email'] == 1) {
                    $attachments[] = ['path' => $invoice_pdf_file, 'name' => $export_file_name . $order_number . '.pdf'];
                }
                if ($payment_setting['send_credit_note_in_email'] == 1) {
                    $attachments[] = ['path' => $credit_note_pdf_file, 'name' => $export_credit_note_file_name . $credit_order_number . '.pdf'];
                }
            }

            $data = array();

            $data['event_id'] = $event_id;

            $data['subject'] = $subject;

            $data['content'] = $content;

            $data['bcc'] = $bcc;

            $data['attachment'] = $attachments;

            $data['view'] = 'email.plain-text';

            $data['from_name'] = $event->organizer_name;
           
            if ($payment_setting['eventsite_send_email_order_creator'] == 2 &&
                isset($billing_address['billing_contact_person_email']) && $billing_address['billing_contact_person_email'] != '') {
                if ($billing_address['billing_contact_person_email']) \Mail::to($billing_address['billing_contact_person_email'], $billing_address['billing_contact_person_name'])->send(new Email($data));
            } elseif ($payment_setting['eventsite_send_email_order_creator'] == 1) {
                $data['cc'] = $cc;
                if ($toemail) \Mail::to($toemail, $first_name . ' ' . $last_name)->send(new Email($data));
            } else {
                if ($toemail) \Mail::to($toemail, $first_name . ' ' . $last_name)->send(new Email($data));
            }
            
        }
    }

    /**
     * @param mixed $order
     * @param mixed $event_id
     * @param mixed $organizer_id
     * @param mixed $language_id
     * @param mixed $order_id
     * @param mixed $credit_note_id
     * @param mixed $invoice_pdf_file
     * @param mixed $credit_note_pdf_file
     *
     * @return [type]
     */
    function sendInvoiceDiffEmailToBookKeeper($eb_order, $event_id, $organizer_id, $language_id, $order_id, $credit_note_id, $invoice_pdf_file, $credit_note_pdf_file)
    {
        $event = $eb_order->_getEvent();

        $toemail = $eb_order->getMainAttendee()->getModel()->email;

        $first_name = $eb_order->getMainAttendee()->getModel()->first_name;

        $last_name = $eb_order->getMainAttendee()->getModel()->last_name;

        $eventSetting = $eb_order->_getEventSetting();

        $eventsite_setting = $eb_order->_getEventSiteSetting();

        $payment_setting = $eb_order->_getPaymentSetting();

        //labels
        $labels = eventsite_labels(['eventsite', 'exportlabels'], ["event_id" => $event_id, "language_id" => $event->language_id]);

        if (empty(trim($payment_setting['bcc_emails']))) {
            return;
        }

        // New invoice
        $order = \App\Models\BillingOrder::where('id', addslashes($order_id))->first();

        if (trim($order['order_number'])) {
            $order_number = $order['order_number'];
        } else {
            $order_number = $order['id'];
        }

        $attendee_id = $order['attendee_id'];

        // Credit note
        $credit_note = \App\Models\BillingOrder::where('id', $credit_note_id)->first();

        if (trim($credit_note->order_number)) {
            $credit_order_number = $credit_note->order_number;
        } else {
            $credit_order_number = $credit_note->id;
        }

        $diff_in_amount = $order['grand_total'] - $credit_note->grand_total;

        $billing_currency = getCurrencyArray();

        foreach ($billing_currency as $key => $cur) {
            if ($order['eventsite_currency'] == $key) {
                $currency = $cur;
            }
        }

        $template = getTemplate('email', 'bookkeeper_invoice_update', $event_id, $language_id);

        $content = getEmailTemplate($template->info[1]['value'], $event['id']);

        $content = stripslashes($content);

        $content = (string)$this->_getTemplateContent($event, $eb_order->getMainAttendee(), $content, $event_id, $organizer_id, $eventSetting, $labels);

        $content = str_replace("{total_difference_amount}", stripslashes(getCurrency($diff_in_amount, $currency)), $content);

        $content = str_replace("{currency}", stripslashes($currency), $content);

        $content = str_replace("{credit_note_number}", stripslashes($credit_order_number), $content);

        $content = str_replace("{credit_note_amount}", stripslashes(getCurrency($credit_note['grand_total'], $currency)), $content);

        $content = str_replace("{new_invoice_number}", stripslashes($order_number), $content);

        $content = str_replace("{new_invoice_amount}", stripslashes(getCurrency($order['grand_total'], $currency)), $content);

        $subject = str_replace("{event_name}", stripslashes($event['name']), $template->info[0]['value']);

        $subject = str_replace('{event_name}', $eventSetting['name'], $subject);

        $subject = str_replace('{attendee_email}', $toemail, $subject);

        $subject = str_replace('{attendee_name}', trim($first_name . ' ' . $last_name), $subject);
        
        $subject = str_replace('{total_difference_amount}', getCurrency($diff_in_amount, $currency), $subject);

        $bookkeeper_email = '';

        $bcc = [];

        if (trim($payment_setting['bcc_emails'])) {
            $arr_emails = explode(',', $payment_setting['bcc_emails']);
            foreach ($arr_emails as $index => $email) {
                if ($index == 0) {
                    $bookkeeper_email = $email;
                } else {
                    $bcc[] = rtrim($email, ';');
                }
            }
        }

        if (!empty($bookkeeper_email)) {
            $export_file_name = sanitizeLabel($labels['EXPORT_ORDER_INVOICE']);
            $export_credit_note_file_name = 'Creditnote';
            $attachments = [];
            $attachments[] = ['path' => $invoice_pdf_file, 'name' => $export_file_name . $order_number . '.pdf'];
            $attachments[] = ['path' => $credit_note_pdf_file, 'name' => $export_credit_note_file_name . $credit_order_number . '.pdf'];

            $data = array();
            $data['event_id'] = $event_id;
            $data['subject'] = $subject;
            $data['content'] = $content;
            $data['bcc'] = $bcc;
            $data['attachments'] = $attachments;
            $data['view'] = 'email.plain-text';
            $data['from_name'] = $event->organizer_name;
            if ($bookkeeper_email) \Mail::to($bookkeeper_email)->send(new Email($data));
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function addSubRegistrationData($order)
    {
        $attendees = array();

        $event = $order->_getEvent();
        
        foreach ($order->getAllAttendees() as $attendee) {

            $attendee_id = $attendee->getModel()->id;

            $order_attendee = $order->_getAttendeeByID($attendee_id)->getOrderAttendee();
      
            $registration_form = $order->getRegistrationForm($attendee_id);

            $registration_form_id = $registration_form ? $registration_form->id : 0;

            $sub_registration = \App\Models\EventSubRegistration::where('event_id', '=', $event->id)->where('registration_form_id', $registration_form_id)->with(['question' => function ($query) {
                return $query->where('status', '=', '1')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
            }])->whereNull('deleted_at')->first();

            foreach($sub_registration->question as $i => $question) {

                // Delete old results for this question
                \App\Models\EventSubRegistrationResult::where('event_id', $order->getOrderEventId())->where('question_id', $question->id)->where('attendee_id', $attendee_id)->delete();

                $this->saveSubRegistrationAnswer($question, $order->getModelAttribute('id'), $attendee_id, $order->getOrderEventId());

            }

            $attendees[] = $attendee_id;
        }

        $setting = $order->_getEventSubregistrationSetting();

        /*if(count($attendees) > 0) {
            $isRegVerificationEnabled = $order->getAttendeeSettingsAttribute('attendee_reg_verification');
            $isAttendeeVerificationPending = $order->getMainAttendee()->isVerificationPending();
            if (($isRegVerificationEnabled && $isAttendeeVerificationPending) || $order->getModelAttribute('is_waitinglist') == '1') {
                return;
            }
            foreach (array_unique($attendees) as $attendee) {
                $this->sendSubregistrationEmailAttendee($order, $attendee);
            }
        }*/
    }

    /**
     * saveSubRegistrationAnswer
     *
     * @param mixed $question
     * @param mixed $order_id
     * @param mixed $attendee_id
     * @param mixed $event_id
     * @return void
     */
    private function saveSubRegistrationAnswer(\App\Models\EventSubRegistrationQuestion $question, $order_id, $attendee_id, $event_id)
    {
        $type = $question->question_type;

        $answers = \App\Models\EventOrderSubRegistrationAnswer::where('order_id', $order_id)->where('attendee_id', $attendee_id)->where('question_id', $question->id)->get();

        switch ($type) {
            case "single":
            case "multiple":
            case "dropdown":
                foreach ($answers as $answer) {

                    $actual_answer = \App\Models\EventSubRegistrationAnswer::where('id', $answer->answer_id)->first();

                    if($actual_answer && $actual_answer->link_to) {

                        $programTicket = ProgramRepository::getProgramTicket(["id" => $actual_answer->link_to]);

                        if($programTicket > 0 || (string)$programTicket == "unlimited") {

                            $agenda_id = $actual_answer->link_to;

                            $agendaAttendeeAttached = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', $attendee_id)->where('agenda_id', $agenda_id)->first();
                            
                            if (!$agendaAttendeeAttached instanceof \App\Models\EventAgendaAttendeeAttached) {
                                $agendaAttendeeAttached = new \App\Models\EventAgendaAttendeeAttached();
                                $agendaAttendeeAttached->attendee_id = $attendee_id;
                                $agendaAttendeeAttached->agenda_id = $agenda_id;
                                $agendaAttendeeAttached->linked_from = 'subregistration';
                                $agendaAttendeeAttached->link_id = $answer->answer_id;
                                $agendaAttendeeAttached->save();
                            }

                        }

                    }
                    
                    $subRegistrationResult = new \App\Models\EventSubRegistrationResult();
                    $subRegistrationResult->event_id = $event_id;
                    $subRegistrationResult->question_id = $question->id;
                    $subRegistrationResult->answer_id = $answer->answer_id;
                    $subRegistrationResult->comments = $answer->comment;
                    $subRegistrationResult->attendee_id = $attendee_id;
                    $subRegistrationResult->answer_type = 'b';
                    $subRegistrationResult->sub_registration_id = $question->sub_registration_id;
                    $subRegistrationResult->save();
                }
                break;
            case "matrix":
                foreach ($answers as $answer) {
                    $subRegistrationResult = new \App\Models\EventSubRegistrationResult();
                    $subRegistrationResult->event_id = $event_id;
                    $subRegistrationResult->question_id = $question->id;
                    $subRegistrationResult->answer = $answer->matrix_id;
                    $subRegistrationResult->answer_id = $answer->answer_id;
                    $subRegistrationResult->comments = $answer->comment;
                    $subRegistrationResult->attendee_id = $attendee_id;
                    $subRegistrationResult->answer_type = 'b';
                    $subRegistrationResult->sub_registration_id = $question->sub_registration_id;
                    $subRegistrationResult->save();
                }
                break;
            case "open":
            case "number":
            case "date":
            case "date_time":
                foreach ($answers as $answer) {
                    $subRegistrationResult = new \App\Models\EventSubRegistrationResult();
                    $subRegistrationResult->event_id = $event_id;
                    $subRegistrationResult->question_id = $question->id;
                    $subRegistrationResult->answer = $answer->answer;
                    $subRegistrationResult->comments = $answer->comment;
                    $subRegistrationResult->attendee_id = $attendee_id;
                    $subRegistrationResult->answer_type = 'b';
                    $subRegistrationResult->sub_registration_id = $question->sub_registration_id;
                    $subRegistrationResult->save();
                }
                break;
        }
    }

    /**
     * @param mixed $order
     * @param mixed $attendee
     *
     * @return [type]
     */
    public function sendSubregistrationEmailAttendee($order, $attendee)
    {
        $subRegSetting = $order->_getEventSubregistrationSetting();

        if ($subRegSetting['result_email'] == 1) {

            $event = $order->_getEvent();

            $language_id = $order->getUtility()->getLangaugeId();

            $sub_registration_result_detail = '';

            $language_id = $order->getUtility()->getLangaugeId();

            $eventSetting = $order->_getEventSetting();

            $order_attendee = $order->_getAttendeeByID($attendee)->getOrderAttendee();
            
            $registration_form = $order->getRegistrationForm($attendee);

            $registration_form_id = $registration_form ? $registration_form->id : 0;

            $sub_registration = \App\Models\EventSubRegistration::where('event_id', '=', $event->id)->where('registration_form_id', $registration_form_id)->with(['question' => function ($query) {
                return $query->where('status', '=', '1')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
            }, 'question.info' => function ($q) use ($language_id) {
                return $q->where('languages_id', '=', $language_id);
            }, 'question.answer' => function ($r) {
                return $r->orderBy('sort_order')->orderBy('id', 'ASC');
            }, 'question.answer.info' => function ($r) use ($language_id) {
                return $r->where('languages_id', '=', $language_id);
            }, 'question.result' => function ($s) use ($attendee) {
                return $s->where('attendee_id', $attendee)->orderBy('id', 'DESC');
            }])->whereNull('deleted_at')->first();

            $n = 0;

            foreach ($sub_registration->question as $question) {
                $n++;
                if ($question['question_type'] == 'single') {
                    $sub_registration_result_detail .= '<br><div class="question-type-open">';
                    if ($question['required_question'] == 1) {
                        $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                    }
                    $sub_registration_result_detail .= '<span style="font-size: 18px;font-weight: bold;">Question: </span>';
                    $sub_registration_result_detail .= $question['info'][0]['value'];
                    $j = 0;
                    foreach ($question['answer'] as $answer) {
                        $sub_registration_result_detail .= '<br><label>';
                        if (request()->old('answer' . $question['id'])[0] == $answer['id']) {
                            $checked_single = 'yes';
                        } elseif (isset($question['result'][0]['answer_id']) && $question['result'][0]['answer_id'] == $answer['id']) {
                            $checked_single = 'yes';
                        } else {
                            $checked_single = '';
                        }
                        if ($checked_single == 'yes') {
                            $sub_registration_result_detail .= '<b style="color: green;">';
                        }
                        $sub_registration_result_detail .= $answer['info'][0]['value'];
                        if ($checked_single == 'yes') {
                            $sub_registration_result_detail .= '</b>';
                        }
                        $sub_registration_result_detail .= '</label>';
                        $j++;
                    }
                    $sub_registration_result_detail .= ' </div>';
                } elseif ($question['question_type'] == 'multiple') {
                    $sub_registration_result_detail .= '<br><div class="question-type-open">';
                    if ($question['required_question'] == 1) {
                        $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                    }
                    $sub_registration_result_detail .= '<span style="font-size: 18px;font-weight: bold;">Question: </span>';
                    $sub_registration_result_detail .= $question['info'][0]['value'];
                    $i = 0;
                    foreach ($question['answer'] as $answer) {
                        $sub_registration_result_detail .= '<br><label>';
                        $checked_multiple = '';
                        if (request()->old('answer' . $question['id'])[$i] == $answer['id']) {
                            $checked_multiple = "yes";
                        } else {
                            foreach ($question['result'] as $result) {
                                if ($result['answer_id'] == $answer['id']) {
                                    $checked_multiple = "yes";
                                    break;
                                }
                            }
                        }
                        if ($checked_multiple == 'yes') {
                            $sub_registration_result_detail .= '<b style="color: green;">';
                        }
                        $sub_registration_result_detail .= $answer['info'][0]['value'];
                        if ($checked_multiple == 'yes') {
                            $sub_registration_result_detail .= '</b>';
                        }
                        $sub_registration_result_detail .= '</label>';
                        $i++;
                    }

                    $sub_registration_result_detail .= ' </div>';
                } elseif ($question['question_type'] == 'dropdown') {
                    $sub_registration_result_detail .= '<br><div class="question-type-open">';
                    if ($question['required_question'] == 1) {
                        $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                    }
                    $sub_registration_result_detail .= '<span style="font-size: 18px;font-weight: bold;">Question: </span>';
                    $sub_registration_result_detail .= $question['info'][0]['value'];
                    $drop_down = $question['answer'];
                    $i = 0;
                    foreach ($drop_down as $answer) {
                        if (request()->old('answer' . $question['id'])[0] == $answer['id']) {
                            $checked_drop = 'yes';
                        } elseif (isset($question['result'][0]['answer_id']) && $question['result'][0]['answer_id'] == $answer['id']) {
                            $checked_drop = 'yes';
                        } else {
                            $checked_drop = '';
                        }
                        $sub_registration_result_detail .= '<br><label>';
                        if ($checked_drop == 'yes') {
                            $sub_registration_result_detail .= '<b style="color: green;">';
                        }
                        $sub_registration_result_detail .= $answer['info'][0]['value'];
                        if ($checked_drop == 'yes') {
                            $sub_registration_result_detail .= '</b>';
                        }
                        $sub_registration_result_detail .= '</label>';
                        $i++;
                    }
                    $sub_registration_result_detail .= '</div>';
                } elseif ($question['question_type'] == 'open') {
                    $sub_registration_result_detail .= '<br><div class="question-type-open">';
                    if ($question['required_question'] == 1) {
                        $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                    }
                    $sub_registration_result_detail .= '<span style="font-size: 18px;font-weight: bold;">Question: </span>';
                    $sub_registration_result_detail .= $question['info'][0]['value'];
                    $answer_open = '';
                    if (request()->old('answer_open' . $question['id'])[0]) {
                        $answer_open = request()->old('answer_open' . $question['id'])[0];
                    } elseif (isset($question['result'][0]['answer']) && $question['result'][0]['answer'] != '') {
                        $answer_open = $question['result'][0]['answer'];
                    }
                    $sub_registration_result_detail .= '<div>' . $answer_open . '</div>';
                    $sub_registration_result_detail .= '</div>';
                } elseif ($question['question_type'] == 'number') {
                    $sub_registration_result_detail .= '<br><div class="question-type-open">';
                    if ($question['required_question'] == 1) {
                        $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                    }
                    $sub_registration_result_detail .= '<span style="font-size: 18px;font-weight: bold;">Question: </span>';
                    $sub_registration_result_detail .= $question['info'][0]['value'];
                    $answer_number = '';
                    if (request()->old('answer_number' . $question['id'])[0]) {
                        $answer_number = request()->old('answer_number' . $question['id'])[0];
                    } elseif (isset($question['result'][0]['answer']) && $question['result'][0]['answer'] != '') {
                        $answer_number = $question['result'][0]['answer'];
                    }
                    $sub_registration_result_detail .= '<div>';
                    $sub_registration_result_detail .= $answer_number;
                    $sub_registration_result_detail .= '</div>';
                    $sub_registration_result_detail .= '</div>';
                } elseif ($question['question_type'] == 'date') {
                    $sub_registration_result_detail .= '<br><div class="question-type-open">';
                    if ($question['required_question'] == 1) {
                        $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                    }
                    $sub_registration_result_detail .= '<span style="font-size: 18px;font-weight: bold;">Question: </span>';
                    $sub_registration_result_detail .= $question['info'][0]['value'];
                    $answer_number = '';
                    if (request()->old('answer_date' . $question['id'])[0] != '') {
                        $answer_date = request()->old('answer_date' . $question['id'])[0];
                    } elseif (isset($question['result'][0]['answer']) && $question['result'][0]['answer'] != '') {
                        $answer_date = $question['result'][0]['answer'];
                    }
                    $sub_registration_result_detail .= '<div>' . $answer_date . '<div>';
                    $sub_registration_result_detail .= '</div>';
                } elseif ($question['question_type'] == 'date_time') {
                    $sub_registration_result_detail .= '<br><div class="question-type-open">';
                    if ($question['required_question'] == 1) {
                        $sub_registration_result_detail .= '<span style="color: red;font-size: 18px;font-weight: bold;">* </span>';
                    }
                    $sub_registration_result_detail .= '<span style="font-size: 18px;font-weight: bold;">Question: </span>';
                    $sub_registration_result_detail .= $question['info'][0]['value'];
                    $answer_date_time = '';
                    if (request()->old('answer_date_time' . $question['id'])[0]) {
                        $answer_date_time = request()->old('answer_date_time' . $question['id'])[0];
                    } elseif (isset($question['result'][0]['answer']) && $question['result'][0]['answer'] != '') {
                        $answer_date_time = $question['result'][0]['answer'];
                    }
                    $sub_registration_result_detail .= '<div>' . $answer_date_time . '</div>';
                    $sub_registration_result_detail .= '</div>';
                }
                if ($question['enable_comments'] == 1) {
                    $sub_registration_result_detail .= '<div class="question-type-open">';
                    $sub_registration_result_detail .= '<br>' . $question['result'][0]['comments'];
                    $sub_registration_result_detail .= '</div>';
                }
            }

            $event_name = $event->name;

            $organizer_name = $event->organizer_name;

            $template = getTemplate('email', 'sub_registration_result_email', $event->id, $language_id);

            $findme = '{sub_registration_result_detail}';

            $content = getEmailTemplate($template->info[1]['value'], $event['id']);

            $pos = strpos($content, $findme);

            if ($pos !== false) {

                $attendee_detail = $order->_getAttendeeByID($attendee)->getModel();

                $subject = str_replace("{event_name}", stripslashes($event['name']), $template->info[0]['value']);

                if ($eventSetting['header_logo'] != '' && $eventSetting['header_logo'] != 'NULL') {
                    $src = cdn('/assets/event/branding/' . $eventSetting['header_logo']);
                } else {
                    $src = cdn("/_admin_assets/images/eventbuizz_logo.png");
                }

                $logo = '<img src="' . $src . '" width="150" />';

                $content = str_replace("{event_logo}", stripslashes($logo), $content);
                $content = str_replace("{first_name}", stripslashes($attendee_detail['first_name']), $content);
                $content = str_replace("{last_name}", stripslashes($attendee_detail['last_name']), $content);
                $content = str_replace("{attendee_name}", stripslashes($attendee_detail['first_name'] . ' ' . $attendee_detail['last_name']), $content);
                $content = str_replace("{event_name}", stripslashes($event_name), $content);
                $content = str_replace("{sub_registration_result_detail}", stripslashes($sub_registration_result_detail), $content);
                $content = str_replace("{event_organizer_name}", stripslashes($organizer_name), $content);

                //Send Email
                $to = $attendee_detail['email'];
                $name = $attendee_detail['first_name'] . ' ' . $attendee_detail['last_name'];

                $data = array();
                $data['event_id'] = $event->id;
                $data['template'] = 'sub_registration_result_email';
                $data['subject'] = $subject;
                $data['content'] = $content;
                $data['view'] = 'email.plain-text';
                $data['from_name'] = $event['organizer_name'];
                if ($to) \Mail::to($to, $name)->send(new Email($data));
            }
            
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function saveReferenceNumber($order)
    {
        $event_id = $order->getOrderEventId();

        $payment_setting = $order->_getPaymentSetting();

        if ($payment_setting["eventsite_billing_fik"] == "1") {
            $referenceNumber = getReferenceNumber($event_id, $order->getModelAttribute('order_number'), $payment_setting["debitor_number"], $payment_setting["invoice_type"]);
        } else {
            $referenceNumber = "";
        }

        $order->getModel()->update([
            "invoice_reference_no" => $referenceNumber
        ]);
    }

    /**
     * @param mixed $order
     * @param mixed $type
     *
     * @return [type]
     */
    public function addAttendeeLog($order, $type = "add")
    {
        $event_id = $order->getOrderEventId();
        $organizer_id = $order->getUtility()->getOrganizerID();
        $attendee_id = $order->getMainAttendee()->getModel()->id;
        $organizer = \App\Models\Organizer::find($organizer_id);
        if ($organizer->crm_integrated == 1) {
            // Firing the add attendee event
            \App\Models\AddAttendeeLog::create([
                'attendee_id' => $attendee_id,
                'event_id' => $event_id,
                'organizer_id' => $organizer_id,
                'type' => $type
            ]);
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function addAttendeePermission($order)
    {
        $event_id = $order->getOrderEventId();

        foreach ($order->getAllAttendees() as $attendee) {
            $attendee_id = $attendee->getModel()->id;
            $attendee = \App\Models\Attendee::find($attendee_id);
            $invited_attendee = \App\Models\AttendeeInvite::where('event_id', $event_id)->where('email', $attendee->email)->first();
            if ($invited_attendee) {
                $event_attendee = \App\Models\EventAttendee::where('event_id', $event_id)->where('attendee_id', $attendee_id)->first();
                if ($event_attendee) {
                    $event_attendee->allow_vote = $invited_attendee['allow_vote'];
                    $event_attendee->ask_to_apeak = $invited_attendee['ask_to_speak'];
                    $event_attendee->save();
                    if ($invited_attendee->ss_number) {
                        $check_attendee = \App\Models\Attendee::where('ss_number', $invited_attendee->ss_number)->where('email', '!=', $attendee->email)->where('organizer_id', $order->getUtility()->getOrganizerID())->first();
                        if (!$check_attendee) {
                            $attendee->ss_number = $invited_attendee->ss_number;
                            $attendee->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param mixed $addon
     *
     * @return [type]
     */
    public function saveTicketItem($addon)
    {
        //Create entry in tickets
        if ($addon->getModel()->ticket_item_id > 0) {
            $ticket_ids = EventTicketRepository::createTicket($addon->getModel()->id, 'billing');

            $addon->getOrder()->setGeneratedTicketsIds($ticket_ids);
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function addKeywordsData($order)
    {
        $event_id = $order->getOrderEventId();
        $organizer_id = $order->getUtility()->getOrganizerID();

        foreach ($order->getAllAttendees() as $attendee) {
            $order_attendee_model = $attendee->getOrderAttendee();
            $payment_setting = $order->_getPaymentFormSetting($order_attendee_model->registration_form_id);
            $keywords = \App\Models\EventOrderKeyword::where('order_id', $order->getModelAttribute('id'))->where('attendee_id', $attendee->getModel()->id)->get();
            if (count($keywords) > 0 && $payment_setting['show_business_dating'] == '1') {
                foreach ($keywords as $keyword) {
                    $attendee_match_object = new \App\Models\AttendeeMatchKeyword();
                    $attendee_match_object->organizer_id = $organizer_id;
                    $attendee_match_object->event_id = $event_id;
                    $attendee_match_object->attendee_id = $attendee->getModel()->id;
                    $attendee_match_object->keyword_id = $keyword->keyword_id;
                    $attendee_match_object->status = 1;
                    $attendee_match_object->save();
                }
            }
        }
    }

    public static function generateCreditNote($order)
    {

        $order = $order->toArray();

        $payment_setting = EventSiteSettingRepository::getPaymentSetting(["event_id" => $order['event_id']]);

        $credit_note_number = $payment_setting->eventsite_invoice_currentnumber;

        $payment_setting->eventsite_invoice_currentnumber = $credit_note_number + 1;

        $payment_setting->save();

        $items = \App\Models\BillingOrderAddon::where('order_id', $order['id'])->get();

        $previous_addons_attendees_array = \App\Models\BillingOrderAttendee::where('order_id', $order['id'])->get();

        //Add Credit Notes.
        $billing_order_credit_note_data = [
            "order_id" => $order['id'],
            "credit_note_create_date" => date('Y-m-d H:i:s'),
        ];

        foreach ($order as $key => $value) {
            if ($key == 'id' || $key == 'event') {
                continue;
            }
            if ($key == 'order_number') {
                $billing_order_credit_note_data[$key] = $credit_note_number;
            } else if ($key == 'deleted_at') {
            } else if ($key == 'is_archive') {
            } else {
                $billing_order_credit_note_data[$key] = (!is_array($value) && !is_object($value)) ? addslashes($value) : $value;
            }
        }

        //Create credit note
        $credit_note_id = \DB::table('conf_billing_orders_credit_notes')->insertGetId($billing_order_credit_note_data);

        $billing_order_items_credit_notes = array();

        foreach ($items as $rs_addon) {
            $billing_order_items_credit_notes[] = array('credit_note_id' => $credit_note_id, 'order_id' => $rs_addon['order_id'], 'attendee_id' => $rs_addon['attendee_id'], 'addon_id' => $rs_addon['addon_id'], 'name' => $rs_addon['name'], 'price' => $rs_addon['price'], 'parent' => $rs_addon['parent'], 'qty' => $rs_addon['qty'], 'order_number' => $credit_note_number, 'discount' => $rs_addon['discount']);
        }

        if (count($billing_order_items_credit_notes) > 0) {
            \DB::table('conf_billing_order_addons_credit_notes')->insert($billing_order_items_credit_notes);
        }

        $billing_order_attendees_credit_notes = array();

        foreach ($previous_addons_attendees_array as $rs_addon_attendee) {
            $billing_order_attendees_credit_notes[] = array('credit_note_id' => $credit_note_id, 'order_id' => $rs_addon_attendee['order_id'], 'attendee_id' => $rs_addon_attendee['attendee_id'], 'order_number' => $credit_note_number);
        }

        if (count($billing_order_attendees_credit_notes) > 0) {
            \DB::table('conf_billing_order_attendees_credit_notes')->insert($billing_order_attendees_credit_notes);
        }

        $billing_order_logs = \App\Models\BillingOrderLog::where('order_id', $order['id'])->get();

        $billing_order_log_credit_notes = array();

        foreach ($billing_order_logs as $history) {
            $billing_order_log_credit_notes[] = array('credit_note_id' => $credit_note_id, 'event_id' => $history['event_id'], 'order_id' => $history['order_id'], 'order_number' => $credit_note_number, 'field_name' => $history['field_name'], 'update_date' => $history['update_date'], 'update_date_time' => $history['update_date_time'], 'data_log' => addslashes($history['data_log']));
        }

        if (count($billing_order_log_credit_notes) > 0) {
            \DB::table('conf_billing_order_log_credit_notes')->insert($billing_order_log_credit_notes);
        }

        if ($payment_setting->show_hotels == '1') {

            $orderHotel = \App\Models\EventOrderHotel::where('order_id', $order['id'])->first();

            if ($orderHotel) {

                $hotel_arr[] = array('hotel_id' => $orderHotel['hotel_id'], 'order_id' => $orderHotel['order_id'], 'name' => $orderHotel['name'], 'price' => $orderHotel['price'], 'price_type' => $orderHotel['price_type'], 'vat' => $orderHotel['vat'], 'vat_price' => $orderHotel['vat_price'], 'rooms' => $orderHotel['rooms'], 'checkin' => $orderHotel['checkin'], 'checkout' => $orderHotel['checkout']);

                \DB::table('conf_event_credit_note_order_hotels')->insert($hotel_arr);


                $hotel_persons = \App\Models\EventHotelPerson::where('order_id', $order['id'])->get();

                $hotel_persons_arr = array();

                foreach ($hotel_persons as $person) {
                    $hotel_persons_arr[] = array('hotel_id' => $person['hotel_id'], 'order_id' => $person['order_id'], 'name' => $person['name'], 'dob' => $person['dob']);
                }

                if (count($hotel_persons_arr) > 0) {
                    \DB::table('conf_event_credit_note_order_hotels')->insert($hotel_arr);
                }
            }
        }

        return $credit_note_id;
    }

    /**
     * deleteOrder
     *
     * @param mixed $formInput
     * @return void
     */
    public static function deleteOrder($formInput)
    {
        $order = \App\Models\BillingOrder::find($formInput['order_id']);

        if ($order) {

            self::updateReportingTableDataOnArchiveOrder($order->id, 1, $order->is_waitinglist ? 1 : 0);

            foreach($order->tickets()->get() as $ticket)
            {
                $ticket->delete();
            }

            \App\Models\BillingOrderAddon::where('order_id', $order->id)->delete();

            $order_attendees = \App\Models\BillingOrderAttendee::where('order_id', $order->id)->get();

            foreach($order_attendees as $order_attendee) {

                $attendee = \App\Models\Attendee::where('id', $order_attendee->attendee_id)->first();

                if($attendee) {

                    AttendeeRepository::unAssign(['event_id' => $formInput['event_id'], 'attendee_id' => $order_attendee->attendee_id], $order_attendee->attendee_id);

                    $order_attendee->delete();

                }
            }

            $order->is_archive = 1;

            $order->save();

            $waiting_attendee = \App\Models\WaitingListAttendee::where('event_id', $formInput['event_id'])->where('attendee_id', '=', $order->attendee_id)->first();

            if($waiting_attendee) {

                if($formInput['force_waiting_attendee_delete']) {

                    $waiting_attendee->delete();

                } else {

                    $waiting_attendee->status = '3';

                    $waiting_attendee->save();

                }
            }

            \App\Models\EventOrderHotel::where('order_id', $order->id)->delete();

            \App\Models\EventOrderHotelRoom::where('order_id', $order->id)->delete();

            \App\Models\EventHotelPerson::where('order_id', $order->id)->delete();

            \App\Models\BillingOrderLog::where('order_id', $order->id)->delete();
            
        }
    }

    /**
     * updateReportingTableDataOnArchiveOrder
     *
     * @param mixed $order_id
     * @param mixed $is_archive
     * @return void
     */
    public static function updateReportingTableDataOnArchiveOrder($order_id, $is_archive = '', $is_waiting = false)
    {

        $order = \App\Models\BillingOrder::where('id', $order_id)->first();

        if (!$order) {
            return;
        }

        if($is_waiting) {
            $revenue = \App\Models\ReportingRevenueTable::where('event_id', '=', $order->event_id)->whereDate('date', '=', date('Y-m-d', strtotime($order->order_date)))->where('waiting_order_ids', 'LIKE', '%' . $order->id . '%')->first();
        } else {
            $revenue = \App\Models\ReportingRevenueTable::where('event_id', '=', $order->event_id)->whereDate('date', '=', date('Y-m-d', strtotime($order->order_date)))->where('order_ids', 'LIKE', '%' . $order->id . '%')->first();
        }
        
        if ($revenue) {

            $order_attendees = \App\Models\BillingOrderAttendee::where('order_id', $order->id)->get()->toArray();
            
            if($is_waiting) {
                $waiting_order_ids = str_replace($order->id . ',', '', $revenue->waiting_order_ids);
                $revenue->waiting_order_ids = $waiting_order_ids;
                $revenue->waiting_tickets = ($revenue->waiting_tickets - count($order_attendees));
            } else {
                $order_ids = str_replace($order->id . ',', '', $revenue->order_ids);
                $revenue->order_ids = $order_ids;
                $revenue->total_tickets = ($revenue->total_tickets - count($order_attendees));
                $revenue->total_revenue = ($revenue->total_revenue - $order->reporting_panel_total);
            }
          
            $revenue->save();
            
        }

    }
    
    /**
     * cleanReportingRevenue
     *
     * @param  object $event
     * @return void
     */
    public static function cleanReportingRevenue($event) {
        
        if ($event->eventsitesettings->payment_type != 1) {
            return;
        }

        $orders = \App\Models\BillingOrder::with(['order_attendees'])->where('event_id', $event->id)->where('status', 'completed')->where('is_archive', 0)->currentOrder()->get();
        
        //Clean event revenue
        \App\Models\ReportingRevenueTable::where('event_id', $event->id)->update([
            'order_ids' => '',
            'waiting_order_ids' => '',
            'total_tickets' => 0,
            'waiting_tickets' => 0,
            'event_total_tickets' => 0,
            'total_revenue' => 0
        ]);

        if(count($orders) > 0) {

            foreach($orders as $order) {

                $revenue = \App\Models\ReportingRevenueTable::whereDate('date', \Carbon\Carbon::parse($order->order_date)->toDateString())->where('event_id', $order->event_id)->first();

                if($revenue) {
                    if($order->is_waitinglist == 1) {       
                        $tickets_waiting = \App\Models\WaitingListAttendee::where('event_id', $order->event_id)->where('attendee_id', $order->attendee_id)->whereNotIn('status',  [3,4])->where('type', 1)->whereNull('deleted_at')->count();
                        if($tickets_waiting <= 0){
                            continue;
                        }
                        $revenue->waiting_order_ids =  $revenue->waiting_order_ids . $order->id . ',';
                        $revenue->waiting_tickets = $revenue->waiting_tickets +  count($order->order_attendees);
                    } else {
                        $revenue->order_ids = $revenue->order_ids . $order->id . ',';
                        $revenue->total_tickets = $revenue->total_tickets + count($order->order_attendees);
                        $revenue->total_revenue = $revenue->total_revenue + $order->reporting_panel_total;
                    }
                    $revenue->event_total_tickets = (int) $event->eventsite_settings->ticket_left;
                    $revenue->save();
                } else {
                    if($order->is_waitinglist == 1) {
                        $tickets_waiting = \App\Models\WaitingListAttendee::where('event_id', $order->event_id)->where('attendee_id', $order->attendee_id)->whereNotIn('status',  [3,4])->where('type', 1)->whereNull('deleted_at')->count();
                        if($tickets_waiting <= 0){
                            continue;
                        }
                        $revenue = \App\Models\ReportingRevenueTable::create([
                            'waiting_order_ids' => $order->id . ',',
                            'waiting_tickets' => count($order->order_attendees),
                            'event_id' => $event->id,
                            'date' => \Carbon\Carbon::parse($order->order_date)->toDateString(),
                            'event_total_tickets' => (int) $event->eventsite_settings->ticket_left
                        ]);
                    } else {
                        $revenue = \App\Models\ReportingRevenueTable::create([
                            'order_ids' => $order->id . ',',
                            'total_revenue' => $order->reporting_panel_total,
                            'total_tickets' => count($order->order_attendees),
                            'event_id' => $event->id,
                            'date' => \Carbon\Carbon::parse($order->order_date)->toDateString(),
                            'event_total_tickets' => (int) $event->eventsite_settings->ticket_left
                        ]);
                    }
                }

                //Update order
                $order->is_added_reporting = '1';
                $order->save();
            }
            
        }

    }

    /**
     * cancelWaitingListOrder
     *
     * @param mixed $order_id
     * @param mixed $event_id
     * @param mixed $labels
     * @return void
     */
    public static function cancelWaitingListOrder($order_id, $event_id, $labels)
    {

        $order = \App\Models\BillingOrder::where('id', $order_id)->where('is_archive', '0')->first();

        if ($order) {

            if ($order->is_waitinglist == 1) {

                $event_attendee = \App\Models\EventAttendee::where('attendee_id', $order->attendee_id)->where('event_id', $event_id)->first();

                //attendee is assigned to event. maybe admin assigned attendee manually. do not proceed
                if ($event_attendee instanceof \App\Models\EventAttendee) {
                    return [
                        "success" => false,
                        "message" => $labels['WAITING_LIST_ALREADY_REGISTERED']
                    ];
                }

                self::deleteOrder(['event_id' => $event_id, 'order_id' => $order_id, 'force_waiting_attendee_delete' => false]);

                return [
                    "success" => true,
                    "message" => $labels['WAITING_LIST_NOT_ACCEPTED_MESSAGE']
                ];

            } else {
                return [
                    "success" => false,
                    "message" => $labels['WAITING_LIST_ALREADY_REGISTERED']
                ];
            }

        } else {
            return [
                "success" => false,
                "message" => $labels['WAITING_LIST_ORDER_NOT_FOUND']
            ];
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function attachToMainEvent($order)
    {
        $organizer_id = $order->getUtility()->getOrganizerID();
        $event = $order->getEvent();
        $language_id = $order->getUtility()->getLangaugeId();
        if ($event->parent_event_id) {
            $parent_event = \App\Models\Event::where('id', $event->parent_event_id)->first();
            if ($parent_event) {
                foreach ($order->getAttendees() as $attendee) {
                    if($event->registration_type == "sponsor" || $event->registration_type == "exhibitor") {
                        $info = \App\Models\AttendeeInfo::where('attendee_id', $attendee->getModel()->id)->where('languages_id', $language_id)->where('name', 'company_name')->first();
                        $company_name = ($info ? $info->value : ($event->registration_type == "sponsor" ? "Sponsor" : "Exhibitor"));
                        $this->assignAttendeesToMainEvent($order, $attendee->getModel()->id, $parent_event->id, $parent_event->language_id, $event->registration_type);
                        $main = $order->getMainAttendee()->getModel()->id == $attendee->getModel()->id ? true : false;
                        if (trim($company_name)) {
                            if ($event->portal_access == 1 || $event->portal_access == 2 || ($event->portal_access == 0 && $attendee->getModel()->id == $order->getMainAttendee()->getModel()->id)) {
                                if ($event->registration_type == "sponsor") {
                                    $id = $this->addSponsorHubAdmin($event, $organizer_id, $attendee->getModel(), $parent_event->id, $company_name, $main, $event->registration_type, $order->getModel()->id);
                                    $order->getModel()->registration_type = 'sponsor';
                                    $order->getModel()->registration_type_id = $id;
                                    $order->getModel()->save();
                                } else {
                                    $id = $this->addExhibitorHubAdmin($event, $organizer_id, $attendee->getModel(), $parent_event->id, $company_name, $main, $event->registration_type, $order->getModel()->id);
                                    $order->getModel()->registration_type = 'exhibitor';
                                    $order->getModel()->registration_type_id = $id;
                                    $order->getModel()->save();
                                }
                            }
                        }
                    } else {
                        $this->assignAttendeesToMainEvent($order, $attendee->getModel()->id, $parent_event->id, $parent_event->language_id);
                    }
                    if($order->getMainAttendee()->getModel()->id == $attendee->getModel()->id) {
                        $this->assignKeywordsToMainEvent($order, $organizer_id, $attendee->getModel()->id, $parent_event->id, $event->id);
                    }
                    //Assign attendee type
                    $event_attendee = \App\Models\EventAttendee::where('event_id', $parent_event->id)->where('attendee_id', $attendee->getModel()->id)->first();
                    if ($event_attendee) {
                        $event_attendee->attendee_type = $event->parent_event_attendee_type;
                        $event_attendee->save();
                    }
                }
            }
        }
    }

    /**
     * @param mixed $order
     *
     * @return [type]
     */
    public function addPortalAccess($order)
    {
        $organizer_id = $order->getUtility()->getOrganizerID();
        $event = $order->getEvent();
        $language_id = $order->getUtility()->getLangaugeId();
        foreach ($order->getAttendees() as $attendee) {
            $order_attendee = $attendee->getOrderAttendee();
            $eventsite_setting = $order->_getEventsiteFormSetting($order_attendee->registration_form_id);
            if($eventsite_setting->registration_type == "sponsor" || $eventsite_setting->registration_type == "exhibitor") {
                $info = \App\Models\AttendeeInfo::where('attendee_id', $attendee->getModel()->id)->where('languages_id', $language_id)->where('name', 'company_name')->first();
                $company_name = ($info ? $info->value : ($eventsite_setting->registration_type == "sponsor" ? "Sponsor" : "Exhibitor"));
                $main = $order->getMainAttendee()->getModel()->id == $attendee->getModel()->id ? true : false;
                if (trim($company_name)) {
                    $event->portal_access = $eventsite_setting->portal_access;
                    if ($eventsite_setting->portal_access == 1 || $eventsite_setting->portal_access == 0) {
                        if ($eventsite_setting->registration_type == "sponsor") {
                            $id = $this->addSponsorHubAdmin($event, $organizer_id, $attendee->getModel(), $event->id, $company_name, $main, $eventsite_setting->registration_type, $order->getModel()->id);
                            $order->getModel()->registration_type = 'sponsor';
                            $order->getModel()->registration_type_id = $id;
                            $order->getModel()->save();
                        } else {
                            $id = $this->addExhibitorHubAdmin($event, $organizer_id, $attendee->getModel(), $event->id, $company_name, $main, $eventsite_setting->registration_type, $order->getModel()->id);
                            $order->getModel()->registration_type = 'exhibitor';
                            $order->getModel()->registration_type_id = $id;
                            $order->getModel()->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * assignAttendeesToMainEvent
     *
     * @param mixed $order
     * @param mixed $attendee_id
     * @param mixed $event_id
     * @param mixed $language_id
     * @param mixed $attendeeType
     * @return void
     */
    public function assignAttendeesToMainEvent($order, $attendee_id, $event_id, $language_id, $attendeeType = null)
    {
        $bit = $order->getMainAttendee()->getGdpr();
        $eventAttendees = \App\Models\EventAttendee::where('attendee_id', $attendee_id)->where('event_id', $event_id)->get();
        if (count($eventAttendees) == 0) {
            $event_attendee = new \App\Models\EventAttendee();
            $event_attendee->event_id = $event_id;
            $event_attendee->attendee_id = $attendee_id;
            $event_attendee->default_language_id = $language_id;
            if ($attendeeType == "sponsor") {
                $setting = \App\Models\SponsorSetting::where('event_id', $event_id)->first();
                $event_attendee->sponser = 1;
                if ($setting->gdpr_accepted == 1) $event_attendee->gdpr = 1;
            } else if ($attendeeType == "exhibitor") {
                $setting = \App\Models\ExhibitorSetting::where('event_id', $event_id)->first();
                $event_attendee->exhibitor = 1;
                if ($setting->gdpr_accepted == 1) $event_attendee->gdpr = 1;
            } else if ($attendee_id == $order->getMainAttendee()->getModel()->id) {
                $event_attendee->gdpr = $bit ? 1 : 0;

                //Logs
                $eventGDPR = \App\Models\EventGdpr::where('event_id', $event_id)->whereNull('deleted_at')->first();
                $log['event_id'] = $event_id;
                $log['attendee_id'] = $attendee_id;
                $log['gdpr_accept'] = 1;
                $log['gdpr_description'] = $eventGDPR->description;

                \App\Models\GdprAttendeeLog::create($log);
            }

            $event_attendee->save();

            $languages_arr = get_event_languages($event_id);
            foreach ($languages_arr as $key) {
                foreach (AttendeeRepository::$infoFields as $field) {
                    if ($field == 'custom_field_id') {
                        $field = $field . $event_id;
                    }
                    $info = \App\Models\AttendeeInfo::where('attendee_id', $attendee_id)->where('languages_id', $key)->where('name', $field)->first();
                    if (!$info) {
                        $info = \App\Models\AttendeeInfo::where('attendee_id', $attendee_id)->where('name', $field)->first();
                        if ($info) {
                            $newInfo = $info->replicate();
                            $newInfo->languages_id = $key;
                            $newInfo->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * assignKeywordsToMainEvent
     *
     * @param mixed $order
     * @param mixed $organizer_id
     * @param mixed $attendee_id
     * @param mixed $parent_event_id
     * @param mixed $event_id
     * @return void
     */
    public function assignKeywordsToMainEvent($order, $organizer_id, $attendee_id, $parent_event_id, $event_id)
    {
        $payment_settings = $order->_getPaymentSetting();
        $mainAttendeeID = $order->getMainAttendee()->getModel()->id;
        $event_id = $order->getOrderEventId();
        $organizer_id = $order->getUtility()->getOrganizerID();
        $payment_setting = $order->_getPaymentSetting();
        $keywords = \App\Models\EventOrderKeyword::where('order_id', $order->getModelAttribute('id'))->where('attendee_id', $mainAttendeeID)->get();
        if (count($keywords) > 0 && $payment_setting['show_business_dating'] == '1') {
            foreach ($keywords as $keyword) {
                $attendee_match_object = new \App\Models\AttendeeMatchKeyword();
                $attendee_match_object->organizer_id = $organizer_id;
                $attendee_match_object->event_id = $parent_event_id;
                $attendee_match_object->attendee_id = $attendee_id;
                $attendee_match_object->keyword_id = $keyword->keyword_id;
                $attendee_match_object->status = 1;
                $attendee_match_object->save();
            }
        }
    }

    /**
     * addExhibitorHubAdmin
     *
     * @param mixed $event
     * @param mixed $organizer_id
     * @param mixed $attendee
     * @param mixed $event_id
     * @param mixed $company_name
     * @param mixed $main
     * @param mixed $attendeeType
     * @param mixed $order_id
     * @return void
     */
    public function addExhibitorHubAdmin($event, $organizer_id, $attendee, $event_id, $company_name, $main = true, $attendeeType = null, $order_id = null)
    {
        $exhibitor = \App\Models\EventExhibitor::where('event_id', $event_id)->where('name', strtolower($company_name))->orderBy('id', 'desc')->first();

        if (!$exhibitor) $exhibitor = \App\Models\EventExhibitor::create(['event_id' => $event_id, 'name' => $company_name, 'email' => $attendee->email]);

        if ($event->portal_access == 1 || $event->portal_access == 2) {

            //Add as exhibitor
            $exhibitor_attendee = \App\Models\EventExhibitorAttendee::where('exhibitor_id', $exhibitor['id'])->where('attendee_id', $attendee->id)->count();

            if ($exhibitor_attendee == 0) {
                $data['exhibitor_id'] = $exhibitor['id'];
                $data['attendee_id'] = $attendee->id;
                \App\Models\EventExhibitorAttendee::create($data);
            }

            if ($event->portal_access == 1) {
                //Create hub administrator
                $admin = \App\Models\HubAdministrator::where('organizer_id', $organizer_id)->where('email', $attendee->email)->first();
                if (!$admin) {
                    $admin = \App\Models\HubAdministrator::create(['organizer_id' => $organizer_id, 'first_name' => $attendee->first_name, 'last_name' => $attendee->last_name, 'email' => $attendee->email, 'password' => \Hash::make('123456'), 'status' => 'y']);
                }

                $attachHubAdmin = \App\Models\EventAttachHubAdmin::where('hub_admin_id', $admin['id'])->where('event_id', $event_id)->where('type', 'exhibitor')->where('type_id', $exhibitor['id'])->whereNull('deleted_at')->first();

                //Attach hub admin to event
                if (!$attachHubAdmin) {
                    $input['hub_admin_id'] = $admin['id'];
                    $input['event_id'] = $event_id;
                    $input['type'] = 'exhibitor';
                    $input['type_id'] = $exhibitor['id'];
                    \App\Models\EventAttachHubAdmin::create($input);
                    HubAdministratorRepository::sendEmail($admin['id'], $event_id, $exhibitor['id'], $attendeeType, $organizer_id);
                }
            }
        } else {
            \App\Models\StandSaleRegistrationLink::create([
                'event_id' => $event_id,
                'type' => 'exhibitor',
                'link_id' => $exhibitor['id'],
                'token' => $this->generateUniqueCode(),
                'order_id' => $order_id,
                'attendee_id' => $attendee->id,
                'expire_at' => \Carbon\Carbon::now()->addDays(2),
            ]);
        }

        ExhibitorRepository::createSlots($event_id, $exhibitor['id']);
        return $exhibitor['id'];
    }

    /**
     * Write code on Method
     * @return response()
     */
    public function generateUniqueCode()
    {
        do {
            $token = rand(100000000, 999999999);
        } while (\App\Models\StandSaleRegistrationLink::where("token", $token)->first());

        return $token;
    }

    /**
     * addSponsorHubAdmin
     *
     * @param mixed $event
     * @param mixed $organizer_id
     * @param mixed $attendee
     * @param mixed $event_id
     * @param mixed $company_name
     * @param mixed $main
     * @param mixed $attendeeType
     * @param mixed $order_id
     * @return void
     */
    public function addSponsorHubAdmin($event, $organizer_id, $attendee, $event_id, $company_name, $main = true, $attendeeType = null, $order_id = null)
    {
        $sponsor = \App\Models\EventSponsor::where('event_id', $event_id)->where('name', strtolower($company_name))->orderBy('id', 'desc')->first();

        if (!$sponsor) {
            $sponsor = \App\Models\EventSponsor::create(['event_id' => $event_id, 'name' => $company_name, 'email' => $attendee->email]);
        }

        if ($event->portal_access == 1 || $event->portal_access == 2) {

            //Add as sponsor
            $sponsor_attendee = \App\Models\EventSponsorAttendee::where('sponsor_id', $sponsor['id'])->where('attendee_id', $attendee->id)->count();

            if ($sponsor_attendee == 0) {
                $data['sponsor_id'] = $sponsor['id'];
                $data['attendee_id'] = $attendee->id;
                \App\Models\EventSponsorAttendee::create($data);
            }

            if ($event->portal_access == 1) {
                //Create hub administrator
                $admin = \App\Models\HubAdministrator::where('organizer_id', $organizer_id)->where('email', $attendee->email)->first();
                if (!$admin) {
                    $admin = \App\Models\HubAdministrator::create(['organizer_id' => $organizer_id, 'first_name' => $attendee->first_name, 'last_name' => $attendee->last_name, 'email' => $attendee->email, 'password' => \Hash::make('123456'), 'status' => 'y']);
                }

                $attachHubAdmin = \App\Models\EventAttachHubAdmin::where('hub_admin_id', $admin['id'])->where('event_id', $event_id)->where('type', 'sponsor')->where('type_id', $sponsor['id'])->whereNull('deleted_at')->first();

                //Attach hub admin to event
                if (!$attachHubAdmin) {
                    $input['hub_admin_id'] = $admin['id'];
                    $input['event_id'] = $event_id;
                    $input['type'] = 'sponsor';
                    $input['type_id'] = $sponsor['id'];
                    \App\Models\EventAttachHubAdmin::create($input);
                    HubAdministratorRepository::sendEmail($admin['id'], $event_id, $sponsor['id'], $attendeeType, $organizer_id);
                }
            }

        } else {
            \App\Models\StandSaleRegistrationLink::create([
                'event_id' => $event_id,
                'type' => 'sponsor',
                'link_id' => $sponsor['id'],
                'token' => $this->generateUniqueCode(),
                'order_id' => $order_id,
                'attendee_id' => $attendee->id,
                'expire_at' => \Carbon\Carbon::now()->addDays(2),
            ]);
        }

        SponsorsRepository::createSlots($event_id, $sponsor['id']);
        
        return $sponsor['id'];
    }
        
    /**
     * attachAttendeeGroupsByAttendeeType
     *
     * @param  mixed $order
     * @return void
     */
    public function attachAttendeeGroupsByAttendeeType($order)
    { 
        $language_id = $order->getUtility()->getLangaugeId();
        $event_id = $order->getOrderEventId();
        if($order->getPaymentSettingAttribute('assign_group_by_attendee_type') == 1) {
            foreach ($order->getAttendees() as $attendee) {
                $event_attendee = \App\Models\EventAttendee::where('event_id', $event_id)->where('attendee_id', $attendee->getModel()->id)->first();
                if ($event_attendee) {
                    if((int)$event_attendee->attendee_type > 0) {
                        $attendee_type = \App\Models\EventAttendeeType::where('event_id', $event_id)->where('id', $event_attendee->attendee_type)->first();
                        if($attendee_type) {
                            $groups = $attendee_type->event_groups()->pluck('group_id');
                            if(count($groups) > 0) {
                                $groups = $groups->toArray();
                                $attendee_id = $attendee->getModel()->id;
                                $groups = AttendeeRepository::getGroups(implode(',',$groups), $event_id, $language_id);
                                if (count($groups) > 0) {
                                    $groups = $groups->toArray();
                                    AttendeeRepository::attachAttendeeGroups($groups, $event_id, $attendee_id);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * returns total tickets for an event
     * @param $eventId
     * @return int
     */
    static public function getTotalTicketsForEvent($formInput): int
    {
        $registrationFormId = EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($formInput['event_id'], 'attendee');

        $tickets = EventsiteSetting::where(["event_id"=> $formInput['event_id'],'registration_form_id'=> $formInput['registration_form_id']])->select("ticket_left")->first();

        if ($tickets) {
            return (int)$tickets->ticket_left;
        }

        //default
        return 0;
    }

    /**
     * returns attendees count that are confirmed
     * @param $eventId
     * @return int
     */
    static public function getConfirmedEventAttendeesCount($formInput): int
    {
        $registrationFormId = EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($formInput['event_id'], 'attendee');
        
        if($registrationFormId > 0) {

            $active_orders_ids = BillingOrder::where('event_id', $formInput['event_id'])->where('is_archive', 0)->where('status', 'completed')->currentOrder()->pluck('id');

            return static::confirmOrderAttendeesCount($formInput['registration_form_id'], $active_orders_ids);

        } else {

            return EventAttendee::where("event_id", $formInput['event_id'])->count();

        }
        
    }
    
    public static function confirmOrderAttendeesCount($registrationFormId, $active_orders_ids)
    {
        return BillingOrderAttendee::join('conf_billing_orders', 'conf_billing_orders.id', '=', 'conf_billing_order_attendees.order_id')
        ->whereIn('conf_billing_order_attendees.order_id', $active_orders_ids)->where('conf_billing_order_attendees.registration_form_id', $registrationFormId)->count();
    }

    /**
     * returns valid offer letters count
     * that are still valid
     * @param $eventId
     * @return int
     */
    static function getSentOfferLettersCount($formInput = []): int
    {
        $eventId = $formInput["event_id"];

        //get validity duration value
        $offerValidityDuration = EventWaitingListSetting::where('event_id', $eventId)->select("validity_duration")->first();

        $registrationFormId = EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($eventId, 'attendee');

        if ($offerValidityDuration) {

            $offerValidityDuration = (int)$offerValidityDuration->validity_duration * 60 * 60;

            $currentTime = time();

            $offerLettersCount = 0;

            $eventAttendees = WaitingListAttendee::where('event_id', $eventId)->where('status', '1')->get();

            foreach ($eventAttendees as $eventAttendee) {

                $offerExpiryTime = strtotime($eventAttendee->date_sent) + $offerValidityDuration;

                //if the offer is still not expired
                if ($offerExpiryTime > $currentTime || $offerValidityDuration == 0) {

                    //get billing order attendees count
                    $orderAttendees = BillingOrder::where('attendee_id', '=', $eventAttendee->attendee_id)
                        ->where('event_id', $eventId)
                        ->where('is_waitinglist', '=', '1')
                        ->where('is_archive', '=', '0')
                    ->with(['order_attendees' => function ($query) use ($registrationFormId) {
                        return $query->where('registration_form_id', '=', $registrationFormId);
                    }])->currentOrder()->orderBy('id', 'desc')->first();
                    
                    if(!empty($orderAttendees)){
                        $offerLettersCount = $offerLettersCount + count($orderAttendees->order_attendees);
                    }
                }
            }

            return $offerLettersCount;
        }

        //default
        return 0;
    }

    /**
     * returns pending attendees count
     * @param $eventId
     * @return int
     */
    static public function getPendingAttendeesCount($formInput): int
    {
        $eventId = $formInput['event_id'];

        $pendingAttendeesCount = 0;

        $registrationFormId = EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($eventId, 'attendee');

        //get main attendees from the main waiting list
        $waitingListAttendees = WaitingListAttendee::where("event_id", $eventId)
            ->where("status", '=', '0')
            ->get();

        if ($waitingListAttendees) {

            //find sub attendees for main attendee
            //then count all
            foreach ($waitingListAttendees as $waitingListAttendee) {
                
                $order = BillingOrder::where('attendee_id', '=', $waitingListAttendee->attendee_id)
                    ->where('event_id', $eventId)
                    ->where('is_waitinglist', '=', '1')
                    ->where('is_archive', '=', '0')
                ->with(['order_attendees' => function ($query) use ($registrationFormId) {
                    return $query->where('registration_form_id', '=', $registrationFormId);
                }])->currentOrder()->orderBy('id', 'desc')->first();

                if (!empty($order)) {
                    //count sub attendees
                    $pendingAttendeesCount = $pendingAttendeesCount + count($order->order_attendees);
                }
            }

            return $pendingAttendeesCount;
        }

        //default
        return 0;
    }

    /**
     * returns count of users who
     * has accepted the invite
     * @param $eventId
     * @return int
     */
    static public function getCountOfAttendingUsers($formInput): int
    {
        return WaitingListAttendee::where('event_id', $formInput['event_id'])
            ->where('status','=','2')
            ->count();
    }

    /**
     * returns count of users that
     * has rejected the offer invite
     * @param $eventId
     * @return int
     */
    static public function getNotInterestedUsersCount($formInput): int
    {

        $eventId = $formInput['event_id'];

        $counter = 0;

        $resgistrationFormId = EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($eventId, 'attendee');

        $event_attendees = WaitingListAttendee::where('event_id', $eventId)->where('status','=','3')->get()->toArray();

        foreach ($event_attendees as $atten) {

            $order = BillingOrder::where('attendee_id', '=', $atten['attendee_id'])->where('event_id', '=', $eventId)->where('is_waitinglist', '=', '1')->where('is_archive', '=', '1')->with(['order_attendees' => function ($query) use ($resgistrationFormId) {
                return $query->where('registration_form_id', '=', $resgistrationFormId)->withTrashed();
            }])->currentOrder()->orderBy('id', 'desc')->first();

            if (!empty($order)&&count($order) > 0) {

                $order = $order->toArray();

                $counter = $counter + count($order['order_attendees']);
            }
        }

        return $counter;
    }

    /**
     * get order with id
     * @param $formInput
     * @return array
     */
    static public function getOrder($formInput = []): array
    {
        $orderId = (int) $formInput["order_id"];
        $language_id = (int) $formInput["language_id"];

        if(empty($language_id) || is_null($language_id)) {
            $order_detail = BillingOrder::find($orderId);
            $event_id = $order_detail->event_id;
            $event_detail = Events::find($event_id);
            $language_id = $event_detail->language_id;
        }

        $query = BillingOrder::where('id','=', $orderId)
            ->with(['order_attendee.info' => function ($query) use ($language_id){
            return $query->where('languages_id', '=', $language_id);
        }]);

        $result = $query->first();

        if ($result) return $result->toArray();

        return [];
    }

    /**
     * order attendees count
     * @param $orderId
     * @return int
     */
    static public function getOrderAttendeesCount($formInput = []): int
    {
        $orderId = $formInput["order_id"];
        $result = BillingOrderAttendee::where('order_id','=', $orderId)->get();

        if($result) return count($result->toArray());

        return 0;
    }

    /**
     * event attendees count
     * @param $eventId
     * @return int
     */
    static public function getEventAttendeesCount($formInput = []): int
    {
        $eventId = $formInput["event_id"];
        $event_attendees = EventAttendee::where('event_id','=', $eventId)->get();

        if ($event_attendees) return count($event_attendees->toArray());

        return 0;
    }

    /**
     * get total event tickets
     * @param $formInput
     * @return int|mixed
     */
    static public function getTotalEventTickets($formInput): int
    {
        $eventId = $formInput["event_id"];
        $event_setting = EventsiteSetting::where('event_id', '=', $eventId)->first();

        if ($event_setting) return $event_setting->toArray()['ticket_left'];

        return 0;
    }

    /**
     * @param array $formInput
     * @return bool
     */
    static public function canSendOfferLetter($formInput = []): bool
    {
        $attendee = $formInput["attendee"];
        $event_id = $formInput["event_id"];

        return EventWaitingListSetting::canSendOfferLetter($attendee, $event_id);
    }

    /**
     * @param $formInput
     * @return void
     * @throws \Exception
     */
    static public function emailOfferLetter($formInput) : void
    {
        $eventsite_labels = $formInput["event"]["labels"];
        $attendee = $formInput["attendee"];
        $order_id = $formInput["order_id"];
        $event_id = $formInput["event_id"];
        $language_id = $formInput["language_id"];

        $waitingListSetting = EventWaitingListSetting::where('event_id', $event_id)
            ->first()
            ->toArray();

        $event = Events::where('id', '=', $event_id)->with('info')
            ->first()
            ->toArray();

        $timezone = Timezone::where('id', '=', $event['timezone_id'])
            ->first()
            ->toArray();

        $event_tz = new \DateTimeZone($timezone['timezone']);
        $expiry_time =  date('Y-m-d H:i:s', strtotime($waitingListSetting['validity_duration'].' hour'));
        $expiry_time_obj = new \DateTime($expiry_time);
        $expiry_time_1 = $expiry_time_obj->setTimezone($event_tz);
        $expiry_time_string =  $expiry_time_1->format('Y-m-d H:i:s');

        $order_attendee = BillingOrderAttendee::where('order_id',$order_id)->where('attendee_id',$attendee['id'])->first();

        $registration_form_id = $order_attendee ? $order_attendee->registration_form_id : 0;

        $templateData = (object)TemplateRepository::getTemplateDataByAlias(['event_id' => $event_id, 'alias'=>'waiting_list_registration_invite' , 'registration_form_id' => $registration_form_id, 'language_id' => $language_id]);

        $template = $templateData->template;

        $subject_template = $templateData->subject;

        $template = getEmailTemplate($template, $event_id);

        $contents = stripslashes($template);

        $event_settings = EventSettingRepository::getSettings($event_id, true);

        //get header logo for email template
        if ($event_settings['header_logo']) {
            $src = url('/assets/event/branding/' . $event_settings['header_logo']);
        } else {
            $src = url("/_admin_assets/images/eventbuizz_logo.png");
        }

        $logo = '<img src="'.$src.'" width="150" />';
        $subject = str_replace("{event_name}", stripslashes($event['name']), $subject_template);
        $contents = str_replace("{event_logo}", $logo, $contents);
        $contents = str_replace("{event_name}", stripslashes($event['name']), $contents);
        $contents = str_replace("{attendee_name}", stripslashes($attendee['first_name']), $contents);
        $contents = str_replace("{first_name}", stripslashes($attendee['first_name']), $contents);
        $contents = str_replace("{last_name}", stripslashes($attendee['last_name']), $contents);
        $contents = str_replace("{email}", stripslashes($attendee['email']), $contents);

        if($event['registration_form_id'] == 1) {
            $contents = str_replace("{cancel_link}", '<a style="color:#324c59 !important;" href="'.\Config::get('app.reg_flow_url') . '/' . $event['url'].'/attendee/cancel-waitinglist-order/'.$order_id.'">'.$eventsite_labels['WAITING_LIST_CANCEL_LINK'].'</a>', $contents);
            $contents = str_replace("{accept_link}",'<a style="color:#324c59 !important;" href="'.\Config::get('app.reg_flow_url') . '/' . $event['url'].'/attendee/order-summary/'.$order_id.'/1">'.$eventsite_labels['WAITING_LIST_ACCEPT_LINK'].'</a>', $contents);
        } else {
            //accept link
            $orderCompletionUrl = cdn('/event/' . $event['url'] . '/detail/waitinglist/ordercompletion/' . $order_id);
            $waitingListAcceptLinkLabel = $eventsite_labels['WAITING_LIST_ACCEPT_LINK'];
            $orderCompletionUrl = "<a style=\"color:#324c59 !important;\" href=\"$orderCompletionUrl\">$waitingListAcceptLinkLabel</a>";

            //replace
            $contents = str_replace("{accept_link}", $orderCompletionUrl, $contents);

            //cancel link
            $orderCancellationUrl = cdn('/event/' . $event['url'] . '/detail/waitinglist/ordercancellation/' . $order_id);
            $waitingListCancelLinkLabel = $eventsite_labels['WAITING_LIST_CANCEL_LINK'];
            $orderCancellationUrl = "<a style=\"color:#324c59 !important;\" href=\"$orderCancellationUrl\">$waitingListCancelLinkLabel</a>";

            //replace
            $contents = str_replace("{cancel_link}",$orderCancellationUrl, $contents);
        }

        $contents = str_replace("{event_organizer_name}", $event['organizer_name'], $contents);
        $contents = str_replace("{time}", $expiry_time_string, $contents);
        $recipient_email = $attendee['email'];
        $event['event_id'] = $event_id;

        //send email
        $data = array();
        $data['subject'] = $subject;
        $data['content'] = $contents;
        $data['view'] = 'email.plain-text';
        $data['from_name'] =  "Eventbuizz";

        Mail::to($recipient_email)->send(new Email($data));
    }

    /**
     * @param array $formInput
     * @return void
     * @throws \Exception
     */
    static public function updateWaitingAttendeeStatus(array $formInput = []) : void
    {
        $status = $formInput["status"];
        $event_id = $formInput["event_id"];

        $order = self::getOrder($formInput);

        $order_attendee_id = WaitingListAttendee::where('event_id', '=', $event_id)
            ->where('attendee_id', '=', $order['attendee_id'])
            ->first();

        $order_attendee = WaitingListAttendee::find($order_attendee_id->id);

        if ($status == '1') {
            $order_attendee->status = '1';

            $event = Events::where('id', '=', $event_id)->with('info')
                ->first()
                ->toArray();

            $timezone = Timezone::where('id', '=', $event['timezone_id'])
                ->first()
                ->toArray();

            $event_tz = new \DateTimeZone($timezone['timezone']);
            $date_sent = date( "Y-m-d H:i:s");

            $expiry_time_obj = new \DateTime($date_sent);
            $expiry_time_1 = $expiry_time_obj->setTimezone($event_tz);
            $new_event_date_sent =  $expiry_time_1->format('Y-m-d H:i:s');
            $order_attendee->date_sent = $new_event_date_sent;
        }
        if($status == '2') {
            $order_attendee->status = '2';
        }
        $order_attendee->save();
    }

    public static function getOrderfromEventAttendeeIds($event_id, $attendee_id)
    {   
        $order = \App\Models\BillingOrder::where('event_id', $event_id)->whereHas('order_attendees', function ($query) use ($attendee_id) {
            $query->where('attendee_id', '=', $attendee_id);
        })->currentOrder()->where("status", "!=", "cancelled")->where("status", "!=", "rejected")->where("is_archive", "!=", 1)->first();
        
        return $order;
    }

    public static function getOrderForMainAttendee($event_id, $attendee_id)
    {   
        $order = \App\Models\BillingOrder::where('event_id', $event_id)->where('attendee_id', $attendee_id)->with(['order_attendees'])->currentOrder()->where("status", "!=", "cancelled")->where("status", "!=", "rejected")->where("is_archive", "!=", 1)->orderby("id", "DESC")->first();
        return $order;
    }

    /**
     * cancelOrder
     *
     * @param mixed $formInput
     * @return void
     */
    public static function cancelOrder($formInput)
    {
        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $formInput['order_id']);

        $old_order = $EBOrder->getModel();

        $new_order = $EBOrder->cloneOrder($old_order->id);

        request()->merge([
            "panel" => "admin", 'is_new_flow' => 1
        ]);
        
        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $new_order->id);
        
        $attendee_detail = $EBOrder->_getAttendeeByID($formInput['attendee_id'])->getModel();
        
        //Send email
        self::sendCancelOrderEmail($EBOrder, $formInput);

        if(count($EBOrder->getAllAttendees()) == 1 || ($EBOrder->getMainAttendee()->getModel()->id == $formInput['attendee_id'] && $formInput['cancelOption'] == "whole_order")) {

            //Create credit note
            if($EBOrder->getModel()->clone_of && isset($formInput['create_credit_note']) && $formInput['create_credit_note'] === 1) {
                        
                $cloned = $EBOrder->cloneOrder($EBOrder->getModel()->clone_of, 1);

                $EBOrder->setPreviousVersion(new \App\Eventbuizz\EBObject\EBOrder([], $cloned->id));

            }
            
            foreach($EBOrder->getAllAttendees() as $attendee) {
                AttendeeRepository::unAssign(['event_id' => $formInput['event_id'], 'attendee_id' => $attendee->getModel()->id], $attendee->getModel()->id, false);
            }

            $EBOrder->getModel()->status = 'cancelled';

            $EBOrder->getModel()->save();

            if(isset($formInput['send_credit_note']) && $formInput['send_credit_note'] === 1) {
                //Event trigger
                event(Event::OrderNewCreatedWithCreditNoteInstaller, $EBOrder);
            }

            self::updateReportingTableDataOnArchiveOrder($old_order->id);
            
        } else {
            
            //Create credit note
            if($EBOrder->getModel()->clone_of) {
                        
                $cloned = $EBOrder->cloneOrder($EBOrder->getModel()->clone_of, 1);

                $EBOrder->setPreviousVersion(new \App\Eventbuizz\EBObject\EBOrder([], $cloned->id));

            }

            $EBOrder->deleteAttendee($attendee_detail->id);
            
            AttendeeRepository::unAssign(['event_id' => $formInput['event_id'], 'attendee_id' => $attendee_detail->id], $attendee_detail->id, false);

            $EBOrder->updateOrder();
                
            $EBOrder->save();

            //Event trigger
            event(Event::OrderNewCreatedWithCreditNoteInstaller, $EBOrder);

            // Clean event revenue
            $event = \App\Models\Event::with(['eventsiteSettings'])->where('id', $formInput['event_id'])->first();
            EventsiteBillingOrderRepository::cleanReportingRevenue($event);
            
        }

        return [
            "success" => true,
            "message" => "successfully cancelled",
        ];
    }
    
    /**
     * sendCancelOrderEmail
     *
     * @param  mixed $EbOrder
     * @param  mixed $formInput
     * @return void
     */
    public static function sendCancelOrderEmail($EbOrder, $formInput)
    {
        //Attendee email 

        $attendee_detail = $EbOrder->_getAttendeeByID($formInput['attendee_id'])->getModel();

        $event_attendee = $EbOrder->_getAttendeeByID($formInput['attendee_id'])->getEventAttendeeModel();

        $attendee_detail->info = $EbOrder->_getAttendeeByID($formInput['attendee_id'])->getInfo();
      
        $attendee_detail->info = readArrayKey($attendee_detail, [], 'info');
      
        $attendee_type = $EbOrder->_getAttendeeByID($formInput['attendee_id'])->getAttendeeType();

        $registration_form_id = $EbOrder->_getAttendeeByID($formInput['attendee_id'])->getRegistrationFormIdByAttendeeType($attendee_type);

        $language_id = $EbOrder->getUtility()->getLangaugeId();

        $event_id = $EbOrder->getUtility()->getEventId();

        $event = $EbOrder->_getEvent();

        $event_info = $EbOrder->_getEventInfo();

        $templateData = (object)TemplateRepository::getTemplateDataByAlias(['event_id' => $event_id, 'registration_form_id' => $registration_form_id, 'language_id' => $language_id, 'alias'=>'attendee_cancel_registration']);

        $subject = str_replace("{event_name}", stripslashes($event['name']), $templateData->subject);

        $template = getEmailTemplate($templateData->template, $event['id']);

        $eventSetting = $EbOrder->_getEventSetting();

        if ($eventSetting['header_logo']) {
            $src = cdn('/assets/event/branding/'.$eventSetting['header_logo']);
        } else {
            $src = cdn("/_admin_assets/images/eventbuizz_logo.png");
        }

        $attendee_type = EventRepository::getEventAttendeeType(['event_id' => $event_id, 'language_id' => $language_id, 'id' => $attendee_type]);
        
        $logo = '<img src="'.$src.'" width="150" />';
        
        $template = getEmailTemplate($template, $event_id);

        if($language_id == 4 && $attendee_detail->info['gender'] == 'male'){
            $gender = 'geehrter';
        } elseif($language_id == 4 && $attendee_detail->info['gender'] == 'female') {
            $gender = 'geehrte';
        } else {
            $gender = $attendee_detail->info['gender'];
        }
     
        $template 		= str_replace("{event_name}",stripslashes($event->name), $template);
        $template 		= str_replace("{comment}",stripslashes($formInput['comment']), $template);
        $template       = str_replace("{event_logo}", $logo, $template);
        $template 		= str_replace("{attendee_name}",stripslashes($attendee_detail->first_name.' '.$attendee_detail->last_name), $template);
        $template 		= str_replace("{initial}",stripslashes($attendee_detail->info['initial']), $template);
        $template 		= str_replace("{first_name}",stripslashes($attendee_detail->first_name), $template);
        $template 		= str_replace("{last_name}",stripslashes($attendee_detail->last_name), $template);
        $template       = str_replace("{gender}", stripslashes($gender), $template);
        $template       = str_replace("{attendee_type}", stripslashes($attendee_type->name), $template);
        $template = str_replace("{event_organizer_name}", $event->organizer_name, $template);

        $data = array();
        $data['event_id'] = $event->id;
        $data['template'] = 'attendee_cancel_registration';
        $data['subject'] = $subject;
        $data['content'] = $template;
        $data['view'] = 'email.plain-text';
        $data['from_name'] = $event->organizer_name;
        \Mail::to($attendee_detail->email)->send(new Email($data));

        $support_email = $event_info['support_email'];
        
        //Organizer email
        if($support_email) {
            $subject = 'Cancelled order';
            $content = 'Dear '.$event->organizer_name.'<br/>Registration of '.$attendee_detail->first_name.' '.$attendee_detail->last_name.' has been cancelled with the following comment:<br/>'.$formInput['comment'];
            $data = array();
            $data['event_id'] = $event->id;
            $data['template'] = 'unsubscribe_link';
            $data['subject'] = $subject;
            $data['content'] = $content;
            $data['view'] = 'email.plain-text';
            $data['from_name'] = $event->organizer_name;
            \Mail::to($support_email)->send(new Email($data));
        }
        
    }
    
    /**
     * unsubscribeAttendee
     *
     * @param  mixed $formInput
     * @param  mixed $method
     * @return void
     */
    public function unsubscribeAttendee($formInput, $method) {
        $eventsite_setting = \App\Models\EventsiteSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', 0)->first();
        $event = $formInput['event'];
        $labels = $formInput['event']['labels'];
        $attendee = \App\Models\Attendee::where('email', $formInput['email'])->where('organizer_id', $event['organizer_id'])->first();
        if($attendee) {
            $attendee_id = $attendee->id;
            $event_attendee = \App\Models\EventAttendee::where('attendee_id', $attendee_id)->where('event_id', $event['id'])->first();
            if($method == "POST") {
                $support_email = $event['detail']['support_email'];
                $attendee_invite = \App\Models\AttendeeInvite::where('id', $formInput['id'])->where('event_id', $event['id'])->first();
                if($attendee_invite) {
                    $attendee_invite->is_attending = 1;
                    $attendee_invite->save();
                }
                if($event_attendee) {
                    $order = EventsiteBillingOrderRepository::getOrderfromEventAttendeeIds($event['id'], $attendee_id);
                    $order_id = $order ?  $order->id  : null;
                    request()->merge(['order_id' =>  $order_id, 'attendee_id' => $attendee_id]); 
                    $data = EventsiteBillingOrderRepository::cancelOrder(request()->all());
                } 
                if($support_email) {
                    $subject = 'Cancelled order';
                    $content = 'Dear '.$event['organizer_name'].'<br/>Registration of '.$attendee->first_name.' '.$attendee->last_name.' has been cancelled with the following comment:<br/>'.$formInput['comment'];
                    $data = array();
                    $data['event_id'] = $event['id'];
                    $data['template'] = 'unsubscribe_link';
                    $data['subject'] = $subject;
                    $data['content'] = $content;
                    $data['view'] = 'email.plain-text';
                    $data['from_name'] = $event['organizer_name'];
                    \Mail::to($support_email)->send(new Email($data));
                }
                return [
                    'success' => true,
                    'message' => $labels['EVENTSITE_UNSUBSCRIBE_THANK']
                ];
            } else {
                $errorMessage = null;
                $end_date = date('Y-m-d', strtotime($eventsite_setting->not_attending_expiry_date));
                $end_time = date('H:i', strtotime($eventsite_setting->not_attending_expiry_time));
                $expired_at = $end_date . ' ' . $end_time;
                $end_date_expiry = date('d-m-Y', strtotime($eventsite_setting->not_attending_expiry_date));
                if ($labels['NOT_ATTENDING_EXPIRY_ERROR']) {
                    $message = $labels['NOT_ATTENDING_EXPIRY_ERROR'];
                } else {
                    $message = "Last date for unsubscribe was {end_date}  {end_time}. Please contact the organizer.";
                }
                $message = str_replace('{end_date}', $end_date_expiry, $message);
                $message = str_replace("{end_time}", $end_time, $message);
                $current_date = date('Y-m-d H:i');
                if (!in_array($eventsite_setting->not_attending_expiry_date, ["0000-00-00 00:00:00", "0000-00-00"]) && $eventsite_setting->not_attending_expiry_date) {
                    if ($event_attendee) {
                        if (strtotime($expired_at) < strtotime($current_date)) {
                            $errorMessage = $message;
                        }
                        if(!$errorMessage) {
                            if ($labels['EVENTSITE_UNSUBSCRIBE_ERROR_AFTER_REGISTRATION']) {
                                $errorMessage = $labels['EVENTSITE_UNSUBSCRIBE_ERROR_AFTER_REGISTRATION'];
                            } else {
                                $errorMessage = 'This option is no longer available - please contact organizer';
                            }
                        }
                    }
                }
                if(!$errorMessage) {
                    $message = str_replace('{end_date}', $end_date_expiry, $message);
                    $message = str_replace("{end_time}", $end_time, $message);
                    $current_date = date('Y-m-d H:i');
                    if ($expired_at != "0000-00-00 00:00") {
                        if (strtotime($expired_at) < strtotime($current_date)) {
                            $errorMessage = $message;
                        }
                    }
                }
                return [
                    'success' => $errorMessage ? false : true,
                    'message' => $errorMessage
                ];
            }
        } else {
            return [
                'success' => true,
            ];
        }
    }

    /**
     * isInvoiceUpdate
     *
     * @param  mixed $settings
     * @return void
     */
    public function isInvoiceUpdate($settings) {

        if( $settings->invoice_modification_end_date != "0000-00-00 00:00:00" && $settings->invoice_modification_end_date != "0000-00-00" ) {
            $start_date = date('Y-m-d', strtotime($settings->invoice_modification_end_date));
            $end_date = date('H:i:s', strtotime($settings->invoice_modification_end_time));
            $combinedDT = date('Y-m-d H:i:s', strtotime("$start_date $end_date"));
            $current_date = \Carbon\Carbon::now();
            $end_date = \Carbon\Carbon::parse($combinedDT);
            $are_different = $current_date->gt($end_date);
            if($are_different){
                return false;
            }
        }

        return true;
    }
        
    /**
     * getOrderPersons
     *
     * @param  mixed $order_id
     * @param  mixed $attendees
     * @return void
     */
    public static function getOrderPersons($order_id, $attendees) {
        return \App\Models\EventHotelPerson::where('order_id', $order_id)->whereIn('attendee_id', $attendees)->count();
    }

    /**
     * getSaleTypes
     *
     * @param  mixed $organizer_id
     * @return void
    */
    public static function getSaleTypes($organizer_id) {
        
        $types = array();

        $results = \App\Models\SaleType::where('organizer_id', $organizer_id)->orderBy('sort_order', 'ASC')->get();

        foreach($results as $result) {
            $name = $result->name;
            if(trim($result->code) != '') $name .= ' - '.$result->code;
            $types[] = array(
                "id" => $result->id,
                "name" => $name,
            );
        }

        return $types;

    }

}
