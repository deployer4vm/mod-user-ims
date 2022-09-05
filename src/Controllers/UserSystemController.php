<?php

namespace hpsynapse\moduser\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use hpsynapse\moduser\Facades\UserRepo;
use hpsynapse\moduser\Facades\RoleRepo;
use hpsynapse\moduser\Facades\UserAuth;
use hpsynapse\moduser\Facades\UserSystemRepo;

use App\Base\BaseController;

class UserSystemController extends BaseController
{
    protected $accessRuleKey = 'moduser.system_user';

    public function __construct()
    {
        $this->forceApiOutput();
    }

    public function readList(Request $request){
        if(!UserAuth::hasAccess($this->accessRuleKey,'r')){
            $this->setError(__('alert.access_denied'),false,403);
            return $this->done();
        }

        $orderBy = [];
        $filter = [];

        if($request->input('q', false))
            $filter['q'] = $request->input('q');

        //jika menyertakan status
        if($request->input('status', false))
            $filter[] = ['status', $request->input('status')];

        $filter[] = ['system_user', true];
        
        if(UserAuth::isLogin()){
            $filter[] = ['id','!=',UserAuth::user('id')];
            $filter[] = ['level','>',UserAuth::user('level')];
        }     
        
        //jika menyertakan status
        if($request->input('role', false))
            $filter[] = ['role', 'LIKE', '%;'.$request->input('role').';%'];
            
        if($request->input('level', false)){
            $filter[] = ['level', '!=', $request->input('level')];
        } else if($request->input('level_except', false)){
            $filter[] = ['level', '!=', $request->input('level_except')];
        }

        //jika multitenant aktif dan bukan dari aplikasi owner maka filter berdasarkan tenant nya
        if (config('AppConfig.system.multitenant.active',false) && config('tenant.id',0)!=1) {
            $filter[] = ['tenant_id', config('tenant.id')];
        }
        
        //jika menyertakan order by
        if ($request->input('orderBy', false))
            $orderBy = [$request->input('orderBy'), $request->input('orderType', 'ASC')];
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 0);
        
        $this->output['data'] = UserSystemRepo::listUser(
            $filter,
            $limit['offset'],
            $limit['limit'],
            $orderBy
        );

        return $this->done();
    }
    
    /**
     * GET /api/user/usersystem/{id}
     * 
     * Route Param : 
     *      id : route id
     * @return Array default synapse api return
     *      data 
     *          ...all user record
     *          profile Array record user_proflie
     *          user_role
     *          main_role Array record role utama user
     *      
     */
    public function readOne(Request $request)
    {

        $id = $request->route('id');

        $this->output['data'] = UserSystemRepo::getUser($id);

        if(!(UserAuth::hasAccess($this->accessRuleKey,'r') || $this->output['data']['id'] == UserAuth::user('id') )
            || !UserRepo::checkSystemUser($id))
        {
            $this->setError(__('alert.access_denied'),false,403);
            return $this->done();
        }

        $this->output['data']['user_role'] = UserSystemRepo::getUserRole($this->output['data']['id']);

        foreach($this->output['data']['user_role'] as $key => $val) {
            if($val['is_main_role']){
                $this->output['data']['role_code'] = $key;
            }
        } 

        return $this->done();
    }
    
    /**
     * POST /api/user/
     * 
     * @param Request $request 
     *      name
     *      email
     *      username
     *      password
     *      role_code
     */
    public function create(Request $request)
    {
        if(!UserAuth::hasAccess($this->accessRuleKey,'c')){
            $this->setError(__('alert.access_denied'),false,403);
            return $this->done();
        }

        $userData = $request->all();//$request->only(['name', 'email', 'password']);
        
        //jika berhasil
        if ($user = UserSystemRepo::register($userData,false)) {
            
            $this->setAlert('Data Inserted successfully','success');
        }else{
            $this->setError(UserSystemRepo::error());
        }
        
        return $this->done();
    }

    /**
     * PUT /api/user/{id}
     * 
     * @param Request $request 
     *      name
     *      role_code
     */
    
    /**
     * Update user
     */
    public function update(Request $request)
    {
        if(!UserAuth::hasAccess($this->accessRuleKey,'u')){
            $this->setError(__('alert.access_denied'),false,403);
            return $this->done();
        }
        
        $id = $request->route('id');

        $input = $request->all();

        $validator = [];

        if(isset($input['name'])){
            $validator['name'] = 'required|min:3|max:255';
        }

        if(!empty($validator)){
            $validator = Validator::make($input, $validator); 
            if ($validator->fails()) {
                $this->setError('Input Error :',$validator->messages(),400,true);
                return $this->done();
            }
        }
        
        if(UserSystemRepo::updateUser($id, $input)) {            
            $this->setAlert('Data Updated successfully','success');
        }else{
            $this->setError(UserSystemRepo::error());
        }

        return $this->done();
    }

    public function delete(Request $request)
    {
        if(!UserAuth::hasAccess($this->accessRuleKey,'d')){
            $this->setError(__('alert.access_denied',false,403));
            return $this->done();
        }
        
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

        if(!UserSystemRepo::deleteUser($id)){
            $this->setError('Error : '.UserSystemRepo::error());
        }
        return $this->done();
           
    }

    public function generateToken(Request $request)
    {
        if(!UserAuth::hasAccess($this->accessRuleKey,'u')){
            $this->setError(__('alert.access_denied'),false,403);
            return $this->done();
        }

        $id = $request->route('id');
        $token = UserSystemRepo::generateTokenApi($id);
        if($token){
            $this->setAlert('Token Generated successfully','success');
            $this->output['data'] = $token;
        }else{
            $this->setError(UserSystemRepo::error());
        }
        return $this->done();
    }
}