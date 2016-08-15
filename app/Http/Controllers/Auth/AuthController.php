<?php
/**
 * Created by PhpStorm.
 * User: DuongLT
 * Date: 8/4/2016
 * Time: 4:40 PM
 */

namespace App\Http\Controllers\Auth;

use App\Exceptions\BusinessException;
use App\Http\Controllers\ApiControllerTrait;
use App\User as User;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exception\HttpResponseException;


use Illuminate\Support\Facades\Validator;
use League\Flysystem\Exception;
use Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Tymon\JWTAuth\Facades\JWTAuth;


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

    public function postLogin(Request $request)
    {
        try
        {
            $this->validate($request, [
                'email' => 'required|email|max:255', 'password' => 'required',
            ]);
        }
        catch (HttpResponseException $e)
        {
            return response()->json([
                'error' => [
                    'message'     => 'Invalid auth',
                    'status_code' => IlluminateResponse::HTTP_BAD_REQUEST
                ]],
                IlluminateResponse::HTTP_BAD_REQUEST,
                $headers = []
            );
        }

        $credentials = $this->getCredentials($request);

        try
        {
            // attempt to verify the credentials and create a token for the user
            if ( ! $token = JWTAuth::attempt($credentials))
            {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        }
        catch (JWTException $e)
        {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
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

    public function postSignup(Request $request){

        Log::addInfo("hello world");
        try {
            $validator = Validator::make($request->all(), User::$rules);
            if ($validator->fails()) {
                return $this->error($validator->errors());
            }
            /*try{
                User::query()->where('name', $request->input('name'))->firstOrFail();    
            }catch (ModelNotFoundException $ex){
                throw new BusinessException($ex->getMessage());
            }*/
            
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = \Hash::make($request->input('password'));
            $user->save();
            return $this->respond($user);
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            return $this->error([$e->getMessage()]);
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

//        if ($validator->fails()) {
//            return $this->error($validator->messages());
//        }

//        $email = $request->get('email');
//        $password = $request->get('password');

//        $attributes = [
//            'email' => $email,
//            'password' => app('hash')->make($password),
//        ];

//        $user = $this->userRepository->create($attributes);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = \Hash::make($request->input('password'));
        $user->save();

        // 用户注册事件
        $token = $this->auth->fromUser($user);

        return response()->json(compact('token'));
    }

}