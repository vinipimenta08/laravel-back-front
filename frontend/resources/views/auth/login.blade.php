@extends('layouts.authBase')

@section('content')

<div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative">
    <div class="auth-box row">
        <div class="col-lg-7 col-md-5 modal-bg-img" style="background-image: url('{{asset('assets/images/big/calendario_mobile.png')}}');">
        </div>
        <div class="col-lg-5 col-md-7 bg-white">
            <div class="p-3">
                <div class="text-center">
                    <img src="{{ asset('assets/images/genion-icon.png') }}" alt="wrapkit" style="width: 50px;">
                </div>
                <h2 class="mt-3 text-center">Genion Technology</h2>
                <p class="text-center">Digite login e senha para acessar o painel de administração.</p>
                {{-- form --}}
                <form method="POST" action="{{route('login-autentication')}}">
                    <div class="row">
                        {{-- csrf --}}
                        @csrf
                        <div class="col-lg-12">
                            <span class="text-danger">@error('fail'){{ $message }}@enderror</span>
                            @if (Session::get('fail'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ Session::get('fail')}}
                                </div>
                                @php
                                    Session::forget('fail');
                                @endphp
                            @endif
                            @if (Session::get('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ Session::get('success')}}
                                </div>
                                @php
                                    Session::forget('success');
                                @endphp
                            @endif
                            <div class="form-group">
                                <label class="text-dark" for="email">Email</label>
                                <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="email..." autofocus>
                                <span class="text-danger">@error('email'){{ $message }}@enderror</span>
                                <span class="error-validate" style="color: red;"><p id="validate-email"></p></span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="text-dark" for="password">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="Senha...">
                                <span class="text-danger">@error('password'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <button type="submit" class="btn btn-block btn-dark">Login</button>
                        </div>
                    </div>
                    <div class="p-1">
                        <div class="text-right">
                            <a class="password-recover-link text-dark" style="display: block; cursor:pointer;" onclick="esqueciMinhaSenha()">
                                Esqueci a minha senha
                            </a>
                        </div>
                    </div>
                </form>
                {{-- /form --}}
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    function esqueciMinhaSenha(){
        var email = $('#email').val();

        if (email == "") {
            console.log("email vazio");
            $(('#validate-email')).text("O campo email é obrigatório.");
            $('.error-validate').show();
        }else{
            var formData = new FormData();
            formData.append('email', email);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route("password.request") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    if(result['data'] == undefined){
                        $(('#validate-email')).text("Email não encontrado.");
                        $('.error-validate').css('color', 'red');
                        $('.error-validate').show();
                    }else{
                        $(('#validate-email')).text("Recuperação de senha enviado para o email.");
                        $('.error-validate').css('color', 'blue');
                        $('.error-validate').show();
                    }
                }
            });
        }
        return false;
    }
</script>
