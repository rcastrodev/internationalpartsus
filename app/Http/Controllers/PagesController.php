<?php

namespace App\Http\Controllers;

use SEO;
use Mail;
use Illuminate\Http\Request;
use App\Mail\MailContactanos;
use App\Mail\MessageReceived;
use App\Mail\MessageReceivedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Manufacturer;

class PagesController extends Controller
{
    public function index(Request $request){
        $pieza = $request->input('pieza');
        $productos = $pieza ? 
            DB::table('api_sisparts.supplies')
                ->select('api_sisparts.manufacturers.name as manufacturer', 'api_sisparts.manufacturers.slug as manufacturer_slug', 'supplies.number', 'supplies.slug as number_slug')
                ->leftJoin('api_sisparts.manufacturers', 'api_sisparts.supplies.manufacturers_id', 'api_sisparts.manufacturers.id')
                ->where('number', 'like', "%$pieza%")
                ->where('supplies.slug', '<>', '')
                ->distinct()
                ->paginate(15)
            : DB::table('api_sisparts.supplies')
                ->select('api_sisparts.manufacturers.name as manufacturer', 'api_sisparts.manufacturers.slug as manufacturer_slug', 'api_sisparts.supplies.number', 'api_sisparts.supplies.slug as number_slug')
                ->leftJoin('api_sisparts.manufacturers', 'api_sisparts.supplies.manufacturers_id', 'api_sisparts.manufacturers.id')
                ->where('api_sisparts.manufacturers.slug', '<>', '')
                ->where('api_sisparts.supplies.slug', '<>', '')
                ->paginate(15);

        
        SEO::setTitle('INTL - piezas de refacción');
        SEO::setDescription('piezas de refacción en Mexico con los mejores planes de pago');
        return view('index', compact('productos'));
    }

    public function fabricantes(){
        $fabricantes = DB::table('api_sisparts.manufacturers')
        ->whereNotIn('name', ['GENERICO', 'generico', 'Fabricante', 'fabricante', ''])
        ->paginate(20);

        SEO::setTitle('INTL - piezas de refacción');
        SEO::setDescription('piezas de refacción en Mexico con los mejores planes de pago');

        return view('fabricantes', compact('fabricantes'));
    }

    public function fabricante($name){
        $fabricante = Manufacturer::where('slug', $name)->first();

        $piezas = DB::table('api_sisparts.supplies')->select('manufacturers.name as manufacturer', 'manufacturers.slug as manufacturer_slug', 'supplies.number', 'supplies.slug as number_slug')
        ->leftJoin('manufacturers', 'supplies.manufacturers_id', 'manufacturers.id')
        ->where('manufacturers_id', $fabricante->id)
        ->where('manufacturers.slug', '<>', '')
        ->where('supplies.slug', '<>', '')
        ->distinct()
        ->paginate(20);

        SEO::setTitle("INTL - $fabricante->name");
        SEO::setDescription('piezas de refacción en Mexico con los mejores planes de pago');

        return view('fabricante', compact('name', 'piezas'));
    }

    public function producto($manufacturer , $number){
    
        $pieza = DB::table('api_sisparts.supplies')
            ->select('number', 'short_description', 'large_description', 'supplies.slug as number_slug', 'manufacturers.slug as manufacturer_slug', 'name')
            ->leftJoin('api_sisparts.manufacturers', 'api_sisparts.supplies.manufacturers_id', 'api_sisparts.manufacturers.id')
            ->where('api_sisparts.supplies.slug', $number)
            ->where('api_sisparts.supplies.sync_connection_id', 2)
            ->where('api_sisparts.manufacturers.slug', '<>', '')
            ->where('api_sisparts.supplies.slug', '<>', '')
            ->first(); 

        if(!$pieza)
            $pieza = DB::table('api_sisparts.supplies')
            ->select('number', 'short_description', 'large_description', 'supplies.slug as number_slug', 'manufacturers.slug as manufacturer_slug', 'name')
            ->leftJoin('api_sisparts.manufacturers', 'api_sisparts.supplies.manufacturers_id', 'api_sisparts.manufacturers.id')
            ->where('api_sisparts.supplies.slug', $number)
            ->where('api_sisparts.manufacturers.slug', '<>', '')
            ->where('api_sisparts.supplies.slug', '<>', '')
            ->first();

        if ($pieza){
            SEO::setTitle("INTL - {$pieza->number}");
            SEO::setDescription($pieza->short_description);
        }else{
            abort('404');
        } 
    
        return view('producto', compact('pieza'));
    }

    public function quienesSomos(){

        SEO::setTitle('INTL - quienes somos');
        SEO::setDescription('piezas de refacción en Mexico con los mejores planes de pago');
        return view('quienes-somos');
    }

    public function contacto(){
        
        SEO::setTitle('INTL - Contacto');
        SEO::setDescription('piezas de refacción en Mexico con los mejores planes de pago');

        return view('contacto');
    }

    public function contactanosMail(Request $request)
    {
        
        $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            'name'    => 'required',
            'email'     => 'required|email:rfc,dns',
            'message'   => 'required'
        ],[
            'name.required'      => 'Nombre es requerido',
            'email.required'     => 'Correo es requerido',
            'email.email'        => 'Correo debe tener un formato valido',
            'message.required'   => 'Mensaje es requerido',
            'g-recaptcha-response.required' => 'Recaptcha es requerido',
            'g-recaptcha-response.captcha' => 'Recaptcha es requerido'
        ]);

        $data = [
            'name' => $request['name'],
            'email' => $request['email'],
            'message' => $request['message'],
        ];

        $to = env('MAIL_FROM_RECIVED');

        try {
            Mail::to([$to])->send(new MessageReceived($data));

            $mensaje = "correo enviado";
            $class = "success";
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            $mensaje = "Error al enviar el correo";
            $class = "danger";
        }

        return back()->with('mensaje', $mensaje)->with('class', $class);
    }
}
