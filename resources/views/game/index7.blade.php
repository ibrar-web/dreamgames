<!DOCTYPE html>
<html>

<head>
    <title>3 CARDS MONTE</title>
     <base href="http://renonights.live/" target="_blank">
    <link rel="stylesheet" href="game7/css/reset.css" type="text/css">
    <link rel="stylesheet" href="game7/css/main.css" type="text/css">
    <link rel="stylesheet" href="game7/css/ios_fullscreen.css" type="text/css">
    <link rel="stylesheet" href="game7/css/orientation_utils.css" type="text/css">
    <link rel='shortcut icon' type='image/x-icon' href='./game7/favicon.ico' />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, minimal-ui" />
    <meta name="msapplication-tap-highlight" content="no" />

    <script type="text/javascript" src="game7/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="game7/js/createjs.min.js"></script>
    <script type="text/javascript" src="game7/js/howler.min.js"></script>
    <script type="text/javascript" src="game7/js/screenfull.js"></script>
    <script type="text/javascript" src="game7/js/platform.js"></script>
    <script type="text/javascript" src="game7/js/ios_fullscreen.js"></script>
    <script type="text/javascript" src="game7/js/ctl_utils.js"></script>
    <script type="text/javascript" src="game7/js/sprite_lib.js"></script>
    <script type="text/javascript" src="game7/js/settings.js"></script>
    <script type="text/javascript" src="game7/js/CLang.js"></script>
    <script type="text/javascript" src="game7/js/CPreloader.js"></script>
    <script type="text/javascript" src="game7/js/CMain.js"></script>
    <script type="text/javascript" src="game7/js/CTextButton.js"></script>
    <script type="text/javascript" src="game7/js/CToggle.js"></script>
    <script type="text/javascript" src="game7/js/CGfxButton.js"></script>
    <script type="text/javascript" src="game7/js/CMenu.js"></script>
    <script type="text/javascript" src="game7/js/CGame.js"></script>
    <script type="text/javascript" src="game7/js/CInterface.js"></script>
    <script type="text/javascript" src="game7/js/CHelpPanel.js"></script>
    <script type="text/javascript" src="game7/js/CEndPanel.js"></script>
    <script type="text/javascript" src="game7/js/CCard.js"></script>
    <script type="text/javascript" src="game7/js/CCreditsPanel.js"></script>
    <script type="text/javascript" src="game7/js/CCTLText.js"></script>

</head>

<body ondragstart="return false;" ondrop="return false;">
    <meta name="csrf-token" content="{{ Session::token() }}">
    <div style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%"></div>
    <script>
        var user_id = '<?php echo $user_id; ?>';
        $(document).ready(function() {

            var oMain = new CMain({

                points_to_win: 1, //Number of win points   
                start_num_shuffle: 5, //Starting number of shuffle in a level
                num_level_to_increase_num_shuffle: 2, //Levels to play to increase by 1 the number of shuffle
                start_timespeed_shuffle: 800, //Starting time (in ms) to shuffle 2 cards
                decrease_timespeed_shuffle: -50, //Decrease starting timespeed to shuffle 2 cards every level (in ms)
                minimum_timespeed: 200, //Minimum time speed limit 
                audio_enable_on_startup: false, //ENABLE/DISABLE AUDIO WHEN GAME STARTS 
                show_credits: true,
                fullscreen: true, //SET THIS TO FALSE IF YOU DON'T WANT TO SHOW FULLSCREEN BUTTON
                check_orientation: true //SET TO FALSE IF YOU DON'T WANT TO SHOW ORIENTATION ALERT ON MOBILE DEVICES
            });

            $(oMain).on("start_session", function(evt) {
                if (getParamValue('ctl-arcade') === "true") {
                    parent.__ctlArcadeStartSession();
                }
                //...ADD YOUR CODE HERE EVENTUALLY
            });

            $(oMain).on("end_session", function(evt) {
                if (getParamValue('ctl-arcade') === "true") {
                    parent.__ctlArcadeEndSession();
                }
                //...ADD YOUR CODE HERE EVENTUALLY
            });

            $(oMain).on("restart_level", function(evt, iLevel) {
                if (getParamValue('ctl-arcade') === "true") {
                    parent.__ctlArcadeRestartLevel({
                        level: iLevel
                    });
                }
                //...ADD YOUR CODE HERE EVENTUALLY
            });

            $(oMain).on("save_score", function(evt, iScore) {
                if (getParamValue('ctl-arcade') === "true") {
                    parent.__ctlArcadeSaveScore({
                        score: iScore
                    });
                }
                //...ADD YOUR CODE HERE EVENTUALLY
            });

            $(oMain).on("start_level", function(evt, iLevel) {
                if (getParamValue('ctl-arcade') === "true") {
                    parent.__ctlArcadeStartLevel({
                        level: iLevel
                    });
                }
                //...ADD YOUR CODE HERE EVENTUALLY
            });

            $(oMain).on("end_level", function(evt, iLevel) {
                if (getParamValue('ctl-arcade') === "true") {
                    parent.__ctlArcadeEndLevel({
                        level: iLevel
                    });
                }
                //...ADD YOUR CODE HERE EVENTUALLY
            });

            $(oMain).on("show_interlevel_ad", function(evt) {
                if (getParamValue('ctl-arcade') === "true") {
                    parent.__ctlArcadeShowInterlevelAD();
                }
                //...ADD YOUR CODE HERE EVENTUALLY
            });

            $(oMain).on("share_event", function(evt, iScore, szImage, szTitle, szMsg, szMsgShare) {
                if (getParamValue('ctl-arcade') === "true") {
                    parent.__ctlArcadeShareEvent({
                        img: szImage,
                        title: szTitle,
                        msg: szMsg,
                        msg_share: szMsgShare
                    });
                }
                //...ADD YOUR CODE HERE EVENTUALLY
            });

            if (isIOS()) {
                setTimeout(function() {
                    sizeHandler();
                }, 200);
            } else {
                sizeHandler();
            }
        });
    </script>

    <canvas id="canvas" class='ani_hack' width="1920" height="768"> </canvas>
    <div data-orientation="landscape" class="orientation-msg-container">
        <p class="orientation-msg-text">Please rotate your device</p>
    </div>
    <div id="block_game" style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%; display:none"></div>

</body>

</html>