<style type="text/css">
    .error-validate{
        color: red;
        display: none;
        font-size: 13px;
    }
</style>
    <div class="page-wrapper">
        <!-- CARD ATUALIZA USUARIOS -->
        <!-- zero configuration -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                
                <div class="card">
                    <div class="card-body">
                        <h2>Atualização de Usuário</h2>
                        <form action method="POST" id="idNovoRegistro" autocomplete="off">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <b>Nome:</b>
                                    <div class="form-group">
                                        <input type="text" name="name" id="name" class="form-control" value="{{$user['name']}}">
                                        <span class="error-validate"><p id="validate-name"></p></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <b>Email:</b>
                                    <div class="form-group">
                                        <input type="text" name="email" id="email" class="form-control" value="{{$user['email']}}">
                                        <span class="error-validate"><p id="validate-email"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <b>Senha:</b>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" class="form-control">
                                        <span class="error-validate"><p id="validate-password"></p></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
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
                                            <input type="checkbox" id="alternative_profile" name="alternative_profile" value="1" {{$user['alternative_profile'] == 1 ? 'checked' : ''}} class="chk-col-indigo" onclick="type_profile(this.checked)"/>
                                            <label for="alternative_profile">Perfil Especialista</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="div-simple-client">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Cliente:</b>
                                    <div class="form-group ">
                                        <select id="id_client" name="id_client" class="form-control">
                                            <option value="">Selecione</option>
                                            @if ($clients)
                                                @foreach ($clients as $client)
                                                    <option value="{{$client['id']}}" {{$user['id_client'] == $client['id'] ? 'selected' : ''}} >{{$client['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error-validate"><p id="validate-id_client"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="div-multiple-client" style="display: none;">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Cliente:</b>
                                    <div class="form-group ">
                                        <select id="clients" name="clients[]" class="form-control" multiple>
                                            @if ($clients)
                                                @foreach ($clients as $client)
                                                    <option value="{{$client['id']}}" {{in_array($client['id'], $userClients) ? 'selected' : ''}}>{{$client['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error-validate"><p id="validate-clients"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="div-perfil">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Perfil Acesso:</b>
                                    <div class="form-group ">
                                        <select id="id_profile" name="id_profile" class="form-control">
                                            <option value="">Selecione</option>
                                            @if ($profiles)
                                                @foreach ($profiles as $profile)
                                                    <option value="{{$profile['id']}}" {{$user['id_profile'] == $profile['id'] ? 'selected' : ''}}>{{$profile['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error-validate"><p id="validate-id_profile"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="div-menu" style="display: none;">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Menus:</b>
                                    <div class="form-group ">
                                        <select id="menus" name="menus[]" class="form-control" multiple>
                                            @if ($menus)
                                                @foreach ($menus as $menu)
                                                    <option value="{{$menu['id']}}" {{in_array($menu['id'], $userMenus) ? 'selected' : ''}}>{{$menu['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error-validate"><p id="validate-menus"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <a type="button" class="btn btn-light" onclick="changepage('{{route('usuarios.index')}}')">Cancelar</a>
                                    <button type="submit" id="btn-submit-form-user" class="btn btn-primary waves-effect">Aplicar Alteração</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM CARD ATUALIZA USUARIOS -->
    </div>

    
    <script>
        
        function type_profile(e){
            if(e){
                $('#div-menu').show();
                $('#div-multiple-client').show();
                $('#div-perfil').hide();
                $('#div-simple-client').hide();
            }else{
                $('#div-perfil').show();
                $('#div-simple-client').show();
                $('#div-menu').hide();
                $('#div-multiple-client').hide();
            }
        }
        function force_password(e) {
            if (e.checked == true) {
                $('#password').prop('disabled', true);
                $('#confirm_password').prop('disabled', true);
            } else {
                $('#password').prop('disabled', false);
                $('#confirm_password').prop('disabled', false);
            }
        }
        $(document).ready(function() {
            type_profile("{{$user['alternative_profile'] == 1 ? true : null}}");
            $('#idNovoRegistro').submit(function(e){
                e.preventDefault();
                var originalText = $('#btn-submit-form-user').text();

                $('#btn-submit-form-user').text('Alterando...');
                $('#btn-submit-form-user').prop('disabled', true);
                var formdata = new FormData($("form[id='idNovoRegistro']")[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: "{{route('usuarios.atualizar', ['usuario' => $user['id']])}}",
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
                            $('#btn-submit-form-user').text(originalText);
                            $('#btn-submit-form-user').prop('disabled', false);
                            return false
                        }
                        if (result.error) {
                            Object.entries(result.message).forEach(element => {
                                $((`#validate-${element[0]}`)).text(element[1].join(' / '));
                            });
                            $('.error-validate').show();
                            $('#btn-submit-form-user').text(originalText);
                            $('#btn-submit-form-user').prop('disabled', false);
                            return false;
                        }
                        swal(
                            'Sucesso',
                            'Usuário alterado com sucesso',
                            'success'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao alterar usuário',
                            'warning'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    }
                })

            });
        })
    </script>