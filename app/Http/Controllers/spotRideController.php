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

    // --------------- [ ' For Register ' ] ------------------

    public function register( Request $request ) {
        try {
            $userData = Validator::make( $request->all(), [
                'first_name' => 'required|min:3',
                'last_name' => 'required|min:3',
                'email' => 'required|email|unique:users',
                'contact' => 'required|min:10|unique:users',
                'password' => 'required|min:6'
            ] );
            if ( $userData->fails() ) {
                return response( [ 'errors'=>$userData->errors()->all() ], 422 );
            }
            $user = User::create( [
                'first_name' => $request[ 'first_name' ],
                'last_name' => $request[ 'last_name' ],
                'email' => $request[ 'email' ],
                'password' => bcrypt( $request[ 'password' ] ),
                'contact' => $request[ 'contact' ]
            ] );

            $token = $user->remember_token = $user->createToken( 'LaravelSanctumAuth' )->plainTextToken;
            $userData = User::where( 'id', $user->id )->update( [ 'remember_token' => $token ] );
            $success[ 'first_name' ] =  $user->first_name;
            $status =  config::get( 'constants.status1' );
            $message =  config::get( 'constants.register' );
            $code = config::get( 'constants.success' );
            $data = $token;

            return ResponseBuilder::response( $status, $data, $message, $code );
        } catch ( \Exception $e ) {
            $status = config::get( 'constants.status0' );
            $message = $e->getMessage();
            $data = 1;
            $code = config::get( 'constants.false' );

            return ResponseBuilder::response( $status, $data, $message, $code );
        }
    }

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

    // ---------------- [ ' logout ' ] -------------

    public function logout() {
        try {
            auth()->user()->tokens()->delete();
            $status = config::get( 'constants.status1' );
            $data = config::get( 'constants.revokeData' );
            $message = config::get( 'constants.tokenMessage' );
            $code = config::get( 'constants.false' );

            return ResponseBuilder::response( $status, $data, $message, $code );
        } catch ( \Exception $e ) {
            $status = config::get( 'constants.status0' );
            $message = $e->getMessage();
            $code = config::get( 'constants.false' );
            return ResponseBuilder::response( $status, $data, $message, $code );
        }
    }
}
