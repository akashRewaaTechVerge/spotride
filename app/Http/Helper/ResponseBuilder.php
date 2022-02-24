<?php
namespace App\Http\Helper;

class ResponseBuilder {

    // ----------------- [ 'Response' ] -----------------------
    public static function response( $status, $data, $message, $code ) {
        return $response = array(
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'code' => $code,
        );
    }
    // ------------------- [ ' Rsa_Respons ' ] ----------------
    public static function rsaresponse( $status, $data, $message, $code, $otpkey ) {
        return $response = array(
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'code' => $code,
            'otpkey' =>$otpkey
        );
    }
    // --------------- [ ' For Logout ' ] ---------------------
    public static function logout_response( $status, $message, $code ) {
        return $response = array(
            'status' => $status,
            'message' => $message,
            'code' => $code,
        );
    }
}

?>