<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Appointment;
use Notification;
use App\Notifications\SendEmailNotification;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function addview(){
     if (Auth::id()){
        if  (Auth::user()->usertype==1){
            return view('admin.add_doctor');
        } else {
          return redirect()->back();  
        }
     } else {
         return redirect('login');
     }   
    
    }
    public function upload(Request $req){
    $doctor=new Doctor;
    $image=$req->file;
    $imagename=time().'.'.$image->getClientOriginalExtension();
    $req->file->move('doctorimage',$imagename);
    $doctor->image=$imagename;
    $doctor->name=$req->name;
    $doctor->phone=$req->phone;
    $doctor->room=$req->room;
    $doctor->speciality=$req->speciality;
    $doctor->save();
    return redirect()->back()->with('message','Doktor uspesno dodat!');   
    }
    public function showappointment(){
        if (Auth::id()){
            if  (Auth::user()->usertype==1){
                $data=Appointment::all();
                return view('admin.showappointment',compact('data'));
            } else {
                return redirect()->back();
            }
        } else {
            return redirect('login');  
        }       
    }
    public function approved($id){
        $data=Appointment::find($id);
        $data->status="Potvrdjeno";
        $data->save();
        return redirect()->back();
    }
    public function canceled($id){
        $data=Appointment::find($id);
        $data->status="Otkazano";
        $data->save();
        return redirect()->back();
    }
    public function showdoctor(){
        $data=Doctor::all();
        return view('admin.showdoctor',compact('data'));
    }
    public function deleteDoctor($id){
        $data=doctor::find($id);
        $data->delete();
        return redirect()->back();

    }
    public function updateDoctor($id){
        $data=Doctor::find($id);
        return view('admin.update_doctor',compact('data'));
    }
    public function editDoctor(Request $req,$id){
        $doctor=Doctor::find($id);
        $doctor->name=$req->name;
        $doctor->phone=$req->phone;
        $doctor->speciality=$req->speciality;
        $doctor->room=$req->room;
        $image=$req->file;
        if ($image){
        $imagename=time().'.'.$image->getClientOriginalExtension();
        $req->file->move('doctorimage',$imagename);
        $doctor->image=$imagename;
        }
        $doctor->save();
        return redirect()->back()->with('message','Detalji o doktoru uspesno izmenjeni');
        }
        public function emailView($id){
            $data=Appointment::find($id);
            return view('admin.email_view',compact('data'));
        }
        public function sendEmail(Request $req,$id){
            $data=Appointment::find($id);
            $details=[
                'greeting'=>$req->greeting,
                'body'=>$req->body,
                'actiontext'=>$req->actiontext,
                'actionurl'=>$req->actionurl,
                'endpart'=>$req->endpart,  
            ];
            Notification::send($data,new SendEmailNotification($details));
            return redirect()->back()->with('message','Email je uspesno poslat.');
        }
}
