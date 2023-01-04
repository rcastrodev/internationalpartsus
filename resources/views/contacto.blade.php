@extends('layouts.app')
@section('section__banner_right')
    <div class="mail">
        <h3>Contacto</h3>
        <div class="agileinfo_mail_grids">
            <div class="col-md-4 agileinfo_mail_grid_left">
                <ul>
                    <li><i class="fa fa-home" aria-hidden="true"></i></li>
                    <li>Dirección<span>Tulipan 410 Hacienda Vidrio Int O-102 Salvador Portillo Lopez Tlaquepaque Jalisco, México, C.P 45580</li>
                </ul>
                <ul>
                    <li><i class="fa fa-envelope" aria-hidden="true"></i></li>
                <li>email<span><a href="mailto:{{env('MAIL_FROM_RECIVED')}}">{{env('MAIL_FROM_RECIVED')}}</a></span></li>
                </ul>
                <ul>
                    <li><i class="fa fa-phone" aria-hidden="true"></i></li>
                    <li>LLamar a <span>Tel. <a href="tel:+(01)333334326723056614">(01) 33 3334 3267 2305 6614</a></span></li>
                </ul>
            </div>
            <div class="col-md-8 agileinfo_mail_grid_right">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="opacity: 1">
                    <ul style="list-style: none">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>  
                @endif


                <form action="{{route('enviar')}}" method="post" enctype="multipart/form-data">
                    @if (\Session::has('mensaje'))
                        <div class="alert alert-{{\Session::get('class')}} alert-dismissible fade show" role="alert" style="font-size: 1rem; opacity: 1;">
                            {!! \Session::get('mensaje') !!}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>                 
                    @endif
                @csrf
                    <div class="col-md-6 wthree_contact_left_grid">
                        <input type="text" name="name" placeholder="Nombre"  required="">
                    </div>
                    <div class="col-md-6 wthree_contact_left_grid">
                        <input type="email" name="email" placeholder="Email" required="">
                    </div>
                    <div class="clearfix"> </div>
                    <textarea name="message"></textarea>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! app('captcha')->display() !!}
                        </div>
                    </div>
                    <div class="clearfix"> </div>
                    <input type="submit" value="Enviar">
                    <input type="reset" value="Limpiar">
                </form>
            </div>
            <div class="clearfix"> </div>
        </div>
    </div>
@endsection
