<?php
/**
 * Created by PhpStorm.
 * User: DuongLT
 * Date: 8/4/2016
 * Time: 4:40 PM
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiControllerTrait;
use App\Models\Cart;
use App\Models\UserBalance;
use App\User as User;
use Carbon\Carbon;
use Illuminate\Auth\AuthManager;


use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;
use League\Flysystem\Exception;
use Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    use ApiControllerTrait;
    protected $auth;
    public function __construct(AuthManager $auth)
    {

        $this->auth = $auth;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorWithStatus($validator->errors(), [],403);
        }

        $credentials = $request->only('email', 'password');

        if (! $token = $this->auth->attempt($credentials)) {
            return $this->errorWithStatus(["token"=>false], [], 403);
        }

        //add user to CartInfo
        //@todo apply envent listenner
        $appSession = $request->header("app-session");
        $cart = Cart::query()->with('items')->where('app_session',$appSession)
            ->where('status', Cart::STATUS_INIT)
            ->first();
        if($cart){
            if(!$cart->user_id){
                $user = $this->auth->user();
                $cart->user_id =  $user->id;
                $cart->save();
            }
        }

        return $this->respond(compact('token'));
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    public function signup(Request $request){

        try {
            $validator = Validator::make($request->all(), User::$rules);
            if ($validator->fails()) {
                return $this->errorWithStatus($validator->errors(), [], 500);
            }
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = \Hash::make($request->input('password'));
            $user->save();

            $userBalance = new UserBalance();
            $userBalance->user_id = $user->id;
            $userBalance->main_balance = 0;
            $userBalance->secondary_balance = 0;
            $userBalance->status = UserBalance::STATUS_ACTIVE;
            $userBalance ->save();

            return $this->respond($user);
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $this->errorWithStatus([$e->getMessage()], [], 500);
        }
    }

    public function facebook(Request $request){

        try {

            $accessToken = $request->input("token");

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'https://graph.facebook.com/me?access_token='.$accessToken
            ));
            $result = curl_exec($curl);
            $facebookArr = json_decode($result, true);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            //@todo: check in db if user not exist -> create new else retrieve user infor by facebook id
            if(IlluminateResponse::HTTP_OK == $httpcode){
                $this->validateFacebook($facebookArr['email']);
                $fbId = $facebookArr['id'];
                $user = User::query()->where('facebook_id',$fbId)->first();
                if(!$user){
                    $user = new User();
                    $user->email = $facebookArr['email'];
                    $user->facebook_id = $facebookArr['facebook_id'];
                    $user->locale = $facebookArr['locale'];
                    $user->time_zone = $facebookArr['time_zone'];
//                    $user->birthday = $facebookArr['time_zone'];
//                    $user->sex = $facebookArr['sex'];

                    $user->last_login = Carbon::now()->toDateTimeString();
                    $user->save();

                    $userBalance = new UserBalance();
                    $userBalance->user_id = $user->id;
                    $userBalance->main_balance = 0;
                    $userBalance->secondary_balance = 0;
                    $userBalance->status = UserBalance::STATUS_ACTIVE;
                    $userBalance ->save();
                   
                }else{
                    $user->last_login = Carbon::now()->toDateTimeString();
                    $user->save();
                }

                $token = $this->auth->login($user);

                return $this->respond(compact('token'));
            }else{
                return $this->error(['code'=>$facebookArr['code'], 'message'=>$facebookArr['message']]);
            }
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $this->error([$e->getMessage()]);
        }
    }


    /**
     * @api {post} /token/refresh 
     * @apiDescription refresh token
     * @apiGroup Auth
     * @apiPermission JWT
     * @apiVersion 0.1.0
     * @apiHeader {String} Authorization 
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL21vYmlsZS5kZWZhcmEuY29tXC9hdXRoXC90b2tlbiIsImlhdCI6IjE0NDU0MjY0MTAiLCJleHAiOiIxNDQ1NjQyNDIxIiwibmJmIjoiMTQ0NTQyNjQyMSIsImp0aSI6Ijk3OTRjMTljYTk1NTdkNDQyYzBiMzk0ZjI2N2QzMTMxIn0.9UPMTxo3_PudxTWldsf4ag0PHq1rK8yO9e5vqdwRZLY"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         token: 9UPMTxo3_PudxTWldsf4ag0PHq1rK8yO9e5vqdwRZLY.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL21vYmlsZS5kZWZhcmEuY29tXC9hdXRoXC90b2tlbiIsImlhdCI6IjE0NDU0MjY0MTAiLCJleHAiOiIxNDQ1NjQyNDIxIiwibmJmIjoiMTQ0NTQyNjQyMSIsImp0aSI6Ijk3OTRjMTljYTk1NTdkNDQyYzBiMzk0ZjI2N2QzMTMxIn0.eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
     *     }
     */
    public function refreshToken(){
        $token = $this->auth->refresh();
        return $this->respond(compact('token'));
    }

    private function validateFacebook($email)
    {
        
    }


}