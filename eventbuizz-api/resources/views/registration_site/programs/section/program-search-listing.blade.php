<div class="post-bocks">
    @if ($program_setting['agenda_display_time'] == '1' && $program['hide_time'] == 0)
        <div style="width: 165px;padding: 0;" class="time-box ">
            <div class="rs-timer">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px"
                    width="25px" height="25px" viewBox="0 0 20 20" enable-background="new 0 0 20 20"
                    xml:space="preserve">
                    <defs>
                    </defs>
                    <g>
                        <path fill="{{ $event['settings']['secondary_color'] }}" d="M10,0C4.477,0,0,4.477,0,10s4.477,10,10,10s10-4.477,10-10S15.523,0,10,0z M10,18c-4.411,0-8-3.589-8-8
s3.589-8,8-8s8,3.589,8,8S14.411,18,10,18z" />
                        <path fill="{{ $event['settings']['secondary_color'] }}" d="M11,9.464V5c0-0.552-0.448-1-1-1S9,4.448,9,5v5c0,0.422,0.263,0.779,0.633,0.926l1.781,1.781
c0.391,0.391,1.024,0.391,1.414,0s0.391-1.024,0-1.414L11,9.464z" />
                    </g>
                </svg>
            </div>
            <time>{{ substr(date('H:i', strtotime($program['start_time'])), 0, 5) }} -
                {{ substr(date('H:i', strtotime($program['end_time'])), 0, 5) }} </time>
        </div>
    @endif
    <div class="post-content ebx-break-time" style="{{$program_setting['agenda_display_time'] == '1' && $program['hide_time'] == 0 ? "" :"margin-left:165px"}}">
        <div style="float:left; width: 97%;">
            <h2 style="margin: 0px">{!! html_entity_decode($program['topic']) !!}</h2>
            @if($program['location'])
                <span class="program-location-span">{!! html_entity_decode($program['location']) !!}</span>
            @endif
            @if($program['description'])
                <p style="margin: 0px !important; padding-top: 15px !important; clear:both">{!! html_entity_decode($program['description']) !!}</p>
            @endif
        </div>
        <div class="clear"></div>
        <ul class="tag-list">
            @foreach ($program['program_tracks'] as $track)
                <li>
                    <h3 style="background-color: {{ $track['color'] }}">
                        {{ $track['name'] }}
                    </h3>
                </li>
            @endforeach
        </ul>
        @if (count((array) $program['program_speakers']) > 0)
            <ul class="post-list">
                @foreach ($program['program_speakers'] as $speaker)
                    @if ($speaker['type_resource'] == 0)
                        <li class="w-146">
                            <a
                                href="{{ config('app.eventcenter_url') . '/event/' . $event['url'] . '/detail/speaker_detail/' . $speaker['id'] }}">
                                <div class="img-holder">
                                    @if ($speaker['image'])
                                        <img src="{{ config('app.eventcenter_url') . '/assets/attendees/' . $speaker['image'] }}"
                                            width="105" height="105">
                                    @else
                                        <img src="{{ config('app.eventcenter_url') . '/assets/attendees/no-img.jpg' }}"
                                            width="105" height="105">
                                    @endif
                                </div>
                                <h3> {{ $speaker['first_name'] . ' ' . $speaker['last_name'] }}</h3>
                                @if(($speaker_setting['title'] == '1' && $speaker['info']['title']) || ($speaker_setting['company_name'] == '1' && $speaker['info']['company_name']))
                                    <p>
                                        @if ($speaker_setting['title'] == '1')
                                            {{ $speaker['info']['title'] }}
                                        @endif

                                        @if ($speaker_setting['company_name'] == '1')
                                            <span class="org-heading">{{ $speaker['info']['company_name'] }}</span>
                                        @endif
                                    </p>
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif
    </div>
</div>