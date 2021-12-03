<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class ContactRequestController extends Controller
{
    public function showAll(Request $request){

        $sortBy = $request->get('sortBy');
        $order = $request->get('order');

        if($sortBy && $order){
            return DB::table('contact_requests')
                    ->orderBy($sortBy, $order)
                    ->get();
        }

        $filterBy = $request->get('filterBy');
        $filterBy = json_decode($filterBy, true);
        if($filterBy)
        {
            //filtrare cu query, exemplu gresit, nu exista campul is_verified

            try{
                return DB::table('contact_requests')
                    ->where($filterBy)
                    ->get();
            }catch (\Exception $e){
                return "Status 400";
            }

        }
        return DB::table('contact_requests')->get();

    }

    public function add(Request $request){

        try {
            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users|max:255',
                'message' => 'required|max:5000'
            ]);
            $name = $request->input('name');
            $email = $request->input('email');
            $message = $request->input('message');

            $is_resolved = 0;

            $data = array("name" => $name, "email" => $email, "message" => $message, "is_resolved" => $is_resolved);
            if ($data)
                DB::table('contact_requests')->insert($data);
           /*
            *
            * ---trimitere mail
                Mail::send(['text'=>'mail'], $data, function($message) {
                $message->to('tudor@gmail.com')->subject
                ('ProbaIT');
                $message->from('probaIT@abc.com');
            });

           */
        } catch (\Exception $e){
            return "Status 400";
        }

    }

    public function showById($id){

        $data = DB::table('contact_requests')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }

        return DB::table('contact_requests')
                                ->where('id', '=', $id)
                                ->get();
    }

    public function updateContact(Request $request, $id){


       $data = DB::table('contact_requests')->where('id', $id);

        if ($data->get()->isEmpty()){
            return "Status 404";
        }

        $is_resolved = $request->input('is_resolved');

        if($is_resolved != 1 && $is_resolved != 0)
            return "400";

        $data->update(array('is_resolved' => $is_resolved));

    }

    public function delete($id){

        $data = DB::table('contact_requests')->where('id', $id)->get();

        if ($data->isEmpty()){
            return "Status 404";
        }
        DB::table('contact_requests')->where('id', '=', $id)->delete();



    }
}
