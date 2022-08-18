<style>
    .error-validate{
        color: red;
        display: none;
        font-size: 13px;
    }
</style>
<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1"></h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"> <a class="text-muted">Home</a></li>
                            <li class="breadcrumb-item"> <a class="text-muted">Administrativo</a></li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">Financeiro</li>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body">
                        <h2>
                            Financeiro<br>
                        </h2>
                        <form action method="POST" id="searchFinanceiro" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <b>Data Início:</b>
                                    <div class="form-group">
                                        <input id="data_inicio" name="date_init" type="date" class="form-control bg-white border-0 custom-shadow custom-radius" onblur="fillDataInicio()">
                                        <span class="error-validate"><p id="validate-date_init"></p></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <b>Data Fim:</b>
                                    <div class="form-group">
                                        <input id="data_fim" name="date_end" type="date" class="form-control bg-white border-0 custom-shadow custom-radius" onblur="fillDataFim()">
                                        <span class="error-validate"><p id="validate-date_end"></p></span>
                                    </div>
                                </div>
                                @if ($user['id_profile'] == 1)
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <b>Cliente:</b>
                                        <div class="form-group ">
                                            <select id="id_client" name="id_client" class="form-control">
                                                <option value="">Selecione</option>
                                                @if ($clients)
                                                    @foreach ($clients['data'] as $client)
                                                        <option value="{{$client['id']}}" >{{$client['name']}}</option>
                                                    @endforeach
                                                @else
                                                    <option>Nenhum dado encontrado</option>
                                                @endif
                                            </select>
                                            <span class="error-validate"><p id="validate-id_client"></p></span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <button id="btnSearch" type="submit" class="btn btn-primary btn-block">Filtrar</button>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-group">
                                        <div class="demo-checkbox">
                                            <input type="checkbox" id="search_campaign" name="search_campaign" value="1" class="chk-col-indigo"/>
                                            <label for="search_campaign">Por Centro de Custo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="custo_lista"></div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#searchFinanceiro').submit(function(e){

            var data_inicio = $('#data_inicio').val();
            var data_fim = $('#data_fim').val();

            var originalText = $('#btnSearch').text();
            $('#btnSearch').text('Filtrando...');
            $('#btnSearch').prop('disabled', true);
            if (data_inicio == "" && data_fim == "") {
                $('#data_inicio').val('<?php echo date('Y-m-d') ?>');
                $('#data_fim').val('<?php echo date('Y-m-d') ?>');
            }

            e.preventDefault();
            var formdata = new FormData($("form[id='searchFinanceiro']")[0]);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: "{{route('financeiro.store')}}",
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
                        Object.entries(result.message).forEach((element, index) =>{
                            $((`#validate-${element[0]}`)).text(element[1]);
                        })
                        $('.error-validate').show();
                        return false;
                    }
                    $('#btnSearch').text(originalText);
                    $('#btnSearch').prop('disabled', false);

                    $('#custo_lista').html(result);
                    $('#tab-financy').DataTable();
                },
                error: function (result) {
                }
            })

        });
    })
    function fillDataInicio(){
        var data_inicio = $('#data_inicio').val();
        var data_fim = $('#data_fim').val();
        if (data_inicio == "") {
            return false;
        }
        if (data_fim == "") {
            $('#data_fim').val(data_inicio);
        }else if(data_inicio > data_fim){
            $('#data_fim').val(data_inicio);
        }

    }
    function fillDataFim(){
        var data_inicio = $('#data_inicio').val();
        var data_fim = $('#data_fim').val();

        if (data_fim == "") {
            return false;
        }

        if (data_inicio == "") {
            $('#data_inicio').val(data_fim);
        }else if(data_fim < data_inicio){
            $('#data_inicio').val(data_fim);
        }

    }

</script>
