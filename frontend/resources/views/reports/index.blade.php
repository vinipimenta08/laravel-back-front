<style>
    .error-validate{
        color: red;
        display: none;
        font-size: 13px;
    }
    .btn-light{
        background-color: #fff;
        border-color: #e9ecef;
        color: #7c8798;
    }
    .btn-light:focus, .btn-light.focus {
        color: #7c8798;
        background-color: #fff;
        border-color: #e9ecef;
        box-shadow: 0 0 0 0.2rem rgb(255, 255, 255);
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
                            <li class="breadcrumb-item text-muted active" aria-current="page">Relatório</li>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid" style="padding-bottom: 100px;">

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body">
                        <h2>
                            Relatório Retorno<br>
                        </h2>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <b>Data Início:</b>
                                <div class="form-group">
                                    <input id="init_date" type="date" class="form-control bg-white border-0 custom-shadow custom-radius" onblur="fillDataInicio()">
                                    <span class="error-validate"><p id="validate-init_date"></p></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <b>Data Fim:</b>
                                <div class="form-group">
                                    <input id="end_date" type="date" class="form-control bg-white border-0 custom-shadow custom-radius" onblur="fillDataFim()">
                                    <span class="error-validate"><p id="validate-end_date"></p></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <b>Centro de Custo:</b>
                                <div class="form-group">
                                    <select id="id_campaign" name="id_campaign" class="form-control selectpicker show-tick bg-white border-0 custom-shadow custom-radius" data-live-search="true" data-size="7">
                                        <option value="">Selecione</option>
                                        @foreach ($campaings as $campaing)
                                            <option value="{{$campaing['id']}}">{{$campaing['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <button id="btnSearch" type="button" class="btn btn-primary btn-block" onclick="search()">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="relatorio_lista"></div>
    </div>
</div>
{{-- <script src="{{asset('site/dist/js/custom.js')}}"></script> --}}
<script src="{{asset('assets/extra-libs/smartWizard/jquery.smartWizard.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{asset('assets/plugins/ui/notifications.js')}}"></script>
<script src="{{asset('site/js/bootstrap-select.js')}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<script type="text/javascript">
    $('.selectpicker').selectpicker({
            noneSelectedText: 'Nada selecionado',
            noneResultsText: 'Nenhum resultado encontrado {0}'
        });
    function search(){
        var init_date = $('#init_date').val();
        var end_date = $('#end_date').val();
        var id_campaign = $('#id_campaign').val();

        if (init_date == "" && end_date == "") {
            var date_now = new Date().toISOString().split('T')[0];
            $('#init_date').val(date_now);
            $('#end_date').val(date_now);
            var init_date = $('#init_date').val();
            var end_date = $('#end_date').val();
        }
        let date1 = new Date(init_date);
        let date2 = new Date(end_date);
        let diffTime = Math.abs(date2 - date1);
        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        if (diffDays > 30) {
            let textNotification = "<b>Alerta:</b> Data não pode ultrapassar o limite de 30 dias.";
            showNotification('alert-warning', textNotification, 'top', 'right', '', '');
            $('#relatorio_lista').html("");
            return false;
        }
        var originalText = $('#btnSearch').text();
        $('#btnSearch').prop('disabled', 'disabled');
        $('#btnSearch').html('Filtrando...');

        $('#init_date').prop('disabled', 'disabled');
        $('#end_date').prop('disabled', 'disabled');
        $('#id_campaign').prop('disabled', 'disabled');

        $.ajax({
            type: 'GET',
            url: "{{route('report.search')}}",
            data: { init_date: init_date, end_date: end_date, id_campaign: id_campaign, validate_loged: 'loged' },
            async: true,
            success: function (response) {
                if (response == 200) {
                    location.reload();
                    return false;
                }
                $('.error-validate').children().each(function(i, e) {
                    $(e).text('');
                });
                if (response.error) {
                    swal(
                        `${response.data.title}`,
                        `${response.data.message}`,
                        'error'
                    );
                    Object.entries(response.message).forEach((element, index) =>{
                        $((`#validate-${element[0]}`)).text(element[1]);
                    })
                    $('.error-validate').show();
                    $('#btnSearch').text(originalText);
                    $('#btnSearch').prop('disabled', '');
                    $('#init_date').prop('disabled', '');
                    $('#end_date').prop('disabled', '');
                    $('#id_campaign').prop('disabled', '');
                    $('#relatorio_lista').html(response);
                    return false;
                }
                $('#btnSearch').text(originalText);
                $('#btnSearch').prop('disabled', '');
                $('#init_date').prop('disabled', '');
                $('#end_date').prop('disabled', '');
                $('#id_campaign').prop('disabled', '');
                $('#relatorio_lista').html(response);

                $('#tab-reports').DataTable({});
            }
        });
    }

    function fillDataInicio(){
        var init_date = $('#init_date').val();
        var end_date = $('#end_date').val();

        if (init_date == "") {
            return false;
        }

        if (end_date == "") {
            $('#end_date').val(init_date);
        }else if(init_date > end_date){
            $('#end_date').val(init_date);
        }

    }

    function fillDataFim(){
        var init_date = $('#init_date').val();
        var end_date = $('#end_date').val();

        if (end_date == "") {
            return false;
        }

        if (init_date == "") {
            $('#init_date').val(end_date);
        }else if(end_date < init_date){
            $('#init_date').val(end_date);
        }

    }

    function downloadList(id_campaign = $('#id_campaign').val(), date_download, e = $('#list-all')) {
        var init_date = $('#init_date').val();
        var end_date = $('#end_date').val();
        $(e).hide();
        $(`#`+$(e).data('load-id')).show();
        if (date_download) {
            init_date = date_download;
            end_date = date_download;
        }
        if (init_date == "" && end_date == "") {
            var date_now = new Date().toISOString().split('T')[0];
            $('#init_date').val(date_now);
            $('#end_date').val(date_now);
            var init_date = $('#init_date').val();
            var end_date = $('#end_date').val();
        }
        $.ajax({
            type: 'GET',
            url: "{{route('report.list')}}",
            data: { init_date: init_date, end_date: end_date, id_campaign: id_campaign, validate_loged: 'loged' },
            async: true,
            success: function (response) {
                if (response == 200) {
                    location.reload();
                    return false;
                }
                if (response.error) {
                    swal(
                        `${response.data.title}`,
                        `${response.data.message}`,
                        'error'
                    );
                    $(`#`+$(e).data('load-id')).hide();
                    $(e).show();
                    return false
                }
                if (response.erro == 1) {
                    swal(
                        `${response.title}`,
                        `${response.message}`,
                        'warning'
                    );
                    $(`#`+$(e).data('load-id')).hide();
                    $(e).show();
                    return false
                }
                let a = document.createElement('a');
                var blobData = new Blob(['\ufeff'+response['content']], { type: 'application/vnd.ms-excel' });
                var url = window.URL.createObjectURL(blobData);
                a.href = url;
                a.download = `${response['name']}.csv`;
                a.click();
                $(`#`+$(e).data('load-id')).hide();
                $(e).show();
            }
        });
    }
    function downloadReplySms(id_campaign = $('#id_campaign').val(), date_download, e = $('#reply-all')) {
        var init_date = $('#init_date').val();
        var end_date = $('#end_date').val();
        $(e).hide();
        $(`#`+$(e).data('load-id')).show();
        if (date_download) {
            init_date = date_download;
            end_date = date_download;
        }
        if (init_date == "" && end_date == "") {
            var date_now = new Date().toISOString().split('T')[0];
            $('#init_date').val(date_now);
            $('#end_date').val(date_now);
            var init_date = $('#init_date').val();
            var end_date = $('#end_date').val();
        }
        $.ajax({
            type: 'GET',
            url: "{{route('report.reply')}}",
            data: { init_date: init_date, end_date: end_date, id_campaign: id_campaign, validate_loged: 'loged' },
            async: true,
            success: function (response) {
                if (response == 200) {
                    location.reload();
                    return false;
                }
                if (response.error) {
                    swal(
                        `${response.data.title}`,
                        `${response.data.message}`,
                        'error'
                    );
                    $(`#`+$(e).data('load-id')).hide();
                    $(e).show();
                    return false
                }
                if (response.erro == 1) {
                    swal(
                        `${response.title}`,
                        `${response.message}`,
                        'warning'
                    );
                    $(`#`+$(e).data('load-id')).hide();
                    $(e).show();
                    return false
                }
                let a = document.createElement('a');
                var blobData = new Blob(['\ufeff'+response['content']], { type: 'application/vnd.ms-excel' });
                var url = window.URL.createObjectURL(blobData);
                a.href = url;
                a.download = `${response['name']}.csv`;
                a.click();
                $(`#`+$(e).data('load-id')).hide();
                $(e).show();
            }
        });
    }
    function downloadErrors(id_campaign = $('#id_campaign').val(), date_download, e = $('#error-all')) {
        var init_date = $('#init_date').val();
        var end_date = $('#end_date').val();
        $(e).hide();
        $(`#`+$(e).data('load-id')).show();
        if (date_download) {
            init_date = date_download;
            end_date = date_download;
        }
        if (init_date == "" && end_date == "") {
            var date_now = new Date().toISOString().split('T')[0];
            $('#init_date').val(date_now);
            $('#end_date').val(date_now);
            var init_date = $('#init_date').val();
            var end_date = $('#end_date').val();
        }
        $.ajax({
            type: 'GET',
            url: "{{route('report.errors')}}",
            data: { init_date: init_date, end_date: end_date, id_campaign: id_campaign, validate_loged: 'loged' },
            async: true,
            success: function (response) {
                if (response == 200) {
                    location.reload();
                    return false;
                }
                if (response.error) {
                    swal(
                        `${response.data.title}`,
                        `${response.data.message}`,
                        'error'
                    );
                    $(`#`+$(e).data('load-id')).hide();
                    $(e).show();
                    return false
                }
                if (response.erro == 1) {
                    swal(
                        `${response.title}`,
                        `${response.message}`,
                        'warning'
                    );
                    $(`#`+$(e).data('load-id')).hide();
                    $(e).show();
                    return false
                }
                let a = document.createElement('a');
                let data_type = 'data:text/plain';
                a.href = data_type + ', ' + response['content'];
                a.download = `${response['name']}.txt`;
                a.click();
                $(`#`+$(e).data('load-id')).hide();
                $(e).show();
            }
        });
    }
</script>
