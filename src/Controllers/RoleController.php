<?php

namespace hpsynapse\moduser\Controllers;

use Illuminate\Http\Request;

use Facades\hpsynapse\moduser\Repositories\RoleRepo;
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

        $this->output['data']['filter'] = [
            'q'=>$request->input('q', null)
        ];

        //jika menyertakan status
        if($request->input('status', null))
            $this->output['data']['filter'][] = $request->input('status');

        $this->output['data']['offset'] = $request->input('offset', 0);
        $this->output['data']['limit'] = $request->input('limit', 10);

        $this->output['data'] = Master::listMitra(
            $this->output['data']['filter'], $this->output['data']['offset'], $this->output['data']['limit']
        );

        return $this->done();
    }

    public function readOne(Request $request) {

        $id = $request->route('id');
        $this->output['data'] = Master::getMitra($id);
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
