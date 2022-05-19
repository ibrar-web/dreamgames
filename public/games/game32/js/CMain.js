function CMain(oData) {
    var _bUpdate;
    var _iCurResource = 0;
    var RESOURCE_TO_LOAD = 0;
    var _iState = STATE_LOADING;

    var _oData;
    var _oPreloader;
    var _oMenu;
    var _oHelp;
    var _oGame;

    this.initContainer = function() {
        var canvas = document.getElementById("canvas");
        s_oStage = new createjs.Stage(canvas);
        createjs.Touch.enable(s_oStage, true);

        s_bMobile = isMobile();

        if (s_bMobile === false) {
            s_oStage.enableMouseOver(20);
        }


        s_iPrevTime = new Date().getTime();

        createjs.Ticker.framerate = 30;
        createjs.Ticker.addEventListener("tick", this._update);

        if (navigator.userAgent.match(/Windows Phone/i)) {
            DISABLE_SOUND_MOBILE = true;
        }

        s_oSpriteLibrary = new CSpriteLibrary();

        //ADD PRELOADER
        _oPreloader = new CPreloader();
    };

    this.preloaderReady = function() {
        this._loadImages();

        if (DISABLE_SOUND_MOBILE === false || s_bMobile === false) {
            this._initSounds();
        }

        _bUpdate = true;
    };

    this.soundLoaded = function() {
        _iCurResource++;
        var iPerc = Math.floor(_iCurResource / RESOURCE_TO_LOAD * 100);

        _oPreloader.refreshLoader(iPerc);
    };

    this._initSounds = function() {
        Howler.mute(!s_bAudioActive);


        s_aSoundsInfo = new Array();
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'win', loop: true, volume: 1, ingamename: 'win' });
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'press_but', loop: false, volume: 1, ingamename: 'press_but' });
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'reel_stop', loop: false, volume: 1, ingamename: 'reel_stop' });
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'reels', loop: false, volume: 1, ingamename: 'reels' });
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'choose_chicken', loop: false, volume: 1, ingamename: 'choose_chicken' });
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'start_reel', loop: false, volume: 1, ingamename: 'start_reel' });
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'press_hold', loop: false, volume: 1, ingamename: 'press_hold' });
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'reveal_egg', loop: false, volume: 1, ingamename: 'reveal_egg' });
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'soundtrack', loop: true, volume: 1, ingamename: 'soundtrack' });
        s_aSoundsInfo.push({ path: './games/game32/sounds/', filename: 'soundtrack_bonus', loop: true, volume: 1, ingamename: 'soundtrack_bonus' });

        RESOURCE_TO_LOAD += s_aSoundsInfo.length;

        s_aSounds = new Array();
        for (var i = 0; i < s_aSoundsInfo.length; i++) {
            this.tryToLoadSound(s_aSoundsInfo[i], false);
        }

    };

    this.tryToLoadSound = function(oSoundInfo, bDelay) {

        setTimeout(function() {
            s_aSounds[oSoundInfo.ingamename] = new Howl({
                src: [oSoundInfo.path + oSoundInfo.filename + '.mp3'],
                autoplay: false,
                preload: true,
                loop: oSoundInfo.loop,
                volume: oSoundInfo.volume,
                onload: s_oMain.soundLoaded,
                onloaderror: function(szId, szMsg) {
                    for (var i = 0; i < s_aSoundsInfo.length; i++) {
                        if (szId === s_aSounds[s_aSoundsInfo[i].ingamename]._sounds[0]._id) {
                            s_oMain.tryToLoadSound(s_aSoundsInfo[i], true);
                            break;
                        }
                    }
                },
                onplayerror: function(szId) {
                    for (var i = 0; i < s_aSoundsInfo.length; i++) {
                        if (szId === s_aSounds[s_aSoundsInfo[i].ingamename]._sounds[0]._id) {
                            s_aSounds[s_aSoundsInfo[i].ingamename].once('unlock', function() {
                                s_aSounds[s_aSoundsInfo[i].ingamename].play();
                                if (s_aSoundsInfo[i].ingamename === "soundtrack" && s_oGame !== null) {
                                    setVolume("soundtrack", SOUNDTRACK_VOLUME_IN_GAME);
                                }

                            });
                            break;
                        }
                    }

                }
            });


        }, (bDelay ? 200 : 0));


    };



    this._loadImages = function() {
        s_oSpriteLibrary.init(this._onImagesLoaded, this._onAllImagesLoaded, this);

        s_oSpriteLibrary.addSprite("but_exit", "./games/game32/sprites/but_exit.png");
        s_oSpriteLibrary.addSprite("bg_menu", "./games/game32/sprites/bg_menu.jpg");
        s_oSpriteLibrary.addSprite("bg_game", "./games/game32/sprites/bg_game.jpg");
        s_oSpriteLibrary.addSprite("paytable", "./games/game32/sprites/paytable.jpg");
        s_oSpriteLibrary.addSprite("but_play_bg", "./games/game32/sprites/but_play_bg.png");
        s_oSpriteLibrary.addSprite("mask_slot", "./games/game32/sprites/mask_slot.png");
        s_oSpriteLibrary.addSprite("spin_but", "./games/game32/sprites/but_spin_bg.png");
        s_oSpriteLibrary.addSprite("coin_but", "./games/game32/sprites/but_coin_bg.png");
        s_oSpriteLibrary.addSprite("info_but", "./games/game32/sprites/but_info_bg.png");
        s_oSpriteLibrary.addSprite("bet_but", "./games/game32/sprites/bet_but.png");
        s_oSpriteLibrary.addSprite("win_frame_anim", "./games/game32/sprites/win_frame_anim.png");
        s_oSpriteLibrary.addSprite("but_lines_bg", "./games/game32/sprites/but_lines_bg.png");
        s_oSpriteLibrary.addSprite("but_maxbet_bg", "./games/game32/sprites/but_maxbet_bg.png");
        s_oSpriteLibrary.addSprite("audio_icon", "./games/game32/sprites/audio_icon.png");
        s_oSpriteLibrary.addSprite("hit_area_col", "./games/game32/sprites/hit_area_col.png");
        s_oSpriteLibrary.addSprite("hold_col", "./games/game32/sprites/hold_col.png");
        s_oSpriteLibrary.addSprite("bonus_bg", "./games/game32/sprites/bonus_bg.jpg");
        s_oSpriteLibrary.addSprite("chicken", "./games/game32/sprites/chicken.png");
        s_oSpriteLibrary.addSprite("hit_area_chicken", "./games/game32/sprites/hit_area_chicken.png");
        s_oSpriteLibrary.addSprite("egg", "./games/game32/sprites/egg.png");
        s_oSpriteLibrary.addSprite("but_credits", "./games/game32/sprites/but_credits.png");
        s_oSpriteLibrary.addSprite("msg_box", "./games/game32/sprites/msg_box.png");
        s_oSpriteLibrary.addSprite("logo_ctl", "./games/game32/sprites/logo_ctl.png");
        s_oSpriteLibrary.addSprite("but_fullscreen", "./games/game32/sprites/but_fullscreen.png");

        for (var i = 1; i < NUM_SYMBOLS + 1; i++) {
            s_oSpriteLibrary.addSprite("symbol_" + i, "./games/game32/sprites/symbol_" + i + ".png");
            s_oSpriteLibrary.addSprite("symbol_" + i + "_anim", "./games/game32/sprites/symbol_" + i + "_anim.png");
        }

        for (var j = 1; j < NUM_PAYLINES + 1; j++) {
            s_oSpriteLibrary.addSprite("payline_" + j, "./games/game32/sprites/payline_" + j + ".png");
        }

        RESOURCE_TO_LOAD += s_oSpriteLibrary.getNumSprites();

        s_oSpriteLibrary.loadSprites();
    };

    this._onImagesLoaded = function() {
        _iCurResource++;

        var iPerc = Math.floor(_iCurResource / RESOURCE_TO_LOAD * 100);

        _oPreloader.refreshLoader(iPerc);
    };

    this._onAllImagesLoaded = function() {

    };

    this.onAllPreloaderImagesLoaded = function() {
        this._loadImages();
    };

    this._onRemovePreloader = function() {
        _oPreloader.unload();

        s_oSoundTrack = playSound("soundtrack", 1, true);

        this.gotoMenu();
    };

    this.gotoMenu = function() {
        _oMenu = new CMenu();
        _iState = STATE_MENU;
    };

    this.gotoGame = function() {
        _oGame = new CGame(_oData);

        _iState = STATE_GAME;
    };

    this.gotoHelp = function() {
        _oHelp = new CHelp();
        _iState = STATE_HELP;
    };

    this.stopUpdate = function() {
        _bUpdate = false;
        createjs.Ticker.paused = true;
        $("#block_game").css("display", "block");

        if (DISABLE_SOUND_MOBILE === false || s_bMobile === false) {
            Howler.mute(true);
        }

    };

    this.startUpdate = function() {
        s_iPrevTime = new Date().getTime();
        _bUpdate = true;
        createjs.Ticker.paused = false;
        $("#block_game").css("display", "none");

        if (DISABLE_SOUND_MOBILE === false || s_bMobile === false) {
            if (s_bAudioActive) {
                Howler.mute(false);
            }
        }

    };

    this._update = function(event) {
        if (_bUpdate === false) {
            return;
        }
        var iCurTime = new Date().getTime();
        s_iTimeElaps = iCurTime - s_iPrevTime;
        s_iCntTime += s_iTimeElaps;
        s_iCntFps++;
        s_iPrevTime = iCurTime;

        if (s_iCntTime >= 1000) {
            s_iCurFps = s_iCntFps;
            s_iCntTime -= 1000;
            s_iCntFps = 0;
        }

        if (_iState === STATE_GAME) {
            _oGame.update();
        }

        s_oStage.update(event);

    };

    s_oMain = this;
    _oData = oData;

    PAYTABLE_VALUES = new Array();
    for (var i = 0; i < 8; i++) {
        PAYTABLE_VALUES[i] = oData["paytable_symbol_" + (i + 1)];
    }
    ENABLE_FULLSCREEN = _oData.fullscreen;
    ENABLE_CHECK_ORIENTATION = _oData.check_orientation;
    SHOW_CREDITS = _oData.show_credits;
    s_bAudioActive = oData.audio_enable_on_startup;

    this.initContainer();
}

var s_bMobile;
var s_bAudioActive = true;
var s_bFullscreen = false;
var s_iCntTime = 0;
var s_iTimeElaps = 0;
var s_iPrevTime = 0;
var s_iCntFps = 0;
var s_iCurFps = 0;

var s_oDrawLayer;
var s_oStage;
var s_oMain;
var s_oSpriteLibrary;
var s_oSoundTrack = null;
var s_aSoundsInfo;