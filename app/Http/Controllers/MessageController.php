<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactProductMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    public function contactProduct(Request $request)
    {
        $to = env('MAIL_FROM_RECIVED');
        $data = $request->all();

        $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            'nombre'    => 'required',
            'fabricante'    => 'required',
            'n_parte'    => 'required',
            'email'     => 'required|email:rfc,dns',
        ],[
            'nombre.required'      => 'Nombre es requerido',
            'email.required'     => 'Correo es requerido',
            'email.email'        => 'Correo debe tener un formato valido',
            'g-recaptcha-response.required' => 'Recaptcha es requerido',
            'g-recaptcha-response.captcha' => 'Recaptcha es requerido',
            'fabricante.required'      => 'Fabricante es requerido',
            'n_parte.required'     => 'producto es requerido',
        ]);
        
        try {
            Mail::to([$to])->send(new ContactProductMail($data));    
            $mensaje = "correo enviado";
            $class = "success";
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            $mensaje = "error al enviar correo";
            $class = "danger";
        }
        return back()->with('mensaje', $mensaje)->with('class', $class);
    }
}
