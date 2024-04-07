<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class SocialMediaRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     *SocialMedia clone/default
     *
     * @param array
     */
    public function install($request)
    {
        $setting = \App\Models\SocialMedia::where('event_id', $request['from_event_id'])->get();
        if (count($setting)) {
            foreach ($setting as $record) {
                $record = $record->replicate();
                $record->event_id = $request['to_event_id'];
                $record->save();
            }
        } else {
            $settings = array('twitter' => '', 'twitter_hash_tag' => '', 'facebook' => '', 'flickr' => '', 'linkedin' => '', 'youtube' => '', 'vimeo' => '', 'rss' => '', 'instagram' => '', 'gplus' => '', 'pinterest' => '');

            $social_media_instance = new \App\Models\SocialMedia();

            if (count($settings) > 0) {
                $event_module_settings = $social_media_instance::where('event_id', '=', $request['to_event_id'])->get()->toArray();
                foreach ($settings as $name => $value) {
                    $found = false;
                    foreach ($event_module_settings as $setting) {
                        if ($name == $setting['name']) {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $setting = array();
                        $setting['event_id'] =  $request['to_event_id'];
                        $setting['select_type'] =  'http';
                        $setting['sort_order'] =  0;
                        $setting['name'] = $name;
                        $setting['value'] = $value;
                        $social_media_instance->create($setting);
                    }
                }
            }
        }
    }

    /**
     * Update social media networks
     * @param array
     */
    static public function updateSocialMedia($formInput)
    {
        //Facebook
        $fb = parse_url($formInput['facebook']);
        $fb_scheme = (isset($fb['scheme']) && $fb['scheme'] ? $fb['scheme']. '://' : 'http://');
        $fb_url = (isset($fb['scheme']) && $fb['scheme'] ? $fb['host'] . $fb['path'] : $fb['path']);
        \App\Models\SocialMedia::where('event_id', $formInput['event_id'])->where('name', 'facebook')->update([
            "value" => $fb_url,
            "select_type" => $fb_scheme,
        ]);

        //Twitter
        $tw = parse_url($formInput['twitter']);
        $tw_scheme = (isset($tw['scheme']) && $tw['scheme'] ? $tw['scheme'] . '://' : 'http://');
        $tw_url = (isset($tw['scheme']) && $tw['scheme'] ? $tw['host'] . $tw['path'] : $tw['path']);
        \App\Models\SocialMedia::where('event_id', $formInput['event_id'])->where('name', 'twitter')->update([
            "value" => $tw_url,
            "select_type" => $tw_scheme,
        ]);

        //Google+
        $gplus = parse_url($formInput['gplus']);
        $gplus_scheme = (isset($gplus['scheme']) && $gplus['scheme'] ? $gplus['scheme'] . '://' : 'http://');
        $gplus_url = (isset($gplus['scheme']) && $gplus['scheme'] ? $gplus['host'] . $gplus['path'] : $gplus['path']);
        \App\Models\SocialMedia::where('event_id', $formInput['event_id'])->where('name', 'gplus')->update([
            "value" => $gplus_url,
            "select_type" => $gplus_scheme,
        ]);

        //Pinterest
        $pinterest = parse_url($formInput['pinterest']);
        $pinterest_scheme = (isset($pinterest['scheme']) && $pinterest['scheme'] ? $pinterest['scheme'] . '://' : 'http://');
        $pinterest_url = (isset($pinterest['scheme']) && $pinterest['scheme'] ? $pinterest['host'] . $pinterest['path'] : $pinterest['path']);
        \App\Models\SocialMedia::where('event_id', $formInput['event_id'])->where('name', 'pinterest')->update([
            "value" => $pinterest_url,
            "select_type" => $pinterest_scheme,
        ]);

        //Linkedin
        $linkedin = parse_url($formInput['linkedin']);
        $linkedin_scheme = (isset($linkedin['scheme']) && $linkedin['scheme'] ? $linkedin['scheme'] . '://' : 'http://');
        $linkedin_url = (isset($linkedin['scheme']) && $linkedin['scheme'] ? $linkedin['host'] . $linkedin['path'] : $linkedin['path']);
        \App\Models\SocialMedia::where('event_id', $formInput['event_id'])->where('name', 'linkedin')->update([
            "value" => $linkedin_url,
            "select_type" => $linkedin_scheme,
        ]);
    }

    /**
     * Fetch social media networks
     * @param array
     */
    static public function fetchSocialMedia($formInput)
    {
        return \App\Models\SocialMedia::where('event_id', $formInput['event_id'])->orderBy('sort_order')->get();
    }
}
