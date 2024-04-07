
<style>
	.jconfirm .jconfirm-box {
		padding: 40px 35px 0;
	}
	.post-bocks:last-child .post-list li.w-146 {
		width: 172px;
	}
	.jconfirm .jconfirm-box div.jconfirm-closeIcon {
		font-weight: 100;
		font-size: 40px !important;
		right: 30px;
		top: 20px;
	}
	.jconfirm .jconfirm-box div.jconfirm-closeIcon {
		opacity: 0.6 !important;
	}
</style>
<div style="padding-top: 0" class="post--holder">
    @if ($program)
        <div style="padding: 0 10px" class="post-bocks">
            <div class="post-content">
                <div style="float:left; width: 97%;">
                                @if ($program_setting['agenda_display_time'] == '1' && $program['hide_time'] == 0)
                                        <div style="padding-bottom: 5px" class="ebs-time-box">
                                                <time style="font-size: 15px; color: #444">{{ substr(date('H:i', strtotime($program['start_time'])), 0, 5) }} -
                                                        {{ substr(date('H:i', strtotime($program['end_time'])), 0, 5) }} </time>
                                        </div>
                                @endif
                    <h2 style="margin: 0px; color: #444">{!! html_entity_decode($program['topic']) !!}</h2>
                    @if ($program['description'])
                        <p style="margin: 0px !important; padding-top: 10px !important; color: #444">{!! html_entity_decode($program['description']) !!}</p>
                    @endif
                </div>
                <div class="clear"></div>
                <ul style="padding-top: 20px" class="tag-list">
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
                                        @if (($speaker_setting['title'] == '1' && $speaker['info']['title']) || ($speaker_setting['company_name'] == '1' && $speaker['info']['company_name']))
                                            <p>
                                                @if ($speaker_setting['title'] == '1')
                                                    {{ $speaker['info']['title'] }}
                                                @endif

                                                @if ($speaker_setting['company_name'] == '1')
                                                    <span
                                                        class="org-heading">{{ $speaker['info']['company_name'] }}</span>
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
    @endif
</div>
