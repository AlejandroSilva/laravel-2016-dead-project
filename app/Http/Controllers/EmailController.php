<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Mail;

class EmailController extends Controller {

    function send(){
        header('Access-Control-Allow-Origin: *');

        $title =  "prueba archivos adjuntos";
        $content = "prueba adjuntos";

        Mail::queue('emails.send', ['title' => $title, 'content' => $content], function ($message){
            $message
                ->from('no-responder@sig.seiconsultores.cl', 'SEI Consultores')
                ->to('pm5k.sk@gmail.com', 'Alejandro Silva')
                ->cc('asilva@seiconsultores.cl', 'Alejandro Silva')

                ->subject("prueba adjuntos 9")
//                ->attach('/home/asilva/Steve.jpg')
                ->attach(public_path().'/css/styles.css')
                ->attach(public_path().'/robots.txt')
                ->attach(public_path().'/seedFiles/localesFCV.csv');
        });

        return response()->json(['message' => 'Request completed']);
    }

}
