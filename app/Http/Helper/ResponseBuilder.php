<?php
namespace App\Http\Helper;

class ResponseBuilder {
    public static function response( $status, $data, $message, $code ) {
        return $response = array(
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'code' => $code,
        );
    }
    //  ------------------ [ ' RsaRespons ' ] ----------------
    public static function rsaresponse( $status, $data, $message, $code, $otpkey ) {
        return $response = array(
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'code' => $code,
            'otpkey' =>$otpkey
        );
    }
}

?>