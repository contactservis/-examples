<?php

class CURLRequest {

    public $token;

    /**
     * получение токена при авторизации
     * @param $gradeBookID
     * @param $userName
     * @param $email
     * @return array|mixed
     */
    public function getToken($gradeBookID, $userName, $email)
    {
        $curl = curl_init();
        $POSTFIELDS =
            'gradebookId=' . rawurlencode($gradeBookID)
            . '&username=' . rawurlencode($userName)
            . '&email=' . rawurlencode($email);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bbmprof.ru/login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $POSTFIELDS,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Type: application/x-www-form-urlencoded",
                "Host: api.bbmprof.ru",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return ['success' => false, 'message' => "cURL Error #:" . $err];
        } else {
            return json_decode($response, true);
        }
    }    
    
    /**
     * requestAPI - метод для отправки данных API регистрации
     *
     * @param  mixed $URL     -  url отправки запроса API
     * @param  mixed $Params  - параметры запроса
     * @return void 
     */
    public function requestAPI($url, $Params){        
        if( $curl = curl_init() ) {
            
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Aut-token: qOdt8Ha1WHaD',
                'apikey: '.$this->token
            ]);
            $out = curl_exec($curl);            
            curl_close($curl);
            return $out;

        } else {
            return "error connect";
        }

    }

    public function request1CDataBus($URL)
    {        
        if( $curl = curl_init() ) {
            
            curl_setopt($curl, CURLOPT_URL, $URL);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'apikey: '.$this->token
            ]);
            $out = curl_exec($curl);            
            curl_close($curl);
            return $out;

        } else {
            return "error connect";
        }
    }

    /**
     * получение списка студентов
     * @return mixed|string
     */
    public function getStudents()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bbmprof.ru/feed/students",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Authorization: Bearer " . $this->token,
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
    }

    public function getSchedules($Params)
    {
        $URL = "https://api.bbmprof.ru/v1/schedules/".$Params["DataStart"]."/".$Params["DataEnd"]."/?group=".$Params["group"];
        echo $URL;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => $URL,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => "GET",
            CURLOPT_HTTPHEADER      => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Authorization: Bearer " . $this->token,
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
    }

    public function requestPOSTBitrix($URL){

    }

    public function requestPOSTBUS($URL){
        if( $curl = curl_init() ) {

            $arrayPOST = array( "phone" => "9123456708"); //"{ \"phone\": \"9123456788\"}";
            $arrayPOST = json_encode($arrayPOST);
            curl_setopt($curl, CURLOPT_URL, $URL);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'accept: application/json', 
                'apikey: 2553b8aefc50244df020634293d99383'
            ]);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $arrayPOST);
            $out = curl_exec($curl);            
            curl_close($curl);
            return $out;

        } else {
            return "error connect";
        }
    }

    public function requestPOSTBUSFile($URL, $URL_IMG){
        if( $curl = curl_init() ) {

            $path = $URL_IMG."/1ebaebb29057911e3271a6048b974e90.jpg";
            $arrayPOST = ['avatar' => new \CURLFile($path)]; 
            //"avatar=@".$URL_IMG."/1ebaebb29057911e3271a6048b974e90.jpg;type=image/jpeg"; 
            //"avatar=@1ebaebb29057911e3271a6048b974e90.jpg;type=image/jpeg"

            curl_setopt($curl, CURLOPT_URL, $URL);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'accept: application/json', 
                'apikey: 0ba5b57b6f78778e31633315f935e11f'
            ]);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $arrayPOST);
            $out = curl_exec($curl);            
            curl_close($curl);
            return $out;

        } else {
            return "error connect";
        }
    }

    public function requestDELETEAPI($url){

        //print_r($POST_Params);
        
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'accept: application/json', 
                'apikey: 80f16975563ebc1ba7eb0d49e9c5d164'
            ]);
    
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            
            $out = curl_exec($curl);
            
            curl_close($curl);
            //echo $out;
            return $out;
    
        } else {
            return "error connect";
        }
    
    }
}