<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\EventsiteBillingVoucherRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManageVoucherController extends Controller
{
    public $successStatus = 200;

    protected $eventsiteBillingVoucherRepository;

    /**
     * @param EventsiteBillingVoucherRepository $eventsiteBillingVoucherRepository
     */
    public function __construct(EventsiteBillingVoucherRepository $eventsiteBillingVoucherRepository)
    {
        $this->eventsiteBillingVoucherRepository = $eventsiteBillingVoucherRepository;
    }

    /**
     * @param Request $request
     * @param mixed $event_url
     * @param mixed $order_id
     * 
     * @return [type]
     */
    public function index(Request $request, $event_url, $order_id)
    {
        try {
            if ($request->voucher_code) {

                $voucherData = EventsiteBillingVoucherRepository::getVoucherByCode($request->all());

                $label = $request->event['labels'];
                
                if ($voucherData) {

                    $request->merge(["draft" => true, "panel" => $request->provider ? $request->provider : "attendee"]);

                    $orderInput = array(
                        "voucher_code" => $voucherData->code,
                    );

                    $EBOrder = new \App\Eventbuizz\EBObject\EBOrder($orderInput, $order_id);

                    $order = $EBOrder->updateOrder();

                    $order->save();

                    $order = $order->getModel();

                    if($order->code !== $voucherData->code){
                        return response()->json([
                            'success' => false,
                            'errors' => array(
                                "voucher_code" => $label['REGISTRATION_FORM_VOUCHER_LIMIT_HAS_BEEN_REACHED']
                            ),
                        ], $this->successStatus);
                     }

                    return response()->json([
                        'success' => true,
                        'voucher' => array('id' => $voucherData->id, 'code' => $voucherData->code, 'type' => $voucherData->type, 'discounttype' => $voucherData->discount_type),
                        'message' => $label['REGISTRATION_FORM_VOUCHER_APPLIED_SUCCESSFULLY'],
                    ], $this->successStatus);
                    
                } else {
                    return response()->json([
                        'success' => false,
                        'errors' => array(
                            "voucher_code" => $label['REGISTRATION_FORM_INVALID_VOUCHER_CODE']
                        ),
                    ], $this->successStatus);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'errors' => array(
                        "voucher_code" => $label['REGISTRATION_FORM_PLEASE_ENTER_CODE']
                    ),
                ], $this->successStatus);
            }
        } catch (\Exception $e) {
            return \Response::json([
                'errors' => array(
                     "voucher_code" => $label['REGISTRATION_FORM_VOUCHER_IS_EXPIRED'],
                ),
                "success" => false
            ]);
        }
    }

    /**
     * @param Request $request
     * @param mixed $event_url
     * @param mixed $order_id
     * 
     * @return [type]
     */
    public function remove(Request $request, $event_url, $order_id)
    {
        $request->merge(["draft" => true, "panel" => $request->provider ? $request->provider : "attendee", "action" => "remove-voucher"]);

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

        $order = $EBOrder->updateOrder();

        $order->save();

        return response()->json([
            'success' => true,
            'message' => "Voucher remove successfully!",
        ], $this->successStatus);
    }
}
