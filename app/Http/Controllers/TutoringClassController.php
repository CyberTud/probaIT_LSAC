<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutoringClassController extends Controller
{


    public function findUser(){
        return DB::table('tokens')
            ->orderBy('last_used', 'desc')->value('user_id');

    }
    public function addTutoringClass(Request $request){

      $user_id = $this->findUser();

       $role = DB::table('users')
                    ->where('id', $user_id)
                    ->value('role');

       if($role != 'teacher')
           return "Status 403";

       try {

           $description = $request->input('description');
           $subject = $request->input('subject');

           $data = array("description" => $description, "subject" => $subject, "teacher_id" => $user_id);

           if ($data)
               DB::table('tutoring_classes')->insert($data);

       }catch (\Exception $e) {
           return "Status 400";
       }
    }

    public function getClasses(Request $request){

        $subject = $request->get('subject');
        if($subject == null)
            return DB::table('tutoring_classes') ->get();

       //get classes by subject
        return DB::table('tutoring_classes')
            ->where('subject','=',  $subject)
            ->get();
    }

    public function getClassById($id){

        $data = DB::table('tutoring_classes')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }

        return DB::table('tutoring_classes')
            ->where('id', '=', $id)
            ->get();
    }

    public function validateReq($id){

        /*
         * validare care verifica ca requesturile de tip PATCH si DELETE
         * vor putea modifica numai meditatii care sunt legate de userul
         * care face requestul
         */
        $user_id = $this->findUser();
        $teacher_id = DB::table('tutoring_classes')
                            ->where('id', $id)
                            ->value('teacher_id');
        if($user_id == $teacher_id)
            return true;
        return false;
    }
    public function updateDescription(Request $request, $id){

        $data = DB::table('tutoring_classes')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }

        if($this->validateReq($id) == false)
            return "Status 400";

        try{

            $description = $request->input('description');

            DB::table('tutoring_classes')
                ->where('id', $id)
                ->update(array('description' => $description));
        }catch (\Exception $e) {
            return "Status 400";
        }
    }

    public function deleteClass($id){

        $data = DB::table('tutoring_classes')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }

        if($this->validateReq($id) == false)
            return "Status 400";

        DB::table('reviews')->where('id', '=', $id)->delete();
    }


}
