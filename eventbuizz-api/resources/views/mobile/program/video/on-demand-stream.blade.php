<html lang="en">

<head>
    <link href="https://vjs.zencdn.net/7.14.3/video-js.css" rel="stylesheet" />
    <!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        body {
            overflow: hidden;
        }

    </style>
</head>

<body>
    <input type="hidden" id="thumbnail" value="{{ $thumbnail }}">
    <video id="preview-player" class="video-js vjs-fluid vjs-big-play-centered" controls preload="auto" data-setup="{}">
        <source src="{{ $src }}" type="video/mp4" />
    </video>
    <script src="https://vjs.zencdn.net/7.14.3/video.js"></script>
    <script>
        var thumbnail = document.getElementById("thumbnail").value;
        var player = videojs('preview-player', {
            fluid: true
        });
        player.poster(thumbnail);
    </script>
</body>

</html>
