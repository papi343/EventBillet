<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class UserService
{


    public function create(User $user): User
    {
        $user->password = Hash::make($user->password);
        $user->save();
        return $user;
    }

    public  function update(User $user): User
    {
        $user->save();
        return $user;
    }

    public  function delete(User $user): User
    {
        $user->delete();
        return $user;
    }

    public  function attempt(string $email,string $password):User|bool
    {
       $user = Auth::Attempt(['email'=>$email, 'password'=>$password]);
       if($user){
        return Auth::user();
       }
       return false;
    }
}