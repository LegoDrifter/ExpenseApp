<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    //

    public function store(Request $request){

        $attributes = $request->validate([
            'username' => ['required', 'unique:users,username', 'max:255', 'min:3'],
            'email' => ['required', 'unique:users,email', 'max:255', 'min:3'],
            'password' => ['required', 'min:6'],
        ]);

        $user = new User();
        $user->username = $attributes['username'];
        $user->email= $attributes['email'];
        $user->role = 'admin';
        $hashedPassword = bcrypt($attributes['password']);
        $user->password = $hashedPassword;

        if($user->save()){
            $this->ResponseSuccess([
                'user' => $user,
                'token' => $user->createToken('API Token of ' . $user->username)->plainTextToken
            ]);
            return redirect('/');

        }else{
            $this->ResponseError('Something went wrong','User could not be created.');
        }
    }

    public function login(Request $request)
    {
        $attributes = $request->validate([
            'email' => ['required', 'exists:users,email'],
            'password' => ['required'],
        ]);


        if(auth()->attempt($attributes)){

            $user = auth()->user();

            return $this->ResponseSuccess([
                'user' => $user,
                'token' => $user->createToken('API Token of ' . $user->username)->plainTextToken
            ]);
        }

        return back()
            ->withInput()
            ->withErrors([
                'email' =>'The provided credentials could not be verified.',
            ]);
    }

    public function getUser(Request $request){
        return auth()->user();
    }
}
