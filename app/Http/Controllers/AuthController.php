<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\Concerns\Has;

class AuthController extends Controller
{
    public function findUser(){
        return DB::table('tokens')
            ->orderBy('last_used', 'desc')->value('user_id');

    }

    public function register(Request $request){

        try {


            $this->validate($request, [
                'firstname' => 'required|max:50',
                'lastname' => 'required|max:50',
                'email' => 'required|email|unique:users|max:50',
                'password' => 'required|max:50'
            ]);


            $firstname = $request->input('firstname');
            $lastname = $request->input('lastname');
            $email = $request->input('email');
            $password = $request->input('password');
            $password = Hash::make($password);
            $role = $request->input('role');

            if($role == 'teacher' && substr($email, -18) != 'onmicrosoft.upb.ro')
                return "Status 401";
            if($role == 'student' && substr($email, -11) != 'stud.upb.ro')
                return "Status 401";

            $data = array("firstname" => $firstname, "lastname" => $lastname, "email" => $email, "password" => $password, "role" => $role);
            if ($data)
                DB::table('users')->insert($data);
        }catch (\Exception $e){
            return "Status 400";
        }

    }

    public function login(Request $request)
    {


        try {

            $this->validate($request, [
                'email' => 'required|email|max:50',
                'password' => 'required|min:8|max:50'
            ]);

            $email = $request->input('email');
            $password = $request->input('password');
            $id = DB::table('users')->where('email', $email) -> value('id');
            $hashedPass = DB::table('users')->where('email', $email) -> value('password');
            if (Hash::check($password, $hashedPass)){

                $token = Str::random(60);

                $token_data = DB::table('tokens')->where('id', $id)->get();

                if ($token_data->isEmpty()){
                    $arr = array("user_id" => $id, "token" => $token, "last_used" => Carbon::now());
                    DB::table('tokens')->insert($arr);
                }
                else
                {
                    DB::table('tokens')
                        ->where('user_id', $id)
                        ->update(array('token'=>$token, 'last_used' => Carbon::now()));
                }
                return $token;
            }

            return "Status 401";


        } catch (\Exception $e) {
            return "Status 400";
        }
    }

    public function showUsers(){

        return DB::table('users')->select(['id', 'email', 'firstname', 'lastname', 'role'])->get();

    }

    public function showById($id){

        $data = DB::table('users')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }

        return DB::table('users')
            ->where('id', '=', $id)
            ->select(['id', 'email', 'firstname', 'lastname', 'role'])
            ->get();

    }

    public function updateUser(Request $request, $id){


        $user_id = $this->findUser();
        if($user_id != $id) return "Status 403";


        $data = DB::table('users')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }

        $firstname = $request->input('firstname');
        $lastname = $request->input('lastname');
        $email = $request->input('email');
        $password = $request->input('password');
        $confirmation_password = $request->input('confirmation_password');
        $role = $request->input('role');

        if($password != null && $confirmation_password == null || $password != $confirmation_password)
            return "Status 400";
        $data = DB::table('users')->where('id', $id);

        if($firstname == null)
            $firstname = $data->value('firstname');
        if($lastname == null)
            $lastname = $data->value('lastname');
        if($email == null)
            $email = $data->value('email');
        if($password == null)
            $password = $data->value('password');
        else
            $password = Hash::make($password);
        if($role == null)
            $role = $data->value('role');

        DB::table('users')
            ->where('id', $id)
            ->update(array('firstname' => $firstname,'lastname' => $lastname, 'email' => $email, 'password' => $password, 'role' => $role));

    }

    public function deleteUser($id){

        $user_id = $this->findUser();
        if($user_id != $id) return "Status 403";


        $data = DB::table('users')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }
        DB::table('users')->where('id', '=', $id)->delete();
    }
}
