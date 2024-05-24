<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AltUserController extends BaseController
{
    public function getUser($id){

        $user = User::find($id);
        if(!$user){
           return $this->ResponseError('Users not found','Try again');
        }
        else
        return $this->ResponseSuccess($user);

    }
}
