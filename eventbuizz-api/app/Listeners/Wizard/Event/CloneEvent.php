<?php

namespace App\Listeners\Wizard\Event;

use App\Events\Wizard\Event\CloneEvent as CloneEventInstance;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Request;
use App\Eventbuizz\Repositories\ModuleRepository;
use App\Eventbuizz\Repositories\TemplateRepository;
use App\Eventbuizz\Repositories\DirectoryRepository;
use App\Eventbuizz\Repositories\BillingSectionRepository;
use App\Eventbuizz\Repositories\BadgeRepository;
use App\Eventbuizz\Repositories\EventsiteRepository;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\CheckInOutRepository;
use App\Eventbuizz\Repositories\PollRepository;
use App\Eventbuizz\Repositories\MapRepository;
use App\Eventbuizz\Repositories\SocialMediaRepository;
use App\Eventbuizz\Repositories\ShareRepository;
use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\CompetitionRepository;
use App\Eventbuizz\Repositories\EventBrandingRepository;
use App\Eventbuizz\Repositories\LabelRepository;
use App\Eventbuizz\Repositories\EventsiteRegistrationRepository;
use App\Eventbuizz\Repositories\GeneralRepository;
use App\Eventbuizz\Repositories\SubRegistrationRepository;
use App\Eventbuizz\Repositories\ProgramRepository;
use App\Eventbuizz\Repositories\EventInfoRepository;
use App\Eventbuizz\Repositories\SurveyRepository;
use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\EventsiteBillingVoucherRepository;
use App\Eventbuizz\Repositories\HotelRepository;
use App\Eventbuizz\Repositories\EventThemeRepository;
use App\Eventbuizz\Repositories\LeadRepository;
use App\Eventbuizz\Repositories\NetworkInterestRepository;

//class CloneEvent implements ShouldQueue
class CloneEvent
{
    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $connection = 'database';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'clone-event';

    public $tries = 3;

    /**
     * @param CloneEventInstance $event
     */

    private $request;
    private $moduleRepository;
    private $templateRepository;
    private $directoryRepository;
    private $billingSectionRepository;
    private $badgeRepository;
    private $eventsiteRepository;
    private $eventRepository;
    private $checkInOutRepository;
    private $pollRepository;
    private $mapRepository;
    private $socialMediaRepository;
    private $shareRepository;
    private $competitionRepository;
    private $eventBrandingRepository;
    private $labelRepository;
    private $eventsiteRegistrationRepository;
    private $generalRepository;
    private $subRegistrationRepository;
    private $programRepository;
    private $eventInfoRepository;
    private $surveyRepository;
    private $eventsiteBillingItemRepository;
    private $eventSiteSettingRepository;
    private $eventsiteBillingVoucherRepository;
    private $hotelRepository;
    private $eventThemeRepository;
    private $leadRepository;
    private $networkInterestRepository;

    public function __construct(Request $request, ModuleRepository $moduleRepository, TemplateRepository $templateRepository, DirectoryRepository $directoryRepository, BillingSectionRepository $billingSectionRepository, BadgeRepository $badgeRepository, EventsiteRepository $eventsiteRepository, EventRepository $eventRepository, CheckInOutRepository $checkInOutRepository, PollRepository $pollRepository, MapRepository $mapRepository, SocialMediaRepository $socialMediaRepository, ShareRepository $shareRepository, AttendeeRepository $attendeeRepository, CompetitionRepository $competitionRepository, EventBrandingRepository $eventBrandingRepository, LabelRepository $labelRepository, EventsiteRegistrationRepository $eventsiteRegistrationRepository, GeneralRepository $generalRepository, SubRegistrationRepository $subRegistrationRepository, ProgramRepository $programRepository, EventInfoRepository $eventInfoRepository, SurveyRepository $surveyRepository, EventsiteBillingItemRepository $eventsiteBillingItemRepository, EventSiteSettingRepository $eventSiteSettingRepository, EventsiteBillingVoucherRepository $eventsiteBillingVoucherRepository, HotelRepository $hotelRepository, EventThemeRepository $eventThemeRepository, LeadRepository $leadRepository, NetworkInterestRepository $networkInterestRepository)
    {
        $this->request = $request;
        $this->moduleRepository = $moduleRepository;
        $this->templateRepository = $templateRepository;
        $this->directoryRepository = $directoryRepository;
        $this->billingSectionRepository = $billingSectionRepository;
        $this->badgeRepository = $badgeRepository;
        $this->eventsiteRepository = $eventsiteRepository;
        $this->eventRepository = $eventRepository;
        $this->checkInOutRepository = $checkInOutRepository;
        $this->pollRepository = $pollRepository;
        $this->mapRepository = $mapRepository;
        $this->socialMediaRepository = $socialMediaRepository;
        $this->shareRepository = $shareRepository;
        $this->attendeeRepository = $attendeeRepository;
        $this->competitionRepository = $competitionRepository;
        $this->eventBrandingRepository = $eventBrandingRepository;
        $this->labelRepository = $labelRepository;
        $this->eventsiteRegistrationRepository = $eventsiteRegistrationRepository;
        $this->generalRepository = $generalRepository;
        $this->subRegistrationRepository = $subRegistrationRepository;
        $this->programRepository = $programRepository;
        $this->eventInfoRepository = $eventInfoRepository;
        $this->surveyRepository = $surveyRepository;
        $this->eventsiteBillingItemRepository = $eventsiteBillingItemRepository;
        $this->eventSiteSettingRepository = $eventSiteSettingRepository;
        $this->eventsiteBillingVoucherRepository = $eventsiteBillingVoucherRepository;
        $this->hotelRepository = $hotelRepository;
        $this->eventThemeRepository = $eventThemeRepository;
        $this->leadRepository = $leadRepository;
        $this->networkInterestRepository = $networkInterestRepository;
    }

