<?php

namespace App\Http\Controllers\Wizard;

use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\SocialMediaRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Wizard\Requests\EventSetting\BrandingRequest;
use App\Http\Controllers\Wizard\Requests\EventSetting\GdprDisclaimerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class EventSettingController extends Controller
{
    public $successStatus = 200;

    protected $eventSettingRepository;
    protected $eventSiteSettingRepository;

    public function __construct(EventSettingRepository $eventSettingRepository, EventSiteSettingRepository $eventSiteSettingRepository)
    {
        $this->eventSettingRepository = $eventSettingRepository;
        $this->eventSiteSettingRepository = $eventSiteSettingRepository;
    }

    public function getDisclaimer(Request $request)
    {
        $data = $request->all();
        if (!empty($data['event_id'])) {
            return response()->json([
                'success' => true,
                'message' => __('messages.fetch'),
                'data' => [
                    'disclaimer' => $this->eventSettingRepository->getDisclaimer($data['event_id'], $data['language_id']),
                ],
            ], $this->successStatus);
        }
        return response()->json([
            'success' => $this->successStatus,
            'message' => __('messages.fetch'),
            'data' => [
                'disclaimer' => '',
            ],
        ]);
    }

    public function updateDisclaimer(Request $request)
    {
        $data = $request->all();
        $this->eventSettingRepository->updateDisclaimer($data);
        EventRepository::add_module_progress($request->all(), "disclaimer");
        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
            'data' => [
                'disclaimer' => $this->eventSettingRepository->getDisclaimer($data['event_id'], $data['language_id']),
            ],
        ], $this->successStatus);
    }

    public function getGdprDisclaimer(Request $request)
    {
        $data = $request->all();
        if (!empty($data['event_id'])) {
            return response()->json([
                'success' => true,
                'message' => __('messages.fetch'),
                'data' => $this->eventSettingRepository->getGdprDisclaimer($data['event_id']),
            ], $this->successStatus);
        }
        return response()->json([
            'success' => $this->successStatus,
            'message' => __('messages.fetch'),
            'data' => ['subject' => '', 'inline_text' => '', 'description' => ''],
        ]);
    }

    public function updateGdprDisclaimer(GdprDisclaimerRequest $request)
    {
        $data = $request->all();
        $this->eventSettingRepository->updateGdprDisclaimer($data);
        EventRepository::add_module_progress($request->all(), "gdpr-disclaimer");
        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
            'data' => [
                'disclaimer' => $this->eventSettingRepository->getGdprDisclaimer($data['event_id']),
            ],
        ], $this->successStatus);
    }

    public function modules(Request $request)
    {
        if ($request->isMethod('PUT')) {
            $this->eventSettingRepository->updateModules($request->all());

            $modules = $this->eventSettingRepository->modules($request->all(), config('module.app_module_alias'), 'both');

            $module_permissions = array();

            foreach ($modules as $module) {
                $module_permissions[$module['alias']] = $module['status'];
            }
            $directory_sub_modules= \App\Eventbuizz\Repositories\DirectoryRepository::subModules($request->all());
            $defaultDirectory = Arr::first($directory_sub_modules);
            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
                'data' => [
                    "module_permissions" => $module_permissions,
                    "modules" => $modules,
                    "directory_sub_modules" => $directory_sub_modules,
                    "defaultDirectory" => $defaultDirectory
                ],
            ], $this->successStatus);
        } else {
            $modules = $this->eventSettingRepository->modules($request->all(), config('module.app_module_alias'));

            return response()->json([
                'success' => true,
                'data' => [
                    'modules' => $modules,
                ],
            ], $this->successStatus);
        }
    }

    public function modules_setting(Request $request)
    {
        $response = $this->eventSettingRepository->modules_setting($request->all());
        return response()->json([
            'success' => true,
            'data' => [
                'modules_setting' => $response,
            ],
        ], $this->successStatus);
    }

    public function updateUserInterfaceLanguage(Request $request)
    {
        $organizer = \App\Models\Organizer::find(organizer_id());
        if ($organizer && in_array($request->get('interface_language_id'), [1, 2, 3, 4, 5, 6, 7, 8, 9])) {
            $organizer->language_id = $request->get('interface_language_id');
            $organizer->save();
            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);
        }
        return response()->json([
            'success' => false,
            'message' => __('messages.not_exist'),
        ], $this->successStatus);
    }

    public function branding(BrandingRequest $request)
    {
        if ($request->isMethod('POST')) {
            //validate request data
            $request->merge([
                'header_logo' => ($request->header_logo ? $request->header_logo : ""),
                'app_icon' => ($request->app_icon ? $request->app_icon : ""),
                'social_media_logo' => ($request->social_media_logo ? $request->social_media_logo : ""),
                'fav_icon' => ($request->fav_icon ? $request->fav_icon : ""),
                'eventsite_banners' => ($request->hasFile('eventsite_banners') ? $request->eventsite_banners : ""),
                'facebook' => ($request->facebook ? $request->facebook : ""),
                'twitter' => ($request->twitter ? $request->twitter : ""),
                'gplus' => ($request->gplus ? $request->gplus : ""),
                'pinterest' => ($request->pinterest ? $request->pinterest : ""),
                'linkedin' => ($request->linkedin ? $request->linkedin : ""),
            ]);

            $this->eventSettingRepository->updateBranding($request->all());
            $this->eventSiteSettingRepository->updateEventSiteBanners($request->all());
            SocialMediaRepository::updateSocialMedia($request->all());
            EventRepository::add_module_progress($request->all(), "branding");

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);
        }
    }

    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function getModule(Request $request, $module)
    {
        $request->merge(["alias" => $module]);

        $module = $this->eventSettingRepository->getEventModule($request->all());

        return response()->json([
            'success' => true,
            'data' => array(
                "module" => $module,
            ),
        ], $this->successStatus);
    }
}
