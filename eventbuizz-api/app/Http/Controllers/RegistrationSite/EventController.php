<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Http\Resources\Event as EventResource;
use App\Http\Resources\EventsiteBanner as EventsiteBannerResource;
use App\Http\Resources\Theme as ThemeResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\AdditionalInfoRepository;
class EventController extends Controller
{
    public $successStatus = 200;

    protected $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function index(Request $request, $slug)
    {
        //Fetch event ID
        $id = $this->eventRepository->fetchEventId($slug);

        $request->merge([
            "event_id" => $id
        ]);

        //Fetch Event
        $event = $this->eventRepository->getEventDetails($request->all(), $id);

        $event->attendee_settings = $request['event']['attendee_settings'];
        $event->speaker_settings = $request['event']['speaker_settings'];
        $event->sponsor_settings = $request['event']['sponsor_settings'];
        $event->exhibitor_settings = $request['event']['exhibitor_settings'];
        $event->gdprSettings = $request['event']['gdprSettings'];
        $event->agenda_settings = $request['event']['agenda_settings'];
        $event->settings = $request['event']['settings'];
        $event->info = $request['event']['detail'];

        if($event->news_settings->subscriber_id !== null){
            $event->newsletter_subcription_form_settings = $this->eventRepository->getNewsletterSubscriptionformSettings($event->news_settings->subscriber_id);
            $event->news_settings->subscriber_id = \Crypt::encrypt($event->news_settings->subscriber_id);
        }

        $event->disclaimer = $this->eventRepository->getEventDisclaimer($event);

        $layout_id = isset($request['layout']) && $request['layout'] !== "null" ? (int) $request['layout'] : $event->registration_site_layout_id;

        $event->layoutSections = $this->eventRepository->getEventLayoutSections($id, $layout_id);

        $event->moduleVariations = $this->eventRepository->getEventModuleVariations($id,  $event->registration_site_theme_id, $layout_id);
        
        $event->socialMediaShare = $this->eventRepository->getsocialMediaShare($id, $event->language_id);

        $event->paymentSettings = \App\Models\EventsitePaymentSetting::where('event_id',  $id)->where('registration_form_id', 0)->select(['evensite_additional_attendee'])->first();

        $customSections = $this->eventRepository->getCustomSections($id);

        $event->customSection1 = $customSections['custom_html_1'];
        
        $event->customSection2 = $customSections['custom_html_2'];

        $event->customSection3 = $customSections['custom_html_3'];

        $event->totalAttendees = $this->eventRepository->getTotalAttendees($id);
        
        $event->header_data = $this->eventRepository->getMenuInfo($id);

        $event->country = $this->eventRepository->getEventCountry($event->country_id);

        $event->waitinglistSettings = $this->eventRepository->getWaitingListSettings($id);

        $event->eventContactPersons = $this->eventRepository->getAllEventContactPersons($id);

        $event->eventOpeningHours = $this->eventRepository->getAllEventOpeningHours($id);

        $event->registration_end_date_passed = EventRepository::checkifRegistrationEndDatePassed($id);

        $event->registration_form_info = EventRepository::registrationFormInfo($id);

        $event->labels = $request['event']['labels'];

        $event->interface_labels = trans('registration-flow', [], ($event->language_id == 2 ? 'da' : null));

        return new EventResource($event);
    }

    //TODO make default 404 api response. If event id not found

    public function getThemeModules(Request $request, $slug){

        $id = $request->get('event_id');

        $theme = $this->eventRepository->eventTheme($id);

        return new ThemeResource($theme);
    }

    public function getEventsiteTopBanner(Request $request, $slug) {
        
        $banners = $this->eventRepository->eventThemeBanner($request->all());
        $sortableBanners = $this->eventRepository->eventSortableBanner($request->all());
        $settings = \App\Models\EventSiteBannerSetting::where('event_id',  $request['event_id'])->first();
        
        return response()->json([
            'success' => true,
            'data'=> [
                "banner_top" => EventsiteBannerResource::collection($banners),
                "banner_sort" => EventsiteBannerResource::collection($sortableBanners),
                "settings" => $settings
                ]
            ]);
    }

    public function purgeCacheLabels($event_id)
    {
        \Cache::tags('event-labels-'.$event_id)->flush();
        return response()->json([
            'success' => true,
            'message' => "purge successfull"
            ]);
    }
        
}
