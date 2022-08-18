@php
    use Carbon\Carbon;
@endphp

<style>

    .form-control:focus {
        color: #495057;
        background-color: #fff;
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0rem rgba(0, 123, 255, .25)
    }

    .btn-secondary:focus {
        box-shadow: 0 0 0 0rem rgba(108, 117, 125, .5)
    }

    .close:focus {
        box-shadow: 0 0 0 0rem rgba(108, 117, 125, .5)
    }

    .mt-200 {
        margin-top: 200px
    }

    #smartwizard>ul>li{
        pointer-events: none;
        cursor: not-allowed;
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
                            <li class="breadcrumb-item text-muted active" aria-current="page">Blacklist</li>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive">
                <div class="row">
                    <div class="btnLista responsive" style="margin-left: 15px;">
                        <a class="btn btn-primary waves-effect m-b-15 btnAbrirCollapse" role="button" style="margin-bottom: 30px; color: #fff;" onclick="javascript: abrirCollapse()">
                            <i class="fas fa-plus"></i> <span class="icon-name">Adicionar Lista</span>
                        </a>
                        <a class="btn btn-primary waves-effect m-b-15 btnFecharCollapse" role="button" style="margin-bottom: 30px; display: none; color: #fff;" onclick="javascript: fecharCollapse()">
                            <i class="fas fa-plus"></i> <span class="icon-name">Adicionar Lista</span>
                        </a>
                    </div>
                    <div class="btnAdvancedSearch responsive" style="margin-left: 15px;">
                        <a class="btn btn-secondary waves-effect m-b-15 btnAbrirCollapseAdvancedSearch" role="button" style="margin-bottom: 30px; color: #fff;" onclick="javascript: abrirCollapseAdvancedSearch()">
                            <i class="fas fa-search"></i> <span class="icon-name">Pesquisa Avançada</span>
                        </a>
                        <a class="btn btn-secondary waves-effect m-b-15 btnFecharCollapseAdvancedSearch" role="button" style="margin-bottom: 30px; display: none; color: #fff;" onclick="javascript: fecharCollapseAdvancedSearch()">
                            <i class="fas fa-search"></i> <span class="icon-name">Pesquisa Avançada</span>
                        </a>
                    </div>
                </div>
                <div class="collapse" id="collapseExample">
                    <div class="card">
                        <div class="card-body">
                            <div class="body">
                                <div class="row">
                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                        <h2>
                                            Carregamento de Arquivo
                                        </h2>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <a href="{{route('blacklist.downloadlayout')}}" style="float: right; margin: 10px;"><i class="fas fa-download"></i> Layout</a>
                                    </div>
                                </div>
                                <div class="form-f-box">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <b>Arquivo:</b>
                                                <form >
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-upload"></i></span>
                                                        </div>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input required" accept=".csv" id="file" name="file" />
                                                            <label class="custom-file-label" for="file">Escolher arquivo</label>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div style="text-align: right;">
                                                    <button class="btn btn-primary" type="button" id="btn_salvar" onclick="salvar()">
                                                        <span role="" aria-hidden="true"></span>
                                                        Importar Arquivo
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="collapseAdvancedSearch">
                    <div class="card">
                        <div class="card-body">
                            <div class="body">
                                <div class="row">
                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                        <h2>
                                            Pesquisa Avançada
                                        </h2>
                                    </div>
                                </div>
                                <div class="form-f-box">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <b>Telefone:</b>
                                                        <div class="form-group">
                                                            <input type="tel" name="telefone" id="telefone" class="form-control">
                                                            <span class="error-validate"><p id="validate-telefone"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <button class="btn btn-primary btn-block" type="button" id="btn_salvar" onclick="filtrar(true)">
                                                            <span role="" aria-hidden="true"></span>
                                                            Filtrar
                                                        </button>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <button class="btn btn-secondary btn-block" type="button" id="btn_voltar" onclick="voltar()">
                                                            <span role="" aria-hidden="true"></span>
                                                            Voltar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <h2>
                                    BlackList
                                </h2>
                            </div>
                        </div>
                        <div id="table_blacklist"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <script src="{{asset('site/dist/js/custom.js')}}"></script> --}}
<script src="{{asset('assets/extra-libs/smartWizard/jquery.smartWizard.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{asset('assets/plugins/ui/notifications.js')}}"></script>
<script src="{{asset('assets/plugins/mask/jquery.mask.js')}}"></script>
<script src="{{asset('assets/plugins/mask/jquery.mask.min.js')}}"></script>
<script src="{{asset('site/js/bootstrap-select.js')}}"></script>
<link rel="stylesheet" href="{{asset('assets/extra-libs/smartWizard/smart_wizard.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/extra-libs/smartWizard/smartWizard.min.css')}}">

