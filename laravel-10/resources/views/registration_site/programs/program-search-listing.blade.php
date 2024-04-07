<style>
    .event-prgrams-ajax {
        float: left;
        width: 100%;
        margin-top: 10px;
    }

    .post-list li.w-146 {
        width: 180px;
        padding: 0 19px 32px 0;
        margin: 0 -4px 0 0;
    }

    .post-bocks:last-child .post-list li,
    .post-bocks .post-list li {
        max-width: 180px;
        width: 180px;
    }
    .program-location-span {
        background: url(https://my.eventbuizz.com/_admin_assets/images/location.svg) no-repeat 1px 3px !important;
        padding-left: 18px;
        margin-top: 10px;
        float: left;
    }

</style>

@foreach ($programs as $date => $program)
    <div class="post--holder">
        @if ($program)

            @if (!request()->program_id)
                <div class="post-header">
                    <time>{{ getEventDateFormat($event['id'], $event["language_id"], "program_search", $date)  }}</time>
                </div>
            @endif

            @foreach ($program as $program)
                @if (isset($program['program_workshop']) && $program['program_workshop'] && !request()->program_id)
                    <div class="group-post-container">
                        <div style="background: #f7f7f7" class="group-posts {{ ($input['query'] || $program_setting['agenda_collapse_workshop'] == 0) ? 'open' : '' }}">
                            <div class="group-post-header">
                                <label class="time-box">
                                    <div class="rs-timer">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                            xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px"
                                            width="25px" height="25px" viewBox="0 0 20 20"
                                            enable-background="new 0 0 20 20" xml:space="preserve">
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
                                        {{ substr(date('H:i', strtotime($program['end_time'])), 0, 5) }}</time>
                                </label>
                                <h2 style="margin: 0px">{!! $program['program_workshop'] !!}</h2>
                                <a href="javascript:;" class="workshop_anchor" data-workshop="{{ $program['id'] }}" data-operation="{{ ($input['query'] || $program_setting['agenda_collapse_workshop'] == 0) ? 'close' : 'open' }}">
                                    @if(($input['query'] || $program_setting['agenda_collapse_workshop'] == 0))
                                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="25" viewBox="0 0 55.493 32.165"><g data-name="Group 12" transform="translate(750.378 1857.927) rotate(180)"><line data-name="Line 3" x1="22.635" y2="22.266" transform="translate(699.835 1830.711)" fill="none" stroke="{{ $event['settings']['secondary_color'] }}" stroke-linecap="round" stroke-linejoin="bevel" stroke-width="7"></line><line data-name="Line 4" x2="22.635" y2="22.266" transform="translate(722.794 1830.711)" fill="none" stroke="{{ $event['settings']['secondary_color'] }}" stroke-linecap="round" stroke-linejoin="bevel" stroke-width="7"></line></g></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="35" viewBox="0 0 32.166 55.493">
                                            <g data-name="Group 12" transform="translate(1857.927 -694.885) rotate(90)">
                                                <line data-name="Line 3" x1="22.635" y2="22.266"
                                                    transform="translate(699.835 1830.711)" fill="none" stroke="{{ $event['settings']['secondary_color'] }}"
                                                    stroke-linecap="round" stroke-linejoin="bevel" stroke-width="7"></line>
                                                <line data-name="Line 4" x2="22.635" y2="22.266"
                                                    transform="translate(722.794 1830.711)" fill="none" stroke="{{ $event['settings']['secondary_color'] }}"
                                                    stroke-linecap="round" stroke-linejoin="bevel" stroke-width="7"></line>
                                            </g>
                                        </svg>
                                    @endif
                                </a>
                            </div>
                            <div class="program-accordian" id="{{ $program['id'] }}_program-accordian">
                                @if(isset($program['workshop_programs']['data']) && count((array)$program['workshop_programs']['data']) > 0)
                                    @foreach ($program['workshop_programs']['data'] as $row)
                                        @include('registration_site.programs.section.program-search-listing', ['program' => $row])
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                   @include('registration_site.programs.section.program-search-listing', ['program' => $program])
                @endif
            @endforeach
            
        @else
            <h3 class="no-program-found">{{ $event['labels']['EVENT_NOPROGRAM_FOUND'] }}</h3>
        @endif
    </div>
@endforeach
