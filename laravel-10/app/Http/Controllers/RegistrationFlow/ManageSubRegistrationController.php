<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\SubRegistrationRepository;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

class ManageSubRegistrationController extends Controller
{
    public $successStatus = 200;

    protected $subRegistrationRepository;

    private $eventsiteBillingOrderRepository;

    /**
     * @param SubRegistrationRepository $subRegistrationRepository
     * @param  mixed $eventsiteBillingOrderRepository
     */
    public function __construct(SubRegistrationRepository $subRegistrationRepository, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        $this->subRegistrationRepository = $subRegistrationRepository;
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
    }
    
    /**
     * index
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $order_id
     * @param  mixed $attendee_id
     * @return void
     */
    public function index(Request $request, $event_url, $order_id, $attendee_id)
    {
        $request->merge(['order_id' => $order_id, 'attendee_id' => $attendee_id]);
        
        //try {
            if ($request->isMethod('POST')) { 

                $validate = $this->validateRequest($request->all());

                if($validate['status']) {

                    $this->subRegistrationRepository->saveOrderQuestionAnswers($request->all());

                    $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                    //Save request logs
                    //$EBOrder->saveOrderLogs($request->only(['questions', 'event_id', 'attendee_id', 'order_id']));

                    return response()->json([
                        'success' => true,
                    ], $this->successStatus);

                } else {
                    return response()->json([
                        'success' => false,
                        'data' => array(
                            "questions" => $validate['request_data']['questions']
                        ),
                    ], $this->successStatus);

                    return response()->json($validate, $this->successStatus);
                }

            } else {

                $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

                $order_attendee = $EBOrder->_getAttendeeByID($attendee_id)->getOrderAttendee();

                $registration_form = $EBOrder->getRegistrationForm($attendee_id);

                $registration_form_id = $registration_form ? $registration_form->id : 0;

                $request->merge(['registration_form_id' => $registration_form ? $registration_form->id : 0]);

                $questions = $this->subRegistrationRepository->registrationQuestions($request->all());

                $orderAttendeeAnswers = $this->subRegistrationRepository->getOrderAttendeeQuestionAnswers($request->all());
        
                $labels = $request->event['labels'];

                $event_id = $EBOrder->getOrderEventId();

                $language_id = $EBOrder->getUtility()->getLangaugeId();

                $payment_setting = $EBOrder->_getPaymentSetting();

                $billing_currency = $payment_setting['eventsite_currency'];

                // Order detail summary
                $order = $this->eventsiteBillingOrderRepository->getOrderDetailInvoice("json", $EBOrder, $labels, $language_id, $event_id, $billing_currency, $order_id,1, 1, true, false, 0, 0, true);

                $sub_registration_settings = $this->subRegistrationRepository->getSettings(['event_id' => $event_id]);

                return response()->json([
                    'success' => true,
                    'data' => array(
                        "questions" => $questions,
                        "orderAttendeeAnswers" => $orderAttendeeAnswers,
                        "order" => $order,
                        "order_attendee" => $order_attendee,
                        "sub_registration_settings" => $sub_registration_settings
                    ),
                ], $this->successStatus);
            }
       /*  } catch (\Exception $e) {
            return \Response::json([
                "message" => "Server error",
                "success" => false
            ]);
        } */
    }

    /**
     * validateRequest
     *
     * @param  mixed $request_data
     * @return void
     */
    public function validateRequest($request_data)
    {
        $status = true;

        foreach ($request_data['questions'] as $key => $question) {
            $question = json_decode($question, true);
            $request_data['questions'][$key] = $question;
            if(in_array($question['question_type'], ['multiple'])) {
                $answers = count(array_filter($question['answer'], function($row) {
                    return $row['is_default'] == 1;
                }));
                if($question['required_question'] == 1 && $answers == 0) {
                    $status = false;
                    $request_data['questions'][$key]['error'] = __('messages.field_required');
                } else if(in_array($question['question_type'], ['multiple']) && $question['required_question'] == 1 && $question['min_options'] > 0 && $question['max_options'] > 0 && ($answers < $question['min_options'] || $answers > $question['max_options'])) {
                    $status = false;
                    $request_data['questions'][$key]['error'] = sprintf("You must have selected minimum '%s' and maximum '%s'", $question['min_options'], $question['max_options']);
                }
            } else if(in_array($question['question_type'], ['number', 'open', 'date', 'date_time', 'dropdown', 'single'])) {
                if($question['required_question'] == 1 && !$question['answerValue']) {
                    $status = false;
                    $request_data['questions'][$key]['error'] = __('messages.field_required');
                }
            }
        }

        return [
            'status' => $status,
            'request_data' => $request_data
        ];
    }
}
