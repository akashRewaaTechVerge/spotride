<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class spotRideController extends Controller {
    // --------------- [ ' For Register ' ] ------------------

    public function register( Request $request ) {
        $userData = Validator::make( $request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'contact' => 'required|min:10',
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

        return response()->json( [
            'status' => '200',
            'message' => 'User Register Success',
            'token' => $token
        ] );
    }

    public function login( Request $request ) {
        $attr = $request->validate( [
            'contact' => 'required|min:10',
            'password' => 'required|string|min:6'
        ] );

        if ( !Auth::attempt( $attr ) ) {
            return $this->error( 'Credentials not match', 401 );
        }
        $userToken = auth()->user()->createToken( 'API Token' )->plainTextToken;
        $userData = User::where( 'contact', $request->contact )->update( [ 'remember_token' => $userToken ] );

        return response()->json( [
            'status' => '200',
            'message' => 'User Logged In',
            'token' => $userToken,

        ] );
    }

    // ------------------ [ ' Login ' ] ----------------------------

}
