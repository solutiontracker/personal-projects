<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use App\Eventbuizz\Repositories\GeneralRepository;
use App\Http\Controllers\Wizard\Requests\eventsite\billing\PaymentProviderRequest;

class EventSiteSettingController extends Controller
{
    public $successStatus = 200;

    protected $eventSiteSettingRepository;

    protected $generalRepository;

    protected $eventSettingRepository;

    public function __construct(EventSiteSettingRepository $eventSiteSettingRepository, GeneralRepository $generalRepository, EventSettingRepository $eventSettingRepository)
    {
        $this->eventSiteSettingRepository = $eventSiteSettingRepository;
        $this->generalRepository = $generalRepository;
        $this->eventSettingRepository = $eventSettingRepository;
    }

    public function eventSiteTopMenus(Request $request)
    {
        if ($request->isMethod('PUT')) {
            $this->eventSiteSettingRepository->updateEventSiteTopMenus($request->all());
            $menus = $this->eventSiteSettingRepository->getEventSiteTopMenus($request->all());

            $modules = $this->eventSettingRepository->modules($request->all(), config('module.app_module_alias'), 'both');

            $module_permissions = array();

            foreach ($modules as $module) {
                $module_permissions[$module['alias']] = $module['status'];
            }

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
                'data' => [
                    'menus' => $menus,
                    "module_permissions" => $module_permissions,
                    "modules" => $modules
                ]
            ], $this->successStatus);
        } else {

            $menus = $this->eventSiteSettingRepository->getEventSiteTopMenus($request->all());

            return response()->json([
                'success' => true,
                'data' => [
                    'menus' => $menus
                ]
            ], $this->successStatus);
        }
    }

    public function paymentProviders(PaymentProviderRequest $request)
    {
        if ($request->isMethod('PUT')) {
            $this->eventSiteSettingRepository->savePaymentSetting($request->all());
            $this->eventSiteSettingRepository->savePaymentCards($request->all());
            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ]);
        } else {
            $payment_setting = $this->eventSiteSettingRepository->getPaymentSetting($request->all());
            $saved_payment_cards = $this->eventSiteSettingRepository->getPaymentCards($request->all());
            $saved_payment_cards = unserialize($saved_payment_cards->card_type);
            $event_setting = EventSettingRepository::getEventSetting($request->all());
            $merchantTypes = array(
                [
                    "id" => '0',
                    "name" => "DIBS"
                ],
                [
                    "id" => '2',
                    "name" => "Mistertango"
                ],
                [
                    "id" => '4',
                    "name" => "Swedbank"
                ],
                [
                    "id" => '5',
                    "name" => "PensoPay"
                ],
                [
                    "id" => '6',
                    "name" => "Wirecard"
                ],
                [
                    "id" => '7',
                    "name" => "Stripe"
                ],
                [
                    "id" => '8',
                    "name" => "Bambora"
                ]
            );
            $markets = array(
                [
                    "id" => "LT",
                    "name" => "Lithuania (LT)"
                ],
                [
                    "id" => "LV",
                    "name" => "Latvia (LV)"
                ],
                [
                    "id" => "EE",
                    "name" => "Estonia (EE)"
                ],
                [
                    "id" => "FI",
                    "name" => "Finland (FI)"
                ]
            );
            $mistertango_markets = array();
            foreach ($markets as $market) {
                if (in_array($market['id'], explode(",", $payment_setting->mistertango_markets))) {
                    $row = array(
                        "value" => $market['id'],
                        "label" => $market['name'],
                    );
                    array_push($mistertango_markets, $row);
                }
            }
            $billingLanguages = array(
                [
                    "id" => "da-dk",
                    "name" => "Danish"
                ],
                [
                    "id" => "en-gb",
                    "name" => "English"
                ],
                [
                    "id" => "no-nb",
                    "name" => "Norwegian"
                ],
                [
                    "id" => "sv-se",
                    "name" => "Swedish"
                ],
                [
                    "id" => "fo-fo",
                    "name" => "Faroese"
                ],
                [
                    "id" => "de-de",
                    "name" => "German"
                ],
                [
                    "id" => "nl-nl",
                    "name" => "Dutch"
                ],
                [
                    "id" => "ru-ru",
                    "name" => "Russian"
                ],
                [
                    "id" => "lt-lt",
                    "name" => "Lithuania"
                ]
            );
            $swed_bank_regions = array(
                [
                    "id" => "REGION_LAT",
                    "name" => "Latvia"
                ],
                [
                    "id" => "REGION_LIT",
                    "name" => "Lithuania"
                ],
                [
                    "id" => "REGION_EST",
                    "name" => "Estonia"
                ]
            );
            $swed_bank_region = array();
            foreach ($swed_bank_regions as $region) {
                if (in_array($region['id'], explode(",", $payment_setting->swed_bank_region))) {
                    $row = array(
                        "value" => $region['id'],
                        "label" => $region['name'],
                    );
                    array_push($swed_bank_region, $row);
                }
            }
            $swed_langauges = array(
                [
                    "id" => "LANG_ENG",
                    "name" => "English"
                ],
                [
                    "id" => "LANG_EST",
                    "name" => "Estonian"
                ],
                [
                    "id" => "LANG_LIT",
                    "name" => "Lithuanian"
                ],
                [
                    "id" => "LANG_LAT",
                    "name" => "Latvian"
                ],
                [
                    "id" => "LANG_RUS",
                    "name" => "Russian"
                ]
            );
            $swed_bank_language = array();
            foreach ($swed_langauges as $language) {
                if (in_array($language['id'], explode(",", $payment_setting->swed_bank_language))) {
                    $row = array(
                        "value" => $language['id'],
                        "label" => $language['name'],
                    );
                    array_push($swed_bank_language, $row);
                }
            }

            if (!is_array($saved_payment_cards)) $saved_payment_cards = [];

            $payment_cards = array(
                [
                    "id" => "DK",
                    "name" => "Dankort",
                    "isChecked" => (in_array("DK", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "V-DK",
                    "name" => "VISA/Dankort",
                    "isChecked" => (in_array("V-DK", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "VISA(SE)",
                    "name" => "VISA (SE)",
                    "isChecked" => (in_array("VISA(SE)", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "VISA",
                    "name" => "VISA",
                    "isChecked" => (in_array("VISA", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "MC(DK)",
                    "name" => "Eurocard/Mastercard (DK)",
                    "isChecked" => (in_array("MC(DK)", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "MC(SE)",
                    "name" => "Eurocard/Mastercard (SE)",
                    "isChecked" => (in_array("MC(SE)", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "MC",
                    "name" => "Eurocard/Mastercard",
                    "isChecked" => (in_array("MC", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "DIN(DK)",
                    "name" => "Diners Club (DK)",
                    "isChecked" => (in_array("DIN(DK)", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "DIN",
                    "name" => "Diners Club",
                    "isChecked" => (in_array("DIN", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "AMEX(DK)",
                    "name" => "American Express (DK)",
                    "isChecked" => (in_array("AMEX(DK)", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "AMEX",
                    "name" => "American Express",
                    "isChecked" => (in_array("AMEX", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "MTRO(DK)",
                    "name" => "Maestro (DK)",
                    "isChecked" => (in_array("MTRO(DK)", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "MTRO",
                    "name" => "Maestro",
                    "isChecked" => (in_array("MTRO", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "ELEC",
                    "name" => "VISA Electron",
                    "isChecked" => (in_array("ELEC", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "JCB",
                    "name" => "JCB",
                    "isChecked" => (in_array("JCB", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "FFK",
                    "name" => "Forbrugsforeningen",
                    "isChecked" => (in_array("FFK", $saved_payment_cards) ? true : false)
                ],
                [
                    "id" => "PayPal",
                    "name" => "PayPal",
                    "isChecked" => (in_array("PayPal", $saved_payment_cards) ? true : false)
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_setting' => $payment_setting,
                    'event_setting' => $event_setting,
                    'merchantTypes' => $merchantTypes,
                    'markets' => $markets,
                    'mistertango_markets' => $mistertango_markets,
                    'billingLanguages' => $billingLanguages,
                    'swed_bank_regions' => $swed_bank_regions,
                    'swed_bank_region' => $swed_bank_region,
                    'swed_langauges' => $swed_langauges,
                    'swed_bank_language' => $swed_bank_language,
                    'saved_payment_cards' => unserialize($saved_payment_cards->card_type),
                    'payment_cards' => $payment_cards
                ]
            ], $this->successStatus);
        }
    }

    public function invoiceSettings(Request $request)
    {
        if ($request->isMethod('PUT')) {

            $this->eventSiteSettingRepository->savePaymentSetting($request->all());

            $this->eventSiteSettingRepository->updateSectionFields($request->all());

            //Eventsite payment setting
            $eventsite_payment_setting = \App\Models\EventsitePaymentSetting::where('event_id', $request->event_id)->where('registration_form_id', 0)->first();

            //Eventsite secion fields
            $eventsite_secion_fields = EventSiteSettingRepository::getAllSectionFields(["event_id" => $request->event_id, "language_id" => $request->language_id]);

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
                'eventsite_payment_setting' => $eventsite_payment_setting,
                'eventsite_secion_fields' => $eventsite_secion_fields
            ]);

        } else {

            $payment_setting = $this->eventSiteSettingRepository->getPaymentSetting($request->all());

            $event_setting = EventSettingRepository::getEventSetting($request->all());

            $active_orders = EventsiteBillingOrderRepository::activeOrders($request->all(), true);

            $countries = $this->generalRepository->getMetadata("countries", $request->event_id);

            $eventsite_vat_countries = array();

            foreach ($countries['countries'] as $country) {
                if (in_array($country->id, explode(",", $payment_setting->eventsite_vat_countries))) {
                    $row = array(
                        "value" => $country->id,
                        "label" => $country->name,
                    );
                    array_push($eventsite_vat_countries, $row);
                }
            }

            $currencies = array();

            foreach (getCurrencyArray() as $key => $currency) {
                $row = array(
                    "id" => strval($key),
                    "name" => $currency,
                );
                array_push($currencies, $row);
            }

            //sections data

            $event = $request->event;

            $request->merge(['registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);
            
            $sections_data = $this->eventSiteSettingRepository->getSectionsData($request->all());

            foreach ($sections_data as $i => $section) {
                $temp = array();
                if (count($section['info']) > 0) {
                    foreach ($section['info'] as $val) {
                        $temp[$val['name']] = $val['value'];
                    }
                }
                $fields = $this->eventSiteSettingRepository->getSectionFields($request->all(), $section['field_alias']);
                $section['fields']  = $fields;
                $section['detail'] = $temp;
                $sections_data[$i] = $section;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_setting' => $payment_setting,
                    'event_setting' => $event_setting,
                    'active_orders' => $active_orders,
                    'currencies' => $currencies,
                    'countries' => $countries['countries'],
                    'eventsite_vat_countries' => $eventsite_vat_countries,
                    'bcc_emails' => explode(",", $payment_setting->bcc_emails),
                    'sections_data' => $sections_data
                ]
            ], $this->successStatus);

        }
    }

    public function eanSettings(Request $request)
    {
        if ($request->isMethod('PUT')) {

            $this->eventSiteSettingRepository->savePaymentSetting($request->all());

            return response()->json([
                'success' => true,
                'message' => ($request->auto_invoice ? "Sending electronic invoice is subject to an extra cost and will be charged seperately. Please contact Eventbuizz for further information." : __('messages.update')),
            ]);

        } else {

            $payment_setting = $this->eventSiteSettingRepository->getPaymentSetting($request->all());

            $paymentTerms = array(
                [
                    "id" => "0",
                    "name" => "Net 14 days"
                ],
                [
                    "id" => "1",
                    "name" => "Net 21 days"
                ],
                [
                    "id" => "2",
                    "name" => "Net 8 days"
                ],
                [
                    "id" => "3",
                    "name" => "On going month plus 30 days"
                ],
                [
                    "id" => "4",
                    "name" => "Promt payment"
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_setting' => $payment_setting,
                    'paymentTerms' => $paymentTerms
                ]
            ], $this->successStatus);
            
        }
    }

    public function fikSettings(PaymentProviderRequest $request)
    {
        if ($request->isMethod('PUT')) {
            $this->eventSiteSettingRepository->savePaymentSetting($request->all());
            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ]);
        } else {
            $payment_setting = $this->eventSiteSettingRepository->getPaymentSetting($request->all());
            return response()->json([
                'success' => true,
                'data' => [
                    'payment_setting' => $payment_setting
                ]
            ], $this->successStatus);
        }
    }

    public function paymentMethods(PaymentProviderRequest $request)
    {
        if ($request->isMethod('PUT')) {
            $this->eventSiteSettingRepository->updateSectionFields($request->all());
            $this->eventSiteSettingRepository->savePaymentSetting($request->all());
            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ]);
        } else {
            $sections_data = $this->eventSiteSettingRepository->getSectionsData($request->all());
            foreach ($sections_data as $i => $section) {
                $temp = array();
                if (count($section['info']) > 0) {
                    foreach ($section['info'] as $val) {
                        $temp[$val['name']] = $val['value'];
                    }
                }
                $fields = $this->eventSiteSettingRepository->getSectionFields($request->all(), $section['field_alias']);
                $section['fields']  = $fields;
                $section['detail'] = $temp;
                $sections_data[$i] = $section;
            }
            $payment_setting = $this->eventSiteSettingRepository->getPaymentSetting($request->all());
            return response()->json([
                'success' => true,
                'data' => [
                    'sections_data' => $sections_data,
                    'payment_setting' => $payment_setting
                ]
            ], $this->successStatus);
        }
    }

    public function purchasePolicy(PaymentProviderRequest $request)
    {
        if ($request->isMethod('PUT')) {
            $this->eventSiteSettingRepository->savePaymentCards($request->all());
            return response()->json([
                'success' => true,
                'message' =>  __('messages.update'),
            ]);
        } else {
            $payment_cards = $this->eventSiteSettingRepository->getPaymentCards($request->all());
            return response()->json([
                'success' => true,
                'data' => $payment_cards
            ], $this->successStatus);
        }
    }
}
