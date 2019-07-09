<?php

namespace hpsynapse\moduser\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facades\hpsynapse\moduser\Repositories\UserRepo;
use App\Base\BaseController;

class VerificationController extends BaseController
{

    public function verify(Request $request, $apps_code = '')
    {
        $data['verifyCode'] = $request->query('verifyCode');
        $data['email'] = $request->query('email');
        $data['apps_code'] = $apps_code;
                        
        //jika verified
        if(!UserRepo::varifyEmail($data['email'],$data['verifyCode'])){
            return redirect()->route('auth.emailVerification.fail', ['apps_code'=>$apps_code,'error_message'=>UserRepo::error()]);
        }

        // $userData = UserRepo::getUser(['email'=>$data['email']]);
        // if($userData['status']==0){
        //     $userData = UserRepo::resetPasswordEmailDataFormat($userData['id'],config('cur_apps.id'));
        //     return redirect($userData['resetPasswordUrl'].'&setNewPassword=true');
        // }else{
            return redirect()->route('auth.emailVerification.success', ['apps_code'=>$apps_code]);
        // }
        
    }
    
    public function verifySuccess(Request $request, $apps_code = '')
    {
        return view('user.auth.emailvalidatesuccess', $request->all());
    }
    
    public function verifyFail(Request $request, $apps_code = '')
    {
        return view('user.auth.emailvalidatefail', $request->all());
    }
    

}
