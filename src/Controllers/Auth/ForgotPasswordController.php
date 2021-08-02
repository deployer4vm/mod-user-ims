<?php

namespace hpsynapse\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use hpsynapse\moduser\Facades\UserRepo;

use App\Base\BaseController;

class ForgotPasswordController extends BaseController
{

    use AuthResponseTraits;
    
    // protected $responsableName = 'App\Modules\Auth\Responses\AuthResponse';

    public function __construct()
    {
        $this->middleware('guest');//->except('logout');
    }

    /**
     * halaman form forgot password
     */
    public function forgotPassword(Request $request, $apps_code = '')
    {
        $data['backlink'] = $request->query('backlink');
        return view('auth.forgotpassword', $data);
    }

    /**
     * 
     * @param Request $request
     * @param type $apps_code
     * @return type
     */
    public function doForgotPassword(Request $request, $apps_code = '')
    {        
        $this->output['data']['email'] = $request->input('email');
        $this->response = 'auth.forgotpasswordsuccess';
        
        $user = UserRepo::getUser(['email',$this->output['data']['email']]);
        
        if(!$user || $user['status'] != 1){
            $this->setError(__('auth.forgotpassword.alert.email_not_registered'));
            $this->response = redirect(url()->previous())->withInput();
            return $this->done();            
        }
        
        UserRepo::sendUserResetPasswordEmail($user['id'],config('cur_apps.id'));
        $this->output['message'] = __('auth.forgotpassword.alert.forgot_password_success');
        return $this->done();
    }

}
