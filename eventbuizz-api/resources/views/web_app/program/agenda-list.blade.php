@if ($programs)
    @foreach ($programs as $date => $program)
       @if($prevDate === "" || $prevDate !== $date)
        <li data-role="list-divider"><span
                style="text-transform: capitalize;">{{ getEventDateFormat($event['id'], $event['language_id'], 'mobile_site_program_listing_day_date', $date) }}</span>
            {{ getEventDateFormat($event['id'], $event['language_id'], 'mobile_site_program_listing_time_date', $date) }}
        </li>
       @endif
        @foreach ($program as $program)
            @if (isset($program['program_workshop_web_app']) && $program['program_workshop_web_app'])
                <li style="min-height: 59px;">
                    <a style="padding-top:0px !important; padding-bottom:0px !important; padding-left:55px;"
                        href="{{ config('app.eventcenter_url') . '/event/' . $event['url'] . '/agendas?default=true&workshop=1&workshop_id=' . $program['id'] . '&fav=' . $favs.$isTrack }}">
                        <h2>{!! $program['program_workshop_web_app'] !!}</h2>
                        <p>
                            @if ($program_setting['agenda_display_time'] === 1)
                                {{ substr(date('H:i', strtotime($program['start_time'])), 0, 5) }} -
                                {{ substr(date('H:i', strtotime($program['end_time'])), 0, 5) }}
                                <br />
                            @endif
                        </p>
                    </a>
                </li>
            @else
                <li style="min-height: 59px;">
                    <a style="padding-top:0px !important; padding-bottom:0px !important; padding-left:55px;"
                        @if ($myturnlist === 'true') 
                        href="{{ config('app.eventcenter_url') . '/event/' . $event['url'] . '/index.php?mod=myturnlist&func=agendaDetail&agendaId=' . $program['id'].$isTrack }}" 
                        @else
                                
                        href="{{ config('app.eventcenter_url') . '/event/' . $event['url'] . '/agendas_detail/' . $program['id'] .$isTrack }}" 
                        @endif >
                        @if ($program_setting['admin_fav_attendee'] == 1)
                            @if (in_array($program['id'], $agenda_array))
                                <img data-id="{{ $program['id'] }}"
                                    src="{{ config('app.eventcenter_url') . '/_mobile_assets/images/fav-icon-selected.png' }}"
                                    height="32" width="32" />
                            @elseif (!in_array($program['id'], $agenda_by_group_array))
                                <img data-id="{{ $program['id'] }}"
                                    src="{{ config('app.eventcenter_url') . '/_mobile_assets/images/fav-icon-unselected@2x.png' }}"
                                    height="32" width="32" />
                            @endif
                        @endif
                        <h2>{!! $program['topic'] !!}</h2>
                        <p>
                            @if ($program['hide_time'] === 0 && $program_setting['agenda_display_time'] === 1)
                                {{ substr(date('H:i', strtotime($program['start_time'])), 0, 5) }} -
                                {{ substr(date('H:i', strtotime($program['end_time'])), 0, 5) }}
                            @endif
                            <br />
                            @if ($program['workshop_program']['data']['location'])
                                <span
                                    style="color:#999490">{{ $program['workshop_program']['data']['location'] }}</span>
                            @endif
                        </p>

                        @if ($program_setting['show_tracks'] == 1)
                            <ul class="cs-list">
                                @foreach ($program['program_tracks'] as $track)
                                    <li>
                                        <span class="selectColor"
                                            style=" background-color:{{ $track['color'] !== '' ? $track['color'] : '#000000' }};">
                                        </span>
                                        <span class="rightListInn">{{ $track['name'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </a>
                </li>
            @endif
        @endforeach
    @endforeach
@else
    <h3 class="no-program-found">{{ $event['labels']['GENERAL_NO_RECORD'] }}</h3>
@endif
