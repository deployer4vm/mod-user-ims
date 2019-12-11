<?php

namespace hpsynapse\moduser\Controllers;

use Illuminate\Http\Request;
use Facades\hpsynapse\moduser\Repositories\UserRepo;
use Facades\hpsynapse\moduser\Repositories\RoleRepo;
use Facades\hpsynapse\moduser\Services\UserAuth;

use hpsynapse\moduser\Jobs\BroadcastNotif;

use App\Base\BaseController;

class BroadcastController extends BaseController
{
    protected $accessRuleKey = 'moduser.broadcast';

    public function __construct()
    {
        // $this->forceApiOutput();
    }

    public function index(Request $request)
    {

    }
    
    public function sendBroadcast(Request $request)
    {

        if(!UserAuth::hasAccess($this->accessRuleKey,'c')){
            $this->setError(__('alert.access_denied',false,403));
            return $this->done();
        }

        $input = $request->only(['title','description','message']);
        BroadcastNotif::dispatch(UserAuth::user('id'),false,$input['title'],$input['description'],$input['message']);
        $this->output['message'] = 'Broadcast berhasil dikirim';
        return $this->done();
    }
}