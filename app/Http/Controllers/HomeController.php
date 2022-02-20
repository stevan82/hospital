<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Appointment;

class HomeController extends Controller
{
    public function redirect(){
        if (Auth::id()){
            //ako je ulogovani korisnik klase user
            if (Auth::user()->usertype=='0'){
                $doctor=Doctor::all();              
                return view('user.home',compact('doctor'));
            } else {    
                //ako je ulogovani korisnik klase admin           
                return view('admin.home');
            }

        } else {         
            return redirect()->back();
        }
    }
    public function index(){
        if (Auth::id()){
            return redirect('home');
        }
        else {
        $doctor=Doctor::all();
        return view('user.home',compact('doctor'));
        }
    }
    public function appointment(Request $req){
        $data=new Appointment;
        $data->name=$req->name;
        $data->email=$req->email;
        $data->date=$req->date;
        $data->phone=$req->phone;
        $data->message=$req->message;
        $data->doctor=$req->doctor;
        $data->status='U pripremi';
        //ako je korisnik ulogovan unesi i user id
        if (Auth::id()){
         $data->user_id=Auth::user()->id;   
        }
        $data->save();
        return redirect()->back()->with('message','Pregled uspesno zakazan. Kontaktiracemo Vas uskoro!');  

    }
    public function myappointment(){
        //samo ako je korisnik ulogovan prikazi zakazane termine
        if (Auth::id()){
            if (Auth::user()->usertype==0)
            {
                $userid=Auth::user()->id;
                $appoint=Appointment::where('user_id',$userid)->get();
            return view('user.my_appointment',compact('appoint'));
            }
            
        } else {
            return redirect()->back();
        }
    }
    public function cancel_appoint($id){
        $data=Appointment::find($id);
        $data->delete();
        return redirect()->back();

    }
}
