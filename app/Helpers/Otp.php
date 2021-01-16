<?php

namespace App\Helpers;
use Steevenz\Rajasms;
use Illuminate\Support\Str;

use Illuminate\Support\Carbon;

class Otp {
    public function generateOtp($validTime = 5) {
        $randOtp = rand(1111,9999);

        $expiredTime = Carbon::now()->addMinute($validTime);

        return array(
            'otp' => $randOtp,
            'expired' => $expiredTime
        );
    }

    public function sendOtp($phone, $otp) {
        $template = 'KODE OTP ANDA '.$otp.'. JANGAN PERNAH MEMBAGIKAN KODE OTP ANDA KEPADA SIAPAPUN ';
        $template = str_replace(' ', '%20', $template);
        $this->sendToProvider($template, $phone, 'wa_otp'); //for wa
       // $this->sendToProvider($template, $phone);  //for sms
    }




    public function sendToProvider($message, $phone, $type = null) {
            $curl = curl_init();

            $url = 'http://sms.nstek.co.id';
            $api_key = 'f08fb99ef0fae4713b9aa9892558610dc1966890';
            if($type !== null) {
                $query = 'http://sms.nstek.co.id/customer/api/send?type='.$type.'&api_key='.$api_key.'&destination='.$phone.'&message='.$message;
            }else{
                $query = $url.'http://sms.nstek.co.id/customer/api/send?api_key='.$api_key.'&destination='.$phone.'&message='.$message;
            }

            //dd($query);

            curl_setopt_array($curl, array(
              CURLOPT_URL => $query,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            // if ($err) {
            //   echo "cURL Error #:" . $err;
            // } else {
            //   echo $response;
            // }

            // if ($err) {
            //     return "cURL Error #:" . $err;
            //   } else {
            //     return $response;
            //   }
    }
}