<script>
    $(document).ready(function(){
        $('#tab-blacklists').DataTable({
            "order": [[ 1, "asc" ]]
        });
        $('#file').change(function () {
            var file = $('#file')[0].files[0].name;
            $('.custom-file-label').text(file);
        });
        $('#telefone').mask('(00)00000-0000');
        filtrar(false);
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

    function abrirCollapseAdvancedSearch(){
        $('#collapseAdvancedSearch').collapse('show');
        $('.btnAbrirCollapseAdvancedSearch').hide();
        $('.btnFecharCollapseAdvancedSearch').show();
    }

    function fecharCollapseAdvancedSearch(){
        filtrar(false);
        $('#collapseAdvancedSearch').collapse('hide');
        $('#telefone').val('');
        $('.btnFecharCollapseAdvancedSearch').hide();
        $('.btnAbrirCollapseAdvancedSearch').show();
    }

    function voltar(){
        var telefone = $('#telefone').val();

        if (telefone == "") {
            $('#collapseAdvancedSearch').collapse('hide');
            $('#telefone').val('');
            $('.btnFecharCollapseAdvancedSearch').hide();
            $('.btnAbrirCollapseAdvancedSearch').show();
        }else{
            filtrar(false);
            $('#collapseAdvancedSearch').collapse('hide');
            $('#telefone').val('');
            $('.btnFecharCollapseAdvancedSearch').hide();
            $('.btnAbrirCollapseAdvancedSearch').show();
        }

        return false;

    }

    function salvar() {
        var arquivo = $("#file").val();

        if (arquivo != ""){
            $('#btn_salvar').prop('disabled', 'disabled');
            $('#btn_salvar').html('Importando...');
            $('#btn_salvar span').addClass('spinner-border spinner-border-sm');
            var formData = new FormData();
            var file = $('#file')[0].files[0];
            var filename = file.name;
            var sizefilename = filename.length;
            var extension = filename.substr(sizefilename-3, 3);

            if (file) {
                formData.append('arquivo', arquivo);
                formData.append('file', $('#file')[0].files[0]);
                formData.append('action', 'importarLista');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{route("blacklist.importList")}}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        var file_name_genion = response.data.file_name_genion;
                        localStorage.setItem('file_name_genion', file_name_genion);

                        if (response.error == 500) {
                            swal(
                                `${response.data.title}`,
                                `${response.data.message}`,
                                'error'
                            );

                            $('#btn_salvar').prop('disabled', false);
                            $('#btn_salvar').html('Importar Arquivo');
                            return false
                        }
                        $('#btn_salvar').prop('disabled', false);
                        $('#btn_salvar').html('Importar Arquivo');
                        let data = response.data;
                        if(data.error){
                            $('#btn_salvar span').removeClass('spinner-border spinner-border-sm');
                            $('#btn_salvar').prop('disabled', false);
                            $('#btn_salvar').html('Importar Arquivo');

                            var textNotification = '<strong>Erro:</strong> Formato de arquivo inválido!';
                            showNotification('alert-danger', textNotification, 'top', 'right', '', '');
                            return false;

                        }
                        else{
                            setTimeout(() => {
                                changepage("{{route('blacklist.index')}}")
                            }, 1000);
                            $('#btn_salvar').prop('disabled', false);
                            $('#btn_salvar').html('Importar Arquivo');
                            $('#btn_salvar').hide();
                            return false;

                        }
                    }
                });
            }


        }else{
            swal("", "É Preciso selecionar um arquivo para importação!", "warning");
        }
        return false;
    }

    function filtrar(check_filtrar){
        var telefone = $('#telefone').val();

        var formData = new FormData();
        formData.append('check_filtrar', check_filtrar);
        formData.append('phone', telefone);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{route("blacklist.advancedSearch")}}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $('#table_blacklist').html(response);
            }
        });
    }

    function downloadFile(id, name_file, date_download, e = $('#list-all')) {
        console.log(e);
        $(e).hide();
        $(`#`+$(e).data('load-id')).show();
        console.log(`#`+$(e).data('load-id'));
        $.ajax({
            type: 'GET',
            url: "{{route('blacklist.downloadFile')}}",
            data: { created_at: date_download, id: id, mailing_file_original: name_file, validate_loged: 'loged' },
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
                a.download = `${response['name']}`;
                a.click();
                $(`#`+$(e).data('load-id')).hide();
                $(e).show();
            }
        });
    }

</script>
