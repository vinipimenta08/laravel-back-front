
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
                                    <li class="breadcrumb-item"> <a class="text-muted">Serviços</a></li>
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Centro de Custo</li>
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
                <!-- CARD ADICIONAR CENTRO DE CUSTO -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive">                
                        <div id="conteudo_animated" class="row clearfix margin_page" >
                            <div id="conteudo" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive"></div>
                        </div>
                    </div>
                </div>
                <!-- FIM CARD ADICIONAR CENTRO DE CUSTO -->        
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
                    <i class="fas fa-plus"></i> <span class="icon-name">Novo Centro de Custo</span>
                    </a>        
                    <a class="btn btn-primary waves-effect m-b-15 btnFecharCollapse" role="button" style="margin-bottom: 30px; display: none; color: #fff;" onclick="javascript: fecharCollapse()">
                    <i class="fas fa-plus"></i> <span class="icon-name">Novo Centro de Custo</span>
                    </a>
                    <div class="collapse" id="collapseExample">
                        <div class="card">
                            <div class="card-body">
                                <div class="header">
                                    <div class="row clearfix">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h2>Adicionar Novo Centro de Custo</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="body">
                                    <div class="form-f-box">
                                        <div class="box-body">
                                            <form action method="POST" id="idNovoRegistro" autocomplete="off">
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <b>Nome:</b>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="name" class="form-control">
                                                            <span class="error-validate"><p id="validate-name"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if (count($clients))
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <b>Cliente:</b>
                                                            <div class="form-group">
                                                                <select id="id_client" name="id_client" class="form-control">
                                                                    @if ($clients)
                                                                        @foreach ($clients as $client)
                                                                            <option value="{{$client['id']}}" >{{$client['name']}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif                          
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <button id="btnNovoCentroCusto" type="submit" class="btn btn-primary btn-block">Salvar</button>
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
            <!-- CARD LISTA CENTRO DE CUSTO -->
            <!-- zero configuration -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                
                    <div class="card">
                        <div class="card-body">
                            <h2>
                                Centro de Custo<br>
                            </h2>
                            <div class="table-responsive-sm  table-responsive-md">
                            <table id="tab-cost-center" class="table table-striped table-bordered no-wrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Centro de Custo</th>
                                        <th>Ativo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($campaigns as $campaign)
                                        <tr id="tr-{{$campaign['id']}}">
                                            <td>{{$campaign['id']}}</td>
                                            <td>{{$campaign['name']}}</td>
                                            <td>
                                                <div class="demo-checkbox">
                                                    <input type="checkbox" id="campaign_active{{$campaign['id']}}" {{ $campaign['active'] ? 'checked' : ''}} onchange="active_cost_center(this, {{$campaign['id']}})" name="campaign_active{{$campaign['id']}}" class="chk-col-indigo" style="margin-right: 5px;"/>
                                                    <label id="label-{{$campaign['id']}}" for="campaign_active{{$campaign['id']}}">{{ $campaign['active'] ? 'ativo' : 'inativo'}}</label>
                                                </div>
                                            </td>
                                            <td>
                                                <a class='btn btn-primary' onclick="changepage('{{route('centrodecusto.edit', ['centrodecusto'=>$campaign['id']])}}')">Atualizar</a>
                                                <button type='button' class='btn btn-secondary' style='margin-left: 5px;' id="btn-delele-campaign-{{$campaign['id']}}" onclick="deleteCostCenter({{$campaign['id']}})">Deletar</button>
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
            <!-- FIM CARD LISTA CENTRO DE CUSTO -->
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
        function deleteCostCenter(id) {
            $(`#btn-delele-cost-center-${id}`).text('Deletando...');
            $(`#btn-delele-cost-center-${id}`).prop('disabled', true);
            var baseUrlDelete = "{{route('centrodecusto.destroy', ['centrodecusto'=>'idUser'])}}";
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
                        changepage("{{route('centrodecusto.index')}}");
                        return false
                    }
                    if (result['error']) {
                        swal(
                            'Falha',
                            'Erro ao excluir centro de custo',
                            'warning'
                        );    
                    }
                    swal(
                        'Sucesso',
                        'Centro de custo excluído com sucesso',
                        'success'
                    );
                    changepage("{{route('centrodecusto.index')}}");
                },
                error: function (result) {
                    swal(
                        'Falha',
                        'Erro ao excluir centro de custo',
                        'warning'
                    );
                    $(`#btn-delele-cost-center-${id}`).text('Deletar');
                    $(`#btn-delele-cost-center-${id}`).prop('disabled', false);
                }
            });
        }
        function active_cost_center(e, id) {
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
                url: "{{route('centrodecusto.activecostcenter')}}",
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
            $('#tab-cost-center').DataTable({
                "order": [[ 1, "asc" ]]
            });
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
                    url: "{{route('centrodecusto.store')}}",
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
                        if (result['status'] == 'error') {
                            Object.keys(result.message).forEach(key => {
                                $(('#validate-'+key)).text(result.message[key].join(' / '));
                            });
                            $('.error-validate').show();
                            return false;
                        }
                        swal(
                            'Sucesso',
                            'Centro de Custo cadastrado com sucesso',
                            'success'
                        );
                        changepage("{{route('centrodecusto.index')}}")
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao salvar centro de custo',
                            'warning'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    }
                })

            });
        })
    </script>