<div class="chattext {{$chat->sendBy == 'attendee' ? 'other' : ''}}">
    {{ $chat->message }} <span class="time">{{ \Carbon\Carbon::parse($chat->created_at)->format('H:i A') }}</span>
</div>