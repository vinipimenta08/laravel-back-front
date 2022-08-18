
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
                                    <li class="breadcrumb-item"> <a class="text-muted">Configurações</a></li>
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Menus</li>
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
            </style>
            
        
            <div class="container-fluid">
                <!-- CARD ADICIONAR MENU -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive">                
                        <div id="conteudo_animated" class="row clearfix margin_page" >
                            <div id="conteudo" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive"></div>
                        </div>
                    </div>
                </div>
                <!-- FIM CARD ADICIONAR MENU -->        
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
                    <i class="fas fa-plus"></i> <span class="icon-name">Novo Menu</span>
                    </a>        
                    <a class="btn btn-primary waves-effect m-b-15 btnFecharCollapse" role="button" style="margin-bottom: 30px; display: none; color: #fff;" onclick="javascript: fecharCollapse()">
                    <i class="fas fa-plus"></i> <span class="icon-name">Novo Menu</span>
                    </a>
                    <div class="collapse" id="collapseExample">
                        <div class="card">
                            <div class="card-body">
                                <div class="header">
                                    <div class="row clearfix">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h2>Adicionar Novo Menu</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="body">
                                    <div class="form-f-box">
                                        <div class="box-body">
                                            <form action method="POST" id="idNovoRegistro" autocomplete="off">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        <b>Nome:</b>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="name" class="form-control">
                                                            <span class="error-validate"><p id="validate-name"></p></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        <b>Link:</b>
                                                        <div class="form-group">
                                                            <input type="text" name="href" id="href" class="form-control">
                                                            <span class="error-validate"><p id="validate-href"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        <b>Local do icone:</b>
                                                        <div class="form-group">
                                                            <select id="locate-icon" name="locate-icon" class="form-control">
                                                                <option value="">Selecione</option>
                                                                <option value="fontawesome">Fontawesome</option>
                                                                <option value="feather-icon">Feather-Icon</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        <b>Icone:</b>
                                                        <div class="form-group">
                                                            <input type="text" name="icon" id="icon" class="form-control">
                                                            <span class="error-validate"><p id="validate-icon"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        <b>Tipo:</b>
                                                        <div class="form-group">
                                                            <input type="text" name="slug" id="slug" class="form-control">
                                                            <span class="error-validate"><p id="validate-slug"></p></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        <b>Sequencia:</b>
                                                        <div class="form-group">
                                                            <input type="text" name="sequence" id="sequence" class="form-control">
                                                            <span class="error-validate"><p id="validate-sequence"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="div-perfil">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <b>Perfil Acesso:</b>
                                                        <div class="form-group ">
                                                            <select id="roles" name="roles[]" class="form-control" multiple>
                                                                @if ($profiles)
                                                                    @foreach ($profiles as $profile)
                                                                        <option value="{{$profile['name']}}" >{{$profile['name']}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            <span class="error-validate"><p id="validate-roles"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <button id="btnNovoMenu" type="submit" class="btn btn-primary btn-block">Salvar</button>
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
            <!-- CARD LISTA MENU -->
            <!-- zero configuration -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                
                    <div class="card">
                        <div class="card-body">
                            <h2>
                                Menu<br>
                            </h2>
                            <div class="table-responsive-sm  table-responsive-md">
                                <table id="tab-menu" class="table table-striped table-bordered no-wrap">
                                    <thead>
                                        <tr>
                                            <th>Sequencia</th>
                                            <th>ID</th>
                                            <th>Menu</th>
                                            <th>Ativo</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($menus as $menu)
                                            <tr id="tr-{{$menu['id']}}">
                                                <td>{{$menu['sequence']}}</td>
                                                <td>{{$menu['id']}}</td>
                                                <td>{{$menu['name']}}</td>
                                                <td>
                                                    <div class="demo-checkbox">
                                                        <input type="checkbox" id="menu_active{{$menu['id']}}" {{ $menu['active'] ? 'checked' : ''}} onchange="active_menu(this, {{$menu['id']}})" name="menu_active{{$menu['id']}}" class="chk-col-indigo" style="margin-right: 5px;"/>
                                                        <label id="label-{{$menu['id']}}" for="menu_active{{$menu['id']}}">{{ $menu['active'] ? 'ativo' : 'inativo'}}</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a class='btn btn-primary' onclick="changepage('{{route('menu.edit', ['menu'=>$menu['id']])}}')">Atualizar</a>
                                                    <button type='button' class='btn btn-secondary' style='margin-left: 5px;' id="btn-delele-menu-{{$menu['id']}}" onclick="deleteMenu({{$menu['id']}})">Deletar</button>
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
            <!-- FIM CARD LISTA MENU -->
        </div>
    </div>

    <script>
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
        function deleteMenu(id) {
            $(`#btn-delele-menu-${id}`).text('Deletando...');
            $(`#btn-delele-menu-${id}`).prop('disabled', true);
            var baseUrlDelete = "{{route('menu.destroy', ['menu'=>'idUser'])}}";
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
                        changepage("{{route('menu.index')}}")
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
                        'Menu excluído com sucesso',
                        'success'
                    );
                    changepage("{{route('menu.index')}}")
                },
                error: function (result) {
                    swal(
                        'Falha',
                        'Erro ao excluir menu',
                        'warning'
                    );
                    $(`#btn-delele-menu-${id}`).text('Deletar');
                    $(`#btn-delele-menu-${id}`).prop('disabled', false);
                }
            });
        }
        function active_menu(e, id) {
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
                url: "{{route('menu.active')}}",
                data: data,
                success: function(result) {
                    if (result.error == 500) {
                        swal(
                            `${result.data.title}`,
                            `${result.data.message}`,
                            'error'
                        );
                        return false
                    }
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
        $(document).ready(function() {
            $('#tab-menu').DataTable({});
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
                    url: "{{route('menu.store')}}",
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
                        if (result['error']) {
                            Object.keys(result.message).forEach(key => {
                                $(('#validate-'+key)).text(result.message[key].join(' / '));
                            });
                            $('.error-validate').show();
                            return false;
                        }
                        swal(
                            'Sucesso',
                            'Menu cadastrado com sucesso',
                            'success'
                        );
                        changepage("{{route('menu.index')}}")
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao salvar menu',
                            'warning'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    }
                })

            });
        })
    </script>