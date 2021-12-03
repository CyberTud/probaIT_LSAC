<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrolmentController extends Controller
{
    public function findUser(){
        return DB::table('tokens')
            ->orderBy('last_used', 'desc')->value('user_id');

    }

    public function addEnrolment($id){

        $user_id = $this->findUser();

        $userAuth = DB::table('users')->where('id', $user_id)->get();

        if ($userAuth->isEmpty()){
            return "Status 401";
        }
        $role = DB::table('users')
            ->where('id', $user_id)
            ->value('role');

        if($role != 'student')
            return "Status 403";

        $tutoringAuth = DB::table('tutoring_classes')->where('id', $id)->get();

        if ($tutoringAuth->isEmpty()){
            return "Status 400";
        }

        $data = array("user_id" => $user_id, "tutoring_class_id" => $id);


        DB::table('enrolments')->insert($data);
    }
}
