 @if(count($tracks) > 0)
    @foreach($tracks AS $track)
        <li><a href="{{config('app.eventcenter_url') . '/event/'. $event['url']. '/agendas_by_track_detail/'.$track['id'].$isFavorite}}">
            <div class="track_tag" style="background:<?php echo $track['color']?>"><?php echo $track['name']; ?></div>
            </a>
        </li>
@endforeach
@else
 <li class="tracksNoItems">No Tracks Found...</li>
@endif

