<?php
namespace App\Http\Helper;

class ResponseBuilder {

    public static function response( $status, $data, $message, $code ) {
        return $response = array(
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'code' => $code
        );
    }
}

?>