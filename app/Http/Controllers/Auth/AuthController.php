<?php
/**
 * Created by PhpStorm.
 * User: DuongLT
 * Date: 8/4/2016
 * Time: 4:40 PM
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiControllerTrait;
use App\User as User;
use Illuminate\Auth\AuthManager;


use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;
use League\Flysystem\Exception;
use Log;
use Illuminate\Http\Request;


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
            return $this->error($validator->errors());
        }

        $credentials = $request->only('email', 'password');

        if (! $token = $this->auth->attempt($credentials)) {
            $this->respond(trans('auth.incorrect'));
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
                return $this->error($validator->errors());
            }
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

   

}