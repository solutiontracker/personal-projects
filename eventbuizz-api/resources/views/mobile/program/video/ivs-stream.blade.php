<html lang="en">
<!-- # Copyright Amazon.com, Inc. or its affiliates. All Rights Reserved. -->
<!-- # SPDX-License-Identifier: MIT-0 -->

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Amazon IVS Video.js Integration demo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.6.6/video-js.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.6.6/video.min.js"></script>
    <script src="https://player.live-video.net/1.3.1/amazon-ivs-videojs-tech.min.js"></script>
    <script src="https://player.live-video.net/1.3.1/amazon-ivs-quality-plugin.min.js"></script>

    <style>
        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", sans-serif;
            background: #F2F2F8;
            display: grid;
            place-items: center;
        }

        .player-wrapper {
            width: 100%;
            position: relative;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.3);
            background: black;
        }

    </style>
</head>

<body>

    <div class="player-wrapper">
        <video id="amazon-ivs-videojs" class="video-js vjs-fluid vjs-big-play-centered" controls autoplay
            playsinline></video>
    </div>

</body>
<script>
    const playbackUrl = "{{ $src }}";

    // Initialize player
    (function() {
        // Set up IVS playback tech and quality plugin
        registerIVSTech(videojs);
        registerIVSQualityPlugin(videojs);

        // Initialize video.js player
        const videoJSPlayer = videojs("amazon-ivs-videojs", {
            techOrder: ["AmazonIVS"],
            controlBar: {
                playToggle: {
                    replay: false
                }, // Hides the replay button for VOD
                pictureInPictureToggle: false // Hides the PiP button
            }
        });

        // Use the player API once the player instance's ready callback is fired
        const readyCallback = function() {
            // This executes after video.js is initialized and ready
            window.videoJSPlayer = videoJSPlayer;

            // Get reference to Amazon IVS player
            const ivsPlayer = videoJSPlayer.getIVSPlayer();

            // Show the "big play" button when the stream is paused
            const videoContainerEl = document.querySelector("#amazon-ivs-videojs");
            videoContainerEl.addEventListener("click", () => {
                if (videoJSPlayer.paused()) {
                    videoContainerEl.classList.remove("vjs-has-started");
                } else {
                    videoContainerEl.classList.add("vjs-has-started");
                }
            });

            // Logs low latency setting and latency value 5s after playback starts
            const PlayerState = videoJSPlayer.getIVSEvents().PlayerState;
            ivsPlayer.addEventListener(PlayerState.PLAYING, () => {
                console.log("Player State - PLAYING");
                setTimeout(() => {
                    console.log(
                        `This stream is ${
                  ivsPlayer.isLiveLowLatency() ? "" : "not "
              }playing in ultra low latency mode`
                    );
                    console.log(`Stream Latency: ${ivsPlayer.getLiveLatency()}s`);
                }, 5000);
            });

            // Log errors
            const PlayerEventType = videoJSPlayer.getIVSEvents().PlayerEventType;
            ivsPlayer.addEventListener(PlayerEventType.ERROR, (type, source) => {
                console.warn("Player Event - ERROR: ", type, source);
            });

            // Log and display timed metadata
            ivsPlayer.addEventListener(PlayerEventType.TEXT_METADATA_CUE, (cue) => {
                const metadataText = cue.text;
                const position = ivsPlayer.getPosition().toFixed(2);
                console.log(
                    `Player Event - TEXT_METADATA_CUE: "${metadataText}". Observed ${position}s after playback started.`
                );
            });

            // Enables manual quality selection plugin
            videoJSPlayer.enableIVSQualityPlugin();

            // Set volume and play default stream
            videoJSPlayer.volume(0.5);
            videoJSPlayer.src(playbackUrl);
        };

        // Register ready callback
        videoJSPlayer.ready(readyCallback);
    })();
</script>

</html>
