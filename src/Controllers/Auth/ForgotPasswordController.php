<?php

namespace hpsynapse\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facades\hpsynapse\moduser\Repositories\UserRepo;

use App\Base\BaseController;

class ForgotPasswordController extends BaseController
{

    use AuthResponseTraits;
    
    protected $responsableName = 'App\Modules\Auth\Responses\AuthResponse';

    public function __construct()
    {
        $this->middleware('guest');//->except('logout');
    }

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
        $this->data['data']['email'] = $request->input('email');
        $this->response = 'auth.forgotpasswordsuccess';
        
        $user = UserRepo::getUser(['email',$this->data['data']['email']]);
        
        if(!$user || $user['status'] != 1){
            $this->data['status'] = 400;
            $this->data['message'] = 'Email tidak terdaftar.';
            $this->response = redirect(url()->previous())->withInput();
            return $this->done();            
        }
        
        UserRepo::sendUserResetPasswordEmail($user['id'],config('cur_apps.id'));
        $this->data['message'] = 'Email instruksi forgot password telah dikirim ke email Anda.';
        return $this->done();
    }

}
