
@if(count($subTracks))

    @foreach($subTracks as $track)
    <li><a href="{{config('app.eventcenter_url') . '/event/' . $event['url'] . '/track_agendas_listing/'.$track['track_id'].$isFavorite }}">
            <div class="track_tag"
                 style="background:">{{ $track['name'] }}</div>
        </a>
    </li>
    @endforeach

@else
    <li class="tracksNoItems">No Tracks Found...</li>
@endif


