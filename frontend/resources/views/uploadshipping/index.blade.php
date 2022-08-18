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
                            <li class="breadcrumb-item text-muted active" aria-current="page">Carrega Envio</li>
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
                        <div class="row">
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                <h2>
                                    Carregamento de Arquivo
                                </h2>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <a href="{{route('uploadshipping.downloadlayout')}}" style="float: right; margin: 10px;"><i class="fas fa-download"></i> Layout</a>
                            </div>
                        </div>
                        <body class='snippet-body'>
                            <div class="body">
                                <div id="smartwizard">
                                    <ul>
                                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><a class="step_first" href="#step-1" style="margin-left: -20px;"><strong>Centro de Custo</strong><br /><small>Adicionar</small></a></li>
                                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><a class="step_first" href="#step-2" style="margin-left: -20px;"><strong>Importação Lista</strong><br /><small>Upload</small></a></li>
                                        <li id="li_analise" class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><a class="step_first" href="#step-3" style="margin-left: -20px;"><strong>Análise Lista</strong><br /><small>Resultado</small></a></li>
                                        <li id="li_envio" class="col-lg-3 col-md-2 col-sm-3 col-xs-3"><a class="step_last" href="#step-4" style="margin-left: -20px;"><strong>Envio</strong><br /><small>Disparo</small></a></li>
                                    </ul>
                                    <div class="mt-4">
                                        <div id="step-1">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <b>Centro de Custo:</b>
                                                    <div class="form-group ">
                                                        <select id="campanha" name="campanha" class="form-control selectpicker show-tick bg-white border-0 custom-shadow custom-radius" data-live-search="true" data-icon="glyphicon-star" data-size="7">
                                                            <option value="">Selecione</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign['id']}}">{{$campaign['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button class="btn btn-primary" type="button" id="btn_next" onclick="next()" style="float: right;">
                                                        <span role="" aria-hidden="true"></span>
                                                        Próximo
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="step-2">
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
                                                        <button class="btn btn-secondary" type="button" id="btn_prev" onclick="previous()">
                                                            <span role="" aria-hidden="true"></span>
                                                            Voltar
                                                        </button>
                                                        <button class="btn btn-primary" type="button" id="btn_salvar" onclick="salvar()">
                                                            <span role="" aria-hidden="true"></span>
                                                            Analisar Arquivo
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="step-3" class="">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div id="erros_lista">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div id="employee_table">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div id="div_concluir" style="display: none;padding-bottom: 30px;padding-top: 10px;text-align: right;">
                                                        <button class="btn btn-secondary" type="button" id="btn_cancelar_upload" onclick="cancelarProcesso()">
                                                            <span role="" aria-hidden="true"></span>
                                                            Cancelar
                                                        </button>
                                                        <button class="btn btn-primary" type="button" id="btn_next" onclick="nextValidErrors()">
                                                            <span role="" aria-hidden="true"></span>
                                                            Próximo
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="step-4" class="">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                            <b>Envio SMS:</b>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" id="check_envio_sms" onchange="inputEnvioSms()">
                                                                <!-- <label class="form-check-label">Caso queira enviar o SMS clique aqui.</label> -->
                                                                <label class="form-check-label" for="check_envio_sms">Marque para confirmar o envio do sms.</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                            <b>Agendamento SMS:</b>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" id="check_agendamento_sms" onchange="inputAgendamento()">
                                                                <label class="form-check-label" for="check_agendamento_sms">Marque para agendar o envio do sms.</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                            <b>WhatsApp:</b>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" id="check_verifyWhats" >
                                                                <label class="form-check-label" for="check_verifyWhats">Marque para verificar WhatsApp no envio do sms.</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                            <div id="div_agendamento">
                                                            <b>Data Agendamento:</b>
                                                                <div class="form-group">
                                                                    <input id="data_agendada" name="program_date" type="datetime-local" class="form-control bg-white border-0 custom-shadow custom-radius" >
                                                                    <span class="error-validate"><p id="validate-program_date"></p></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                        </div>
                                                    </div>
                                                    <div style="text-align: right;margin-top: 15px;">
                                                        <button class="btn btn-secondary" type="button" id="btn_prev" onclick="previous()">
                                                                <span role="" aria-hidden="true"></span>
                                                                Voltar
                                                            </button>
                                                        <button class="btn btn-primary" type="button" id="btn_concluir_upload" onclick="subirLista()">
                                                            <span role="" aria-hidden="true"></span>
                                                            Finalizar Processo
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </body>

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
<script src="{{asset('site/js/bootstrap-select.js')}}"></script>
<link rel="stylesheet" href="{{asset('assets/extra-libs/smartWizard/smart_wizard.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/extra-libs/smartWizard/smartWizard.min.css')}}">

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker({
            noneSelectedText: 'Nada selecionado',
            noneResultsText: 'Nenhum resultado encontrado {0}'
        });
        $('#file').change(function () {
            var file = $('#file')[0].files[0].name;
            $('.custom-file-label').text(file);
        });
        $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'arrows',
            autoAdjustHeight:true,
            transitionEffect:'fade',
            showStepURLhash: false,
            keyboardSettings: {
                keyNavigation: false, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
                keyLeft: [], // Left key code
                keyRight: [] // Right key code
            },
        });

        $('.sw-btn-prev').hide();
        $('.sw-btn-next').hide();
        $('#div_agendamento').hide();

    });

    function next(){
        var campanha = $("#campanha").val();

        // /////////////////
        // $('#smartwizard').smartWizard('next');
        // return false;
        // ////////////////

        if (campanha !== "" && campanha !== undefined) {
            $('#smartwizard').smartWizard('next');
        }else{
            swal("", "É necessário selecionar um centro de custo.", "warning");
            return false;
        }

    }

    function nextValidErrors(){
        var erro = $("#valida_erro").val();

        if (typeof erro === 'undefined' || erro == "") {
            $('#smartwizard').smartWizard('next');
        }else{
            swal({
                title: "Confirmar importação?",
                // text: "Lista possui "+ erro +((erro <= 1)? " dado inválido " : " dados inválidos "),
                text: "Ao continuar com a importação, os dados inválidos serão descartados e permanecerão para envio somente os registros válidos.",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: true,
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
            }, function (isConfirm) {
                if (isConfirm) {
                    $('#smartwizard').smartWizard('next');
                } else {
                    swal.close();
                    return false;
                }
            });
        }

    }

    function previous(){
        $('#smartwizard').smartWizard('prev');
    }

    function salvar() {

        var campanha = $("#campanha").val();
        var arquivo = $("#file").val();
        var wizard =  $('#smartwizard').smartWizard();

        // /////////////////
        // wizard.smartWizard('next');
        // $('#div_concluir').show();
        // return false;
        // ////////////////

        if (arquivo != ""){
            if (campanha == '') {
                swal("", "Preencha campanha e descrição para continuar.", "warning");
            }else{
                $('#btn_salvar').prop('disabled', 'disabled');
                $('#btn_salvar').html('Analisando...');
                $('#btn_salvar span').addClass('spinner-border spinner-border-sm');
                var formData = new FormData();
                var file = $('#file')[0].files[0];
                var filename = file.name;
                var sizefilename = filename.length;
                var extension = filename.substr(sizefilename-3, 3);

                if (file) {
                    formData.append('arquivo', arquivo);
                    formData.append('file', $('#file')[0].files[0]);
                    formData.append('campanha', campanha);
                    formData.append('action', 'analisarLista');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '{{route("uploadshipping.validateupload")}}',
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
                                $('#loader-dash').hide()
                                $('#reportrange').css('pointer-events', 'auto');
                                $('#btn_salvar').prop('disabled', false);
                                $('#btn_salvar').html('Analisar Arquivo');
                                return false
                            }
                            $('#btn_salvar').prop('disabled', false);
                            $('#btn_salvar').html('Analisar Arquivo');
                            let data = response.data;
                            if(data.errors_validate == 0){
                                previewLista();
                                $('#btn_salvar span').removeClass('spinner-border spinner-border-sm');
                                $('#btn_salvar').prop('disabled', false);
                                $('#btn_salvar').html('Analisar Arquivo');
                                $('#btn_salvar').hide();
                                wizard.smartWizard('next');
                                return false;

                            }
                            else if(data.error){
                                $('#btn_salvar span').removeClass('spinner-border spinner-border-sm');
                                $('#btn_salvar').prop('disabled', false);
                                $('#btn_salvar').html('Analisar Arquivo');

                                var textNotification = '<strong>Erro:</strong> Formato de arquivo inválido!';
                                showNotification('alert-danger', textNotification, 'top', 'right', '', '');
                                return false;

                            }
                            else{
                                $('#btn_salvar').prop('disabled', false);
                                $('#btn_salvar').html('Analisar Arquivo');
                                $('#btn_salvar').hide();
                                previewLista();
                                let invalido_telefone = 0;
                                let invalido_data_inicio = 0;
                                let invalido_mensagemSMS = 0;
                                let invalido_titulo = 0;
                                let invalido_descricao = 0;
                                let invalido_localizacao = 0;
                                let invalido_identificador = 0;
                                let invalido_coringa1 = 0;
                                let invalido_coringa2 = 0;

                                data.invalids.forEach(element => {
                                    element.fields.forEach(element => {
                                        if (element.field == 'telefone') {
                                            invalido_telefone++
                                        }
                                        if (element.field == 'data_inicio') {
                                            invalido_data_inicio++
                                        }
                                        if (element.field == 'mensagem_sms') {
                                            invalido_mensagemSMS++
                                        }
                                        if (element.field == 'titulo_evento') {
                                            invalido_titulo++
                                        }
                                        if (element.field == 'descricao') {
                                            invalido_descricao++
                                        }
                                        if (element.field == 'localizacao') {
                                            invalido_localizacao++
                                        }
                                        if (element.field == 'identificador') {
                                            invalido_identificador++
                                        }
                                        if (element.field == 'coringa_1') {
                                            invalido_coringa1++
                                        }
                                        if (element.field == 'coringa_2') {
                                            invalido_coringa2++
                                        }
                                    });
                                });
                                let html = '';
                                html += `<input type='text' name='valida_erro' value="${data.errors_validate}" id='valida_erro' class='form-control' style='display: none;'>`;
                                html += "<a tabindex='0' id='erros' type='button' class='btn btn-danger' data-container='body' data-toggle='popover' data-placement='right' ";
                                html += "title='LISTA POSSUI ";
                                html += data.errors_validate + ((data.errors_validate <= 1)? " DADO INVÁLIDO' " : " DADOS INVÁLIDOS' ");
                                html += " data-content='";
                                html += ((invalido_telefone > 0)? "Telefone: " +  invalido_telefone + " <br> " : "");
                                html += ((invalido_data_inicio > 0)? "Data Início: " +  invalido_data_inicio + " <br> " : "");
                                html += ((invalido_mensagemSMS > 0)? "Mensagem SMS: " +  invalido_mensagemSMS + " (Tamanho máximo 130) <br> " : "");
                                html += ((invalido_titulo > 0)? "Titulo: " +  invalido_titulo + " (Tamanho máximo 50) <br> " : "");
                                html += ((invalido_descricao > 0)? "Descrição: " +  invalido_descricao + " (Tamanho máximo 300) <br> " : "");
                                html += ((invalido_localizacao > 0)? "Localização: " +  invalido_localizacao + " (Tamanho máximo 100) <br> " : "");
                                html += ((invalido_identificador > 0)? "Identificador: " +  invalido_identificador + " (Tamanho máximo 50) <br> " : "");
                                html += ((invalido_coringa1 > 0)? "Coringa_1: " +  invalido_coringa1 + " <br> " : "");
                                html += ((invalido_coringa2 > 0)? "Coringa_2: " +  invalido_coringa2 + " <br> " : "");
                                html += "' style='margin-bottom: 20px;'>";
                                html += "Visualizar Erros <i class='fas fa-plus-circle'></i>";
                                html += "</a>";
                                $('#erros_lista').html(html);
                                $('#erros').popover({container: 'body', html: true, trigger: 'hover'});
                                // swal("Erros encontrados!", dados["mensagemInvalido"], "warning");
                                wizard.smartWizard('next');
                                return false;

                            }
                        }
                    });
                }
            }

        }else{
            swal("", "É Preciso selecionar um arquivo para importação!", "warning");
        }
        return false;
    }

    function previewLista(){

        $('#btn_salvar').prop('disabled', false);
        $('#btn_salvar').html('Analisar Arquivo');
        var formData = new FormData();
        formData.append('arquivo', $("#file").val());
        formData.append('file', $('#file')[0].files[0]);

        $.ajax({
            url: '{{route("uploadshipping.analyzelist")}}',
            type: 'POST',
            data: formData,
            processData: false, // tell jQuery not to process the data
            contentType: false, // tell jQuery not to set contentType
            success: function (response) {
                $('#employee_table').html(response);
                $('#div_concluir').show();
            }
        });
    }

    function subirLista(){

        var id_campanha = $("#campanha").val();
        var arquivo = $("#file").val();
        var check_envio_sms = $('#check_envio_sms').is(":checked");
        var check_agendamento_sms = $('#check_agendamento_sms').is(":checked");
        var check_verifyWhats = $('#check_verifyWhats').is(":checked");
        var file_name_genion = "";
        if (localStorage.getItem('file_name_genion')) {
            var file_name_genion = localStorage.getItem('file_name_genion');
        }
        var dataAgendada = $('#data_agendada').val();
        var data = new Date();
        dia = data.getDate().toString();
        month = (data.getMonth() + 1);
        hour = data.getHours().toString();
        minutes = data.getMinutes().toString();

        if (check_agendamento_sms) {
            var getMonth = "";
            if (month.toString().length == 1) {
                getMonth = "0"+month;
            }else{
                getMonth = month;
            }

            var getDay = "";
            if (dia.length == 1) {
                getDay = "0"+dia;
            }else{
                getDay = dia;
            }

            var getHour = "";
            if (hour.length == 1) {
                getHour = "0"+hour;
            }else{
                getHour = hour;
            }

            var getMinutes = "";
            if (minutes.length == 1) {
                getMinutes = "0"+minutes;
            }else{
                getMinutes = minutes;
            }

            var dataAtual = data.getFullYear()+"-"+getMonth+"-"+getDay+"T"+getHour+":"+getMinutes;

            if (dataAgendada < dataAtual) {
                var textNotification = '<strong>Data agendada não pode ser menor que data atual.</strong>';
                showNotification('alert-warning', textNotification, 'top', 'right', '', '');
                return false;
            }
        }

        $('#btn_concluir_upload').prop('disabled', 'disabled');
        $('#btn_concluir_upload').html('Finalizando...');
        $('#btn_concluir_upload span').addClass('spinner-border spinner-border-sm');

        var formData = new FormData();
        var file = $('#file')[0].files[0];
        var filename = file.name;
        var sizefilename = filename.length;
        var extension = filename.substr(sizefilename-3, 3);

        if (file) {
            formData.append('arquivo', arquivo);
            formData.append('file', $('#file')[0].files[0]);
            formData.append('id_campanha', id_campanha);
            formData.append('file_name_genion', file_name_genion);
            formData.append('check_envio_sms', check_envio_sms);
            formData.append('check_agendamento_sms', check_agendamento_sms);
            formData.append('check_verifyWhats', check_verifyWhats);
            formData.append('date_schedule', dataAgendada);
            formData.append('action', 'subirLista');
            $.ajax({
                url: '{{route("uploadshipping.uploadlist")}}',
                type: 'POST',
                data: formData,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                success: function (response) {
                    localStorage.setItem('file_name_genion', "");
                    if(response.error == 1){
                        $('#btn_concluir_upload span').removeClass('spinner-border spinner-border-sm');
                        $('#btn_concluir_upload').prop('disabled', false);
                        $('#btn_concluir_upload').html('Finalizar Processo');

                        swal("Erros encontrados!", dados["mensagemInvalido"], "warning");
                        return false;
                    }
                    if (response.error == 500) {
                        swal(
                            `${response.data.title}`,
                            `${response.data.message}`,
                            'error'
                        );
                        $('#btn_concluir_upload span').removeClass('spinner-border spinner-border-sm');
                        $('#btn_concluir_upload').prop('disabled', false);
                        $('#btn_concluir_upload').html('Finalizar Processo');
                        return false
                    }

                    if (check_agendamento_sms) {
                        var textNotification = '<strong>Agendamento realizado com sucesso</strong>';
                        showNotification('alert-success', textNotification, 'top', 'right', '', '');
                    }else{
                        if (check_envio_sms) {
                            var textNotification = '<strong>Importação está sendo feito em segundo plano</strong>';
                            showNotification('alert-success', textNotification, 'top', 'right', '', '');
                        }else{
                            var textNotification = '<strong>Importação Feita</strong>';
                            showNotification('alert-success', textNotification, 'top', 'right', '', '');
                        }
                    }

                    swal.close();
                    setTimeout(() => {
                        changepage("{{route('uploadshipping.index')}}")
                    }, 1000);
                }
            });
        }
    }

    function cancelarProcesso(){

        var campanha = $("#campanha").val();
        var arquivo = $("#file").val();
        var wizard =  $('#smartwizard').smartWizard();

        // /////////////////
        // wizard.smartWizard('prev');
        // return false;
        // ////////////////

        var file_name_genion = "";
        if (localStorage.getItem('file_name_genion')) {
            var file_name_genion = localStorage.getItem('file_name_genion');
        }

        if (arquivo != ""){

            $('#btn_cancelar_upload').prop('disabled', 'disabled');
            $('#btn_cancelar_upload').html('Cancelando...');
            $('#btn_cancelar_upload span').addClass('spinner-border spinner-border-sm');

            var formData = new FormData();
            var file = $('#file')[0].files[0];
            var filename = file.name;
            var sizefilename = filename.length;
            var extension = filename.substr(sizefilename-3, 3);

            if (file) {
                formData.append('arquivo', arquivo);
                formData.append('file', $('#file')[0].files[0]);
                formData.append('campanha', campanha);
                formData.append('file_name_genion', file_name_genion);
                formData.append('action', 'analisarLista');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{route("uploadshipping.deletelist")}}',
                    type: 'POST',
                    data: formData,
                    processData: false, // tell jQuery not to process the data
                    contentType: false, // tell jQuery not to set contentType
                    success: function (response) {
                        console.log(response);
                        $('#btn_cancelar_upload span').removeClass('spinner-border spinner-border-sm');
                        $('#btn_cancelar_upload').prop('disabled', false);
                        $('#btn_cancelar_upload').html('Cancelar');
                        $('#employee_table').html("");
                        $('#div_concluir').hide();
                        $('#btn_salvar').show();
                        $(".custom-file-label").html('Escolher arquivo');
                        $('#file').val("");
                        $('#erros').hide();
                        $('#valida_erro').val("");
                        wizard.smartWizard('prev');
                        $('#li_analise').removeClass('done');
                        $('#li_envio').removeClass('done');
                        localStorage.setItem('file_name_genion', "");
                    }
                });
            }

        }
        else{
            swal("Arquivo", "Não encontrado.", "warning");
        }

    }

    function inputAgendamento(){
        var data = $('#data_agendada').val()
        var check_agendamento_sms = $('#check_agendamento_sms').is(":checked");
        var data = new Date();
        dia = data.getDate().toString();
        month = (data.getMonth() + 1);
        hour = data.getHours().toString();
        minutes = data.getMinutes().toString();

        if (check_agendamento_sms) {
            var getMonth = "";
            if (month.toString().length == 1) {
                getMonth = "0"+month;
            }else{
                getMonth = month;
            }

            var getDay = "";
            if (dia.length == 1) {
                getDay = "0"+dia;
            }else{
                getDay = dia;
            }

            var getHour = "";
            if (hour.length == 1) {
                getHour = "0"+hour;
            }else{
                getHour = hour;
            }

            var getMinutes = "";
            if (minutes.length == 1) {
                getMinutes = "0"+minutes;
            }else{
                getMinutes = minutes;
            }

            var dataAtual = data.getFullYear()+"-"+getMonth+"-"+getDay+"T"+getHour+":"+getMinutes;

            $('#data_agendada').val(dataAtual);
            $('#div_agendamento').fadeIn();
            $('#check_envio_sms').prop("disabled", true);
        }else{
            $('#div_agendamento').fadeOut();
            $('#check_envio_sms').prop("disabled", false);
        }

    }

    function inputEnvioSms(){

        var check_envio_sms = $('#check_envio_sms').is(":checked");

        if (check_envio_sms) {
            $('#check_agendamento_sms').prop("disabled", true);
        }else{
            $('#check_agendamento_sms').prop("disabled", false);
        }

    }
</script>
