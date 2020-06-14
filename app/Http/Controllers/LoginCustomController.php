<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Product;
use App\MenuCategory;
use Illuminate\Support\Facades\Auth;

class LoginCustomController extends Controller
{

    public function verification(Request $request){

        $user = User::where(['email' => $request->email])->first();

        if($user){
            $pass_lama = $user->password;

            $pass = Hash::check($request->password, $pass_lama);

            if($pass){
                $role = $user->roles()->first();

                $roleTrue = $role->pivot->role_id;

            }

            $data = Product::with('menuCategory')->get();

            if($roleTrue == 1){
                return redirect('admin');
            }else{
                return view('admin.products.index', compact('user', 'data'));
            }

        }else{
            return redirect('login');
        }




    }
}
