<script charset="UTF-8" src="https://ssl.ditonlinebetalingssystem.dk/integration/ewindow/paymentwindow.js" type="text/javascript"></script>
<div id="payment-div"></div>
<script type="text/javascript">
		paymentwindow = new PaymentWindow({
		<?php
		foreach ($params as $key => $value)
		{
			echo "'" . $key . "': \"" . $value . "\",\n";
		} ?>
		'hash': "<?php echo md5(implode("", array_values($params)) . $bambora_secret_key); ?>"
	});
	paymentwindow.append('payment-div');
	paymentwindow.on('completed', function(params){ });
	paymentwindow.on('declined', function(){ });
	paymentwindow.on('close', function(){ });
	paymentwindow.open();
</script>