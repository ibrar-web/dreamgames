var CANVAS_WIDTH = 1500;
var CANVAS_HEIGHT = 768;

var EDGEBOARD_X = 200;
var EDGEBOARD_Y = 70;

var FPS = 30;
var FPS_TIME      = 1000/FPS;
var DISABLE_SOUND_MOBILE = false;
var FONT_GAME_1 = "odin_roundedbold";
var FONT_GAME_2 = "Digital-7";

var STATE_LOADING = 0;
var STATE_MENU    = 1;
var STATE_HELP    = 1;
var STATE_GAME    = 3;

var GAME_STATE_IDLE         = 0;
var GAME_STATE_SPINNING     = 1;
var GAME_STATE_SHOW_ALL_WIN = 2;
var GAME_STATE_SHOW_WIN     = 3;
var GAME_STATE_BONUS        = 4;

var REEL_STATE_START   = 0;
var REEL_STATE_MOVING = 1;
var REEL_STATE_STOP    = 2;

var SPIN_BUT_STATE_SPIN = "spin";
var SPIN_BUT_STATE_STOP = "stop";
var SPIN_BUT_STATE_AUTOSPIN = "autospin";
var SPIN_BUT_STATE_DISABLE = "disable";
var SPIN_BUT_STATE_FREESPIN = "freespin";
var SPIN_BUT_STATE_SKIP = "skip";

var ON_MOUSE_DOWN = 0;
var ON_MOUSE_UP   = 1;
var ON_MOUSE_OVER = 2;
var ON_MOUSE_OUT  = 3;
var ON_DRAG_START = 4;
var ON_DRAG_END   = 5;

var BONUS_BUTTON_1 = "up_left";
var BONUS_BUTTON_2 = "center_left";
var BONUS_BUTTON_3 = "down_left";
var BONUS_BUTTON_4 = "up_right";
var BONUS_BUTTON_5 = "center_right";
var BONUS_BUTTON_6 = "down_right";

var REEL_OFFSET_X = 290;
var REEL_OFFSET_Y = 97;

var NUM_REELS = 5;
var NUM_ROWS = 3;
var NUM_SYMBOLS = 13;
var WILD_SYMBOL = 12;
var BONUS_SYMBOL = 13;
var FREESPIN_SYMBOL = 1;
var NUM_PAYLINES = 20;
var SYMBOL_WIDTH = 171;
var SYMBOL_HEIGHT = 164;
var SYMBOL_ANIM_WIDTH = 340;
var SYMBOL_ANIM_HEIGHT = 326;
var SPACE_BETWEEN_SYMBOLS = 17;
var SPACE_HEIGHT_BETWEEN_SYMBOLS = 4;
var MAX_FRAMES_REEL_EASE = 16;
var MIN_REEL_LOOPS;
var REEL_DELAY;
var REEL_START_Y = REEL_OFFSET_Y - ((SYMBOL_HEIGHT +SPACE_HEIGHT_BETWEEN_SYMBOLS )* 3);
var REEL_ARRIVAL_Y = REEL_OFFSET_Y + ((SYMBOL_HEIGHT+SPACE_HEIGHT_BETWEEN_SYMBOLS) * 3);
var TIME_SHOW_WIN;
var TIME_SHOW_ALL_WINS;
var TIME_SPIN_BUT_CHANGE = 1000;
var TIME_HOLD_AUTOSPIN = 1000;
var COIN_BET;
var MIN_BET;
var MAX_BET;
var TOTAL_MONEY;
var WIN_OCCURRENCE;
var SLOT_CASH;
var MIN_WIN;
var FREESPIN_OCCURRENCE;
var BONUS_OCCURRENCE;
var FREESPIN_SYMBOL_NUM_OCCURR;
var NUM_FREESPIN;
var BONUS_PRIZE;
var BONUS_PRIZE_OCCURR;
var COIN_BET;
var PAYTABLE_VALUES;

var BONUS_FREESPIN = 1;
var BONUS_GOALKEEPER = 2;
var WHEEL_SETTINGS;
var SEGMENT_ROT = 360 /20;
var TIME_ANIM_IDLE = 10000;
var MIN_FAKE_SPIN = 3;
var WHEEL_SPIN_TIMESPEED = 2600;
var ANIM_SPIN_TIMESPEED = 50;
var TIME_ANIM_WIN = 5000;
var ANIM_WIN1_TIMESPEED = 300;
var ANIM_WIN2_TIMESPEED = 50;
var LED_SPIN = 3;
var ANIM_IDLE1_TIMESPEED = 2000;
var ANIM_IDLE2_TIMESPEED = 100;
var ANIM_IDLE3_TIMESPEED = 150;

var STATE_BONUS_IDLE = 0;
var STATE_BONUS_KICK = 1;
var STATE_BONUS_WIN = 2;
var STATE_BONUS_LOSE = 3;

var NUM_SPIN_FOR_ADS;
var ENABLE_FULLSCREEN;
var ENABLE_CHECK_ORIENTATION;
var SHOW_CREDITS;
var SOUNDTRACK_VOLUME_IN_GAME = 1;