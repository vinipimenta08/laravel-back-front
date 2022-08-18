@extends('layouts.authBase')

@section('content')

<div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative">
    <div class="auth-box row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive bg-white">
            <div class="p-3">
                <div class="text-center">
                    <img src="{{ asset('assets/images/genion-icon.png') }}" alt="wrapkit" style="width: 50px;">
                </div>
                <h2 class="mt-3 text-center">Recuperação de senha</h2>
                <p class="text-center">Preencha os campos abaixo.</p>
                <form action method="POST" id="formProfile" autocomplete="off">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <b>Senha:</b>
                                <div class="form-group">
                                    <input type="password" name="password" id="password" class="form-control">
                                    <span class="error-validate"><p id="validate-password"></p></span>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <b>Confirmar Senha:</b>
                                <div class="form-group">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                                    <span class="error-validate"><p id="validate-confirm_password"></p></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row" style="padding-left: 100px;">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <button class="btn btn-primary" type="button" id="btn_concluir" onclick="salvar()">
                            <span role="" aria-hidden="true"></span>
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script >

    function salvar(){

        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();

        $('#btn_concluir').prop('disabled', 'disabled');
        $('#btn_concluir').html('Confirmando...');
        $('#btn_concluir span').addClass('spinner-border spinner-border-sm');

        if (password == "" && confirm_password == "") {
            $('#btn_concluir span').removeClass('spinner-border spinner-border-sm');
            $('#btn_concluir').prop('disabled', false);
            $('#btn_concluir').html('Confirmar');

            $(('#validate-password')).text("Campo senha não pode ser vazio.");
            $('.error-validate').css('color', 'red');
            $('.error-validate').show();
            return false;
        }else if(password != confirm_password){
            $('#btn_concluir span').removeClass('spinner-border spinner-border-sm');
            $('#btn_concluir').prop('disabled', false);
            $('#btn_concluir').html('Confirmar');

            $(('#validate-password')).text("As senhas não são iguais.");
            $('.error-validate').css('color', 'red');
            $('.error-validate').show();
            return false;
        }

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const token = urlParams.get('token')
        console.log(token);
        if (token == "") {
            $('#btn_concluir span').removeClass('spinner-border spinner-border-sm');
            $('#btn_concluir').prop('disabled', false);
            $('#btn_concluir').html('Confirmar');

            $(('#validate-password')).text("Erro ao recuperar, favor entrar em contato");
            $('.error-validate').css('color', 'red');
            $('.error-validate').show();
            return false;
        }
        var formData = new FormData();
        formData.append('token', token);
        formData.append('password', password);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route("password.reset") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(result) {
                setTimeout(() => {
                    window.location.href = "{{route('login')}}";
                    $('#btn_concluir span').removeClass('spinner-border spinner-border-sm');
                    $('#btn_concluir').prop('disabled', false);
                    $('#btn_concluir').html('Confirmar');
                }, 1000);
            }
        });
    }

</script>
