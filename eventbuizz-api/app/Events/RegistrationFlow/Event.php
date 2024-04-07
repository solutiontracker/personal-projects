<?php

namespace App\Events\RegistrationFlow;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    const OrderGeneralActionInstaller = 'OrderGeneralActionInstaller';
    
    const OrderNewCreatedWithCreditNoteInstaller = 'OrderNewCreatedWithCreditNoteInstaller';

    const OrderAttendeeBeforeSaveInstaller = 'OrderAttendeeBeforeSaveInstaller';

    const OrderAttendeeAfterSaveInstaller = 'OrderAttendeeAfterSaveInstaller';

    const OrderAttendeeLogAfterSaveInstaller = 'OrderAttendeeLogAfterSaveInstaller';

    const OrderAttendeeAddonSaveAfterInstaller = 'OrderAttendeeAddonSaveAfterInstaller';

    const OrderUpdateAfterInstaller = 'OrderUpdateAfterInstaller';

    const OrderKeywordsSaveInstaller = 'OrderKeywordsSaveInstaller';

    const addReportingRevenueInstaller = 'addReportingRevenueInstaller';

    private $order;

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
        return [];
    }
}
