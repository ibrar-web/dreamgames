<!DOCTYPE html>
<html>

<head>
    <title>JACKS OR BETTER</title>
     <base href="http://renonights.live/" target="_blank">
    <link rel="stylesheet" href="games/game49/css/reset.css" type="text/css">
    <link rel="stylesheet" href="games/game49/css/main.css" type="text/css">
    <link rel="stylesheet" href="games/game49/css/orientation_utils.css" type="text/css">
    <link rel="stylesheet" href="games/game49/css/ios_fullscreen.css" type="text/css">
    <link rel='shortcut icon' type='image/x-icon' href='./favicon.ico' />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,minimal-ui" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="msapplication-tap-highlight" content="no" />
    <script type="text/javascript" src="games/game49/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="games/game49/js/createjs.min.js"></script>
    <script type="text/javascript" src="games/game49/js/screenfull.min.js"></script>
    <script type="text/javascript" src="games/game49/js/platform.js"></script>
    <script type="text/javascript" src="games/game49/js/ios_fullscreen.js"></script>
    <script type="text/javascript" src="games/game49/js/howler.min.js"></script>
    <script type="text/javascript" src="games/game49/js/ctl_utils.js"></script>
    <script type="text/javascript" src="games/game49/js/sprite_lib.js"></script>
    <script type="text/javascript" src="games/game49/js/settings.js"></script>
    <script type="text/javascript" src="games/game49/js/CLang.js"></script>
    <script type="text/javascript" src="games/game49/js/CPreloader.js"></script>
    <script type="text/javascript" src="games/game49/js/CMain.js"></script>
    <script type="text/javascript" src="games/game49/js/CTextButton.js"></script>
    <script type="text/javascript" src="games/game49/js/CGfxButton.js"></script>
    <script type="text/javascript" src="games/game49/js/CToggle.js"></script>
    <script type="text/javascript" src="games/game49/js/CMenu.js"></script>
    <script type="text/javascript" src="games/game49/js/CGame.js"></script>
    <script type="text/javascript" src="games/game49/js/CInterface.js"></script>
    <script type="text/javascript" src="games/game49/js/CGameSettings.js"></script>
    <script type="text/javascript" src="games/game49/js/CCard.js"></script>
    <script type="text/javascript" src="games/game49/js/CGameOver.js"></script>
    <script type="text/javascript" src="games/game49/js/CPayTable.js"></script>
    <script type="text/javascript" src="games/game49/js/CPayTableSettings.js"></script>
    <script type="text/javascript" src="games/game49/js/CHandEvaluator.js"></script>
    <script type="text/javascript" src="games/game49/js/CCreditsPanel.js"></script>
    <script type="text/javascript" src="games/game49/js/CCTLText.js"></script>

</head>

<body ondragstart="return false;" ondrop="return false;">
    <div style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%"></div>
    <script>
            var user_id='<?php echo $user_id ;?>';
        $(document).ready(function() {
            $.post("detail", {
                _token: $('meta[name=csrf-token]').attr('content'),
                id: 49,
                user_id:user_id
            }, function(t) {
                load(t);
            })

            function load(t) {
                var oMain = new CMain({
                    win_occurrence: JSON.parse(t.game[0]['win']), //WIN OCCURRENCE PERCENTAGE
                    game_cash: 100, //MONEY IN GAME CASH. IF THE GAME DOESN'T HAVE ENOUGHT MONEY, THE PLAYER MUST LOSE.
                    bets: [0.2, 0.3, 0.5, 1, 2, 3, 5], //ALL THE AVAILABLE BETS FOR THE PLAYER
                    combo_prizes: [250, 50, 25, 9, 6, 4, 3, 2, 1], //WINS FOR FIRST COLUMN
                    money: JSON.parse(t.data[0]['amount']), //STARING CREDIT FOR THE USER
                    recharge: false, //RECHARGE WHEN MONEY IS ZERO. SET THIS TO FALSE TO AVOID AUTOMATIC RECHARGE
                    audio_enable_on_startup: false, //ENABLE/DISABLE AUDIO WHEN GAME STARTS 
                    fullscreen: true, //SET THIS TO FALSE IF YOU DON'T WANT TO SHOW FULLSCREEN BUTTON
                    check_orientation: true, //SET TO FALSE IF YOU DON'T WANT TO SHOW ORIENTATION ALERT ON MOBILE DEVICES
                    show_credits: true, //ENABLE/DISABLE CREDITS BUTTON IN THE MAIN SCREEN
                    num_hand_before_ads: 10 //NUMBER OF HANDS PLAYED BEFORE AD SHOWN
                    //
                    //// THIS FUNCTIONALITY IS ACTIVATED ONLY WITH CTL ARCADE PLUGIN.///////////////////////////
                    /////////////////// YOU CAN GET IT AT: /////////////////////////////////////////////////////////
                    // http://codecanyon.net/item/ctl-arcade-wordpress-plugin/13856421 ///////////
                });

                $(oMain).on("recharge", function(evt) {
                    if (s_oGame && !AUTOMATIC_RECHARGE) {
                        var iMoney = 100;
                        s_oGame.recharge(iMoney);
                    }
                    //alert("recharge");
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

                $(oMain).on("bet_placed", function(evt, iBet) {

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

                $(oMain).on("show_interlevel_ad", function(evt) {
                    if (getParamValue('ctl-arcade') === "true") {
                        parent.__ctlArcadeShowInterlevelAD();
                    }
                    //...ADD YOUR CODE HERE EVENTUALLY
                });

                $(oMain).on("share_event", function(evt, iScore) {
                    if (getParamValue('ctl-arcade') === "true") {
                        parent.__ctlArcadeShareEvent({
                            img: "200x200.jpg",
                            title: TEXT_CONGRATULATIONS,
                            msg: TEXT_SHARE_1 + iScore + TEXT_SHARE_2,
                            msg_share: TEXT_SHARE_3 + iScore + TEXT_SHARE_4
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
            }
        });
    </script>

    <div class="check-fonts">
        <p class="check-font-1">test 1</p>
        <p class="check-font-2">test 2</p>
    </div>


    <canvas id="canvas" class='ani_hack' width="1920" height="768"> </canvas>
    <div data-orientation="landscape" class="orientation-msg-container">
        <p class="orientation-msg-text">Please rotate your device</p>
    </div>
    <div id="block_game" style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%; display:none"></div>
</body>

</html>