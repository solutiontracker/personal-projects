<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Eventbuizz\Repositories\EventRepository;
use App\Mail\Email;
class JiraController extends Controller
{
    public $successStatus = 200;

    protected $eventRepository;

    /**
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param Request $request
     * @param mixed $id
     *
     * @return [type]
     */
    public function createIssue(Request $request)
    {
        $event = $this->eventRepository->getEventDetail($request->all());
        
        $info = readArrayKey($event, [], 'info');

        $client = new Client(['base_uri' => config("services.jira.host")]);

        $assignedAttendees = $this->eventRepository->getAssignedAttendees($request->all(), true);

        $response = $client->request('POST', '/rest/api/2/issue', [
            'body' => json_encode([
                'fields' => [
                    "project" => [
                        "key" => "VD",
                    ],
                    "summary" => $event->name,
                    "issuetype" => [
                        "name" => "Story",
                    ],
                    "customfield_10061" => (string) $event->id,
                    "customfield_10040" => $event->organizer_name,
                    "customfield_10043" => $event->name,
                    "customfield_10062" => $assignedAttendees,
                    "customfield_10059" => \Carbon\Carbon::parse($event->start_date.' '.$event->start_time)->toIso8601String(),
                    "customfield_10060" => \Carbon\Carbon::parse($event->end_date.' '.$event->end_time)->toIso8601String(),
                    "customfield_10045" => $info['location_name'],
                    "customfield_10046" => $info['support_email'],
                ],
            ]),
            'headers' => [
                'Content-Type'     => 'application/json',
            ],
            'auth' => [config("services.jira.username"), config("services.jira.password")]
        ]);

        $content = json_decode($response->getBody(), true);

        //email
		$data = array();
		$data['subject'] = "Virtual app enabled";
		$data['content'] = "Virtal app has been activated.<br><br>".$event->name."<br>".$request->organizer->first_name." ".$request->organizer->last_name."<br><br>https://eventbuizz.atlassian.net/browse/".$content["key"];
		$data['view'] = 'email.plain-text';
		$data['from_name'] =  "Eventbuizz";
		\Mail::to(config('setting.eventbuizz_support_email'))->send(new Email($data));

        return \Response::json($content);
    }
}
