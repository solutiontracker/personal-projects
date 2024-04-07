<style>
    button {
        position: absolute;
        top: 50%;
        left: 44%;
        width: 232px;
        height: 50px;
        font-size: 18px;
        background: {{$event_settings['primary_color']}};
    }
</style>
<form method="POST" action="{{ route('registration-flow-order-converge-checkout', ['slug' => $event_url, 'order_id' => $order_id]) }}">
    <script
        src="{{$hpp_endpoint}}/client/index.js"
        class="converge-button"
        data-session-id="{{$session}}"
    ></script>
</form>
