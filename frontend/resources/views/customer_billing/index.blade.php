
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
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Tarifação</li>
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
                <!-- CARD ADICIONAR TARIFAÇÃO -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive">
                        <div id="conteudo_animated" class="row clearfix margin_page" >
                            <div id="conteudo" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive"></div>
                        </div>
                    </div>
                </div>
                <!-- FIM CARD ADICIONAR TARIFAÇÃO -->
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
                    <i class="fas fa-plus"></i> <span class="icon-name">Nova Tarifação</span>
                    </a>
                    <a class="btn btn-primary waves-effect m-b-15 btnFecharCollapse" role="button" style="margin-bottom: 30px; display: none; color: #fff;" onclick="javascript: fecharCollapse()">
                    <i class="fas fa-plus"></i> <span class="icon-name">Nova Tarifação</span>
                    </a>
                    <div class="collapse" id="collapseExample">
                        <div class="card">
                            <div class="card-body">
                                <div class="header">
                                    <div class="row clearfix">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h2>Adicionar Nova Tarifação</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="body">
                                    <div class="form-f-box">
                                        <div class="box-body">
                                            <form action method="POST" id="idNovoRegistro" autocomplete="off">
                                                <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <b>Cliente:</b>
                                                    <div class="form-group ">
                                                        <select id="id_client" name="id_client" class="form-control">
                                                            <option value="">Selecione</option>
                                                            @if ($clients)
                                                                @foreach ($clients as $client)
                                                                    <option value="{{$client['id']}}" >{{$client['name']}}</option>
                                                                @endforeach
                                                            @else
                                                                <option>Nenhum dado encontrado</option>
                                                            @endif
                                                        </select>
                                                        <span class="error-validate"><p id="validate-id_client"></p></span>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <b>Valor(R$):</b>
                                                        <div class="form-group">
                                                            <input type="number" step=".01" type="value" name="value" id="value" class="form-control">
                                                            <span class="error-validate"><p id="validate-value"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        <button id="btnNovaTarifação" type="submit" class="btn btn-primary btn-block">Salvar</button>
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
            <!-- CARD LISTA TARIFAÇÃO -->
            <!-- zero configuration -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h2>
                                Tarifação<br>
                            </h2>
                            <div class="table-responsive-sm  table-responsive-md">
                            <table id="tab-customer" class="table table-striped table-bordered no-wrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Valor</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customer as $custome)
                                        <tr id="tr-{{$custome['id']}}">
                                            <td>{{$custome['id']}}</td>
                                            <td>{{$custome['id_client']}}</td>
                                            <td>{{ 'R$ '.number_format($custome['value'], 2, ',', '.') }}</td>
                                            <td>
                                                <a class='btn btn-primary' onclick="changepage('{{route('customer.edit', ['customer'=>$custome['id']])}}')">Atualizar</a>
                                                <button type='button' class='btn btn-secondary' style='margin-left: 5px;' id="btn-delele-customer-{{$custome['id']}}" onclick="delete_customer({{$custome['id']}})">Deletar</button>
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
            <!-- FIM CARD LISTA TARIFAÇÃO -->
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
        function delete_customer(id) {
            $(`#btn-delele-customer-${id}`).text('Deletando...');
            $(`#btn-delele-customer-${id}`).prop('disabled', true);
            var baseUrlDelete = "{{route('customer.destroy', ['customer'=>'idCustomer'])}}";
            var urlDelete = baseUrlDelete.replace('idCustomer', id);
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
                        changepage("{{route('customer.index')}}");
                        return false
                    }
                    if (result['error']) {
                        swal(
                            'Falha',
                            'Erro ao excluir tarifação',
                            'warning'
                        );
                    }
                    swal(
                        'Sucesso',
                        'Tarifação excluída com sucesso',
                        'success'
                    );
                    changepage("{{route('customer.index')}}");
                },
                error: function (result) {
                    $(`#btn-delele-customer-${id}`).text('Deletar');
                    $(`#btn-delele-customer-${id}`).prop('disabled', false);
                }
            });
        }
        $(document).ready(function() {
            $('#tab-customer').DataTable({});
            $('#idNovoRegistro').submit(function(e){
                e.preventDefault();
                var originalText = $('#btn-submit-form-customer').text();
                $('#btn-submit-form-customer').text('Salvando...');
                $('#btn-submit-form-customer').prop('disabled', true);
                var formdata = new FormData($("form[id='idNovoRegistro']")[0]);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: "{{route('customer.store')}}",
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
                            'Tarifação cadastrada com sucesso',
                            'success'
                        );
                        changepage("{{route('customer.index')}}")
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao salvar tarifação',
                            'warning'
                        );
                        $('#btn-submit-form-customer').text(originalText);
                        $('#btn-submit-form-customer').prop('disabled', false);
                    }
                })

            });
        })
    </script>
