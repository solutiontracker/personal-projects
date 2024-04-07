<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;
use App\Eventbuizz\Repositories\OrganizerRepository;
use App\Http\Controllers\Wizard\Requests\eventsite\billing\BillingItemRequest;

class EventsiteBillingItemController extends Controller
{
    public $successStatus = 200;

    protected $eventSiteSettingRepository;

    protected $eventsiteBillingItemRepository;

    protected $organizerRepository;

    public function __construct(EventSiteSettingRepository $eventSiteSettingRepository, EventsiteBillingItemRepository $eventsiteBillingItemRepository, OrganizerRepository $organizerRepository)
    {
        $this->eventSiteSettingRepository = $eventSiteSettingRepository;
        $this->eventsiteBillingItemRepository = $eventsiteBillingItemRepository;
        $this->organizerRepository = $organizerRepository;
    }

    public function listing(Request $request, $page = 1)
    {

        $currencies = getCurrencyArray();

        $event = $request->event;

        $setting = $this->eventSiteSettingRepository->getSetting($request->all());

        //verify admin fee item is created  
        if ($setting->payment_type == 1) {
            $this->eventsiteBillingItemRepository->isItemInserted($request->all(), "event_fee");
        }

        $request->merge(['page' =>  $page, "is_free" => ($setting->payment_type == 0 ? 1 : 0), 'registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') :0]);
        
        $payment_setting = $this->eventSiteSettingRepository->getPaymentSetting($request->all());
        
        $items = $this->eventsiteBillingItemRepository->listing($request->all());
        
        $groups = $this->eventsiteBillingItemRepository->getAllGroups($request->all());
        
        $permissions = [
            "add" => $this->organizerRepository->getOrganizerPermissionsModule('eventsite', 'add')
        ];

        return response()->json([
            'success' => true,
            'data' => $items,
            'groups' => $groups,
            'permissions' => $permissions,
            'payment_setting' => $payment_setting,
            'setting' => $setting,
            'currency' => (isset($currencies[$payment_setting->eventsite_currency]) ? $currencies[$payment_setting->eventsite_currency] : '')
        ], $this->successStatus);
    }

    public function create(BillingItemRequest $request)
    {
        $event = $request->event;

        if ($request->isMethod('PUT')) {

            $request->merge(['registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);

            if ($request->type == "item") {

                $max = \App\Models\BillingItem::where('event_id', '=', $request->event_id)->max('sort_order');

                $request->merge([
                    "sort_order" => ($max + 1)
                ]);

                $this->eventsiteBillingItemRepository->createItem($request->all());

            } else {

                $this->eventsiteBillingItemRepository->createItemGroup($request->all());

            }

            return response()->json([
                'success' => true,
                'message' => __('messages.create'),
            ]);
        }
    }

    public function edit(BillingItemRequest $request, $id)
    {
        if ($request->isMethod('PUT')) {
            if ($request->type == "item") {
                $this->eventsiteBillingItemRepository->updateItem($request->all(), $id);
            } else {
                $this->eventsiteBillingItemRepository->updateItemGroup($request->all(), $id);
            }

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ]);
        } else {
            $data = $this->eventsiteBillingItemRepository->getItem($request->all(), $id);
            return response()->json([
                'success' => true,
                'data' => $data
            ], $this->successStatus);
        }
    }

    public function destroy(Request $request, $id)
    {
        if ($id == "selected" || $id == "all") {
            $setting = $this->eventSiteSettingRepository->getSetting($request->all());
            $request->merge(["is_free" => ($setting->payment_type == 0 ? 1 : 0)]);
            $items = $this->eventsiteBillingItemRepository->listing($request->all());
            if ($id == "selected") {
                $ids = $request->ids;
                foreach ($items as $item) {
                    if (in_array($item["id"], $ids)) {
                        if ($item["delete"] == "delete") {
                            $this->eventsiteBillingItemRepository->deleteItem($request->all(), $item["id"]);
                        } else if ($item["delete"] == "archive") {
                            $this->eventsiteBillingItemRepository->archiveItem($request->all(), $item["id"]);
                        }
                    }
                }
            } else if ($id == "all") {
                foreach ($items as $item) {
                    if ($item["delete"] == "delete") {
                        $this->eventsiteBillingItemRepository->deleteItem($request->all(), $item["id"]);
                    } else if ($item["delete"] == "archive") {
                        $this->eventsiteBillingItemRepository->archiveItem($request->all(), $item["id"]);
                    }
                }
            }
            return response()->json([
                'success' => true,
                'message' => __('messages.delete'),
            ], $this->successStatus);
        } else {
            if ($request->action == "delete") {
                $this->eventsiteBillingItemRepository->deleteItem($request->all(), $id);
                return response()->json([
                    'success' => true,
                    'message' => __('messages.delete'),
                ], $this->successStatus);
            } else if ($request->action == "archive") {
                $this->eventsiteBillingItemRepository->archiveItem($request->all(), $id);
                return response()->json([
                    'success' => true,
                    'message' => __('messages.archive'),
                ], $this->successStatus);
            }
        }
    }

    public function updateItemStatus(Request $request, $id)
    {
        $this->eventsiteBillingItemRepository->updateItemStatus($request->all(), $id);
        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    public function updateItemOrder(Request $request)
    {
        $this->eventsiteBillingItemRepository->updateItemOrder($request->all());
        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    public function linkToSearch(Request $request)
    {
        if ($request->billing_item_type == 0) {
            $data = $this->eventsiteBillingItemRepository->programs($request->all());
        } else if ($request->billing_item_type == 1) {
            $data = $this->eventsiteBillingItemRepository->tracks($request->all());
        } else if ($request->billing_item_type == 2) {
            $data = $this->eventsiteBillingItemRepository->workshops($request->all());
        } else if ($request->billing_item_type == 3) {
            $data = $this->eventsiteBillingItemRepository->attendee_groups($request->all());
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ], $this->successStatus);
    }
}
