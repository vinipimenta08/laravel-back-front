<style type="text/css">
    .error-validate{
        color: red;
        display: none;
        font-size: 13px;
    }
</style>
    <div class="page-wrapper">
        <!-- CARD ATUALIZA CLIENTE -->
        <!-- zero configuration -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body">
                        {{-- @php
                            dd();
                        @endphp --}}
                        <h2>Atualização de Cliente</h2>
                        <form action method="POST" id="idNovoRegistro" autocomplete="off">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Nome:</b>
                                    <div class="form-group">
                                        <input type="text" name="name" value="{{$client['name']}}" id="name" class="form-control">
                                        <span class="error-validate"><p id="validate-name"></p></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Contato:</b>
                                    <div class="form-group">
                                        <input type="text" name="contact" value="{{$client['contact']}}" id="contact" class="form-control">
                                        <span class="error-validate"><p id="validate-contact"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Senha:</b>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" class="form-control">
                                        <span class="error-validate"><p id="validate-password"></p></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Confirmar Senha:</b>
                                    <div class="form-group">
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                                        <span class="error-validate"><p id="validate-confirm_password"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-group">
                                        <div class="demo-checkbox">
                                            <label for="just_sms">Envio apenas de sms:</label>
                                            <input type="checkbox" id="just_sms" name="just_sms" value="1" {{$client['just_sms'] == 1 ? 'checked' : ''}} class="chk-col-indigo" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="div-perfil">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Corretora SMS:</b>
                                    <div class="form-group ">
                                        <select id="brokers" name="brokers[]" class="form-control" multiple>
                                            @if ($brokers)
                                                @foreach ($brokers as $broker)
                                                    @if ($broker['active'] == 1)
                                                        <option value="{{$broker['id']}}" {{in_array($broker['name'], $rules) ? 'selected' : ''}}>{{$broker['name']}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error-validate"><p id="validate-brokers"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <a type="button" class="btn btn-light" onclick="changepage('{{route('cliente.index')}}')">Cancelar</a>
                                    <button type="submit" id="btn-submit-form-client" class="btn btn-primary waves-effect">Aplicar Alteração</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM CARD ATUALIZA CLIENTE -->
    </div>

    <script>

        $(document).ready(function() {
            $('#idNovoRegistro').submit(function(e){
                e.preventDefault();
                var originalText = $('#btn-submit-form-client').text();

                $('#btn-submit-form-client').text('Alterando...');
                $('#btn-submit-form-client').prop('disabled', true);
                var formdata = new FormData($("form[id='idNovoRegistro']")[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "{{route('cliente.atualizar', ['cliente' => $client['id']])}}",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        $('.error-validate').children().each(function(i, e) {
                            $(e).text('');
                        });
                        if (result.error == 500) {
                            swal(
                                `${result.data.title}`,
                                `${result.data.message}`,
                                'error'
                            );
                            $('#btn-submit-form-client').text(originalText);
                            $('#btn-submit-form-client').prop('disabled', false);
                            return false
                        }
                        if (result['error'] == 100) {
                            Object.keys(result.message).forEach(key => {
                                $(('#validate-'+key)).text(result.message[key].join(' / '));
                            });
                            $('.error-validate').show();
                            $('#btn-submit-form-client').text(originalText);
                            $('#btn-submit-form-client').prop('disabled', false);
                            return false;
                        }
                        swal(
                            'Sucesso',
                            'Cliente alterado com sucesso',
                            'success'
                        );
                        $('#btn-submit-form-client').text(originalText);
                        $('#btn-submit-form-client').prop('disabled', false);
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao alterar cliente',
                            'warning'
                        );
                        $('#btn-submit-form-client').text(originalText);
                        $('#btn-submit-form-client').prop('disabled', false);
                    }
                })

            });
        })
    </script>
