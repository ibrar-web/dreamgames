<?php

namespace App\Http\Controllers;
// namespace App\Http\Middleware;
use App\Models\User;
use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Storage;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Arr;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Cookie;
class UserGamesController extends Controller
{
 public function findgame(Request $request)
    {
        try {
            // Log::info($request->all());
            $data = [];
            $data['status'] = 'Success';
            $id = $request->input('GameId');
            $id = DB::table('gamescontrol')->where('GameID', $id)->pluck('id');
            if (count($id) > 0) {
                $data['game'] = 'index' . $id[0];
                return $data;
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }
    }
    public function game(Request $request,$gameid,$userid,$token,$username)
    {
       $User=$this->decrypt($username);
       $Game=$this->decrypt($gameid);
       if($this->login($User)){
            if (Auth::check()) {
                $expiresAt = Carbon::now()->addMinutes(2);
                Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);
                User::where('id', Auth::user()->id)->update(['last_seen' => (new \DateTime())->format("Y-m-d H:i:s")]);
            }
           $user = Auth::user()->id;

            $verifytoken=DB::table('users')->where('id',$userid)->pluck('token')[0];
            if($verifytoken==$token){
                $newtoken=$this->random_strings(64);
                 DB::table('users')->where('id',$userid)->update(['token' =>$newtoken ,'created_at'=>now()->addMinutes("5")]);
                 
                //  return view('game/' .$Game,['user_id'=>$userid,''=>$newtoken])
                //  ->withCookie(cookie('name','dsadsa',3));
                 $cookie = cookie('salman','askdj askdj laksjdl askdlajskd alsjdkals jd',2);
                 return response(view('game/' .$Game,['user_id'=>$userid,''=>$newtoken]))->cookie($cookie);
                 //return response(view('game/' .$Game,['user_id'=>$userid,''=>$newtoken]))->cookie('name','value',2);
            }else{
               return abort(404);
            } 
       }else{
           return abort(404);
       }
   
    }
    protected function decrypt($data){
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryption =$data;
        $decryption_iv = '1234567891011121';
        $decryption_key = "xznmsdfgkluiodasop";
        $decryption=openssl_decrypt ($encryption, $ciphering, 
                $decryption_key, $options, $decryption_iv);
        return  $decryption; 
    }
    protected function login($username){
            $fieldType = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            if (auth()->attempt(array($fieldType => $username, 'password' => $username))) {
                $user = Auth::user()->id;
                $confirmation = DB::table('users')
                    ->select('role', 'ban', 'dispute', 'close')
                    ->where('id', $user)
                    ->get()
                    ->toArray();
                $confirmation = array_map(function ($value) {
                    return (array)$value;
                },   $confirmation);
                if ($confirmation[0]['ban'] != 'active' || $confirmation[0]['dispute'] || $confirmation[0]['close']) {
                    Auth::logout();
                    return false;
                }
                return true;
            } else {
               return false;
            }
    }
    protected function random_strings($length_of_string)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return substr(
            str_shuffle($str_result),
            0,
            $length_of_string
        );
    }
}
