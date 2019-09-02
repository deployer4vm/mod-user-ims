<?php

namespace hpsynapse\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;

use Facades\hpsynapse\moduser\Repositories\UserRepo;
use Facades\hpsynapse\moduser\Repositories\UserNotifRepo;
use Facades\hpsynapse\moduser\Repositories\RoleRepo;

use Facades\hpsynapse\moduser\Services\UserAuth;

//use Carbon\Carbon;

use App\Base\BaseController;

class LoginController extends BaseController
{

    use ThrottlesLogins,
        AuthResponseTraits;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function username()
    {
        return 'email';
    }

    /**
     * halaman web login
     */
    public function login(Request $request)
    {
        $data['backlink'] = $request->input('backlink');

        return view('auth.login', $data);
    }

    public function doLogin(Request $request)
    {
        $returnParam['backlink'] = $response['backlink'] = $request->input('backlink')?$request->input('backlink'):config('cur_apps.home_url');
        $response['reff'] = 'login';
        
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:3|max:255'
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if (Auth::attempt(
                $request->only('email', 'password'), $request->filled('remember')
            )) {
            
            //$request->session()->regenerate();
            $this->clearLoginAttempts($request);
            $userData = Auth::user();
            
            //jika di banned
            if($userData['status']==2){
                return redirect()->route('auth.login', $returnParam)->with('alert', ['type' => 'danger', 'message' => 'Login Failed. Account Banned.']);
            //jika pertama kali aktifikasi
            }else if($userData['status']==0){
                UserRepo::updateUser($userData['id'],['status'=>1]);
            }
            
            UserAuth::setUser($userData->id);
            
            $response['isLogin'] = 1;
            $response['token'] = UserAuth::getToken();
                        
            //jika belum aktif, maka aktifkan
            if($userData->status == '0')UserRepo::activateUser($userData->id);
                        
            return $this->authDone($response);
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
        
        return $this->authDone($response);
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
     * @return json array
     */
    public function apiLogin(Request $request)
    {
        $this->forceApiOutput();

        $authParam = $request->only('username', 'password');

        if(!isset($authParam['username']) ||!isset($authParam['password'])){
            $this->setError(__('alert.incorect_parameter'));
            return $this->done();
        }        
        
        if($user = UserRepo::loginCheck($authParam['username'],$authParam['password'], config('tenant.id'))){
            $pushParam = false;
            if($request->input('pushNotifToken')){
                $pushParam = [
                    'token' => $request->input('pushNotifToken'),
                    'type' => $request->input('pushType',1)
                ];
            }
            $token = UserRepo::generateToken($user['id'],1,$request->input('deviceId',''),$pushParam);
            $this->output['message'] = __('alert.auth_success');
            $this->output['data'] = UserAuth::getCurTimeStamp();
            $this->output['data']['token'] =$token['api_token'];            
            $this->output['data']['user'] = $user;
            $this->output['data']['role'] = UserRepo::getUserRole($user['id']);
            foreach($this->output['data']['role'] as $key => $val) {
                if($val['is_main_role']){
                    $this->output['data']['role_code'] = $key;
                }
            }          
            
            // if(!isset($this->output['data']['role_code'])){
            //     $tmp = explode(';', trim($user['role'],";"));
            //     $this->output['data']['role_code'] = $tmp[0];
            // }
            
            //subscribekan ke channel/topic berdasarkan user role nya
            if($request->input('pushNotifToken')){
                $notifChannel[] = 'all';
                // if($response['data']['userData']['is_admin'])$notifChannel[] = 'admin';
                                
                UserNotifRepo::subscribeToChannel($notifChannel,$pushParam['token']);
            }
            return $this->done();
        }
        $this->setError(__('alert.auth_failed'));
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
