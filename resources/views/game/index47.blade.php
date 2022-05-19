<!DOCTYPE html>
<html>

<head>
    <title>PLINKO</title>
     <base href="http://renonights.live/" target="_blank">
    <link rel="stylesheet" href="games/game48/css/reset.css" type="text/css">
    <link rel="stylesheet" href="games/game48/css/main.css" type="text/css">
    <link rel="stylesheet" href="games/game48/css/orientation_utils.css" type="text/css">
    <link rel='shortcut icon' type='image/x-icon' href='./favicon.ico' />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, minimal-ui" />
    <meta name="msapplication-tap-highlight" content="no" />

    <script type="text/javascript" src="games/game47/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="games/game47/js/createjs.min.js"></script>
    <script type="text/javascript" src="games/game47/js/howler.min.js"></script>
    <script type="text/javascript" src="games/game47/js/platform.js"></script>
    <script type="text/javascript" src="games/game47/js/screenfull.js"></script>
    <script type="text/javascript" src="games/game47/js/sprintf.js"></script>
    <script type="text/javascript" src="games/game47/js/ctl_utils.js"></script>
    <script type="text/javascript" src="games/game47/js/sprite_lib.js"></script>
    <script type="text/javascript" src="games/game47/js/settings.js"></script>
    <script type="text/javascript" src="games/game47/js/CLang.js"></script>
    <script type="text/javascript" src="games/game47/js/CPreloader.js"></script>
    <script type="text/javascript" src="games/game47/js/CMain.js"></script>
    <script type="text/javascript" src="games/game47/js/CTextButton.js"></script>
    <script type="text/javascript" src="games/game47/js/CToggle.js"></script>
    <script type="text/javascript" src="games/game47/js/CGfxButton.js"></script>
    <script type="text/javascript" src="games/game47/js/CMenu.js"></script>
    <script type="text/javascript" src="games/game47/js/CGame.js"></script>
    <script type="text/javascript" src="games/game47/js/CInterface.js"></script>
    <script type="text/javascript" src="games/game47/js/CCreditsPanel.js"></script>
    <script type="text/javascript" src="games/game47/js/CAreYouSurePanel.js"></script>
    <script type="text/javascript" src="games/game47/js/CEndPanel.js"></script>
    <script type="text/javascript" src="games/game47/js/CGridMapping.js"></script>
    <script type="text/javascript" src="games/game47/js/CCell.js"></script>
    <script type="text/javascript" src="games/game47/js/CBall.js"></script>
    <script type="text/javascript" src="games/game47/js/CBallGenerator.js"></script>
    <script type="text/javascript" src="games/game47/js/CInsertTubeController.js"></script>
    <script type="text/javascript" src="games/game47/js/CSlot.js"></script>
    <script type="text/javascript" src="games/game47/js/CScoreBasketController.js"></script>
    <script type="text/javascript" src="games/game47/js/CBasket.js"></script>
    <script type="text/javascript" src="games/game47/js/CGUIExpandible.js"></script>
    <script type="text/javascript" src="games/game47/js/CCTLText.js"></script>

</head>

<body ondragstart="return false;" ondrop="return false;">
    <div style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%"></div>
    <script>
            var user_id='<?php echo $user_id ;?>';
        $(document).ready(function() {
            $.post("detail", {
                _token: $('meta[name=csrf-token]').attr('content'),
                id: 47,
                user_id:user_id
            }, function(t) {
                load(t);
            })

            function load(t) {
                var oMain = new CMain({
                    start_credit: JSON.parse(t.data[0]['amount']), //Starting credits value
                    start_bet: JSON.parse(t.game[0]['minbet']), //Base starting bet. Will increment with multiplier in game
                    max_multiplier: 5, //Max multiplier value

                    bank_cash: 100, //Starting credits owned by the bank. When a player win, founds will be subtract from here. When a player lose or bet, founds will be added here. If bank is 0, players always lose, in order to fill the bank.

                    prize: [0, 20, 100, 50, 0, 10], //THE AMOUNT WON BY THE PLAYER;
                    prize_probability: [10, 8, 1, 4, 10, 10], //THE OCCURENCY WIN OF THAT PRIZE. THE RATIO IS CALCULATED BY THE FORMULA: (single win occurrence/sum of all occurrence). For instance, in this case, prize 100 have 1/43 chance. Prize 50 have 4/43 chance.

                    audio_enable_on_startup: false, //ENABLE/DISABLE AUDIO WHEN GAME STARTS 
                    show_credits: true, //SET THIS VALUE TO FALSE IF YOU DON'T WANT TO SHOW CREDITS BUTTON
                    fullscreen: true, //SET THIS TO FALSE IF YOU DON'T WANT TO SHOW FULLSCREEN BUTTON
                    check_orientation: true, //SET TO FALSE IF YOU DON'T WANT TO SHOW ORIENTATION ALERT ON MOBILE DEVICES   

                    //////////////////////////////////////////////////////////////////////////////////////////
                    ad_show_counter: 5 //NUMBER OF BALL PLAYED BEFORE AD SHOWN
                    //
                    //// THIS FUNCTIONALITY IS ACTIVATED ONLY WITH CTL ARCADE PLUGIN.///////////////////////////
                    /////////////////// YOU CAN GET IT AT: /////////////////////////////////////////////////////////
                    // http://codecanyon.net/item/ctl-arcade-wordpress-plugin/13856421?s_phrase=&s_rank=27 ///////////

                });

                $(oMain).on("recharge", function(evt) {
                    //INSERT HERE YOUR RECHARGE SCRIPT THAT RETURNS MONEY TO RECHARGE
                    var iMoney = 100;
                    if (s_oGame !== null) {
                        s_oGame.addNewCredits(iMoney);
                    }
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

                $(oMain).on("save_score", function(evt, iScore, szMode) {
                    if (getParamValue('ctl-arcade') === "true") {
                        parent.__ctlArcadeSaveScore({
                            score: iScore,
                            mode: szMode
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

                $(oMain).on("share_event", function(evt, iScore) {
                    if (getParamValue('ctl-arcade') === "true") {
                        parent.__ctlArcadeShareEvent({
                            img: TEXT_SHARE_IMAGE,
                            title: TEXT_SHARE_TITLE,
                            msg: TEXT_SHARE_MSG1 + iScore + TEXT_SHARE_MSG2,
                            msg_share: TEXT_SHARE_SHARE1 + iScore + TEXT_SHARE_SHARE1
                        });
                    }
                    //...ADD YOUR CODE HERE EVENTUALLY
                });

                $(oMain).on("bet_placed", function(evt, iTotBet) {
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
        <p class="check-font-1">impact</p>
    </div>


    <canvas id="canvas" class='ani_hack' width="1280" height="1920"> </canvas>
    <div data-orientation="portrait" class="orientation-msg-container">
        <p class="orientation-msg-text">Please rotate your device</p>
    </div>
    <div id="block_game" style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%; display:none"></div>

</body>

</html>