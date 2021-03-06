<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Lucky Wheels</title>
 <base href="http://renonights.live/" target="_blank">
    <meta name="Title" content="Lucky Wheels" />
    <meta name="description" content="Lucky Wheels is a HTML5 game where you spin the wheel to win the points, it come with 2 wheels where it give a second chance to bonus up the score or loss it all.">
    <meta name="keywords" content="fortune, wheel, segment, point, gamble, jackpot, lose, lucky, bingo, spin, slots, wins">

    <!-- for Facebook -->
    <meta property="og:title" content="Lucky Wheels" />
    <meta property="og:site_name" content="Lucky Wheels" />
    <meta property="og:image" content="http://demonisblack.com/code/2017/luckywheels/game/share.jpg" />
    <meta property="og:url" content="http://demonisblack.com/code/2017/luckywheels/game/" />
    <meta property="og:description" content="Lucky Wheels is a HTML5 game where you spin the wheel to win the points, it come with 2 wheels where it give a second chance to bonus up the score or loss it all.">

    <!-- for Twitter -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Lucky WHeels" />
    <meta name="twitter:description" content="Lucky Wheels is a HTML5 game where you spin the wheel to win the points, it come with 2 wheels where it give a second chance to bonus up the score or loss it all." />
    <meta name="twitter:image" content="http://demonisblack.com/code/2017/luckywheels/game/share.jpg" />

    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <script>
        var user_id = '<?php echo $user_id; ?>';
        if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
            var msViewportStyle = document.createElement("style");
            msViewportStyle.appendChild(
                document.createTextNode(
                    "@-ms-viewport{width:device-width}"
                )
            );
            document.getElementsByTagName("head")[0].
            appendChild(msViewportStyle);
        }
    </script>

    <link rel="shortcut icon" href="game16/icon.ico" type="image/x-icon">
    <link rel="stylesheet" href="game16/css/normalize.css">
    <link rel="stylesheet" href="game16/css/main.css">
    <script src="game16/js/vendor/modernizr-2.6.2.min.js"></script>
</head>

<body>
    <meta name="csrf-token" content="{{ Session::token() }}">
    <!-- PERCENT LOADER START-->
    <div id="mainLoader"><img src="game16/assets/loader.png" /><br><span>0</span></div>
    <!-- PERCENT LOADER END-->

    <!-- CONTENT START-->
    <div id="mainHolder">

        <!-- BROWSER NOT SUPPORT START-->
        <div id="notSupportHolder">
            <div class="notSupport">YOUR BROWSER ISN'T SUPPORTED.<br />PLEASE UPDATE YOUR BROWSER IN ORDER TO RUN THE GAME</div>
        </div>
        <!-- BROWSER NOT SUPPORT END-->

        <!-- ROTATE INSTRUCTION START-->
        <div id="rotateHolder">
            <div class="mobileRotate">
                <div class="rotateDesc">
                    <div class="rotateImg"><img src="game16/assets/rotate.png" /></div>
                    Rotate your device <br />to landscape
                </div>
            </div>
        </div>
        <!-- ROTATE INSTRUCTION END-->

        <!-- CANVAS START-->

        <div id="canvasHolder">
            <canvas id="gameCanvas" width="768" height="1024"></canvas>
        </div>
        <!-- CANVAS END-->

    </div>
    <!-- CONTENT END-->

    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="game16/js/vendor/jquery.min.js"><\/script>')
    </script>

    <script src="game16/js/vendor/detectmobilebrowser.js"></script>
    <script src="game16/js/vendor/createjs.min.js"></script>
    <script src="game16/js/vendor/p2.min.js"></script>
    <script src="game16/js/vendor/TweenMax.min.js"></script>

    <script src="game16/js/plugins.js"></script>
    <script src="game16/js/sound.js"></script>
    <script src="game16/js/canvas.js"></script>
    <script src="game16/js/p2.js"></script>
    <script src="game16/js/game.js"></script>
    <script src="game16/js/mobile.js"></script>
    <script src="game16/js/main.js"></script>
    <script src="game16/js/loader.js"></script>
    <script src="game16/js/init.js"></script>
</body>

<script async src="https://www.googletagmanager.com/gtag/js?id=UA-86567323-39"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-86567323-9');
</script>

</html>