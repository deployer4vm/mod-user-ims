<?php

namespace App\Modules\Auth\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Facades\BSSystem\LIBAccount\Repositories\UserRepo;
use Facades\BSSystem\LIBAccount\Repositories\RoleRepo;
use Facades\BSSystem\LIBAccount\Repositories\UserNotifRepo;

use App\Modules\Auth\Responses\AuthResponse;
use Facades\App\Services\AcSSOService;

use BSSystem\Core\Base\BaseController;

class RegisterController extends BaseController
{

    use AuthResponseTraits;

    public function __construct()
    {
        $this->middleware('guest');        
    }

    public function register(Request $request, $apps_code = '')
    {
        $data['backlink'] = $request->input('backlink');
        return view('auth.register', $data);
    }

    public function doRegister(Request $request, $apps_code = '')
    {        
        $returnParam['backlink'] = $response['backlink'] = $request->input('backlink')?$request->input('backlink'):config('cur_apps.home_url');
        $returnParam['apps_code'] = $apps_code;        
        $response['isRegistered'] = 1;
        $response['isLogin'] = 1;
        $response['reff'] = 'register';
            
        $userData = $request->all();//$request->only(['name', 'email', 'password', 'password_confirmation']);
        $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:20',
            'password' => 'required|min:5|max:255',
            'password_confirmation' => 'required|min:5|max:255|same:password',
            'tos_confirm' => 'required'
        ]);
        
        $regUserData = UserRepo::register($userData);
        
        //jika berhasil
        if ($regUserData) {            
            $userModel = UserRepo::getUserModel($regUserData['id']);
            Auth::login($userModel);
            AcSSOService::setUser(
                $regUserData['id'],
                [
                    'id'=>$regUserData['token_id'],
                    'api_token'=>$regUserData['token']
                ]);
            $response['token'] = AcSSOService::getUserToken();
            UserRepo::activateUser($regUserData['id']);
            $response['alert'] = ['type' => 'info', 'message' => __('auth.registersuccess')];
            return $this->authDone($response);
        }
        
        return redirect()->route('auth.register', $returnParam)->with('alert', ['type' => 'warning', 'message' => __('auth.registerfailed',['error' => UserRepo::error()])])->withInput();
    }
    
    /*
     * =========================================================================
     */
    
    /**
     * API REQUEST ONLY
     * 
     * Api resource untuk registrasi
     * 
     * @param Request $request
     * @param type $apps_code
     */
    public function apiRegister(Request $request, $apps_code = '')
    {            
        $userData = $request->all();//$request->only(['name', 'email', 'gender', 'password', 'password_confirmation']);
        $validator = \Validator::make($userData, [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:20',
            'password' => 'required|min:5|max:255',
            'tos_confirm' => 'required'
        ]);
        
        $response = ['status'=>400,'message'=>'Authentificaion Failed','data'=>null,'errors'=>[true]];
        
        if ($validator->fails()) {            
            $response['message'] = __('validation.inputerror');
            $response['errors'] = [$validator->messages()];
        }else{
        
            $regUserData = UserRepo::register($userData,false);

            //jika berhasil
            if ($regUserData) {            
                UserRepo::activateUser($regUserData['id']);   
                
                if($request->input('pushNotifToken')){
                    $pushParam = [
                        'token' => $request->input('pushNotifToken'),
                        'type' => $request->input('pushType',1)
                    ];
                }
                $token = UserRepo::generateToken($regUserData['id'],0,1,$request->input('deviceId',''));
                $response['status'] = 200;
                $response['errors'] = null;
                $response['message'] = __('auth.registersuccess');
                $response['data'] = AcSSOService::getCurTimeStamp();
                $response['data']['token'] = $token['api_token'];
                $response['data']['userData'] = $regUserData;
                $response['data']['userRole'] = RoleRepo::getRoleByUserId($regUserData['id']); 
                
                //subscribekan ke channel/topic berdasarkan user role nya
                if($request->input('pushNotifToken')){
                    $notifChannel[] = 'all';
                    if(isset($response['data']['userRole'][9]))$notifChannel[] = 'member';                        
                    if(isset($response['data']['userRole'][10]))$notifChannel[] = 'reseller';                    
                    if(isset($response['data']['userRole'][11]))$notifChannel[] = 'merchant';
                    if($response['data']['userData']['is_admin'])$notifChannel[] = 'admin';

                    UserNotifRepo::subscribeToChannel($notifChannel,$pushParam['token']);
                }
            
            }else{
                $response['message'] = __('auth.registerfailed',['error' => UserRepo::error()]);
            }
        }
        
        return response()->json($response,$response['status']);
    }
}
