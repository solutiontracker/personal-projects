<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Http\Resources\Attendee as AttendeeResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendeeController extends Controller
{
    public $successStatus = 200;

    protected $attendeeRepository;

    public function __construct(AttendeeRepository $attendeeRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
    }

    public function getAttendees(Request $request, $slug)
    {
        $request->merge(['limit' =>  $request->limit ? $request->limit : 10]); 
        $attendees = $this->attendeeRepository->getFrontEventAttendees($request->all()); 
        return AttendeeResource::collection($attendees);
    }
    
    public function getAttendee(Request $request, $slug, $id)
    {
        $attendee = $this->attendeeRepository->getFrontEventAttendee($request->all(), $id);
 
        if ($attendee->currentEventAttendee->speaker == 1) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($request->event_id, 'speaker', $request->language_id);
                $attendee['labels'] = AttendeeRepository::getAttendeeFieldsByAttendeeType($attendee_type_id, $request->event_id, $request->language_id);
		} elseif ($attendee->currentEventAttendee->exhibitor == 1) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($request->event_id, 'exhibitor', $request->language_id);
                $attendee['labels'] = AttendeeRepository::getAttendeeFieldsByAttendeeType($attendee_type_id, $request->event_id, $request->language_id);
		} elseif ($attendee->currentEventAttendee->sponser == 1) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($request->event_id, 'sponsor', $request->language_id);
                $attendee['labels'] = AttendeeRepository::getAttendeeFieldsByAttendeeType($attendee_type_id, $request->event_id, $request->language_id);
		} else {
            $attendee['labels'] = AttendeeRepository::getAttendeeFieldsByAttendeeType($attendee->currentEventAttendee->attendee_type, $request->event_id, $request->language_id);
		}

        return AttendeeResource::collection([$attendee])[0];
    }
    
    public function getSpeakers(Request $request, $slug)
    {
        $request->merge(['limit' =>  $request->limit ? $request->limit : 10]); 
        $speakers = $this->attendeeRepository->getEventSpeakers($request->all());
        return AttendeeResource::collection($speakers);
    }
    
    public function getSpeaker(Request $request, $slug, $id)
    {
        $speaker = $this->attendeeRepository->getEventSpeaker($request->all(), $id);

        if ($speaker->currentEventAttendee->speaker == 1) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($request->event_id, 'speaker', $request->language_id);
                $speaker['labels'] = AttendeeRepository::getAttendeeFieldsByAttendeeType($attendee_type_id, $request->event_id, $request->language_id);
		} elseif ($speaker->currentEventAttendee->exhibitor == 1) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($request->event_id, 'exhibitor', $request->language_id);
                $speaker['labels'] = AttendeeRepository::getAttendeeFieldsByAttendeeType($attendee_type_id, $request->event_id, $request->language_id);
		} elseif ($speaker->currentEventAttendee->sponser == 1) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($request->event_id, 'sponsor', $request->language_id);
                $speaker['labels'] = AttendeeRepository::getAttendeeFieldsByAttendeeType($attendee_type_id, $request->event_id, $request->language_id);
		} else {
            $speaker['labels'] = AttendeeRepository::getAttendeeFieldsByAttendeeType($attendee->currentEventAttendee->attendee_type, $request->event_id, $request->language_id);
		}
         
        return response()->json([
            'data' => $speaker,
        ]);
    }
    
    public function attendeeProfile(Request $request, $slug)
    {
        $attendeeProfile = $this->attendeeRepository->getFrontEventAttendeeProfile($request->all(), $request->user()->id); 
        
        $attendeeProfile['settings'] = AttendeeRepository::getAttendeeTypeSettingsWithSort($request->event_id, $attendeeProfile['attendee']['current_event_attendee']['attendee_type']);
        
        $attendeeProfile['labels'] = AttendeeRepository::getAttendeeFieldsByAttendeeType($attendeeProfile['attendee']['current_event_attendee']['attendee_type'], $request->event_id, $request->language_id);

        return response()->json([
            'data' => $attendeeProfile,
        ]);
    }
    
    public function attendeeUpdateProfile(Request $request, $slug)
    {
        $attendeeProfile = $this->attendeeRepository->updateFrontEventAttendeeProfile($request->all(), $request->user()->id);         
        return response()->json([
            'data' => $attendeeProfile,
        ]);
    }
    
    public function getNewsletterSubscription(Request $request, $slug) {
        
        $subscriptions = $this->attendeeRepository->getNewsletterSubscription($request->all(), $request->user()->id);
        return response()->json([
            'data' => $subscriptions,
        ]);
    }
    
    public function updateNewsletterSubscription(Request $request, $slug) {
        
        $update = $this->attendeeRepository->updateNewsletterSubscription($request->all(), $request->user()->id);
        return response()->json([
            'success' => true,
        ]);
    }
    
    public function getbillingProfile(Request $request, $slug, $id ) {
        
        $surveyListing = $this->attendeeRepository->getbillingProfile($request->all(), $id);
        return response()->json([
            'success' => true,
            'data'=> $surveyListing
        ]);
    }
    
    public function attendeeNotAttending(Request $request, $slug) {
        
        $response = $this->attendeeRepository->attendeeNotAttending($request->all());

        return response()->json($response);
        
    }
    
    /**
     * validateAttendee
     *
     * @param  mixed $request
     * @param  mixed $slug
     * @param  mixed $attendee_id
     * @param  mixed $verification_id
     * @return void
     */
    public function validateAttendee(Request $request, $slug, $verification_id, $attendee_id) {

        $response = $this->attendeeRepository->validateAttendee($request->all(), $attendee_id, $verification_id);

        return response()->json($response);
    }

}
