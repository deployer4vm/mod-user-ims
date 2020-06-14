<?php

namespace hpsynapse\moduser\Services;

// use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

/**
 * 
 */
class SmsSrv
{
	/**
     * Zenziva::$userkey and $passkey
     *
     * Zenziva Account .
     *
     * @access  protected
     * @type    string
     */
    // protected $userkey = "userkey"; //userkey zenziva
    // protected $passkey = "passkey"; //passkey zenziva

    /**
     * Phone number
     *
     * @var string
     */
    public $to;
    /**
     * Message
     *
     * @var string
     */
    public $text;

    /**
     * Set destination phone number
     *
     * @param $to  Phone number
     *
     * @return self
     */
    // public function to($to)
    // {
    //     $this->to = $to;
    //     return $this;
    // }

    /**
     * Set userkey
     *
     * @param $userkey
     *
     * @return self
     */
    // public function userkey()
    // {
    //     $this->userkey = $userkey;
    //     return $this;
    // }

    /**
     * Set passkey
     *
     * @param $passkey
     *
     * @return self
     */
    // public function passkey()
    // {
    //     $this->passkey = $passkey;
    //     return $this;
    // }

    /**
     * Set messages
     *
     * @param $text  Message
     *
     * @return self
     */
    // public function text($text)
    // {
    //     if (! is_string($text)) {
    //         throw new \Exception('Text should be string type.');
    //     }
    //     $this->text = $text;
    //     return $this;
    // }

    /**
     * Send 
     */
    public function send($data){

        /*$data = http_build_query([
		    "userkey"  => $this->userkey,
		    "passkey"  => $this->passkey,
		    "nohp"  =>  $this->to,
		    "tipe"  => 'reguler',
		    "pesan"  => $this->text
        ]);*/
        $configSms = [];
        if (config('hpsynapse.send_sms_zenziva')) {
            $configSms = array_merge($configSms, config('hpsynapse.send_sms_zenziva'));
        }

        config(['hpsynapse.send_sms_zenziva' => $configSms]);
        
        // $client = new Client(['base_uri' => 'http://subdomain.zenziva.com']);
        // $request = $client->post('/apps/smsapi.php?'.$data.'');
        // $response = $request->getBody()->getContents();
        
        $response = Http::post('http://subdomain.zenziva.com/apps/smsapi.php?'.$data);        
       
        return $response->json();
    }
	
}