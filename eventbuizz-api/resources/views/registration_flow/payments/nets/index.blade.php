<!DOCTYPE html>
<html>

<head>
    <title>Nets payment</title>
</head>

<body>
    <div id="checkout-container-div"> </div>
    <script src="{{ (in_array(app()->environment(), ['production']) && $event['id'] != 11378 ? 'https://checkout.dibspayment.eu/v1/checkout.js?v=1' : 'https://test.checkout.dibspayment.eu/v1/checkout.js?v=1') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentId = "{{ $order->transaction_id }}";
            if (paymentId) {
                const checkoutOptions = {
                    checkoutKey: "{{ $event['payment_setting']['nets_app_key'] }}", // Replace!
                    paymentId: paymentId,
                    containerId: "checkout-container-div",
                };
                const checkout = new Dibs.Checkout(checkoutOptions);
                checkout.on('payment-completed', function(response) { });
            } else {
                alert("Expected a paymentId");
            }
        });
    </script>
</body>

</html>
