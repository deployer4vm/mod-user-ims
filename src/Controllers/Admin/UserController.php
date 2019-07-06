<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use Facades\BSSystem\LIBAccount\Repositories\UserRepo;
use Facades\BSSystem\LIBAccount\Repositories\RoleRepo;

use BSSystem\Core\Base\BaseController;

class UserController extends BaseController
{
    public function __construct()
    { 
        \Breadcrumb::add('Home', route('admin.dashboard'));
        \Breadcrumb::add('User', route('user.index'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('acuser.list');
    }

    public function userDataTables()
    {
        return UserRepo::getUserDataTables();
    }

    public function create()
    {
        \Breadcrumb::add('Create User ', route('user.create'));

        $data['mode'] = 'create';
        $data['role'] = RoleRepo::listRole(['user_level'=>98]); 

        return view('acuser.form', $data);
    }

    public function store(Request $request)
    {
        $userData = $request->all();//$request->only(['name', 'email', 'password']);
        // dd($userData);
        $validator = \Validator::make($userData, [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:20',
            'password' => 'required|min:5|max:255',
            'role'=> 'required',
//            'role_code'=> 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.create')->with('alert', ['type' => 'warning', 'message' => __('auth.registerfailed',['error' => $validator->messages()])])->withInput();
        }

        $regUserData = UserRepo::register($userData);
        
        //jika berhasil
        if ($regUserData) {            
            $this->setAlert('Data Inserted successfully','success');
            return redirect()->route('user.index');
        }
        
        return redirect()->route('user.create')->with('alert', ['type' => 'warning', 'message' => __('auth.registerfailed',['error' => UserRepo::error()])])->withInput();
    }

    public function edit($userId)
    {
        $data['data'] = UserRepo::getUser($userId);
        $data['mode'] = 'edit';
        $data['role'] = RoleRepo::listRole(['user_level'=>98]); 
        $data['data_role'] = RoleRepo::getRoleByUserId($userId);
        $data['user_role'] = RoleRepo::getUserRoleCode($userId);
        \Breadcrumb::add('Edit : '.$data['data']['name'], route('user.create'));
        // foreach ($data['data_role'] as $key => $value) {
        //     dd($value['has_auth_grant']);
        // }
        // dd($data);
        return view('acuser.form', $data);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:20',
            'role'=> 'required',
//            'role_code'=> 'required',
//            'is_main_role'=> 'required'
        ]);

        if ($validator->fails()) {
            $this->setError('Input Error :',$validator->messages(),400,true);
            return $this->done();
        }

        if($input['banned_note'] == null){
            unset($input['banned_note']);
        }

        // dd($input);

        UserRepo::updateUser($id, $input);
        $this->setAlert('Data Updated successfully','success');
        return redirect()->route('user.index');
    }

    public function suspend($id)
    {
        UserRepo::suspendUser($id);
        return response()->json(['status' => 'ok', 'code' => 200]);
    }

    public function password(Request $request, $id)
    {
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
            return redirect()->route('user.create')->with('alert', ['type' => 'warning', 'message' => __('auth.registerfailed',['error' => 'Kata Sandi Lama Salah !'])])->withInput();
        }

        $this->setAlert('Password Updated successfully','success');
        return redirect()->route('user.index');
    }

    public function destroy($id)
    {
           
    }
}
