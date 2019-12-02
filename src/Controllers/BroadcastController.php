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
    public function __construct()
    {
        // $this->forceApiOutput();
    }

    public function index(Request $request)
    {

    }
    
    public function sendBroadcast(Request $request)
    {
        $input = $request->only(['title','description','message']);
        BroadcastNotif::dispatch(false,$input['title'],$input['description'],$input['message']);
        $this->output['message'] = 'Broadcast berhasil dikirim';
        return $this->done();
    }
}