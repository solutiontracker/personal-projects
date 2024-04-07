<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\EventsiteBillingVoucherRepository;
use App\Eventbuizz\Repositories\OrganizerRepository;
use App\Http\Controllers\Wizard\Requests\eventsite\billing\VoucherRequest;
use App\Eventbuizz\Repositories\ImportRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;

class EventsiteBillingVoucherController extends Controller
{
    public $successStatus = 200;

    protected $eventsiteBillingVoucherRepository;

    protected $organizerRepository;

    protected $importRepository;

    protected $eventSiteSettingRepository;

    public function __construct(EventsiteBillingVoucherRepository $eventsiteBillingVoucherRepository, OrganizerRepository $organizerRepository, ImportRepository $importRepository, EventSiteSettingRepository $eventSiteSettingRepository)
    {
        $this->eventsiteBillingVoucherRepository = $eventsiteBillingVoucherRepository;
        $this->organizerRepository = $organizerRepository;
        $this->importRepository = $importRepository;
        $this->eventSiteSettingRepository = $eventSiteSettingRepository;
    }

    public function listing(Request $request, $page = 1)
    {
        $event = $request->event;

        $request->merge(['page' =>  $page, 'registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);

        $vouchers = $this->eventsiteBillingVoucherRepository->listing($request->all());

        $permissions = [
            "add" => $this->organizerRepository->getOrganizerPermissionsModule('eventsite', 'add')
        ];

        return response()->json([
            'success' => true,
            'data' => $vouchers,
            'permissions' => $permissions,
        ], $this->successStatus);
    }

    public function create(VoucherRequest $request)
    {
        $event = $request->event;

        if ($request->isMethod('PUT')) {

            $request->merge(['registration_form_id' => $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);

            $this->eventsiteBillingVoucherRepository->createVoucher($request->all());

            return response()->json([
                'success' => true,
                'message' => __('messages.create'),
            ]);

        }
        
    }

    public function edit(VoucherRequest $request, $id)
    {
        if ($request->isMethod('PUT')) {
            $this->eventsiteBillingVoucherRepository->updateVoucher($request->all(), $id);

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ]);
        } else {
            $voucher = $this->eventsiteBillingVoucherRepository->getVoucher($request->all(), $id);
            $items = $this->eventsiteBillingVoucherRepository->items($request->all());
            if ($id && $voucher) {
                if (count($items) > 0) {
                    foreach ($items as $key => $row) {
                        $items[$key]["checked"] = false;
                        $items[$key]["useage"] = 0;
                        $items[$key]["discount_type"] = 1;
                        $items[$key]["discount_price"] = 0;
                        foreach ($voucher['items'] as $i => $coupon_item) {
                            if ($row['id'] == $coupon_item->item_id) {
                                $items[$key]["checked"] = true;
                                $items[$key]["useage"] = $coupon_item->useage;
                                $items[$key]["discount_type"] = $coupon_item->discount_type;
                                $items[$key]["discount_price"] = $coupon_item->price;
                            }
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'voucher' => $voucher,
                'items' => $items
            ], $this->successStatus);
        }
    }

    public function destroy(Request $request, $id)
    {
        if ($id == "selected" || $id == "all") {
            if ($id == "selected") {
                $ids = $request->ids;
            } else if ($id == "all") {
                $ids = \App\Models\BillingVoucher::where('event_id', $request->event_id)->pluck('id');
            }
            foreach ($ids as $id) {
                $response = $this->eventsiteBillingVoucherRepository->deleteVoucher($id);
            }
            return response()->json([
                'success' => true,
                'message' => __('messages.delete'),
            ], $this->successStatus);
        } else {
            $response = $this->eventsiteBillingVoucherRepository->deleteVoucher($id);

            return response()->json([
                'success' => true,
                'message' => __('messages.delete'),
            ], $this->successStatus);
        }
    }

    public function updateVoucherStatus(Request $request, $id)
    {
        $this->eventsiteBillingVoucherRepository->updateVoucherStatus($request->all(), $id);
        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    public function items(VoucherRequest $request, $id = null)
    {
        $event = $request->event;

        $currencies = getCurrencyArray();

        $setting = $this->eventSiteSettingRepository->getSetting($request->all());

        $request->merge(["is_free" => ($setting->payment_type == 0 ? 1 : 0), 'registration_form_id' => $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);

        $payment_setting = $this->eventSiteSettingRepository->getPaymentSetting($request->all());

        $items = $this->eventsiteBillingVoucherRepository->items($request->all());

        if (count($items) > 0) {

            foreach ($items as $key => $row) {

                $items[$key]["checked"] = false;

                $items[$key]["useage"] = 0;

                $items[$key]["discount_type"] = 1;

                $items[$key]["discount_price"] = 0;

                foreach ($request->selectedItems as $i => $coupon_item) {

                    if ($row['id'] == $coupon_item['id'] && $coupon_item['checked']) {

                        $items[$key]["checked"] = true;

                        $items[$key]["useage"] = $coupon_item['useage'];

                        $items[$key]["discount_type"] = $coupon_item['discount_type'];

                        $items[$key]["discount_price"] = $coupon_item['discount_price'];

                    }

                }

            }

        }

        return response()->json([
            'success' => true,
            'items' => $items,
            'currency' => (isset($currencies[$payment_setting->eventsite_currency]) ? $currencies[$payment_setting->eventsite_currency] : '')
        ], $this->successStatus);
    }

    public function export(VoucherRequest $request)
    {
        $records = $this->eventsiteBillingVoucherRepository->getVoucherExportData($request->all());
        $settings = $this->eventsiteBillingVoucherRepository->getVoucherExportSettings();

        $header_data = array();
        foreach ($settings['fields'] as $headers) {
            $header_data[] = $headers['label'];
        }

        array_unshift($records, $header_data);

        $filename = 'export_voucher..csv';

        $this->importRepository->export($request->all(), $records, $filename, '', false);
    }

    public function generateCode(VoucherRequest $request)
    {
        $code = $this->eventsiteBillingVoucherRepository->generateRandomCode(6);
        return response()->json([
            'success' => true,
            'code' => $code,
        ], $this->successStatus);
    }
}
