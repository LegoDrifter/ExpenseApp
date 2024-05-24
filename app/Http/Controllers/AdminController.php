<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //



    public function store(Request $request, $id = null, $action = ''){
       if($action == 'EDIT' && $id){
           $attributes = $request->validate([
               'email' => ['required', 'unique:users,email'],
               'password' => ['required'],
               'username' => ['required', 'unique:users,username'],
               'role' => ['required']
           ]);

           $user = User::findOrFail($id);

           $user->email = $attributes['email'];
           $user->password = $attributes['password'];
           $user->username = $attributes['username'];
           $user->role = $attributes['role'];

           $user->save();

           return redirect("/admin");

       }else if($action == 'DELETE'){
           $user = User::find($id);

           if($user) $user->delete();
           return redirect("/admin");
       }else{
           $userController = new UserController();
           return $userController->store($request, true);

       }
    }
}
