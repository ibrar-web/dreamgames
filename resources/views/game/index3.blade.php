<!DOCTYPE html>
<html>

<head>
    <title>BLACKJACK n</title>
     <base href="http://renonights.live/" target="_blank">
    <link rel="stylesheet" href="game3/css/reset.css" type="text/css">
    <link rel="stylesheet" href="game3/css/main.css" type="text/css">
    <link rel="stylesheet" href="game3/css/orientation_utils.css" type="text/css">
    <link rel="stylesheet" href="game3/css/ios_fullscreen.css" type="text/css">
    <link rel='shortcut icon' type='image/x-icon' href='./game3/favicon.ico' />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,minimal-ui" />
    <meta name="msapplication-tap-highlight" content="no" />
    <script type="text/javascript" src="game3/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="game3/js/createjs-2015.11.26.min.js"></script>
    <script type="text/javascript" src="game3/js/platform.js"></script>
    <script type="text/javascript" src="game3/js/ios_fullscreen.js"></script>
    <script type="text/javascript" src="game3/js/howler.min.js"></script>
    <script type="text/javascript" src="game3/js/screenfull.js"></script>
    <script type="text/javascript" src="game3/js/ctl_utils.js"></script>
    <script type="text/javascript" src="game3/js/sprite_lib.js"></script>
    <script type="text/javascript" src="game3/js/settings.js"></script>
    <script type="text/javascript" src="game3/js/CLang.js"></script>
    <script type="text/javascript" src="game3/js/CPreloader.js"></script>
    <script type="text/javascript" src="game3/js/CMain.js"></script>
    <script type="text/javascript" src="game3/js/CTextButton.js"></script>
    <script type="text/javascript" src="game3/js/CGfxButton.js"></script>
    <script type="text/javascript" src="game3/js/CToggle.js"></script>
    <script type="text/javascript" src="game3/js/CMenu.js"></script>
    <script type="text/javascript" src="game3/js/CGame.js"></script>
    <script type="text/javascript" src="game3/js/CInterface.js"></script>
    <script type="text/javascript" src="game3/js/CTweenController.js"></script>
    <script type="text/javascript" src="game3/js/CSeat.js"></script>
    <script type="text/javascript" src="game3/js/CFichesController.js"></script>
    <script type="text/javascript" src="game3/js/CVector2.js"></script>
    <script type="text/javascript" src="game3/js/CGameSettings.js"></script>
    <script type="text/javascript" src="game3/js/CEasing.js"></script>
    <script type="text/javascript" src="game3/js/CHandController.js"></script>
    <script type="text/javascript" src="game3/js/CCard.js"></script>
    <script type="text/javascript" src="game3/js/CInsurancePanel.js"></script>
    <script type="text/javascript" src="game3/js/CGameOver.js"></script>
    <script type="text/javascript" src="game3/js/CMsgBox.js"></script>
    <script type="text/javascript" src="game3/js/CCreditsPanel.js"></script>
    <script type="text/javascript" src="game3/js/CFiche.js"></script>
    <script type="text/javascript" src="game3/js/CCTLText.js"></script>
</head>

<body ondragstart="return false;" ondrop="return false;">
    <meta name="csrf-token" content="{{ Session::token() }}">
    <div style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%"></div>
    <script>
        var user_id = '<?php echo $user_id; ?>';
        $(document).ready(function() {
            $.post("detail", {
                _token: $('meta[name=csrf-token]').attr('content'),
                id: 3,
                user_id:user_id
            }, function(t) {
                load(t);
            });

            function load(t) {
                var oMain = new CMain({
                    win_occurrence: JSON.parse(t.game[0]['win']), //WIN OCCURRENCE PERCENTAGE. VALUES BETWEEN 0-100
                    min_bet: JSON.parse(t.game[0]['minbet']), //MINIMUM COIN FOR BET
                    max_bet: JSON.parse(t.game[0]['maxbet']),
                    bet_time: 10000, //WAITING TIME FOR PLAYER BETTING
                    money: JSON.parse(t.data[0]['amount']), //STARING CREDIT FOR THE USER
                    blackjack_payout: 1.5, //PAYOUT WHEN USER WINS WITH BLACKJACK (DEFAULT IS 3 TO 2). BLACKJACK OCCURS WHEN USER GET 21 WITH FIRST 2 CARDS
                    game_cash: 500, //GAME CASH AVAILABLE WHEN GAME STARTS
                    show_credits: true, //ENABLE/DISABLE CREDITS BUTTON IN THE MAIN SCREEN
                    fullscreen: true, //SET THIS TO FALSE IF YOU DON'T WANT TO SHOW FULLSCREEN BUTTON
                    check_orientation: true, //SET TO FALSE IF YOU DON'T WANT TO SHOW ORIENTATION ALERT ON MOBILE DEVICES
                    //////////////////////////////////////////////////////////////////////////////////////////
                    ad_show_counter: 3 //NUMBER OF HANDS PLAYED BEFORE AD SHOWN
                    //
                    //// THIS FUNCTIONALITY IS ACTIVATED ONLY WITH CTL ARCADE PLUGIN.///////////////////////////
                    /////////////////// YOU CAN GET IT AT: /////////////////////////////////////////////////////////
                    // http://codecanyon.net/item/ctl-arcade-wordpress-plugin/13856421 ///////////
                });



                $(oMain).on("recharge", function(evt) {
                    //INSERT HERE YOUR RECHARGE SCRIPT THAT RETURN MONEY TO RECHARGE

                    if (s_oGame !== null) {
                        s_oGame.setMoney(0);
                    }
                });

                $(oMain).on("bet_placed", function(evt, iTotBet) {
                    //...ADD YOUR CODE HERE EVENTUALLY
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
                            img: TEXT_SHARE_IMAGE,
                            title: TEXT_SHARE_TITLE,
                            msg: TEXT_SHARE_MSG1 + iScore + TEXT_SHARE_MSG2,
                            msg_share: TEXT_SHARE_SHARE1 + iScore + TEXT_SHARE_SHARE1
                        });
                    }
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
    </div>

    <canvas id="canvas" class='ani_hack' width="1700" height="768"> </canvas>
    <div data-orientation="landscape" class="orientation-msg-container">
        <p class="orientation-msg-text">Please rotate your device</p>
    </div>
    <div id="block_game" style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%; display:none"></div>
</body>

</html>