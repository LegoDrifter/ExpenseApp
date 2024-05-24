<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function store(Request $request, $admin){

        $attributes = $request->validate([
            'username' => ['required', 'unique:users,username', 'max:255', 'min:3'],
            'email' => ['required', 'unique:users,email', 'max:255', 'min:3'],
            'password' => ['required', 'min:6'],
        ]);

//        dd($attributes);

        $user = User::create($attributes);


            return redirect('/admin')->with('success', 'User created');


    }

    public function login(Request $request)
    {
        $attributes = $request->validate([
            'email' => ['required', 'exists:users,email'],
            'password' => ['required'],
        ]);


        if(auth()->attempt($attributes)){
            session()->regenerate();

            return redirect('/admin');
        }

        return back()
            ->withInput()
            ->withErrors([
                'email' =>'The provided credentials could not be verified.',

            ]);
    }
}
