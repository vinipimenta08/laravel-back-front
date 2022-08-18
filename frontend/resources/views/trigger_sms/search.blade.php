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
                            <li class="breadcrumb-item"> <a class="text-muted">Serviços</a></li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">Disparo SMS</li>
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
                            Disparo SMS<br>
                        </h2>
                        <form action method="POST" id="searchDisparoSMS" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <b>Data Início:</b>
                                    <div class="form-group">
                                        <input id="data_inicio" name="init_date" type="date" class="form-control bg-white border-0 custom-shadow custom-radius" onblur="fillDataInicio()">
                                        <span class="error-validate"><p id="validate-init_date"></p></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <b>Data Fim:</b>
                                    <div class="form-group">
                                        <input id="data_fim" name="end_date" type="date" class="form-control bg-white border-0 custom-shadow custom-radius" onblur="fillDataFim()">
                                        <span class="error-validate"><p id="validate-end_date"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if ($user['id_profile'] == 1)
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <b>Cliente:</b>
                                        <div class="form-group ">
                                            <select id="id_client" name="id_client" class="form-control bg-white border-0 custom-shadow custom-radius">
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
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="disparo_sms_lista"></div>

    </div>
</div>

<script src="{{asset('assets/plugins/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{asset('assets/plugins/ui/notifications.js')}}"></script>

<script type="text/javascript">

    $(document).ready(function() {
        $('#searchDisparoSMS').submit(function(e){

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
            var formdata = new FormData($("form[id='searchDisparoSMS']")[0]);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: "{{route('disparosms.store')}}",
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
                    $('#disparo_sms_lista').html(result);
                    $('#zero_config').DataTable();
                    $('#btnSearch').text(originalText);
                    $('#btnSearch').prop('disabled', false);
                },
                error: function (result) {
                    $('#btnSearch').text(originalText);
                    $('#btnSearch').prop('disabled', false);
                }
            })

        });
    })


    function fillDataInicio(){
        var data_inicio = $('#data_inicio').val();
        var data_fim = $('#data_fim').val();

        if (data_inicio == "") {
            return false
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
            return false
        }

        if (data_inicio == "") {
            $('#data_inicio').val(data_fim);
        }else if(data_fim < data_inicio){
            $('#data_inicio').val(data_fim);
        }

    }



    function dispararSMS(id_campanha, id_cliente){
        var data_inicio = $('#data_inicio').val();
        var data_fim = $('#data_fim').val();
        var status = $(`#status-${id_campanha}-${id_cliente}`).val();
        if (status == "") {
            let textNotification = "<b>Aviso:</b> É necessário selecionar um status.";
            showNotification('alert-warning', textNotification, 'top', 'right', '', '');
            return false;
        }

        $('#btn_disparo-'+id_campanha).prop('disabled', true);
        $('#btn_disparo-'+id_campanha+' i').removeClass('fas fa-play');
        $('#btn_disparo-'+id_campanha+' span').addClass('spinner-border spinner-border-sm');

        var formData = new FormData();

        formData.append('id_campaign', id_campanha);
        formData.append('id_send_sms', status);
        formData.append('run', 1);
        formData.append('interval', 1000000);
        formData.append('init_date', data_inicio);
        formData.append('end_date', data_fim);
        formData.append('id_client', id_cliente);

        $.ajax({
            url: '{{route("disparosms.resendSMS")}}',
            type: 'POST',
            data: formData,
            processData: false, // tell jQuery not to process the data
            contentType: false, // tell jQuery not to set contentType
            success: function (response) {
                if(response.error == 1){
                    $(".popover").popover('hide');

                    swal("Erros encontrados!", dados["mensagemInvalido"], "warning");
                    $('#btn_disparo-'+id_campanha+' span').removeClass('spinner-border spinner-border-sm');
                    $('#btn_disparo-'+id_campanha+' i').addClass('fas fa-play');
                    $('#btn_disparo-'+id_campanha).prop('disabled', false);
                    return false;
                }
                if (response.error == 500) {
                    swal(
                        `${response.data.title}`,
                        `${response.data.message}`,
                        'error'
                    );
                    $('#loader-dash').hide()
                    $('#reportrange').css('pointer-events', 'auto');
                    $('#btn_disparo-'+id_campanha+' span').removeClass('spinner-border spinner-border-sm');
                    $('#btn_disparo-'+id_campanha+' i').addClass('fas fa-play');
                    $('#btn_disparo-'+id_campanha).prop('disabled', false);
                    return false
                }
                $(".popover").popover('hide');
                var textNotification = '<strong>Disparo realizado com sucesso</strong>';
                showNotification('alert-success', textNotification, 'top', 'right', '', '');
                $('#btn_disparo-'+id_campanha+' span').removeClass('spinner-border spinner-border-sm');
                $('#btn_disparo-'+id_campanha+' i').addClass('fas fa-play');
                $('#btn_disparo-'+id_campanha).prop('disabled', false);

                swal.close();
                setTimeout(() => {
                    changepage('{{route("disparosms.index")}}');
                }, 1000);

            }
        });

    }

</script>
