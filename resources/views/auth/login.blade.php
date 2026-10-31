@extends('layouts.guest')

@section('content')
    <div class="col-lg-7">
        <div class="text-center mb-5">
            <!-- <h1 class="text-white">{{ __('SEPRO M.E') }}</h1> -->
        </div>
        <div class="card-group d-block d-md-flex row">
            <div class="card col-md-7 p-4 mb-0">
                <div class="card-body">
                    <h2 class="text-center mb-4">{{ __('Iniciar Sesión') }}</h2>
                    @include('layouts.includes.errors')
                    
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                                </svg>
                            </span>
                            <input class="form-control @error('username') is-invalid @enderror" 
                                   type="text" name="usuario"
                                   placeholder="{{ __('Usuario') }}" required autofocus>
                            @error('username')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="{{ asset('icons/coreui.svg#cil-lock-locked') }}"></use>
                                </svg>
                            </span>
                            <input class="form-control @error('password') is-invalid @enderror" 
                                   type="password" name="password"
                                   placeholder="{{ __('Contraseña') }}" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <div class="col-12">
                                <button class="btn w-100 px-4" 
                                        type="submit" 
                                        style="background-color: #19346A; color: white;">
                                    {{ __('Ingresar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card col-md-5 text-white py-5 d-flex align-items-center justify-content-center" 
                 style="background-color: #19346A;">
                <div>
                    <img src="{{ asset('/img/logo-sepro-blanco.png') }}" 
                         alt="Logo" 
                         class="card-img" 
                         style="width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>
@endsection
