function CGameOver() {
    var _oTextTitle;
    var _oButRecharge;
    var _oButExit;
    var _oContainer;

    this._init = function() {
        _oContainer = new createjs.Container();
        s_oStage.addChild(_oContainer);
        _oContainer.on("click", function() {});

        var oBg = createBitmap(s_oSpriteLibrary.getSprite('msg_box'));
        _oContainer.addChild(oBg);

        _oTextTitle = new CTLText(_oContainer,
            CANVAS_WIDTH / 2 - 170, 280, 340, 130,
            32, "center", "#fff", FONT_GAME_1, 1,
            0, 0,
            TEXT_NO_MONEY,
            true, true, true,
            false);

        _oTextTitle.setShadow("#000000", 2, 2, 2);


        _oButRecharge = new CTextButton(CANVAS_WIDTH / 2 - 100, 450, s_oSpriteLibrary.getSprite('but_game_bg'), TEXT_RECHARGE, FONT_GAME_1, "#fff", 14, _oContainer);
        _oButRecharge.addEventListener(ON_MOUSE_UP, this._onRecharge, this);

        _oButExit = new CTextButton(CANVAS_WIDTH / 2 + 100, 450, s_oSpriteLibrary.getSprite('but_game_bg'), TEXT_EXIT, FONT_GAME_1, "#fff", 14, _oContainer);
        _oButExit.addEventListener(ON_MOUSE_UP, this._onExit, this);

        this.hide();
    };

    this.unload = function() {
        _oButRecharge.unload();
        _oButExit.unload();
        _oContainer.off("click", function() {});
    };

    this.show = function() {
        //_oContainer.visible = true;
    };

    this.hide = function() {
        _oContainer.visible = false;
    };

    this._onRecharge = function() {
        $(s_oMain).trigger("recharge");
    };

    this._onExit = function() {
        s_oGame.onExit();
    };

    this._init();
}