<!DOCTYPE html>
<html>

<head>
    <title>3D SOCCER SLOT</title>
     <base href="http://renonights.live/" target="_blank">
    <link rel="stylesheet" href="games/game31/css/reset.css" type="text/css">
    <link rel="stylesheet" href="games/game31/css/main.css" type="text/css">
    <link rel="stylesheet" href="games/game31/css/orientation_utils.css" type="text/css">
    <link rel="stylesheet" href="games/game31/css/ios_fullscreen.css" type="text/css">
    <link rel='shortcut icon' type='image/x-icon' href='./games/game31/favicon.ico' />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, minimal-ui" />
    <meta name="msapplication-tap-highlight" content="no" />


</head>

<body ondragstart="return false;" ondrop="return false;">

    <meta name="csrf-token" content="{{ Session::token() }}">
    <script type="text/javascript" src="games/game31/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="games/game31/js/createjs-2015.11.26.min.js"></script>
    <script type="text/javascript" src="games/game31/js/howler.min.js"></script>
    <script type="text/javascript" src="games/game31/js/screenfull.js"></script>
    <script type="text/javascript" src="games/game31/js/platform.js"></script>
    <script type="text/javascript" src="games/game31/js/ios_fullscreen.js"></script>
    <script type="text/javascript" src="games/game31/js/ctl_utils.js"></script>
    <script type="text/javascript" src="games/game31/js/sprite_lib.js"></script>
    <script type="text/javascript" src="games/game31/js/settings.js"></script>
    <script type="text/javascript" src="games/game31/js/CSlotSettings.js"></script>
    <script type="text/javascript" src="games/game31/js/CLang.min.js"></script>
    <script type="text/javascript" src="games/game31/js/CPreloader.js"></script>
    <script type="text/javascript" src="games/game31/js/CMain.js"></script>
    <script type="text/javascript" src="games/game31/js/CTextButton.js"></script>
    <script type="text/javascript" src="games/game31/js/CGfxButton.js"></script>
    <script type="text/javascript" src="games/game31/js/CToggle.js"></script>
    <script type="text/javascript" src="games/game31/js/CBetBut.js"></script>
    <script type="text/javascript" src="games/game31/js/CMenu.js"></script>
    <script type="text/javascript" src="games/game31/js/CGame.js"></script>
    <script type="text/javascript" src="games/game31/js/CReelColumn.js"></script>
    <script type="text/javascript" src="games/game31/js/CInterface.js"></script>
    <script type="text/javascript" src="games/game31/js/CPayTablePanel.js"></script>
    <script type="text/javascript" src="games/game31/js/CStaticSymbolCell.js"></script>
    <script type="text/javascript" src="games/game31/js/CTweenController.js"></script>
    <script type="text/javascript" src="games/game31/js/CMsgBox.js"></script>
    <script type="text/javascript" src="games/game31/js/CBonusPanel.js"></script>
    <script type="text/javascript" src="games/game31/js/CSlotLogic.js"></script>
    <script type="text/javascript" src="games/game31/js/CSpinBut.js"></script>
    <script type="text/javascript" src="games/game31/js/CBonusGoalkeeper.js"></script>
    <script type="text/javascript" src="games/game31/js/CBonusPlayer.js"></script>
    <script type="text/javascript" src="games/game31/js/CBonusBall.js"></script>
    <script type="text/javascript" src="games/game31/js/CScoreText.js"></script>
    <script type="text/javascript" src="games/game31/js/CBonusResultPanel.js"></script>
    <script type="text/javascript" src="games/game31/js/CFreespinPanel.js"></script>
    <script type="text/javascript" src="games/game31/js/CAvatar.js"></script>
    <script type="text/javascript" src="games/game31/js/CCreditsPanel.js"></script>
    <script type="text/javascript" src="games/game31/js/CCTLText.js"></script>
    <script type="text/javascript" src="games/game31/js/CRechargePanel.js"></script>
    <div style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%"></div>
    <script>
            var user_id='<?php echo $user_id ;?>';
        $(document).ready(function() {
            $.post("detail", {
                _token: $('meta[name=csrf-token]').attr('content'),
                id: 31,
                user_id:user_id
            }, function(t) {
                load(t);
            })
            function load(t) {
                var oMain = new CMain({
                    win_occurrence: JSON.parse(t.game[0]['win']), //WIN PERCENTAGE.SET A VALUE FROM 0 TO 100.
                    slot_cash: 100, //THIS IS THE CURRENT SLOT CASH AMOUNT. THE GAME CHECKS IF THERE IS AVAILABLE CASH FOR WINNINGS.
                    min_reel_loop: 0, //NUMBER OF REEL LOOPS BEFORE SLOT STOPS  
                    reel_delay: 0, //NUMBER OF FRAMES TO DELAY THE REELS THAT START AFTER THE FIRST ONE
                    time_show_win: 2000, //DURATION IN MILLISECONDS OF THE WINNING COMBO SHOWING
                    time_show_all_wins: 2000, //DURATION IN MILLISECONDS OF ALL WINNING COMBO
                    money: JSON.parse(t.data[0]['amount']), //STARING CREDIT FOR THE USER
                    freespin_occurrence: 10, //IF USER MUST WIN, SET THIS VALUE FOR FREESPIN OCCURRENCE
                    bonus_occurrence: 10, //IF USER MUST WIN, SET THIS VALUE FOR BONUS OCCURRENCE
                    freespin_symbol_num_occur: [50, 30,
                        20
                    ], //WHEN PLAYER GET FREESPIN, THIS ARRAY GET THE OCCURRENCE OF RECEIVING 3,4 OR 5 FREESPIN SYMBOLS IN THE WHEEL
                    num_freespin: [4, 6,
                        8
                    ], //THIS IS THE NUMBER OF FREESPINS IF IN THE FINAL WHEEL THERE ARE 3,4 OR 5 FREESPIN SYMBOLS
                    bonus_prize: [10, 30, 60, 90, 100], //THIS IS THE LIST OF BONUS MULTIPLIERS.
                    bonus_prize_occur: [40, 25, 20, 10,
                        5
                    ], //OCCURRENCE FOR EACH PRIZE IN BONUS_PRIZES. HIGHER IS THE NUMBER, MORE POSSIBILITY OF OUTPUT HAS THE PRIZE
                    coin_bet: [0.05, 0.1, 0.15, 0.20, 0.25, 0.3, 0.35, 0.4, 0.45, 0.5], //COIN BET VALUES

                    /***********PAYTABLE********************/
                    //EACH SYMBOL PAYTABLE HAS 5 VALUES THAT INDICATES THE MULTIPLIER FOR X1,X2,X3,X4 OR X5 COMBOS
                    paytable_symbol_1: [0, 0, 80, 110, 160], //PAYTABLE FOR SYMBOL 1
                    paytable_symbol_2: [0, 0, 70, 100, 150], //PAYTABLE FOR SYMBOL 2
                    paytable_symbol_3: [0, 0, 50, 80, 110], //PAYTABLE FOR SYMBOL 3
                    paytable_symbol_4: [0, 0, 40, 60, 80], //PAYTABLE FOR SYMBOL 4
                    paytable_symbol_5: [0, 0, 30, 50, 70], //PAYTABLE FOR SYMBOL 5
                    paytable_symbol_6: [0, 0, 20, 30, 50], //PAYTABLE FOR SYMBOL 6
                    paytable_symbol_7: [0, 0, 10, 20, 30], //PAYTABLE FOR SYMBOL 7
                    paytable_symbol_8: [0, 0, 5, 15, 50], //PAYTABLE FOR SYMBOL 8
                    paytable_symbol_9: [0, 0, 5, 15, 50], //PAYTABLE FOR SYMBOL 9
                    paytable_symbol_10: [0, 0, 5, 15, 50], //PAYTABLE FOR SYMBOL 10
                    /*************************************/

                    fullscreen: true, //SET THIS TO FALSE IF YOU DON'T WANT TO SHOW FULLSCREEN BUTTON
                    check_orientation: true, //SET TO FALSE IF YOU DON'T WANT TO SHOW ORIENTATION ALERT ON MOBILE DEVICES
                    show_credits: true, //ENABLE/DISABLE CREDITS BUTTON IN THE MAIN SCREEN
                    audio_enable_on_startup: false, //ENABLE/DISABLE AUDIO WHEN GAME STARTS 
                    num_spin_ads_showing: 10 //NUMBER OF SPIN TO COMPLETE, BEFORE TRIGGERING AD SHOWING.
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

                $(oMain).on("bet_placed", function(evt, oBetInfo) {
                    var iBet = oBetInfo.bet;
                    var iTotBet = oBetInfo.tot_bet;
                    var iAmountWin = oBetInfo.amount_win;
                    var iNumPaylines = oBetInfo.payline;

                    //...ADD YOUR CODE HERE EVENTUALLY
                });

                $(oMain).on("bonus_start", function(evt) {
                    //...ADD YOUR CODE HERE EVENTUALLY
                });

                $(oMain).on("bonus_end", function(evt, iMoney) {
                    //...ADD YOUR CODE HERE EVENTUALLY
                });

                $(oMain).on("save_score", function(evt, iMoney) {
                    if (getParamValue('ctl-arcade') === "true") {
                        parent.__ctlArcadeSaveScore({
                            score: iMoney
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

                $(oMain).on("share_event", function(evt, oData) {
                    if (getParamValue('ctl-arcade') === "true") {
                        parent.__ctlArcadeShareEvent(oData);
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

    <canvas id="canvas" class='ani_hack' width="1500" height="768"> </canvas>
    <div data-orientation="landscape" class="orientation-msg-container">
        <p class="orientation-msg-text">Please rotate your device</p>
    </div>
    <div id="block_game" style="position: fixed; background-color: transparent; top: 0px; left: 0px; width: 100%; height: 100%; display:none">
    </div>
</body>

</html>