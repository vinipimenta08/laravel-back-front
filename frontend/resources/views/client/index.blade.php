
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
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Clientes</li>
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
                <!-- CARD ADICIONAR CLIENTE -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive">
                        <div id="conteudo_animated" class="row clearfix margin_page" >
                            <div id="conteudo" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive"></div>
                        </div>
                    </div>
                </div>
                <!-- FIM CARD ADICIONAR CLIENTE -->
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
                    <i class="fas fa-plus"></i> <span class="icon-name">Novo Cliente</span>
                    </a>
                    <a class="btn btn-primary waves-effect m-b-15 btnFecharCollapse" role="button" style="margin-bottom: 30px; display: none; color: #fff;" onclick="javascript: fecharCollapse()">
                    <i class="fas fa-plus"></i> <span class="icon-name">Novo Cliente</span>
                    </a>
                    <div class="collapse" id="collapseExample">
                        <div class="card">
                            <div class="card-body">
                                <div class="header">
                                    <div class="row clearfix">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h2>Adicionar Novo Cliente</h2>
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
                                                        <b>Contato:</b>
                                                        <div class="form-group">
                                                            <input type="text" name="contact" id="contact" class="form-control">
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
                                                                <input type="checkbox" id="just_sms" name="just_sms" value="1" class="chk-col-indigo" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="div-broker">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <b>Corretora SMS:</b>
                                                        <div class="form-group ">
                                                            <select id="brokers" name="brokers[]" class="form-control" multiple>
                                                                @if ($brokers)
                                                                    @foreach ($brokers as $broker)
                                                                        <option value="{{$broker['id']}}" >{{$broker['name']}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            <span class="error-validate"><p id="validate-brokers"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <button id="btnNovoClient" type="submit" class="btn btn-primary btn-block">Salvar</button>
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
            <!-- CARD LISTA CLIENTE -->
            <!-- zero configuration -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h2>
                                Cliente<br>
                            </h2>
                            <div class="table-responsive-sm  table-responsive-md">
                            <table id="tab-client" class="table table-striped table-bordered no-wrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Contato</th>
                                        <th>Ativo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clients as $client)
                                        <tr id="tr-{{$client['id']}}">
                                            <td>{{$client['id']}}</td>
                                            <td>{{$client['name']}}</td>
                                            <td>{{$client['contact']}}</td>
                                            <td>
                                                <div class="demo-checkbox">
                                                    <input type="checkbox" id="client_active-{{$client['id']}}" {{ $client['active'] ? 'checked' : ''}} onchange="active_client(this, {{$client['id']}})" name="client_active-{{$client['id']}}" class="chk-col-indigo" style="margin-right: 5px;"/>
                                                    <label id="label-{{$client['id']}}" for="client_active-{{$client['id']}}">{{ $client['active'] ? 'ativo' : 'inativo'}}</label>
                                                </div>
                                            </td>
                                            <td>
                                                <a class='btn btn-primary' onclick="changepage('{{route('cliente.edit', ['cliente'=>$client['id']])}}')">Atualizar</a>
                                                <button type='button' class='btn btn-secondary' style='margin-left: 5px;' id="btn-delele-client-{{$client['id']}}" onclick="delete_client({{$client['id']}})">Deletar</button>
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
            <!-- FIM CARD LISTA CLIENTE -->
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
        function delete_client(id) {
            $(`#btn-delele-client-${id}`).text('Deletando...');
            $(`#btn-delele-client-${id}`).prop('disabled', true);
            var baseUrlDelete = "{{route('cliente.destroy', ['cliente'=>'idUser'])}}";
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
                        changepage("{{route('cliente.index', ['cliente'=>$client['id']])}}");
                        return false
                    }
                    if (result['error']) {
                        swal(
                            'Falha',
                            'Erro ao excluir cliente',
                            'warning'
                        );
                    }
                    swal(
                        'Sucesso',
                        'Cliente excluído com sucesso',
                        'success'
                    );
                    changepage("{{route('cliente.index', ['cliente'=>$client['id']])}}");
                },
                error: function (result) {
                    $(`#btn-delele-client-${id}`).text('Deletar');
                    $(`#btn-delele-client-${id}`).prop('disabled', false);
                }
            });
        }
        function active_client(e, id) {
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
                url: "{{route('cliente.active')}}",
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
            $('#tab-client').DataTable({});
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
                    url: "{{route('cliente.store')}}",
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
                        if (result['error'] == 100) {
                            Object.keys(result.message).forEach(key => {
                                $(('#validate-'+key)).text(result.message[key].join(' / '));
                            });
                            $('.error-validate').show();
                            return false;
                        }
                        swal(
                            'Sucesso',
                            'Cliente cadastrado com sucesso',
                            'success'
                        );
                        changepage("{{route('cliente.index')}}")
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao salvar cliente',
                            'warning'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    }
                })

            });
        })
    </script>
