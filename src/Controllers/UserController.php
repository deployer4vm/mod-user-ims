<?php

namespace hpsynapse\moduser\Controllers;

use Illuminate\Http\Request;
use Facades\hpsynapse\moduser\Repositories\UserRepo;
use Facades\hpsynapse\moduser\Repositories\RoleRepo;
use Facades\hpsynapse\moduser\Services\UserAuth;
use App\Base\BaseController;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->forceApiOutput();
    }
    
    public function readList(Request $request) {
        
        $filter = [
            'q'=>$request->input('q', null)
        ];

        //jika menyertakan status
        if($request->input('status', null))
            $filter[] = ['status', $request->input('status')];
        
        if(UserAuth::isLogin()){
            $filter[] = ['id','!=',UserAuth::user('id')];
            $filter[] = ['level','>',UserAuth::user('level')];
        }            
        
        $this->output['data']['offset'] = $request->input('offset', 0);
        $this->output['data']['limit'] = $request->input('limit', 10);

        $this->output['data'] = UserRepo::listUser(
            $filter, $this->output['data']['offset'], $this->output['data']['limit']
        );

        return $this->done();
    }
    
    public function readOne(Request $request)
    {
        $id = $request->route('id');
        $this->output['data'] = UserRepo::getUser($id);
        $this->output['data']['user_role'] = UserRepo::getUserRole($this->output['data']['id']);
        foreach($this->output['data']['user_role'] as $key => $val) {
            if($val['is_main_role']){
                $this->output['data']['role_code'] = $key;
            }
        } 

        return $this->done();
    }

    /**
     * 
     */
    public function create(Request $request)
    {
        $userData = $request->all();//$request->only(['name', 'email', 'password']);

        $validator = [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:5|max:255'
        ];

        if(isset($userData['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }

        $validator = \Validator::make($userData, $validator);

        if ($validator->fails()) {       
            $this->setError(__('validation.inputerror'),$validator->messages());
            return $this->done();
        }
        
        //jika berhasil
        if (UserRepo::register($userData,false)) {            
            $this->setAlert('Data Inserted successfully','success');
        }else{
            $this->setError(UserRepo::error(),'success');
        }        
        
        return $this->done();
    }

    /**
     * Update user
     */
    public function update(Request $request)
    {
        $id = $request->route('id');

        $input = $request->all();

        $validator = [];

        if(isset($input['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        if(isset($input['name'])){
            $validator['name'] = 'required|min:3|max:255';
        }
        if(isset($input['email'])){
            $validator['email'] = 'required|email|min:3|max:255';
        }
        if(isset($input['phone'])){
            $validator['phone'] = 'required|min:3|max:255';
        }

        if(!empty($validator)){
            $validator = \Validator::make($input, $validator); 
            if ($validator->fails()) {
                $this->setError('Input Error :',$validator->messages(),400,true);
                return $this->done();
            }
        }

        if($input['banned_note'] == null){
            unset($input['banned_note']);
        }

        if(UserRepo::updateUser($id, $input)) {            
            $this->setAlert('Data Updated successfully','success');
        }else{
            $this->setAlert(UserRepo::error(),'danger');
            $this->setError(UserRepo::error());
        }

        return $this->done();
    }
    

    public function updateProfile(Request $request)
    {
        
        if(UserAuth::isLogin()){
            $id = UserAuth::user('id');
        }else{
            $this->setError('User belum login');
            return $this->done();
        }

        $input = $request->all();

        $validator = [];

        if(isset($input['username'])){
            $validator['username'] = 'required|min:3|max:255';
        }
        if(isset($input['name'])){
            $validator['name'] = 'required|min:3|max:255';
        }
        if(isset($input['email'])){
            $validator['email'] = 'required|email|min:3|max:255';
        }
        if(isset($input['phone'])){
            $validator['phone'] = 'required|min:3|max:255';
        }

        if(!empty($validator)){
            $validator = \Validator::make($input, $validator); 
            if ($validator->fails()) {
                $this->setError('Input Error :',$validator->messages(),400,true);
                return $this->done();
            }
        }
        
        if(isset($input['role_code']))unset($input['role_code']);
        if(isset($input['status']))unset($input['status']);

        if(UserRepo::updateUser($id, $input)) {            
            $this->setAlert('Data Updated successfully','success');
        }else{
            $this->setAlert(UserRepo::error(),'danger');
            $this->setError(UserRepo::error());
        }

        return $this->done();
    }
    
    /**
     * update password
     * 
     * @param Request $request
     *      password
     *      password_confirmatin
     */
    public function updatePassword(Request $request)
    {
        $id = $request->route('id');
        $userData = $request->all();

        $validator = \Validator::make($userData, [
            'password' => 'required|min:8|max:255',
            'password_confirmation' => 'required|min:8|max:255|same:password'
        ]);

        if ($validator->fails()) {
            $this->setError('Input Error :',$validator->messages(),400,true);
            return $this->done();
        }

        $change = UserRepo::updateUser($id, $userData);
        if (!$change) {
            return $this->done();
        }

        $this->setAlert('Password Updated successfully','success');
        return $this->done();
    }

    public function suspend(Request $request)
    {
        $id = $request->route('id');

        UserRepo::suspendUser($id);
        return $this->done();
    }

    public function delete(Request $request)
    {

        $id = $request->route('id');
           
        if(UserAuth::isLogin() && $id != UserAuth::user('id')){
            $filter[] = ['id', $id];
            $filter[] = ['level','>',UserAuth::user('level')];
            $data = UserRepo::listUser($filter);
            if($data['count']<=0){
                $this->setError('Permission denied');
                return $this->done();;
            }
        }   

        if(!UserRepo::deleteUser($id)){
            $this->setError('Error : '.UserRepo::error());
        }
        return $this->done();
           
    }
}
