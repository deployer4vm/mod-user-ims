<?php

namespace hpsynapse\moduser\Controllers\Auth;


trait AuthResponseTraits
{
 
    /**
     * redirect hasil SSO dan Auth kembali ke halaman aplikasi client sso grab
     * 
     * @param array $param parameter yg di passing
     * 
     * @return redirect parameter yg di passing saat redirect :
     */
    protected function authDone($param=[])
    {   
        return redirect()->away(config('cur_apps.session_grab_url').'?'.http_build_query($param));
    }
    
    protected function filterAuthParam($param)
    {
        
    }
}