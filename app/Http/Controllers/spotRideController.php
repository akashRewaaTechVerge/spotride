<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class spotRideController extends Controller {

    // ------------------ [ ' Login ' ] ------------------------

    public function login( Request $request ) {
        try {
            $validate = $request->validate( [
                'contact' => 'required|min:10',
                'password' => 'required|string|min:6'
            ] );
            if ( !Auth::attempt( $validate ) ) {
                return $this->error( 'Credentials not match', 401 );
            }
            $userToken = auth()->user()->createToken( 'API Token' )->plainTextToken;
            $userData = User::where( 'contact', $request->contact )->update( [ 'remember_token' => $userToken ] );
            $status = config::get( 'constants.status1' );
            $message = config::get( 'constants.login' );
            $code = config::get( 'constants.success' );
            $data = $userToken;

            return ResponseBuilder::response( $status, $data, $message, $code );
        } catch ( \Exception $e ) {
            $status = 'false';
            $message = $e->getMessage();
            $code = config::get( 'constants.status0' );
            return ResponseBuilder::response( $status, $data, $message, $code );
        }
    }

}