    public function eventPreInstaller($request)
    {
        $this->eventRepository->preInstallation($request);
    }

    public function eventSettingInstaller($request)
    {
        //Event setting default installtion
        $this->eventRepository->install($request);

        //Clone Module Settings
        $modules = array("agendas", "attendees", "attendee_fields", "attendee_field_sorting", "speakers", "speakers_field_sorting", "ddirectory", "sponsors", "exhibitors", "banner", "polls" => array("polltemplate"), "qa", "social", "gallery", "checkIn" => array("print", "selfcheckin"), "mydocuments", "myturnlist", "subregistration", "social_wall", "mobile_app", "waiting_list", "eventsitebaner", "general_settings", "alert_setting", "social_media_setting", "custom_field");

        $this->moduleRepository->cloneEventModulesSetting($modules, $request['from_event_id'], $request['to_event_id']);

        $this->generalRepository->cloneEventGeneralSetting(["branding", "sections", "disclaimer", "gdpr", "event_module_tab_setting", "event_native_app_settings", 'speaker_list_projector_attendee_fields'], $request['from_event_id'], $request['to_event_id'], $request['languages']);
    }

    public function eventDataInstaller($request)
    {
        $this->eventRepository->copyEventData($request);
    }

    public function eventSiteRegistrationInstaller($request)
    {
        //Eventsite Registration installtion
        $this->eventsiteRegistrationRepository->installation($request);
    }

    public function eventLabelsInstaller($request)
    {
        //Event Labels installtion
        $this->labelRepository->install($request);
        $this->labelRepository->cloneEventModulesLabels($request);
    }

    public function eventCheckInOutInstaller($request)
    {
        //Event checkin/checkout installation
        $this->checkInOutRepository->install($request);
    }

    public function eventPollsInstaller($request)
    {
        //Event polls installation
        $this->pollRepository->install($request);
    }

    public function eventTemplateInstaller($request)
    {
        //Event templates installation
        $this->templateRepository->install($request);
        $this->templateRepository->copyTemplates(['sms', 'email'], $request['from_event_id'], $request['to_event_id'], $request['languages']);
    }

    public function eventSocialMediaInstaller($request)
    {
        //Social media installation
        $this->socialMediaRepository->install($request);
    }

    public function eventShareInstaller($request)
    {
        //Event share template installation
        $this->shareRepository->install($request);
    }

    public function eventAttendeeInstaller($request)
    {
        //Event Attendee installation
        $this->attendeeRepository->install($request);
    }

    public function eventCompetitionInstaller($request)
    {
        //Event competition installation
        $this->competitionRepository->install($request);
    }

    public function eventBrandingInstaller($request)
    {
        //Event branding installation
        $this->eventBrandingRepository->install($request);
    }

    public function eventDirectoryInstaller($request)
    {
        //Event directory installation
        $this->directoryRepository->install($request);
    }

    public function eventBadgeInstaller($request)
    {
        //Event badge installation
        $this->badgeRepository->install($request);
    }

    public function eventSiteInstaller($request)
    {
        //Eventsite installation
        $this->eventsiteRepository->install($request);
    }

