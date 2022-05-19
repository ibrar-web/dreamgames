<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Cache;
use App\User;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Illuminate\Http\Response;
use Cookie;
class UserHomeController extends Controller
{
    public function vendorhistory(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $data = [];
            $data['data'] = DB::table('usercredithistory')->where('userid', $sequence)->get();
            $data['Credit'] = DB::table('users')->where('id', $sequence)->get();
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function getinfo()
    {
        try {
            $sequence = $request->input('user_id');
            $data = DB::table('users')->where('id', $sequence)->get();
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function balance()
    {

        try {
            $sequence = $request->input('user_id');
            if (DB::table('users')->where('id', $sequence)->pluck('ban')[0] == 'active' && DB::table('users')->where('id', $sequence)->pluck('close')[0] == false) {
                $data['data'] = DB::table('users')->where('id', $sequence)->select('amount', 'reward', 'name', 'username')->get();
                $data['status'] = 'Success';
                return response($data);
            }
            $data['status'] = 'later';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function user_actions(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $amount = round($request->input('amount'),2);
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $userreward = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
            $useramount =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            if ($amount > 0.9 && $userreward - $amount > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => $amount + $useramount]);
                DB::table('users')->where('id', $sequence)->update(['reward' => $userreward - $amount]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'name' => $name, 'amount' => $amount, 'description' => 'winning to deposit',
                    'frombalance' => $useramount, 'tobalance' => $amount + $useramount, 'created_at' => now(), 'updated_at' => now(), 'color' => 7
                ]);
            } else if ($amount > 0 && $userreward - $amount < 1) {
                $data['status'] = 'danger';
            }
            $data['status'] = 'Success';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return $data;
    }
    public function dailybonus()
    {
        try {
            $sequence = $request->input('user_id');
            $username = auth()->user()->username;
            $name = auth()->user()->name;
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $useramount =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $dailybonus = rand(1, 3);
            $bouncebackdate = DB::table('users')->where('id', $sequence)->whereDate('last_bonus', '=', date('Y-m-d', time()))->pluck('id');
            if (count($bouncebackdate) > 0) {
                $data['status'] = 'Success';
                $data['alert'] = 'danger';
                $data['request'] = false;
                $data['reason'] = 'You have already availed daily bonus';
                return response($data);
            }
            DB::table('users')->where('id', $sequence)->update(['amount' => $dailybonus + $useramount, 'last_bonus' => date('Y-m-d', time())]);
            DB::table($vendorid . '_accounthistory')->insert([
                'account' => $username, 'amount' => $dailybonus, 'description' => 'daily bonus', 'name' => $name,
                'frombalance' => $useramount, 'tobalance' => $useramount + $dailybonus, 'created_at' => now(), 'updated_at' => now(), 'color' => 5
            ]);
            $data['status'] = 'Success';
            $data['alert'] = 'success';
            $data['request'] = true;
            $data['reason'] = 'your daily bonus amount is ' . $dailybonus;
            return response($data);
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return $data;
    }
    public function gameslist()
    {
        try {
            $data = DB::table('gamescontrol')->where('status', 'active')->get();
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return $data;
    }
    private function checksession($userid,$amount,$bet,$request){
         if (Cache::has('user-is-online-' . $userid))
            {   $header = $request->header();
                Log::info($header);
                return 'validate';
            }
            else{
                return 'invalidate';
                
            }
    }
    public function detail(Request $request)
    {
        $val = $request->header();
        $val2=$request->cookie('salman');
        $sequence = $request->input('user_id');
        $data=[];
        try {
            $gameid = $request->input('id');
            $data['game'] = DB::table('gamescontrol')->where('id', $gameid)->get();
            $data['id'] = $gameid;
           
            if (DB::table('users')->where('id', $sequence)->pluck('ban')[0] == 'active' && DB::table('users')->where('id', $sequence)->pluck('close')[0] == false) {
                $data['data'] = DB::table('users')->where('id', $sequence)->select('amount', 'reward', 'username')->get();
                $data['status'] = 'Success';
                return response($data);
            }
            $data['status'] = 'later';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function update1(Request $request)
    {
        $val = $request->header();
        // $val2=$request->cookie('salman');
        // log::info('check cookies ');
        // log::info($val);
        // log::info($val);
        $amount = round($request->input('amount'),2);
        $sequence = $request->input('user_id');
        $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
        $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
        $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
        $amounttype = $request->input('type');
        $bet = $request->input('bet');
        $validate=$this->checksession($sequence,$amount,$bet,$request);
        // if($validate=='invalidate'){
        //   return $amount; 
        // }
        try {
            if ($amounttype == '4') {
             $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                $differ = $previous - $amount;
                $differ = round($differ, 2);
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'Christmas bet', 'name' => $name,
                    'frombalance' =>  round($previous,2), 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } elseif ($amounttype == '1') {
             $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                $reward = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
             $previousreward = $reward;
                $differ = $amount - $previous;
                $differ = round($differ, 2);
                $reward = $reward + $differ;
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => round(($previous + $bet),2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'Christmas reward', 'name' => $name,
                    'frombalance' =>  round($previousreward,2), 'tobalance' => round($reward,2), 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }

    public function update2(Request $request)
    {
        try {
            Log::info($request->input('type'));
            Log::info($request->input('amount'));
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
            $amounttype = $request->input('type');
            if ($amounttype == '2') {
             $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                if ($amount > $previous) {
                    return response($previous);
                }
                $differ = $previous - $amount;
                $differ = round($differ, 2);
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'Baccarat bet', 'name' => $name,
                    'frombalance' =>  round($previous,2), 'tobalance' => round($amount, 2), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } elseif ($amounttype == '5') {
             $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                $reward = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
             $previousreward = $reward;
                $bet = $request->input('bet');
                $differ = $amount - $previous - $bet;
                $differ = round($differ, 2);
                $reward = $reward + $differ;
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => round(($previous + $bet),2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'Baccarat reward', 'name' => $name,
                    'frombalance' =>  round($previousreward,2), 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            } elseif ($amounttype == '3') {
             $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                $reward = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
             $previousreward = $reward;
                $differ = $amount - $previous;
                $differ = round($differ, 2);
                $reward = $reward + $differ;
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'bet cancelled Baccarat', 'name' => $name,
                    'frombalance' =>  $previousreward, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 3
                ]);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }

    public function update3(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
            $amounttype = $request->input('type');
            switch ($amounttype) {
                case '3':
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $previous - $amount;
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'BlackJack bet', 'name' => $name,
                        'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                case '9':
                    $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $reward = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $bet = $request->input('bet');
                    $differ = $amount - $previous;
                    $previousreward = $reward;
                    $reward = $reward + $differ;
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $amount]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'BlackJack reward', 'name' => $name,
                        'frombalance' =>  $previousreward, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($previous + $bet);
                    break;
                case '6':
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $amount - $previous;
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'BlackJack Cancelled', 'name' => $name,
                        'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 3
                    ]);
                    return response($amount);
                    break;
                case '7':
                    $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $previous - $amount;
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'BlackJack bet ', 'name' => $name,
                        'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                default:
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response('sads');
    }

    public function update4(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
         $previousamount =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $amount = round($request->input('amount'),2);
            $bet = $request->input('bet');
            $reward = $request->input('reward');
            $amount = $amount + $reward;
            $rewardamountpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
            $rewardamount = $rewardamountpre + $reward;
            DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
            DB::table('users')->where('id', $sequence)->update(['reward' => round($rewardamount, 2)]);
            DB::table($vendorid . '_accounthistory')->insert([
                'account' => $username, 'amount' => round($bet, 2), 'description' => 'Roulette bet ', 'name' => $name,
                'frombalance' =>  $previousamount, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
            ]);
            DB::table($vendorid . '_accounthistory')->insert([
                'account' => $username, 'amount' => round($reward, 2), 'description' => 'Roulette reward ', 'name' => $name,
                'frombalance' =>  $rewardamountpre, 'tobalance' => $rewardamount, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
            ]);
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update5(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
            $amounttype = $request->input('type');
            $bet = $request->input('bet');
            switch ($amounttype) {
                case '1':
                 $previousamount =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($bet, 2), 'description' => 'Kino bet ', 'name' => $name,
                        'frombalance' =>  $previousamount, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                case '2':
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $differ = $amount - $previous;
                    if ($differ == 0) {
                        return response($previous);
                    }
                    $reward = $rewardpre + $differ;
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2)]);
                    $previousamount =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'Kino reward ', 'name' => $name,
                        'frombalance' =>  $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($previous + $bet);
                    break;
                default:
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response('sads');
    }
    public function getusercreditgame6(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $data = DB::table('users')->where('id', $sequence)->select('amount')->get();
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function update6(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
            $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Piratesslot bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Piratesslot reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous);
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function update8(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
         $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $bet = $request->input('bet');
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'LuckyWheel bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'LuckyWheel reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update9(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $bet = $request->input();
            $differ = $previous - $amount;

            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Bingo bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'Bingo reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update10(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $bet = $request->input('bet');
            $differ = $previous - $amount;

            if ($differ > 0) {
                $note = 'bet amount -' . $differ . '';
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Champions bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Champions reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }

    public function update12(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $bet = $request->input('bet');
            $differ = $previous - $amount;

            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Space adventure bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'Space adventure reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }

    public function update13(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $bet = $request->input('bet');
            $win = $request->input('win');
            $differ = $previous - $amount;

            if ($differ > 0) {
                $note = 'bet amount -' . $differ . '';
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => '3card poker bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $differ =  $amount + $win - $bet - $previous;
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => '3card reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }

    public function update14(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            $bet = $request->input('bet');
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'soccer bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'soccer reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function updateusercreditgame15(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;

            if ($differ > 0) {
                $note = 'bet amount -' . $differ . '';
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'golf bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'golf reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function updateusercreditgame16(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;

            if ($differ > 0) {
                $note = 'bet amount -' . $differ . '';
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'poker bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'poker reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function update17(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
            $amounttype = $request->input('type');
            Log::info($amounttype);
            if ($amounttype == '4') {
             $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                $differ = $previous - $amount;
                $differ = round($differ, 2);
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'Toodle bet', 'name' => $name,
                    'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } elseif ($amounttype == '1') {
                $bet = $request->input('bet');
             $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                $reward = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
             $previousreward = $reward;
                $differ = $amount - $previous;
                $differ = round($differ, 2);
                $reward = $reward + $differ;
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round($differ, 2), 'description' => 'Toodle reward', 'name' => $name,
                    'frombalance' =>  $previousreward, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update18(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;

            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Deuces bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Deuces reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update19(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Craps bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Craps reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous);
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update20(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Jokerpoker bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Jokerpoker reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update21(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Ramsestreasure bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward');
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Ramsestreasure reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update22(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
            $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            switch ($request->input('type')) {
                case '2':
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Highorlow bet ', 'name' => $name,
                        'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                case '4':
                    $bet = $request->input('bet');
                    $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $reward = $rewardpre + abs($differ);
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Highorlow reward ', 'name' => $name,
                        'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($previous + $bet);
                    break;
                case '1':
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Highorlow bet clear ', 'name' => $name,
                        'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 3
                    ]);
                    return response($amount);
                    break;
                default:
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }


        return response($amount);
    }
    public function update23(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;

            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Fruits bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Fruits reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update24(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
            switch ($request->input('id')) {
                case '1':
                 $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $previous - $amount;
                    if ($differ > 0) {
                        DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                        DB::table($vendorid . '_accounthistory')->insert([
                            'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'HorseRacing bet ', 'name' => $name,
                            'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                        ]);
                    } else if ($differ < 0) {
                        DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                        DB::table($vendorid . '_accounthistory')->insert([
                            'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'HorseRacing betreset ', 'name' => $name,
                            'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 3
                        ]);
                    }
                    break;
                case '2':
                    $bet = $request->input('bet');
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $reward = $rewardpre + abs($amount);
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($amount), 2), 'description' => 'HorseRacing reward ', 'name' => $name,
                        'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($previous + $bet);
                    break;
                default:
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function update25(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Paigow bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Paigow reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update26(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Arabiannight bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Arabiannight reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update27(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'ScratchFruit bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'ScratchFruit reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous + $bet);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update28(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
            switch ($request->input('id')) {
                case '1':
                 $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $previous - $amount;
                    if ($differ > 0) {
                        DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                        DB::table($vendorid . '_accounthistory')->insert([
                            'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Greyhound bet ', 'name' => $name,
                            'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                        ]);
                    } else if ($differ < 0) {

                        DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                        DB::table($vendorid . '_accounthistory')->insert([
                            'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Greyhound betreset ', 'name' => $name,
                            'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 3
                        ]);
                    }
                    break;
                case '2':
                    $bet = $request->input('bet');
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $reward = $rewardpre + abs($amount);
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($amount), 2), 'description' => 'Greyhound reward ', 'name' => $name,
                        'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($bet + $previous);
                    break;
                default:
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function update29(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($request->input('type') == '2') {
                //from the file cseat just found new data
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($amount);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($amount), 2), 'description' => 'Roulete3D reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous);
            }
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Roulete3D bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Roulete3D reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update30(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'ChecikenSlot bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'ChecikenSlot reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update31(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => '3DSoccer bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => '3DSoccer reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update32(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'crypto bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'crypto reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update33(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'squid bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'squid reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update34(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Katana bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Katana reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update35(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Billionaire bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Billionaire reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update36(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Monster bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Monster reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update37(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
            $amounttype = $request->input('type');
            switch ($amounttype) {
                case '2':
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $previous - $amount;
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'reddog bet', 'name' => $name,
                        'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                case '1':
                    $bet = $request->input('bet');
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $reward = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $differ = $amount - $previous;
                 $previousreward = $reward;
                    $reward = $reward + $differ;
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'reddog reward', 'name' => $name,
                        'frombalance' =>  $previousreward, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    break;
                    return response($previous + $bet);
                case '3':
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $amount - $previous;
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'reddog Cancelled', 'name' => $name,
                        'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 3
                    ]);
                    return response($amount);
                    break;
                case '7':
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $previous - $amount;
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'reddog bet ', 'name' => $name,
                        'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                default:
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update38(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
            $amounttype = $request->input('type');
            switch ($amounttype) {
                case '3':
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $previous - $amount;
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'carribean stud bet', 'name' => $name,
                        'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                case '2':
                    $bet = $request->input('bet');
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $reward = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $differ = $amount - $previous;
                 $previousreward = $reward;
                    $reward = $reward + $differ;
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'carribean stud reward', 'name' => $name,
                        'frombalance' =>  $previousreward, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($bet + $previous);
                    break;
                case '4':
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $amount - $previous;
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'carribean stud Cancelled', 'name' => $name,
                        'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 3
                    ]);
                    return response($amount);
                    break;
                case '7':
                 $previous =   round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
                    $differ = $previous - $amount;
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round($differ, 2), 'description' => 'reddog bet ', 'name' => $name,
                        'frombalance' =>  $previous, 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                default:
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update39(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Jewelinjewel bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Jewelinjewel reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update40(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            $amounttype = $request->input('type');
            switch ($amounttype) {
                case '1':
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'FORTUNE TREE bet ', 'name' => $name,
                        'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                case '3':
                    $bet = $request->input('bet');
                    $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $reward = $rewardpre + abs($differ);
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'FORTUNE TREE reward ', 'name' => $name,
                        'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($bet + $previous);
                    break;
            }

            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update41(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'SPIN & WIN bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'SPIN & WIN reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update42(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'),2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'spinandwin bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
            } else if ($differ < 0) {
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'spinandwin reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
            }
            $data = 'sdsd';
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($data);
    }
    public function update43(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            $amounttype = $request->input('type');
            switch ($amounttype) {
                case '1':
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'football bet ', 'name' => $name,
                        'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                case '2':
                    $bet = $request->input('bet');
                    $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $reward = $rewardpre + abs($differ);
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'football reward ', 'name' => $name,
                        'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($bet + $previous);
                    break;
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update44(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            $amounttype = $request->input('type');
            switch ($amounttype) {
                case '3':
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'roultte bet ', 'name' => $name,
                        'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                case '5':
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'roultte bet clear', 'name' => $name,
                        'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 3
                    ]);
                    return response($amount);
                    break;
                case '6':
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'roultte bet clear', 'name' => $name,
                        'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 3
                    ]);
                    return response($amount);
                    break;
                case '0':
                    $bet = $request->input('bet');
                    $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $reward = $rewardpre + abs($differ);
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'roultte reward ', 'name' => $name,
                        'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($previous + $bet);
                    break;
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update45(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'royalfruit bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'royalfruit reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update46(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Avengers bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Avengers reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update47(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Plinko bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Plinko reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update48(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Avengers bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Avengers reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function update49(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'jacks or better bet ', 'name' => $name,
                    'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $bet + $previous]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'jacks or better reward ', 'name' => $name,
                    'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
        public function update50(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            if ($differ > 0) {
                DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'goldenfruit bet ', 'name' => $name,
                    'frombalance' =>   round($previous,2), 'tobalance' => $amount, 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                ]);
                return response($amount);
            } else if ($differ < 0) {
                $bet = $request->input('bet');
                $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                $reward = $rewardpre + abs($differ);
                DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => round(($bet + $previous),2)]);
                DB::table($vendorid . '_accounthistory')->insert([
                    'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'goldenfruit reward ', 'name' => $name,
                    'frombalance' =>  round($rewardpre,2), 'tobalance' => round($reward,2), 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                ]);
                return response($bet + $previous);
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
    public function fish(Request $request)
    {
        try {
            $sequence = $request->input('user_id');
            $vendorid = DB::table('vendorsuser')->where('userid', $sequence)->pluck('vendorid')[0];
            $username = DB::table('users')->where('id', $sequence)->pluck('username')[0];
            $name = DB::table('users')->where('id', $sequence)->pluck('name')[0];
            $amount = round($request->input('amount'), 2);
         $previous = round(DB::table('users')->where('id', $sequence)->pluck('amount')[0],2);
            $differ = $previous - $amount;
            $amounttype = $request->input('type');
            switch ($amounttype) {
                case '1':
                    DB::table('users')->where('id', $sequence)->update(['amount' => round($amount, 2),'revert'=>false]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Fish bet ', 'name' => $name,
                        'frombalance' =>$previous, 'tobalance' => $request->input('amount'), 'created_at' => now(), 'updated_at' => now(), 'color' => 1
                    ]);
                    return response($amount);
                    break;
                case '2':
                    $bet = $request->input('bet');
                    $rewardpre = DB::table('users')->where('id', $sequence)->pluck('reward')[0];
                    $reward = $rewardpre + abs($differ);
                    DB::table('users')->where('id', $sequence)->update(['reward' => round($reward, 2), 'amount' => $previous + $bet]);
                    DB::table($vendorid . '_accounthistory')->insert([
                        'account' => $username, 'amount' => round(abs($differ), 2), 'description' => 'Fish reward ', 'name' => $name,
                        'frombalance' =>   $rewardpre, 'tobalance' => $reward, 'created_at' => now(), 'updated_at' => now(), 'color' => 2
                    ]);
                    return response($previous + $bet);
                    break;
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return response($amount);
    }
}
