<!DOCTYPE html>
<head>
    <title>Zoom Meeting</title>
    <meta charset="utf-8" />
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.8.5/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.8.5/css/react-select.css" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body>
	<script>
	 var meeting_id = '<?php echo $meeting_id; ?>';
	 var password = '<?php echo $password; ?>';
     var userName = '<?php echo $attendee->first_name.' '.$attendee->last_name; ?>';
     var userEmail = '<?php echo $attendee->email; ?>';
	 var signature = '<?php echo $signature; ?>';
     var apiKey = '<?php echo $api_key ?>';
	</script>
    <script src="https://source.zoom.us/1.8.5/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/1.8.5/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/1.8.5/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/1.8.5/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/1.8.5/lib/vendor/jquery.min.js"></script>
    <script src="https://source.zoom.us/1.8.5/lib/vendor/lodash.min.js"></script>
    <script src="https://source.zoom.us/zoom-meeting-1.8.5.min.js"></script>
    {{ HTML::script('plugins/zoom/js/tool.js') }}
    {{ HTML::script('plugins/zoom/js/index.js') }}
</body>
</html>