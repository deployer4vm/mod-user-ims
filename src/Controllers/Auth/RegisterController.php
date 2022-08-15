<?php

namespace hpsynapse\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use hpsynapse\moduser\Facades\UserRepo;
use hpsynapse\moduser\Facades\RoleRepo;
use hpsynapse\moduser\Facades\UserNotifRepo;
use hpsynapse\moduser\Facades\UserAuth;

use App\Base\BaseController;

class RegisterController extends BaseController
{

    use AuthResponseTraits;

    public function __construct()
    {
        $this->middleware('guest');        
    }

    /**
     * Register Form (blade)
     */
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
     *      name
     *      email
     *      phone
     *      username        String      *optional jika tidak disertakan maka email akan dijadikan username
     *      role_code       String      *optional rolecode level 3 keatas
     *      tos_confirm     String      *optional
     * 
     * @return Array 
     *      on success :
     * 
     *      data
     *          user        Array record
     *          tenant
     *          role_code   String main role code
     *          role        Array list role yg dimiliki user
     *          token       string
     *          lastUpdate
     *          validUntil
     * 
     *      on failed :
     */
    public function apiRegister(Request $request)
    {            
        $userData = $request->all();//$request->only(['name', 'email', 'gender', 'password', 'password_confirmation']);

        // jika tidak menyer
        if(!isset($userData['username']))
            $userData['username'] = $userData['email'];

        // jika wajib ada tos_confirm maka validasi
        if(config('AppConfig.packageLocal.moduser.registration.tos_confirm',0)==1){
            if(!isset($userData['tos_confirm']) || $userData['tos_confirm'] == 0){
                $this->setError(__('auth.register.alert.tos_confirm_required'));
                return $this->done();
            }
        }

        // validasi user role jika ada "khusus level 3 keatas"
        if(isset($userData['role_code'])){

        }

        $validator = \Validator::make($userData, [
            'username' => 'required|min:3|max:255',
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'max:20',
            'password' => 'required|min:5|max:255'
        ]);
        
        if ($validator->fails()) {            
            $this->setError(__('auth.register.alert.validation_error'),$validator->messages());
        }else{        
            $regUserData = UserRepo::register($userData,false);

            //jika berhasil
            if ($regUserData) {      
                
                if(config('AppConfig.packageLocal.moduser.registration.auto_activate',0)==1)      
                    UserRepo::activateUser($regUserData['id']);   
                
                if($request->input('pushNotifToken')){
                    $pushParam = [
                        'token' => $request->input('pushNotifToken'),
                        'type' => $request->input('pushType',1)
                    ];
                }
                $this->output['message'] =  __('auth.register.alert.register_success');
                $this->output['data'] = UserAuth::getCurTimeStamp();
                $this->output['data']['user'] = $regUserData;
                $this->output['data']['role'] = UserRepo::getUserRole($regUserData['id']); 
                foreach($this->output['data']['role'] as $key => $val) {
                    if($val['is_main_role']){
                        $this->output['data']['role_code'] = $key;
                    }
                }  

                $token = UserRepo::generateToken($regUserData['id'],$this->output['data']['role_code'],1,$request->input('deviceId',''));
                $this->output['data']['token'] = $token['api_token'];

                //subscribekan ke channel/topic berdasarkan user role nya
                if($request->input('pushNotifToken')){
                    $notifChannel[] = 'all';
                    // if(isset($response['data']['role'][9]))$notifChannel[] = 'member';     

                    UserNotifRepo::subscribeToChannel($notifChannel,$pushParam['token']);
                }
            
            }else{
                $this->setError(UserRepo::error(),UserRepo::errorValidator());
            }
        }
        
        return $this->done();    
    }
}
