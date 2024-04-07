<?php

namespace App\Events\Wizard\Event;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\Request;

class CloneEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    const eventPreInstaller = 'Event.eventPreInstaller';
    const eventSettingInstaller = 'Event.eventSettingInstaller';
    const eventDataInstaller = 'Event.eventDataInstaller';
    const eventSiteRegistrationInstaller = 'Event.eventSiteRegistrationInstaller';
    const eventLabelsInstaller = 'Event.eventLabelsInstaller';
    const eventCheckInOutInstaller = 'Event.eventCheckInOutInstaller';
    const eventPollsInstaller = 'Event.eventPollsInstaller';
    const eventTemplateInstaller = 'Event.eventTemplateInstaller';
    const eventSocialMediaInstaller = 'Event.eventSocialMediaInstaller';
    const eventShareInstaller = 'Event.eventShareInstaller';
    const eventAttendeeInstaller = 'Event.eventAttendeeInstaller';
    const eventCompetitionInstaller = 'Event.eventCompetitionInstaller';
    const eventBrandingInstaller = 'Event.eventBrandingInstaller';
    const eventDirectoryInstaller = 'Event.eventDirectoryInstaller';
    const eventBadgeInstaller = 'Event.eventBadgeInstaller';
    const eventSiteInstaller = 'Event.eventSiteInstaller';
    const subRegistrationInstaller = 'Event.subRegistrationInstaller';
    const programInstaller = 'Event.programInstaller';
    const eventInfoInstaller = 'Event.eventInfoInstaller';
    const eventSurveyInstaller = 'Event.eventSurveyInstaller';
    const eventBillingItemInstaller = 'Event.eventBillingItemInstaller';
    const eventSiteSettingInstaller = 'Event.eventSiteSettingInstaller';
    const eventBillingVoucherInstaller = 'Event.eventBillingVoucherInstaller';
    const eventBillingHotelsInstaller = 'Event.eventBillingHotelsInstaller';
    const eventMapInstaller = 'Event.eventMapInstaller';
    const eventThemeInstaller = 'Event.eventThemeInstaller';
    const eventLeadsInstaller = 'Event.eventLeadsInstaller';

    private $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new PrivateChannel('channel-name');
        return [];
    }
}
