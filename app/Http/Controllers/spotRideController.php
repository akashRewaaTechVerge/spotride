<?php

namespace App\Http\Controllers;
use App\Http\Helper\ResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use OTPHP\TOTP;
use Illuminate\Encryption\Encrypter;

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
                return response( [ 'faild'=>$userData->errors()->all() ], 422 );
            }
            $otp = TOTP::create();
            $secret = $otp->getSecret();
            $timestamp = time();
            $otp = $otp->at( $timestamp );
            $user = User::create( [
                'first_name' => $request[ 'first_name' ],
                'last_name' => $request[ 'last_name' ],
                'email' => $request[ 'email' ],
                'password' => bcrypt( $request[ 'password' ] ),
                'contact' => $request[ 'contact' ],
                'user_otp' => $otp,
            ] );
            $token = $user->remember_token = $user->createToken( 'LaravelSanctumAuth' )->plainTextToken;
            $userData = User::where( 'id', $user->id )->update( [ 'rememberToken' => $token ] );
            $otpkey = base64_encode( Encrypter::generateKey( $otp ) );

            $success[ 'first_name' ] =  $user->first_name;
            $status =  config::get( 'constants.status.true' );
            $message =  config::get( 'constants.register.success' );
            $code = config::get( 'constants.code.success' );
            $data = $token;
            $otpkey = $otpkey;

            return ResponseBuilder::rsaresponse( $status, $data, $message, $code, $otpkey );
        } catch ( \Exception $e ) {
            $status =  config::get( 'constants.status.false' );
            $message =  config::get( 'constants.register.faild' );
            $code = config::get( 'constants.code.false' );
            $data = [];
            $otpkey = [];
            return ResponseBuilder::rsaresponse( $status, $data, $message, $code, $otpkey );
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
                return $this->error( 'Credentials not match', 422 );
            }
            $userToken = auth()->user()->createToken( 'API Token' )->plainTextToken;
            $userData = User::where( 'contact', $request->contact )->update( [ 'remember_token' => $userToken ] );
            $status = config::get( 'constants.status.ture' );
            $message = config::get( 'constants.login.success' );
            $code = config::get( 'constants.code.success' );
            $data = $userToken;
            return ResponseBuilder::response( $status, $data, $message, $code );
        } catch ( \Exception $e ) {
            $status = config::get( 'constants.status.false' );
            $message = $e->getMessage();
            $data = 1;
            $code = config::get( 'constants.code.false' );
            return ResponseBuilder::response( $status, $data, $message, $code );
        }
    }
    // ---------------- [ ' logout ' ] -------------

    public function logout() {
        try {
            auth()->user()->tokens()->delete();
            $status = config::get( 'constants.status.ture' );
            $message = config::get( 'constants.token.success' );
            $code = config::get( 'constants.code.false' );
            return ResponseBuilder::logout_response( $status, $message, $code );
        } catch ( \Exception $e ) {
            $status = config::get( 'constants.status_false' );
            $message = $e->getMessage();
            $code = config::get( 'constants.false' );
            return ResponseBuilder::logout_response( $status, $message, $code );
        }
    }
    // ------------- [ ' For Otp Registration ' ] -------

    public function otpVarification( Request $request ) {
        try {
            $validator = Validator::make( $request->all(), [
                'phone' => 'required|min:10',
                'otp' => 'required|min:6',
            ] );
            if ( $validator->fails() ) {
                return response( [ 'faild'=>$validator->errors()->all() ], 422 );
            }
            $user = User::where( [ 'contact' => $request->phone, 'user_otp' => $request->otp ] )->first();
            if ( $user || Hash::check( $request->otp, $request->phone ) ) {
                $status =  config::get( 'constants.status.true' );
                $message =  config::get( 'constants.otp.success' );
                $code = config::get( 'constants.code.success' );
                $data = 1;
                return ResponseBuilder::response( $status, $data, $message, $code );
            }
            $status =  config::get( 'constants.status.false' );
            $message =  config::get( 'constants.otp.false' );
            $code = config::get( 'constants.code.false' );
            return ResponseBuilder::logout_response( $status, $message, $code );
        } catch ( \Exception $e ) {
            $status =  config::get( 'constants.status.false' );
            $message =  $e->getMessage();
            $code = config::get( 'constants.code.false' );
            $data = [];
            return ResponseBuilder::logout_response( $status, $message, $code );
        }
    }

    // ------------- [ ' End Class ' ] -----------------
}