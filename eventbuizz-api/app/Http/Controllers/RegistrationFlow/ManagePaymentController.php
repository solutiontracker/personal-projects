<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\GeneralRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RegistrationFlow\Requests\PaymentRequest;
use App\Http\Controllers\RegistrationFlow\Requests\PoNumberRequest;
use Illuminate\Http\Request;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use Stripe;
use QuickPay\QuickPay;
use \Illuminate\Support\Str;
use GuzzleHttp\Client as GuzzleClient;

class ManagePaymentController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    protected $generalRepository;

    private $eventsiteBillingOrderRepository;
    
    /**
     * @param AttendeeRepository $attendeeRepository
     * @param  mixed $eventsiteBillingOrderRepository
     * @param GeneralRepository $generalRepository
     */
    public function __construct(AttendeeRepository $attendeeRepository, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository, GeneralRepository $generalRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->generalRepository = $generalRepository;
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
    }
        
    /**
     * index
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function index(PaymentRequest $request, $event_url, $order_id = null)
    {
        $event = $request->event;
        
        $labels = $request->event['labels'];
        
        try {
            if ($request->isMethod('POST') && $order_id) {

                request()->merge([ "panel" => $request->provider ? $request->provider : "attendee", "draft" => true]);
                
                //Get order
                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                $attendee = $EBOrder->getMainAttendee()->getModel();

                $request_data = $this->makeRequestData($request->all());
                
                request()->merge([ "action" => "update-attendee-billing", "draft" => true, "attendee" => $request_data, "attendee_id" => $attendee->id]);

                $EBOrder->updateOrder();
                
                $EBOrder->save();

                return response()->json([
                    'success' => true,
                    'data' => array(
                        "order" => $EBOrder->getModel(),
                    ),
                ], $this->successStatus);
            } else {

                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                $event_id = $EBOrder->getOrderEventId();

                $attendee = $EBOrder->getMainAttendee()->getModel();
                
                $language_id = $EBOrder->getUtility()->getLangaugeId();

                $payment_setting = $EBOrder->_getPaymentSetting();

                $sections = EventSiteSettingRepository::getAllSections(["event_id" => $request->event_id, "language_id" => $request->language_id, "status" => 1, 'registration_form_id' => 0]);

                $metadata = $this->generalRepository->getMetadata('countries,country_codes', $request->event_id);

                $attendee_billing = $this->attendeeRepository->getOrderAttendeeBilling($request->event_id, $order_id, $attendee->id); 
                    
                //Default payment method
                if(!$attendee_billing['company_type']) {
                    foreach($sections as $section) {
                        foreach($section['fields'] as $field) {
                            if($field['field_alias'] == 'company_invoice_payment') {
                                $attendee_billing['company_type'] = "invoice";
                                break;
                            }
                            if($field['field_alias'] == 'credit_card_payment' && !in_array($request->provider, ['sale'])) {
                                $attendee_billing['company_type'] = 'private';
                                break;
                            }
                            if($field['field_alias'] == 'company_public_payment') {
                                $attendee_billing['company_type'] = 'public';
                                break;
                            }
                        }
                    }
                }

                if(!$attendee_billing['member']) {
                    $attendee_billing['company_country'] = $event['country_id'];
                }

                if(!$attendee_billing['member_number']) {
                    $attendee_billing['member'] = 0;
                }

                $billing_currency = $payment_setting['eventsite_currency'];

                // Order detail summary
                $order = $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("json", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_id,1, 1, true, false, 0, 0, true);

                return response()->json([
                    'success' => true,
                    'data' => array(
                        "sections" => $sections,
                        "metadata" => $metadata,
                        "attendee_billing" => $attendee_billing,
                        "order" => $order
                    ),
                ], $this->successStatus);
            }
        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }

    /**
     * @param mixed $request_data
     *
     * @return [type]
     */
    public function makeRequestData($request_data)
    {
        $fields = array('member_number', 'company_type', 'company_registration_number', 'bruger_id', 'ean', 'contact_person_name', 'contact_person_email', 'contact_person_mobile_number', 'company_street', 'company_house_number', 'company_post_code', 'company_city', 'company_country', 'poNumber', 'company_state', 'company_street_2', 'company_invoice_payer_company_name', 'company_invoice_payer_street_house_number', 'company_invoice_payer_post_code', 'company_invoice_payer_city', 'company_invoice_payer_country');

        unset($request_data['event']);
        
        foreach ($fields as $field)
        {
            if(array_key_exists($field, $request_data))
            {
                $request_data['billing_'.$field] = ($request_data[$field] && $request_data[$field] !== "null" ? $request_data[$field] : "");
            }
        }

        $request_data['billing_membership'] = $request_data['member'];

        return $request_data;
    }
    
    /**
     * onlinePayment
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @param  mixed $type
     * @return void
     */
    public function onlinePayment(Request $request, $event_url, $order_id = null, $type = "stripe")
    {
        //Get order
        try {
            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            // Order detail summary
            $order_detail = $EBOrder->getInvoiceSummary();

            //labels
            $labels = $request->event['labels'];

            $event_id = $EBOrder->getOrderEventId();

            $language_id = $EBOrder->getUtility()->getLangaugeId();

            $payment_setting = $EBOrder->_getPaymentSetting();

            $billing_currency = $payment_setting['eventsite_currency'];
            
            $order = $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("json", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_detail['order']['id'],1, 1, true, false, 0, 0, true);

            return response()->json([
                'success' => true,
                'data' => array(
                    "order" => $order,
                ),
            ], $this->successStatus);
        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }

    /**
     * createStripePaymentIntent
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function createStripePaymentIntent(Request $request, $event_url, $order_id = null)
    {
        $event = $request->event;

        $labels = $request->event['labels'];

        try {

            Stripe\Stripe::setApiKey($event['payment_setting']['stripe_secret_key']);

            $intent = Stripe\PaymentIntent::create([
                'setup_future_usage' => 'off_session',
                'amount' => round($request->amount * 100),
                'currency' => $request->currency,
                "payment_method_types" => ["card"],
            ]);

            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            if($EBOrder->verifyAttendees()) {

                //Save transaction id
                $EBOrder->getModel()->transaction_id = $intent->id;
                $EBOrder->getModel()->status = "awaiting_payment";
                $EBOrder->getModel()->save();
    
                return response()->json([
                    'success' => true,
                    'data' => array(
                        "amount" => $intent->amount,
                        "payment_id" => $intent->id,
                        "client_secret" => $intent->client_secret,
                    ),
                ], $this->successStatus);

            } else {

                return response()->json([
                    'success' => false,
                    'errors' => array(
                        "order" => $labels['REGISTRATION_FORM_ORDER_ALREADY_PLACED']
                    ),
                ], $this->successStatus);

            }

        } catch (\Throwable $e){
            return response()->json([
                'success' => false,
                'message' => "Server error",
            ], $this->successStatus);
        }
        
    }

    /**
     * createNetsPaymentIntent
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function createNetsPaymentIntent(Request $request, $event_url, $order_id = null)
    {
        try {

            $event = $request->event;

            $labels = $request->event['labels'];

            $reference = $event['id'] . '-' . $order_id;

            $amount = round($request->amount * 100);

            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            $attendee = $EBOrder->getMainAttendee()->getModel();

            $payload = [
                "order" => [
                    "items" => [
                        [
                            "reference" => $reference,
                            "name" => $event['name'],
                            "quantity" => 1,
                            "unit" => "pcs",
                            "unitPrice" => $amount,
                            "taxRate" => 0,
                            "taxAmount" => 0,
                            "grossTotalAmount" => $amount,
                            "netTotalAmount"=> $amount
                        ]
                    ],
                    "amount" => $amount,
                    "currency" => $request->currency,
                    "reference" => $reference
                ],
                "checkout" => [
                    "charge" => true,
                    "url" => config('app.url')."/registration/event/".$event."/registration/nets-checkout/".$order_id,
                    "termsUrl" => config('app.url')."/registration/event/".$event."/registration/nets-checkout/".$order_id,
                    "IntegrationType" => "EmbeddedCheckout",
                    "merchantHandlesConsumerData" => true,
                    "consumer" => [
                        "reference" => $attendee->id,
                        "email" => $attendee->email,
                        "privatePerson" => [
                            "firstName" => $attendee->first_name,
                            "lastName" => $attendee->last_name
                        ],
                    ],
                ],
                "notifications" => [
                    "webhooks" => [
                        [
                            "eventName" => "payment.checkout.completed",
                            "url" => config('app.url')."/registration/webhook/nets-ipn",
                            "authorization" => "a2lAZXZlbnRidWl6ei5jb206KkV2ZW50QDIwMjIq"
                        ]
                    ]
                ],
                "merchantNumber" => 100034103
            ];

            try {
                $endpoint = \App::environment('production') && !myIp($_SERVER['HTTP_X_FORWARDED_FOR'])  ? 'https://api.dibspayment.eu/v1/payments' : 'https://test.api.dibspayment.eu/v1/payments';
                $ch = curl_init($endpoint);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'Authorization: '. $event['payment_setting']['nets_secret_key'])
                );
                $result = curl_exec($ch);
                $result = json_decode($result);
                
                if(isset($result->paymentId)){

                    if($EBOrder->verifyAttendees()) {

                        //Save transaction id
                        $EBOrder->getModel()->transaction_id = $result->paymentId;
                        $EBOrder->getModel()->status = "awaiting_payment";
                        $EBOrder->getModel()->save();

                    } else {

                        return response()->json([
                            'success' => false,
                            'errors' => array(
                                "order" => $labels['REGISTRATION_FORM_ORDER_ALREADY_PLACED']
                            ),
                        ], $this->successStatus);
        
                    }

                    return response()->json([
                        'success' => true,
                        'link' => route('registration-flow-order-nets-checkout', [$event_url, $order_id]),
                    ], $this->successStatus);
                }

                return response()->json([
                    'success' => false,
                    'message' => "Error Nets gateway: ". $result->message,
                ], $this->successStatus);

            } catch (\Throwable $e){
                return response()->json([
                    'success' => false,
                    'message' => "Server error",
                ], $this->successStatus);
            }
        } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        }
    }

    /**
     * createQuickpayPaymentIntent
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function createQuickpayPaymentIntent(Request $request, $event_url, $order_id = null)
    {
        try {

            $event = $request->event;

            $labels = $request->event['labels'];

            $reference = $event['id'] . '-' . $order_id. '-' .Str::random(6);

            $amount = round($request->amount * 100);

            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            try {
                //Initialize client
                $client = new QuickPay(":" . $event['payment_setting']['qp_secret_key']);
            
                //Create payment
                $payment = $client->request->post('/payments', [
                    'order_id' => $reference,
                    'currency' => $request->currency,
                ]);
            
                $status = $payment->httpStatus();
            
                $paymentObject = $payment->asObject();

                //Determine if payment was created successfully
                if ($status === 201) {
            
                    if($EBOrder->verifyAttendees()) {

                        //Save transaction id
                        $EBOrder->getModel()->transaction_id = $paymentObject->id;
                        $EBOrder->getModel()->status = "awaiting_payment";
                        $EBOrder->getModel()->save();

                        //Construct url to create payment link
                        $endpoint = sprintf("/payments/%s/link", $paymentObject->id);
                
                        //Issue a put request to create payment link
                        $link = $client->request->put($endpoint, [
                            'amount' => $amount,
                            'auto_capture' => $event['payment_setting']['qp_auto_capture'] ? true : false,
                            'payment_methods' => 'creditcard',
                            'continue_url' => config('app.reg_flow_url').'/'.$event['url'].'/attendee/registration-success/'.$EBOrder->getModel()->id,
                            'cancel_url' => config('app.reg_flow_url').'/'.$event['url'].'/attendee/order-summary/'.$EBOrder->getModel()->id,
                            'callback_url' => config('app.url').'/registration/webhook/quickpay-ipn'
                        ]);
                
                        //Determine if payment link was created succesfully
                        if ($link->httpStatus() === 200) {
                            return response()->json([
                                'success' => true,
                                'link' => $link->asObject()->url,
                            ], $this->successStatus);
                        }

                        return response()->json([
                            'success' => false,
                            'message' => $paymentObject->message,
                        ], $this->successStatus);

                    } else {

                        return response()->json([
                            'success' => false,
                            'errors' => array(
                                "order" => $labels['REGISTRATION_FORM_ORDER_ALREADY_PLACED']
                            ),
                        ], $this->successStatus);
        
                    }

                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $paymentObject->message,
                    ], $this->successStatus);
                }
            } catch (\Exception $e) {
                 return response()->json([
                     'success' => false,
                     'message' => "Server error",
                 ], $this->successStatus);
            }
        } catch (\Exception $e) {
             return \Response::json([
                 "message" => "Server error",
                 "success" => false
             ]);
        }
    }

    /**
     * createBamboraPaymentIntent
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function createBamboraPaymentIntent(Request $request, $event_url, $order_id = null)
    {

        $event = $request->event;

        $labels = $request->event['labels'];

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        if($EBOrder->verifyAttendees()) {

            return response()->json([
                'success' => true,
                'link' => route('registration-flow-order-bambora-checkout', [$event_url, $order_id]),
            ], $this->successStatus);
            
        } else {

            return response()->json([
                'success' => false,
                'errors' => array(
                    "order" => $labels['REGISTRATION_FORM_ORDER_ALREADY_PLACED']
                ),
            ], $this->successStatus);

        }
    }

    /**
     * netsCheckout
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function netsCheckout(Request $request, $event_url, $order_id = null) {

        $event = $request->event;

        $labels = $request->event['labels'];

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        if($EBOrder->verifyAttendees()) {

            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            $order = $EBOrder->getModel();

            return \View::make('registration_flow.payments.nets.index', compact('event', 'order'));

        } else {

            return response()->json([
                'success' => false,
                'errors' => array(
                    "order" => $labels['REGISTRATION_FORM_ORDER_ALREADY_PLACED']
                ),
            ], $this->successStatus);

        }
    }

    /**
     * bamboraCheckout
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function bamboraCheckout(Request $request, $event_url, $order_id = null) {
        $event = $request->event;
        $labels = $request->event['labels'];
        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);
        if($EBOrder->verifyAttendees()) {
            $order = $EBOrder->getModel();
            $amount = round($order->grand_total * 100);
            $attendee = $EBOrder->getMainAttendee()->getModel();
            $currency_array = getCurrencyArray();
            $currency = $currency_array[$EBOrder->getModelAttribute('eventsite_currency')];
            $merchantid = $EBOrder->getPaymentSettingAttribute('eventsite_merchant_id');
            $bambora_secret_key = $EBOrder->getPaymentSettingAttribute('bambora_secret_key');
            $success_url = config('app.reg_flow_url').'/'.$event['url'].'/attendee/registration-success/'.$EBOrder->getModel()->id;
            $cancelUrl = config('app.reg_flow_url').'/'.$event['url'].'/attendee/order-summary/'.$EBOrder->getModel()->id;
            $callbackUrl = config('app.url').'/registration/webhook/bambora-ipn/'.$event['id'].'/'.$EBOrder->getModel()->id;
            $consumerEmail = $attendee->email;
            $firstname = $attendee->first_name;
            $lastname = $attendee->last_name;
            $params = array(
                "merchantnumber" => $merchantid,
                "amount" => $amount,
                "currency" => $currency,
                "windowstate" => 2,
                "paymentcollection" => 1,
                "instantcallback" => 1,
                "iframeheight" => 600,
                "iframewidth" => 1200,
                "accepturl" => $success_url,
                "cancelurl" => $cancelUrl,
                "callbackurl" => $callbackUrl,
                "instantcapture" => 1,
                "group" => $event['id'],
                "description" => $firstname.' '.$lastname.' '.$consumerEmail,

            );
            return \View::make('registration_flow.payments.bambora.index', compact('event', 'order', 'params', 'merchant_id', 'currency', 'amount', 'merchantid', 'success_url', 'cancelUrl', 'callbackUrl', 'bambora_secret_key'));
        } else {
            return response()->json([
                'success' => false,
                'errors' => array(
                    "order" => $labels['REGISTRATION_FORM_ORDER_ALREADY_PLACED']
                ),
            ], $this->successStatus);
        }
    }

    /**
     * validateCvr
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @return void
     */
    public function validateCvr(Request $request, $event_url)
    {
        $validate = EventSiteSettingRepository::validateCvr($request->cvr);

        if($validate) {
            return \Response::json([
                "success" => true
            ]);
        } else {
            return \Response::json([
                "message" => 'Invalid CVR',
                "success" => false
            ]);
        }
    }

    /**
     * validateEan
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @return void
     */
    public function validateEan(Request $request, $event_url)
    {
        $validate = EventSiteSettingRepository::validateEan($request->ean);

        return \Response::json($validate);
    }

    /**
     * validatePoNumber
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @return void
     */
    public function validatePoNumber(PoNumberRequest $request, $event_url)
    {
        return \Response::json([
            "success" => true
        ]);
    }

    /**
     * createConvergePaymentIntent
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function createConvergePaymentIntent(Request $request, $event_url, $order_id = null)
    {

        $event = $request->event;

        $labels = $request->event['labels'];

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        if($EBOrder->verifyAttendees()) {

            return response()->json([
                'success' => true,
                'link' => route('registration-flow-order-converge-checkout', [$event_url, $order_id]),
            ], $this->successStatus);

        } else {

            return response()->json([
                'success' => false,
                'errors' => array(
                    "order" => $labels['REGISTRATION_FORM_ORDER_ALREADY_PLACED']
                ),
            ], $this->successStatus);

        }
    }

    /**
     * convergeCheckout
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @return void
     */
    public function convergeCheckout(Request $request, $event_url, $order_id = null) {

        try {

            $event = $request->event;
    
            $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

            $order = $EBOrder->getModel();

            $amount = $order->grand_total;

            $attendee = $EBOrder->getMainAttendee()->getModel();

            $currency_array = getCurrencyArray();

            $currency = $currency_array[$EBOrder->getModelAttribute('eventsite_currency')];

            $converge_public_key = $EBOrder->getPaymentSettingAttribute('converge_public_key');

            $converge_secret_key = $EBOrder->getPaymentSettingAttribute('converge_secret_key');

            $converge_merchant_alias = $EBOrder->getPaymentSettingAttribute('converge_merchant_alias');

            $success_url = config('app.reg_flow_url').'/'.$event['url'].'/attendee/registration-success/'.$EBOrder->getModel()->id;

            $cancelUrl = config('app.reg_flow_url').'/'.$event['url'].'/attendee/order-summary/'.$EBOrder->getModel()->id;

            $callbackUrl = config('app.url').'/registration/webhook/bambora-ipn/'.$event['id'].'/'.$EBOrder->getModel()->id;

            $consumerEmail = $attendee->email;

            $firstname = $attendee->first_name;

            $lastname = $attendee->last_name;

            $api_key = 'Basic '.base64_encode($converge_merchant_alias.':'.$converge_secret_key);

            $endpoint = \App::environment('production') && !myIp($_SERVER['HTTP_X_FORWARDED_FOR']) ? 'https://api.eu.convergepay.com' : 'https://uat.api.converge.eu.elavonaws.com';

            $client = new \GuzzleHttp\Client(['base_uri' => $endpoint]);

            if(request()->isMethod('POST')) {
                
                $input = @file_get_contents("php://input");

                if($input) {

                    list($convergePaymentToken, $hostedCard, $sessionId) = explode('&', $input);
                    
                    $hostedCard = explode("=", $hostedCard);

                    $session = explode("=", $sessionId);

                    $response = $client->request('POST', '/transactions', [
                        'body' => json_encode([
                            "type" => "sale",
                            "source" => "hppIframeLightbox",
                            "total" => [
                                "amount" => $amount,
                                "currencyCode" => $currency
                            ],
                            "hostedCard" => $hostedCard[1],
                            "paymentSession" => $session[1],
                        ]),
                        'headers' => [
                            'Accept'     => 'application/json',
                            'Content-Type'     => 'application/json',
                            'Authorization'     => $api_key,
                        ]
                    ]);
        
                    $content = $response->getBody()->getContents();
        
                    $content = json_decode($content, true);

                    if($content['state'] == "authorized") {
                        
                        request()->merge([
                            "panel" => "attendee",
                            "organizer_id" => $event['organizer_id'],
                            "language_id" => $event['language_id'],
                            "event_id" => $event['id'],
                            "draft" => false,
                            'is_new_flow' => 1
                        ]);
    
                        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order->id);
    
                        $EBOrder->_setStateInProgress();
    
                        $EBOrder->setIsEdit();
    
                        $EBOrder->setOrderPaymentReceived(1);
    
                        $EBOrder->setOrderStatus("completed");
    
                        $EBOrder->setPaymentResponse($content);
    
                        $EBOrder->setTransactionID($content['id']);
    
                        $EBOrder->save();

                    }

                    //Push response into redis for page redirection on registration form
                    $socket_channel_name = 'registration-order-' . $order_id;

                    $data = [
                        'event' => $socket_channel_name,
                        'data' => [
                            'info' => json_encode([
                                'order_id' => $order_id,
                                'provider' => 'converge',
                                'payment' => $content['state'] == "authorized" ? 'accepted' : 'decline'
                            ]),
                        ],
                    ];

                    \Redis::publish('event-buizz', json_encode($data));
                }

            } else {
    
                $response = $client->request('POST', '/orders', [
                    'body' => json_encode([
                        "total" => [
                            "amount" => $amount,
                            "currencyCode" => $currency
                        ],
                        "description" => "Payment for Order# ".$order->order_number,
                        "items" => [
                            [
                                "total" => [
                                    "amount" => $amount,
                                    "currencyCode" => $currency
                                ],
                                "description" => "Payment for Order# ".$order->order_number,
                            ]
                        ]
                    ]),
                    'headers' => [
                        'Accept'     => 'application/json',
                        'Content-Type'     => 'application/json',
                        'Authorization'     => $api_key,
                    ]
                ]);
    
                $content = $response->getBody()->getContents();
    
                $content = json_decode($content, true);
    
                $order_ref = $content['href'];
                
                if($order_ref) {
                    $response = $client->request('POST', '/payment-sessions', [
                        'body' => json_encode([
                            "order" => $order_ref,
                            "originUrl" => "http://*.eventbuizz.com",
                            "hppType" => "lightbox",
                        ]),
                        'headers' => [
                            'Accept'     => 'application/json',
                            'Content-Type'     => 'application/json',
                            'Authorization'     => $api_key,
                        ]
                    ]);
                }
    
                $content = $response->getBody()->getContents();
    
                $content = json_decode($content, true);
    
                $session = $content['id'];
    
                $hpp_endpoint = \App::environment('production') && !myIp($_SERVER['HTTP_X_FORWARDED_FOR']) ? 'https://hpp.eu.convergepay.com' : 'https://uat.hpp.converge.eu.elavonaws.com';
    
                $event_settings = $EBOrder->_getEventSetting();

                return \View::make('registration_flow.payments.converge.index', compact('session', 'hpp_endpoint', 'order_id', 'event_url', 'event_settings'));

            }

        } catch (RequestException $e) {
            echo $e->getMessage();
        }
        
    }
}
