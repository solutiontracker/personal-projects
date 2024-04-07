<?php

namespace App\Listeners\RegistrationFlow;

use App\Events\RegistrationFlow\Event;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

class Listener 
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
    public $queue = 'registration-flow';

    public $tries = 3;

    private $eventsiteBillingOrderRepository;

    /**
     * @param Request $request
     */
    public function __construct(Request $request, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository)
    {
        $this->request = $request;
        $this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
    }

    /**
     * @param mixed $order
     * 
     * @return [type]
     */
    public function OrderGeneralActionInstaller($order)
    {
        $this->eventsiteBillingOrderRepository->attachToMainEvent($order);

        $this->eventsiteBillingOrderRepository->addPortalAccess($order);
        
        $this->eventsiteBillingOrderRepository->generatePdfForTicketItems($order);
        
        $this->eventsiteBillingOrderRepository->resetVoucherSessions($order);

        $this->eventsiteBillingOrderRepository->addWaitingList($order);

        $this->eventsiteBillingOrderRepository->addGdprLog($order);

        $this->eventsiteBillingOrderRepository->addFoodsAllergiesLog($order);

        $this->eventsiteBillingOrderRepository->sendAttendeeVerificationEmail($order);  

        $this->eventsiteBillingOrderRepository->printCard($order);

        $this->eventsiteBillingOrderRepository->sendEmailWhenSellingItemQtyBecomeZero($order);
       
        $this->eventsiteBillingOrderRepository->sendConfirmationEmail($order);

        $this->eventsiteBillingOrderRepository->addNewsSubscriber($order);
    }

    /**
     * @param mixed $order
     * 
     * @return [type]
     */
    public function OrderNewCreatedWithCreditNoteInstaller($order)
    {
        //Previous version of order
        if($order->getModel()->clone_of) {

            $prev_order = $order->getPreviousVersion();

            $this->eventsiteBillingOrderRepository->sendCreditNote($prev_order, $order);

        }
    }

    /**
     * @param mixed $order
     * 
     * @return [type]
     */
    public function OrderAttendeeBeforeSaveInstaller($order)
    { 
        $order_attendee_ids = $order->getActiveOrderAttendees();

        $event_id = $order->getOrderEventId();

        $group_ids = \App\Models\EventGroup::where('event_id', $event_id)
                        ->pluck('id')
                        ->toArray();

        if(count($order_attendee_ids) > 0 && count($group_ids) > 0) {
            \App\Models\EventAttendeeGroup::whereIn('attendee_id', (array)$order_attendee_ids)->whereIn('group_id', $group_ids)->delete();
        }

        $agendas_ids = \App\Models\EventAgenda::where('event_id', $event_id)->pluck('id')->toArray();

        if(count($agendas_ids) > 0 && count($order_attendee_ids) > 0) {
            \App\Models\EventAgendaAttendeeAttached::whereIn('attendee_id', (array)$order_attendee_ids)->whereIn('agenda_id', $agendas_ids)->delete();
        }
}

    /**
     * @param mixed $order
     * 
     * @return [type]
     */
    public function OrderAttendeeAfterSaveInstaller($order)
    { 
        $this->eventsiteBillingOrderRepository->addSubRegistrationData($order);

        $this->eventsiteBillingOrderRepository->saveReferenceNumber($order);

        $this->eventsiteBillingOrderRepository->attachAttendeeGroupsByAttendeeType($order);
    }
    
    /**
     * @param mixed $order
     * @param string $action
     * 
     * @return [type]
     */
    public function OrderAttendeeLogAfterSaveInstaller($order, $action = 'add')
    {

        if($action == "add") {

            $this->eventsiteBillingOrderRepository->addAttendeeLog($order, 'add');
        
            $this->eventsiteBillingOrderRepository->addAttendeePermission($order);

        } else {

            $this->eventsiteBillingOrderRepository->addAttendeeLog($order, 'update');

        }
        
    }

    /**
     * @param mixed $order
     * 
     * @return [type]
     */
    public function OrderAttendeeAddonSaveAfterInstaller($order, $addon = array())
    { 
        $this->eventsiteBillingOrderRepository->saveTicketItem($addon);
        $this->eventsiteBillingOrderRepository->attachAttendee($order, $addon);
    }
    
    /**
     * @param mixed $order
     * 
     * @return [type]
     */
    public function OrderUpdateAfterInstaller($order)
    { 
        $this->eventsiteBillingOrderRepository->updateReportingRevenue($order);
    }

    /**
     * @param mixed $order
     * 
     * @return [type]
     */
    public function OrderKeywordsSaveInstaller($order)
    { 
        $this->eventsiteBillingOrderRepository->addKeywordsData($order);
    }

    /**
     * @param mixed $order
     * 
     * @return [type]
     */
    public function addReportingRevenueInstaller($order)
    {
        $this->eventsiteBillingOrderRepository->addReportingRevenue($order);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            Event::OrderGeneralActionInstaller,
            'App\Listeners\RegistrationFlow\Listener@OrderGeneralActionInstaller'
        );

        $events->listen(
            Event::OrderNewCreatedWithCreditNoteInstaller,
            'App\Listeners\RegistrationFlow\Listener@OrderNewCreatedWithCreditNoteInstaller'
        );

        $events->listen(
            Event::OrderAttendeeBeforeSaveInstaller,
            'App\Listeners\RegistrationFlow\Listener@OrderAttendeeBeforeSaveInstaller'
        );

        $events->listen(
            Event::OrderAttendeeAfterSaveInstaller,
            'App\Listeners\RegistrationFlow\Listener@OrderAttendeeAfterSaveInstaller'
        );

        $events->listen(
            Event::OrderAttendeeLogAfterSaveInstaller,
            'App\Listeners\RegistrationFlow\Listener@OrderAttendeeLogAfterSaveInstaller'
        );

        $events->listen(
            Event::OrderAttendeeAddonSaveAfterInstaller,
            'App\Listeners\RegistrationFlow\Listener@OrderAttendeeAddonSaveAfterInstaller'
        );

        $events->listen(
            Event::OrderUpdateAfterInstaller,
            'App\Listeners\RegistrationFlow\Listener@OrderUpdateAfterInstaller'
        );

        $events->listen(
            Event::OrderKeywordsSaveInstaller,
            'App\Listeners\RegistrationFlow\Listener@OrderKeywordsSaveInstaller'
        );

        $events->listen(
            Event::addReportingRevenueInstaller,
            'App\Listeners\RegistrationFlow\Listener@addReportingRevenueInstaller'
        );
    }

    /**
     * @return [type]
     */
    public function failed()
    {
        // Need to handle there
    }
}
