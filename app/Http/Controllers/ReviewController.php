<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function findUser(){
        return DB::table('tokens')
            ->orderBy('last_used', 'desc')->value('user_id');

    }

    public function addReview(Request $request){

        //  try{
            $this->validate($request, [
                'message' => 'required|max:5000'
            ]);


            $user_id = $request->input('user_id');
            $message = $request->input('message');

            //check if logged
            if(DB::table('users')->where('id', $user_id)->get()->isEmpty())
                return "Status 403";
            $data = array("user_id" => $user_id, "message" => $message);

            if ($data)
                DB::table('reviews')->insert($data);
       // }catch (\Exception $e) {
         //   return "Status 400";
        //}
    }

    public function showReviews(){
        return DB::table('reviews')->get();
    }

    public function showById($id){
        $data = DB::table('reviews')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }

        return DB::table('reviews')
            ->where('id', '=', $id)
            ->get();
    }


//validare care verifica ca requesturile de tip PATCH si DELETE vor putea modifica numai reviewuri care sunt legate de userul care face requestul
    public function isValid($id){

        $user_id1 = $this->findUser();
        $user_id2 = DB::table('reviews')->where('id', $id)->value('user_id');

        if($user_id1 == $user_id2) return true;

        return false;

    }

    public function updateReview(Request $request, $id){

        $data = DB::table('reviews')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }


        if($this->isValid($id) == false) $this->findUser();

        try{

            $message = $request->input('message');

            DB::table('reviews')
                ->where('id', $id)
                ->update(array('message' => $message));

        }catch (\Exception $e) {
            return "Status 400";
        }

    }

    public function deleteReviews($id){
        $data = DB::table('reviews')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }
        if($this->isValid($id) == false) return "Status 403";
        DB::table('reviews')->where('id', '=', $id)->delete();
    }
}
