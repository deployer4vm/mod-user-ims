<?php

namespace hpsynapse\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use hpsynapse\moduser\Controllers\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Log;

use hpsynapse\moduser\Facades\UserRepo;
use hpsynapse\moduser\Facades\UserNotifRepo;
use hpsynapse\moduser\Facades\RoleRepo;

use hpsynapse\moduser\Facades\UserAuth;

//use Carbon\Carbon;

use App\Base\BaseController;

class LoginController extends BaseController
{

    use ThrottlesLogins,
        AuthResponseTraits;

    public function __construct()
    {
        $this->middleware('guest')->except(['logout','revalidate']);
    }
    
    // public function username()
    // {
    //     return 'email';
    // }

    /**
     * halaman login WEB, baik SSO dan non SSO
     */
    public function login(Request $request)
    {
        $data['backlink'] = $request->input('backlink');

        return view('auth.login', $data);
    }

    /**
     * halaman login WEB, baik SSO dan non SSO
     */
    public function doLogin(Request $request)
    {
        $returnParam = [];
        $returnParam['backlink'] = $response['backlink'] = $request->input('backlink','');

        // jika menyertakan appCode berarti SSO
        if($request->route('appCode')){
            $returnParam['appCode'] = $request->route('appCode');
            // get app Data
            // ...
            $appData = ['url_home'=>'','url_get_session'=>''];

            $backLink = $appData['url_get_session'];// ke halaman get session applikasi menggunakan SSO ini
        }else{
            $backLink = $request->input('backlink',route('dashboard'));
        }

        
        $response['reff'] = 'login';
        
        $request->validate([
            'username' => 'required|max:255',
            'password' => 'required|min:3|max:255'
        ]);

        $authData = $request->only('username', 'password');
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            Log::info('Login Failed ! user : "'.$authData['username'].'" - password : "'.$authData['password'].'"');
            Log::info('Too many login attemp');
            return $this->sendLockoutResponse($request);
        }
        
        $tenantId = config('tenant.id');
        if (config('AppConfig.system.multitenant.autodetect_login')==1) $tenantId = null;
        
        if($user = UserRepo::loginCheck($authData['username'],$authData['password'], $tenantId)){
            if (Auth::attempt(
                    ['username'=>$user['username'],'password'=>$authData['password']], $request->filled('remember')
                ) || Auth::attempt(
                    ['email'=>$user['email'],'password'=>$authData['password']], $request->filled('remember')
                )) {
            
                //$request->session()->regenerate();
                $this->clearLoginAttempts($request);
                $userData = Auth::user();
                
                //jika di banned
                if($userData->status==2){
                    return redirect()->route('auth.login', $returnParam)->with('alert', ['type' => 'danger', 'message' => __('auth.login.alert.user_banned')]);
                //jika pertama kali aktifikasi
                }else if($userData->status==0){
                    UserRepo::updateUser($userData->id,['status'=>1]);
                    UserRepo::activateUser($userData->id);
                }
                
                UserAuth::setUser($userData->id);
                
                $response['isLogin'] = 1;
                $response['token'] = UserAuth::getToken();
               
                return redirect()->away($backLink.'?'.http_build_query($response));
            }
        }

        $this->incrementLoginAttempts($request);

        return redirect()->route('auth.login', $returnParam)->with('alert', ['type' => 'warning', 'message' => 'Login Failed.']);
    }
    
    public function logout(Request $request)
    {
        
        UserAuth::unsetUser();
        Auth::logout();
        
        //$request->session()->invalidate();
        $response['backlink'] = $request->input('backlink')?$request->input('backlink'):config('cur_apps.home_url');
        $response['reff'] = 'logout';
        $response['isLogin'] = 0;
        
        return $this->authDone($response,route('auth.login'));
    }

    /**
     * halaman yang diakses pertama oleh applikasi pengguna SSO untuk detect session
     * user saat ini
     */
    public function revalidate(Request $request)
    {
        $returnParam = [];
        $returnParam['backlink'] = $response['backlink'] = $request->input('backlink','');

        if($request->route('appCode')){
            $returnParam['appCode'] = $request->route('appCode');
            // get app Data
            // ...
            $appData = ['url_home'=>'','url_get_session'=>''];

            $backLink = $appData['url_get_session'];// ke halaman get session applikasi menggunakan SSO ini
        }else{
            // jika tidak menyertakan appCode maka redirect ke halaman login jika sudah login 
            // dan ke home jika belum login
            $this->response = redirect();
        }

        return $this->done();
    }
    
    /**
     * API
     * =========================================================================
     */
    
    /**
     * API OUTPUT ONLY
     * create token user
     * 
     * @param Request $request
     *      username
     *      password
     *      
     * @return Array default synapse api return
     *      data
     *          user        Array record
     *          tenant
     *          role_code   String main role code
     *          role        Array list role yg dimiliki user
     *          token       string
     *          lastUpdate
     *          validUntil
     */
    public function apiLogin(Request $request)
    {
        $this->forceApiOutput();

        $authParam = $request->only('username', 'password');

        if(!isset($authParam['username']) ||!isset($authParam['password'])){
            $this->setError(__('alert.incorect_parameter'));
            return $this->done();
        }        

        $tenantId = config('tenant.id');
        if (config('AppConfig.system.multitenant.autodetect_login')==1) 
            $tenantId = null;

        if($user = UserRepo::loginCheck($authParam['username'],$authParam['password'], $tenantId)){
            $pushParam = false;
            if($request->input('pushNotifToken')){
                $pushParam = [
                    'token' => $request->input('pushNotifToken'),
                    'type' => $request->input('pushType',1)
                ];
            }
            $this->output['message'] = __('alert.auth_success');
            $this->output['data'] = UserAuth::getCurTimeStamp();
            
            $this->output['data']['user'] = $user;

            if(isset($user['tenant']))
                $this->output['data']['tenant'] = $user['tenant'];
            
            $this->output['data']['role'] = UserRepo::getUserRole($user['id']);
            foreach($this->output['data']['role'] as $key => $val) {
                if($val['is_main_role']){
                    $this->output['data']['role_code'] = $key;
                }
            }

            // jika main role tidak ada berarti ada yang salah di insert user ke databasenya
            if(!isset($this->output['data']['role_code'])){
                $this->setError(__('alert.auth_failed').'<br><i>Main Role</i> user tidak terdeteksi.');
                return $this->done();
            }

            $this->output['data']['role'] = UserRepo::getUserRole($user['id']);        
            $token = UserRepo::generateToken($user['id'],$this->output['data']['role_code'],1,$request->input('deviceId',''),$pushParam);
            $this->output['data']['token'] = $token['api_token'];

            //subscribekan ke channel/topic berdasarkan user role nya
            if($request->input('pushNotifToken')){
                $notifChannel[] = 'all';
                // if($response['data']['userData']['is_admin'])$notifChannel[] = 'admin';
                                
                UserNotifRepo::subscribeToChannel($notifChannel,$pushParam['token']);
            }
            return $this->done();
        }
        // $this->setError(__('alert.auth_failed'));
        $this->setError(UserRepo::errorFull());
        return $this->done();
    }
    
    /**
     * 
     * @param Request $request
     */
    public function apiLogout(Request $request)
    {
        
    }
}
