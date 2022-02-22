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
        $validator = Validator::make( $request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:10',
            'password' => 'required|min:6'
        ] );
        if ( $validator->fails() ) {
            return response( [ 'errors'=>$validator->errors()->all() ], 422 );
        }
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt( $request->password );
        $user->contact = $request->phone;
        $user->save();

        $token = $user->remember_token = $user->createToken( 'LaravelSanctumAuth' )->plainTextToken;
        $userData = User::where( 'id', $user->id )->update( [ 'remember_token' => $token ] );
        $success[ 'name' ] =  $user->name;

        return response()->json( [
            'status' => '200',
            'message' => 'User Register Success',
            'token' => $token
        ] );
    }
}