    public function subRegistrationInstaller($request)
    {
        //Sub registration installation
        $this->subRegistrationRepository->install($request);

        //Keywords
        $this->networkInterestRepository->install($request);
    }

    public function programInstaller($request)
    {
        //copy programs content
        $this->programRepository->install($request);
    }

    public function eventInfoInstaller($request)
    {
        //event info clone => practical info/general info/additional info
        $this->eventInfoRepository->install($request);
    }

    public function eventSurveyInstaller($request)
    {
        //copy survey content
        $this->surveyRepository->install($request);
    }

    public function eventBillingItemInstaller($request)
    {
        //copy billing items
        $this->eventsiteBillingItemRepository->install($request);
    }

    public function eventSiteSettingInstaller($request)
    {
        //copy registration settings
        $this->eventSiteSettingRepository->install($request);
    }

    public function eventBillingVoucherInstaller($request)
    {
        //copy billing vouchers
        $this->eventsiteBillingVoucherRepository->install($request);
    }

    public function eventBillingHotelsInstaller($request)
    {
        //copy billing hotels 
        $this->hotelRepository->install($request);
    }

    public function eventMapInstaller($request)
    {
        //copy event map info
        $this->mapRepository->install($request);
    }

    public function eventThemeInstaller($request)
    {
        //copy/create event theme 
        $this->eventThemeRepository->install($request);
    }

    public function eventLeadsInstaller($request)
    {
        //copy/create event theme 
        $this->leadRepository->install($request);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            CloneEventInstance::eventPreInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventPreInstaller'
        );
        
        $events->listen(
            CloneEventInstance::eventSettingInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventSettingInstaller'
        );

        $events->listen(
            CloneEventInstance::eventDataInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventDataInstaller'
        );

        $events->listen(
            CloneEventInstance::eventSiteRegistrationInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventSiteRegistrationInstaller'
        );

        $events->listen(
            CloneEventInstance::eventLabelsInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventLabelsInstaller'
        );

        $events->listen(
            CloneEventInstance::eventCheckInOutInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventCheckInOutInstaller'
        );

        $events->listen(
            CloneEventInstance::eventPollsInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventPollsInstaller'
        );

        $events->listen(
            CloneEventInstance::eventTemplateInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventTemplateInstaller'
        );

        $events->listen(
            CloneEventInstance::eventSocialMediaInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventSocialMediaInstaller'
        );

        $events->listen(
            CloneEventInstance::eventShareInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventShareInstaller'
        );

        $events->listen(
            CloneEventInstance::eventAttendeeInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventAttendeeInstaller'
        );

        $events->listen(
            CloneEventInstance::eventCompetitionInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventCompetitionInstaller'
        );

        $events->listen(
            CloneEventInstance::eventBrandingInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventBrandingInstaller'
        );

        $events->listen(
            CloneEventInstance::eventDirectoryInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventDirectoryInstaller'
        );

        $events->listen(
            CloneEventInstance::eventBadgeInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventBadgeInstaller'
        );

        $events->listen(
            CloneEventInstance::eventSiteInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventSiteInstaller'
        );

        $events->listen(
            CloneEventInstance::subRegistrationInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@subRegistrationInstaller'
        );

        $events->listen(
            CloneEventInstance::programInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@programInstaller'
        );

        $events->listen(
            CloneEventInstance::eventInfoInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventInfoInstaller'
        );

        $events->listen(
            CloneEventInstance::eventSurveyInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventSurveyInstaller'
        );

        $events->listen(
            CloneEventInstance::eventBillingItemInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventBillingItemInstaller'
        );

        $events->listen(
            CloneEventInstance::eventSiteSettingInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventSiteSettingInstaller'
        );

        $events->listen(
            CloneEventInstance::eventBillingVoucherInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventBillingVoucherInstaller'
        );

        $events->listen(
            CloneEventInstance::eventBillingHotelsInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventBillingHotelsInstaller'
        );

        $events->listen(
            CloneEventInstance::eventMapInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventMapInstaller'
        );
        
        $events->listen(
            CloneEventInstance::eventThemeInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventThemeInstaller'
        );
        $events->listen(
            CloneEventInstance::eventLeadsInstaller,
            'App\Listeners\Wizard\Event\CloneEvent@eventLeadsInstaller'
        );
    }

    public function failed()
    {
        // Need to handle there
    }
}
