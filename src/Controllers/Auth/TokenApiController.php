<?php

namespace hpsynapse\moduser\Auth\Controllers;

use Facades\hpsynapse\moduser\Repositories\UserNotifRepo;

use Facades\hpsynapse\moduser\Repositories\UserRepo;
use Facades\hpsynapse\moduser\Repositories\RoleRepo;
use Facades\hpsynapse\moduser\Repositories\AppsRepo;
use Facades\hpsynapse\moduser\Repositories\SessionRepo;
use Facades\hpsynapse\moduser\Services\UserAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use BSSystem\Core\Base\BaseController;

class TokenApiController extends BaseController
{
    /**
     * API OUTPUT ONLY
     * /[APPS_CODE]/token
     * 
     * generate new apps token
     * 
     * @param Request $request
     *      deviceId : device id mobile apps, *optional
     * 
     * @param type $apps_code
     */
    public function generateToken(Request $request, $apps_code = '')
    {        
        $token = AppsRepo::generateToken(config('cur_apps.id'),0,1,$request->input('deviceId',''));
        $response['status'] = 200;
        $response['message'] = 'Token Generated';
        $response['data'] = ['token'=>$token['api_token']];
        $response['errors'] = null;
        
        return response()->json($response);
    }
    
    /**
     * API OUTPUT ONLY
     * /auth/token/validate
     * 
     * validate token and get user data
     * 
     * @param Request $request
     */
    public function validateToken(Request $request)
    {
        $apiToken = $request->user()->toArray();        
        $isTokenValid = $apiToken['is_apps_token']?
            AppsRepo::isTokenValid($apiToken['api_token']):
            UserRepo::isTokenValid($apiToken['api_token']);
                                
        $response['status'] = 200;
        //jika token sudah tidak valid
        if(!$isTokenValid){
            $response['message'] = 'Token Invalid';
            $response['data'] = null;
            $response['errors'] = [true];
        }else{
            $response['message'] = 'Token Valid';
            $response['data'] = UserAuth::getCurTimeStamp();
            //jika user token
            if($apiToken['is_apps_token']){
                $response['data']['userData'] = null;
                $response['data']['userRole'] = null;
            }else{
                $response['data']['userData'] = UserRepo::getOneWithProfile($apiToken['user_id']);
                $response['data']['userRole'] = RoleRepo::getRoleByUserId($apiToken['user_id']);
                $notifToken = $request->input('pushNotifToken',false);
                //jika menyertakan update token notif
                if($notifToken){
                    //jika token berubah maka subscribe ulang
                    if($apiToken['push_token'] = $notifToken){
                        $notifChannel[] = 'all';
                        if(isset($response['data']['userRole'][9]))$notifChannel[] = 'member';                        
                        if(isset($response['data']['userRole'][10]))$notifChannel[] = 'reseller';                
                        if(isset($response['data']['userRole'][11]))$notifChannel[] = 'merchant';
                        if($response['data']['userData']['is_admin'])$notifChannel[] = 'admin';
                        
                        UserNotifRepo::subscribeToChannel($notifChannel,$notifToken);
                        UserNotifRepo::unsubscribeFromChannel($apiToken['push_token'],$notifToken);
                        
                        UserNotifRepo::changePushToken($apiToken['push_token'],$notifToken);
                    }
                }
            }
            $response['errors'] = null;
            AppsRepo::setSingleTokenLastUpdate($apiToken['api_token'],$response['data']['lastUpdate']);
        }
        
        return response()->json($response, 200);
    }
}