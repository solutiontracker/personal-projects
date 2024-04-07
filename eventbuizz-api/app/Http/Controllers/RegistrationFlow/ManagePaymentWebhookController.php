<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use App\Eventbuizz\Repositories\EventRepository;

class ManagePaymentWebhookController extends Controller
{
    public $successStatus = 200;

    /**
     * stripe_ipn
     *
     * @param  mixed $request
     * @return void
     */
    public function stripe_ipn(Request $request)
    { 
        $input = @file_get_contents("php://input");

        $stripe = json_decode($input);

        switch ($stripe->type) {
            case 'charge.succeeded':

                $order = \App\Models\BillingOrder::where('transaction_id', $stripe->data->object->payment_intent)->where('status', '!=', 'completed')->first();

                if($order) {
                    
                    $event = EventRepository::getEventDetail((["event_id" => $order->event_id]));

                    request()->merge([
                        "panel" => "attendee",
                        "organizer_id" => $event->organizer_id,
                        "language_id" => $event->language_id,
                        "event_id" => $event->id,
                        "draft" => false,
                        'is_new_flow' => 1
                    ]);

                    $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order->id);

                    $EBOrder->_setStateInProgress();

                    $EBOrder->setIsEdit();

                    $EBOrder->setOrderPaymentReceived(1);

                    $EBOrder->setOrderStatus("completed");

                    $EBOrder->setPaymentResponse($input);

                    $EBOrder->save();
                }

                return response()->json([
                    'success' => true
                ], $this->successStatus);

            default:
                $order = \App\Models\BillingOrder::where('transaction_id', $stripe->data->object->payment_intent)->first();
                
                if($order) {
                    $order->payment_response = $input;
                    $order->save();
                }

                return response()->json([
                    'success' => true
                ], $this->successStatus);
        }
    }

    /**
     * quickpay_ipn
     *
     * @param  mixed $request
     * @return void
     */
    public function quickpay_ipn(Request $request)
    { 
        $input = @file_get_contents("php://input");

        $payload = json_decode($input);

        list($event_id, $order_id) = explode('-', $payload->order_id);

        $payment_setting = EventSiteSettingRepository::getPaymentSetting(['event_id' => $event_id]);

        switch ($payload->accepted) {
            case '1':

                $order = \App\Models\BillingOrder::where('id', $order_id)->where('event_id', $event_id)->where('status', '!=', 'completed')->first();

                if($order) {

                    $event = EventRepository::getEventDetail((["event_id" => $order->event_id]));

                    request()->merge([
                        "panel" => "attendee",
                        "organizer_id" => $event->organizer_id,
                        "language_id" => $event->language_id,
                        "event_id" => $event->id,
                        "draft" => false,
                        'is_new_flow' => 1
                    ]);

                    $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order->id);

                    $EBOrder->_setStateInProgress();

                    $EBOrder->setIsEdit();

                    $EBOrder->setOrderPaymentReceived(1);

                    $EBOrder->setOrderStatus("completed");

                    $EBOrder->setPaymentResponse($input);

                    $EBOrder->setTransactionID($payload->id);

                    $EBOrder->save();
                }
                
                //Push response into redis for page redirection on registration form
                $socket_channel_name = 'registration-order-' . $order_id;
                $data = [
                    'event' => $socket_channel_name,
                    'data' => [
                        'info' => json_encode([
                            'order_id' => $order_id,
                            'provider' => 'quickpay',
                            'payment' => 'accepted'
                        ]),
                    ],
                ];

                \Redis::publish('event-buizz', json_encode($data));

                return response()->json([
                    'success' => true
                ], $this->successStatus);

            default:
                $order = \App\Models\BillingOrder::where('id', $order_id)->where('event_id', $event_id)->first();

                if($order) {
                    $order->payment_response = $input;
                    $order->save();
                }

                return response()->json([
                    'success' => true
                ], $this->successStatus);
        }
    }

    public function sign($base, $private_key) {
        return hash_hmac("sha256", $base, $private_key);
    }

    /**
     * nets_ipn
     *
     * @param  mixed $request
     * @return void
     */
    public function nets_ipn(Request $request)
    { 
        $input = @file_get_contents("php://input");

        $payload = json_decode($input);

        list($event_id, $order_id) = explode('-', $payload->data->order->reference);

        switch ($payload->event) {
            case 'payment.checkout.completed':

                $order = \App\Models\BillingOrder::where('id', $order_id)->where('event_id', $event_id)->where('status', '!=', 'completed')->first();

                if($order) {

                    $event = EventRepository::getEventDetail((["event_id" => $order->event_id]));

                    request()->merge([
                        "panel" => "attendee",
                        "organizer_id" => $event->organizer_id,
                        "language_id" => $event->language_id,
                        "event_id" => $event->id,
                        "draft" => false,
                        'is_new_flow' => 1
                    ]);

                    $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order->id);
                    
                    $EBOrder->_setStateInProgress();

                    $EBOrder->setIsEdit();

                    $EBOrder->setOrderPaymentReceived(1);

                    $EBOrder->setOrderStatus("completed");

                    $EBOrder->setPaymentResponse($input);

                    $EBOrder->setTransactionID($payload->data->paymentId);

                    $EBOrder->save();
                }
                
                //Push response into redis for page redirection on registration form
                $socket_channel_name = 'registration-order-' . $order_id;

                $data = [
                    'event' => $socket_channel_name,
                    'data' => [
                        'info' => json_encode([
                            'order_id' => $order_id,
                            'provider' => 'nets',
                            'payment' => 'accepted'
                        ]),
                    ],
                ];

                \Redis::publish('event-buizz', json_encode($data));

                return response()->json([
                    'success' => true
                ], $this->successStatus);

            default:
                $order = \App\Models\BillingOrder::where('id', $order_id)->where('event_id', $event_id)->first();

                if($order) {
                    $order->payment_response = $input;
                    $order->save();
                }

                return response()->json([
                    'success' => true
                ], $this->successStatus);
        }
    }

    /**
     * bambora_ipn
     *
     * @param  mixed $request
     * @return void
     */
    public function bambora_ipn(Request $request, $event_id, $order_id)
    {
        $params = $_GET;
        
        $var = "";
  
        foreach ($params as $key => $value)
        {
            if($key != "hash")
            {
                $var .= $value;
            }
        }
        
        $order = \App\Models\BillingOrder::where('id', $order_id)->where('event_id', $event_id)->where('status', '!=', 'completed')->first();

        if($order) {

            $event = EventRepository::getEventDetail((["event_id" => $order->event_id]));

            request()->merge([
                "panel" => "attendee",
                "organizer_id" => $event->organizer_id,
                "language_id" => $event->language_id,
                "event_id" => $event->id,
                "draft" => false,
                'is_new_flow' => 1
            ]);

            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order->id);

            $genstamp = md5($var . $EBOrder->getPaymentSettingAttribute('bambora_secret_key'));
        
            if($genstamp == $_GET["hash"])
            {
                $EBOrder->_setStateInProgress();

                $EBOrder->setIsEdit();

                $EBOrder->setOrderPaymentReceived(1);

                $EBOrder->setOrderStatus("completed");

                $EBOrder->setPaymentResponse($params);

                $EBOrder->setTransactionID($params['txnid']);

                $EBOrder->save();

                return response()->json([
                    'success' => true
                ], $this->successStatus);
            }

        }
    }
      
}
