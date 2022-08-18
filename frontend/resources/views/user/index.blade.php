    <div class="page-wrapper">
        <div id="conteudo_animated">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <h3 class="page-title text-truncate text-dark font-weight-medium mb-1"></h3>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"> <a class="text-muted">Home</a></li>
                                    <li class="breadcrumb-item"> <a class="text-muted">Administrativo</a></li>
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Usuários</li>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->

            <style type="text/css">
                .btnNovoRegistro:hover{
                    color: #fff;
                }
                .btnNovoRegistro:focus{
                    color: #fff;
                }
                .error-validate{
                    color: red;
                    display: none;
                    font-size: 13px;
                }
                .loader {
                    margin-right: 1rem;
                    border: 6px solid #f3f3f3;
                    border-top: 6px solid #3498db;
                    border-radius: 50%;
                    width: 30px;
                    height: 30px;
                    animation: spin 2s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>


            <div class="container-fluid">
                <!-- CARD ADICIONAR USUARIOS -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive">
                        <div id="conteudo_animated" class="row clearfix margin_page" >
                            <div id="conteudo" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive"></div>
                        </div>
                    </div>
                </div>
                <!-- FIM CARD ADICIONAR USUARIOS -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive">
                    <a class="btn btn-primary waves-effect m-b-15 btnAbrirCollapse" role="button" style="margin-bottom: 30px; color: #fff;" onclick="javascript: abrirCollapse()">
                    <i class="fas fa-user-plus"></i> <span class="icon-name">Novo Usuário</span>
                    </a>
                    <a class="btn btn-primary waves-effect m-b-15 btnFecharCollapse" role="button" style="margin-bottom: 30px; display: none; color: #fff;" onclick="javascript: fecharCollapse()">
                    <i class="fas fa-user-plus"></i> <span class="icon-name">Novo Usuário</span>
                    </a>
                    <div class="collapse" id="collapseExample">
                        <div class="card">
                            <div class="card-body">
                                <div class="header">
                                    <div class="row clearfix">
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <h2>Adicionar Novo Usuário</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="body">
                                    <div class="form-f-box">
                                        <div class="box-body">
                                            <form action method="POST" id="idNovoRegistro" autocomplete="off">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                        <b>Nome:</b>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="name" class="form-control">
                                                            <span class="error-validate"><p id="validate-name"></p></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                        <b>Email:</b>
                                                        <div class="form-group">
                                                            <input type="text" name="email" id="email" class="form-control">
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
                                                                <input type="checkbox" id="alternative_profile" name="alternative_profile" value="1" class="chk-col-indigo" onclick="type_profile(this)"/>
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
                                                                @if ($clients)
                                                                    @foreach ($clients as $client)
                                                                        <option value="{{$client['id']}}" >{{$client['name']}}</option>
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
                                                                        <option value="{{$client['id']}}" >{{$client['name']}}</option>
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
                                                                @if ($profiles)
                                                                    @foreach ($profiles as $profile)
                                                                        <option value="{{$profile['id']}}" >{{$profile['name']}}</option>
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
                                                                        <option value="{{$menu['id']}}">{{$menu['name']}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            <span class="error-validate"><p id="validate-menus"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <button id="btnNovoUsuario" type="submit" class="btn btn-primary btn-block">Salvar</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- CARD LISTA USUARIOS -->
            <!-- zero configuration -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h2>
                                Usuários<br>
                            </h2>
                            <div class="table-responsive-sm  table-responsive-md">
                            <table id="tab-user" class="table table-striped table-bordered no-wrap">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>
                                            @if ($Currentuser['id_profile'] == 1)
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div id="loader-dash" class="loader" style="display: none;"></div>
                                                        <input type="checkbox" id="user_all" {{$initActive}} onchange="active_all(this)" name="user_all" class="chk-col-indigo active-user" style="margin-right: 5px;"/>
                                                    </div>
                                                    <div class="col-9">
                                                        Ativo
                                                    </div>
                                                </div>
                                            @else
                                                Ativo
                                            @endif
                                        </th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr id="tr-{{$user['id']}}">
                                            <td>{{$user['name']}}</td>
                                            <td>{{$user['email']}}</td>
                                            <td>
                                                <div class="demo-checkbox">
                                                    <input type="checkbox" id="user_active{{$user['id']}}" {{ $user['active'] ? 'checked' : ''}} onchange="active_profile(this, {{$user['id']}})" name="user_active{{$user['id']}}" class="chk-col-indigo active-user" style="margin-right: 5px;"/>
                                                    <label id="label-{{$user['id']}}" for="user_active{{$user['id']}}">{{ $user['active'] ? 'ativo' : 'inativo'}}</label>
                                                </div>
                                            </td>
                                            <td>
                                                <a class='btn btn-primary' onclick="changepage('{{route('usuarios.edit', ['usuario'=>$user['id']])}}')">Atualizar</a>
                                                <button type='button' class='btn btn-secondary' style='margin-left: 5px;' id="btn-delele-user-{{$user['id']}}" onclick="deleteUser({{$user['id']}})">Deletar</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FIM CARD LISTA USUARIOS -->
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#tab-user').DataTable({});
            $('#idNovoRegistro').submit(function(e){
                e.preventDefault();
                var originalText = $('#btn-submit-form-user').text();
                $('#btn-submit-form-user').text('Salvando...');
                $('#btn-submit-form-user').prop('disabled', true);
                var formdata = new FormData($("form[id='idNovoRegistro']")[0]);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: "{{route('usuarios.store')}}",
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
                            return false
                        }
                        if (result.error) {
                            Object.entries(result.message).forEach(element => {
                                $((`#validate-${element[0]}`)).text(element[1].join(' / '));
                            });
                            $('.error-validate').show();
                            return false;
                        }
                        swal(
                            'Sucesso',
                            'Usuário cadastrado com sucesso',
                            'success'
                        );
                        changepage("{{route('usuarios.index')}}")
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao salvar usuário',
                            'warning'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    }
                })

            });
        });
        function abrirCollapse(){
            $('#collapseExample').collapse('show');
            $('.btnAbrirCollapse').hide();
            $('.btnFecharCollapse').show();
        }
        function fecharCollapse(){
            $('#collapseExample').collapse('hide');
            $('.btnFecharCollapse').hide();
            $('.btnAbrirCollapse').show();
        }
        function type_profile(e){
            if(e.checked){
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
        function deleteUser(id) {
            $(`#btn-delele-user-${id}`).text('Deletando...');
            $(`#btn-delele-user-${id}`).prop('disabled', true);
            var baseUrlDelete = "{{route('usuarios.destroy', ['usuario'=>'idUser'])}}";
            var urlDelete = baseUrlDelete.replace('idUser', id);
            data = {
                method: 'delete'
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'DELETE',
                url: urlDelete,
                data: data,
                success: function(result) {
                    if (result.error == 500) {
                        swal(
                            `${result.data.title}`,
                            `${result.data.message}`,
                            'error'
                        );
                        changepage("{{route('usuarios.index')}}")
                        return false
                    }
                    if (result['error']) {
                        swal(
                            'Erro',
                            `${result['message']}`,
                            'error'
                        );    
                        return false;
                    }
                    swal(
                        'Sucesso',
                        'Usuário excluído com sucesso',
                        'success'
                    );
                    changepage("{{route('usuarios.index')}}")
                },
                error: function (result) {
                    swal(
                        'Falha',
                        'Erro ao excluir usuário',
                        'warning'
                    );
                    $(`#btn-delele-user-${id}`).text('Deletar');
                    $(`#btn-delele-user-${id}`).prop('disabled', false);
                }
            });
        }
        function active_profile(e, id) {
            checkedVal = e.checked;
            $(e).prop('checked', !checkedVal);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            data = {
                'id': id,
                'active': checkedVal
            };
            $.ajax({
                type: 'POST',
                url: "{{route('usuarios.activeprofile')}}",
                data: data,
                success: function(result) {
                    if (checkedVal) {
                        $('#label-'+id).text('ativo');
                    } else {
                        $('#label-'+id).text('inativo');
                    }
                    $(e).prop('checked', checkedVal);
                },
                error: function (result) {

                }
            });
        }

        function active_all(e) {
            $('#loader-dash').show();
            $('#user_all').hide();
            checkedVal = e.checked;
            $(e).prop('checked', !checkedVal);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            data = {
                'active': checkedVal
            };
            $.ajax({
                type: 'POST',
                url: "{{route('usuarios.activeall')}}",
                data: data,
                success: function(result) {
                    if (result.error == 500) {
                        swal(
                            `${result.data.title}`,
                            `${result.data.message}`,
                            'error'
                        );
                        $('#loader-dash').hide();
                        $('#user_all').show();
                        return false
                    }
                    changepage("{{route('usuarios.index')}}");
                    $('#loader-dash').hide();
                    $('#user_all').show();
                },
                error: function (result) {
                    $('#loader-dash').hide();
                    $('#user_all').show();
                }
            });
        }
    </script>