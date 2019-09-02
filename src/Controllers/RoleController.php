<?php

namespace hpsynapse\moduser\Controllers;

use Illuminate\Http\Request;

use Facades\hpsynapse\moduser\Repositories\RoleRepo;
use Facades\hpsynapse\moduser\Services\UserAuth;

use App\Base\BaseController;

class RoleController extends BaseController
{
    public function __construct()
    {
        $this->forceApiOutput();
    }

    /**
     * list pengajuan
     */
    public function readList(Request $request)
    {

        $orderBy = false;
        $filter = [];

        if($request->input('q', false))
            $filter['q'] = $request->input('q');

        //jika menyertakan status
        if($request->input('status', null))
            $filter[] = ['status', $request->input('status')];

        if(UserAuth::isLogin()){
            $roles = explode(';',trim(UserAuth::user('role'),';'));
            foreach($roles as $role){
                $filter[] = ['role_code','!=',$role];
            }
            $filter[] = ['level','>',UserAuth::user('level')];
        }   
        
        //jika multitenatn aktif maka filter berdasarkan tenant nya
        if (config('AppConfig.system.web_admin.multitenant.active')==1 && config('tenant.id')) {
            $filter[] = [
                ['tenant_id', config('tenant.id')],
                ['OR tenant_group_id',config('tenant.tenant_group_id')],
                [
                    'OR',
                    ['tenant_id',0],['tenant_group_id',0]
                ]
            ];
        }

        //jika menyertakan order by
        if ($request->input('orderBy', null))
            $orderBy = [$request->input('orderBy'), $request->input('orderType', 'ASC')];
        
        $limit['offset'] = $request->input('offset', 0);
        $limit['limit'] = $request->input('limit', 0);

        $this->output['data'] = RoleRepo::listRole(            
            $filter,
            $limit['offset'],
            $limit['limit'],
            $orderBy
        );

        return $this->done();
    }

    public function readOne(Request $request) {

        $id = $request->route('id');
        $this->output['data'] = RoleRepo::getRole($id);
        if(!$this->output['data']){
            $this->setError('Data Not Found');
        }
        return $this->done();
    }

    public function creat(Request $request) {
        
    }

    public function update(Request $request) {
        
    }
    public function delete(Request $request) {
        
    }
}
