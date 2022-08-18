@php
    use Carbon\Carbon;
@endphp
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
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Agendar SMS</li>
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
        <div class="container-fluid" style="padding-bottom: 100px;">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 m-b-15 responsive">
                    <a class="btn btn-primary waves-effect m-b-15 btnAbrirCollapse" role="button" style="margin-bottom: 30px; color: #fff;" onclick="javascript: abrirCollapse()">
                    <i class="fas fa-plus"></i> <span class="icon-name">Novo Agendamento</span>
                    </a>
                    <a class="btn btn-primary waves-effect m-b-15 btnFecharCollapse" role="button" style="margin-bottom: 30px; display: none; color: #fff;" onclick="javascript: fecharCollapse()">
                    <i class="fas fa-plus"></i> <span class="icon-name">Novo Agendamento</span>
                    </a>
                    <div class="collapse" id="collapseExample">
                        <div class="card">
                            <div class="card-body">
                                <div class="header">
                                    <div class="row clearfix">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h2>Adicionar Novo Agendamento</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="body">
                                    <div class="form-f-box">
                                        <div class="box-body">
                                            <form action method="POST" id="idNovoRegistro" autocomplete="off">
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <b>Centro de Custo:</b>
                                                        <div class="form-group ">
                                                            <select id="id_campaign" name="id_campaign" class="form-control selectpicker show-tick bg-white border-0 custom-shadow custom-radius" data-live-search="true" data-icon="glyphicon-star" data-size="7">
                                                                <option value="">Selecione</option>
                                                                @if ($campaigns)
                                                                    @foreach ($campaigns as $campaign)
                                                                        <option value="{{$campaign['id']}}" >{{$campaign['name']}}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option>Nenhum dado encontrado</option>
                                                                @endif
                                                            </select>
                                                            <span class="error-validate"><p id="validate-id_campaign"></p></span>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                <div id="erros_lista">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div style="padding-bottom: 10px;padding-left: 15px">
                                                                <button class="btn btn-primary" type="button" id="btn_salvar" onclick="analisarArquivo()">
                                                                    <span role="" aria-hidden="true"></span>
                                                                    Analisar Arquivo
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="div_agendamento">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <b>Data Agendamento:</b>
                                                            <div class="form-group">
                                                                <input type="datetime-local" name="programmed_at" id="programmed_at" class="form-control">
                                                                <span class="error-validate"><p id="validate-programmed_at"></p></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div style="padding-bottom: 10px;padding-top: 10px;padding-left: 15px">
                                                            <button class="btn btn-secondary" type="button" id="btn_cancelar_upload" onclick="cancelarArquivo()">
                                                                <span role="" aria-hidden="true"></span>
                                                                Cancelar
                                                            </button>
                                                            <!-- <button id="btnNovaTarifação" type="submit" class="btn btn-primary">Salvar</button> -->
                                                            <button class="btn btn-primary" type="button" id="btn_concluir_upload" onclick="nextValidErrors()">
                                                                <span role="" aria-hidden="true"></span>
                                                                Salvar
                                                            </button>
                                                        </div>
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
                                Agendar SMS<br>
                            </h2>
                            <div class="table-responsive-sm  table-responsive-md">
                            <table id="tab-program" class="table table-striped table-bordered no-wrap">
                                <thead>
                                    <tr>
                                        <th>Centro de Custo</th>
                                        <th>Arquivo</th>
                                        <th>Data Agendada</th>
                                        <th>Ativo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($smsProgram as $program)
                                        <tr id="tr-{{$program['id']}}">
                                            <td>{{$program['name_campaign']}}</td>
                                            <td>{{$program['mailing_file_original']}}</td>
                                            <td>{{Carbon::parse($program['programmed_at'])->format('d/m/Y H:i:s')}}</td>
                                            <td>
                                                <div class="demo-checkbox">
                                                    <input type="checkbox" id="program_active{{$program['id']}}" {{ $program['active'] ? 'checked' : ''}} onchange="active_program(this, {{$program['id']}})" name="program_active{{$program['id']}}" class="chk-col-indigo" style="margin-right: 5px;"/>
                                                    <label id="label-{{$program['id']}}" for="program_active{{$program['id']}}">{{ $program['active'] ? 'ativo' : 'inativo'}}</label>
                                                </div>
                                            </td>
                                            <td>
                                                <a class='btn btn-primary' onclick="changepage('{{route('program.edit', ['program'=>$program['id']])}}')">Atualizar</a>
                                                <button type='button' class='btn btn-secondary' style='margin-left: 5px;' id="btn-delele-program-{{$program['id']}}" onclick="delete_program({{$program['id']}})">Deletar</button>
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

    <script src="{{asset('assets/plugins/ui/notifications.js')}}"></script>
    <script src="{{asset('site/js/bootstrap-select.js')}}"></script>

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
        function delete_program(id) {
            $(`#btn-delele-program-${id}`).text('Deletando...');
            $(`#btn-delele-program-${id}`).prop('disabled', true);
            var baseUrlDelete = "{{route('program.destroy', ['program'=>'idProgram'])}}";
            var urlDelete = baseUrlDelete.replace('idProgram', id);
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
                        changepage("{{route('program.index')}}");
                        return false
                    }
                    if (result['error']) {
                        swal(
                            'Falha',
                            'Erro ao excluir agendamento',
                            'warning'
                        );
                    }
                    swal(
                        'Sucesso',
                        'Agendamento excluída com sucesso',
                        'success'
                    );
                    changepage("{{route('program.index')}}");
                },
                error: function (result) {
                    $(`#btn-delele-program-${id}`).text('Deletar');
                    $(`#btn-delele-program-${id}`).prop('disabled', false);
                }
            });
        }
        function active_program(e, id) {
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
                url: "{{route('program.activesmsprogram')}}",
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
            $('.div_agendamento').hide();
            // $('#btn_prev').hide();
            $('#tab-program').DataTable({});
            $('.selectpicker').selectpicker({
                noneSelectedText: 'Nada selecionado',
                noneResultsText: 'Nenhum resultado encontrado {0}'
            });
            $('#file').change(function () {
                var file = $('#file')[0].files[0].name;
                $('.custom-file-label').text(file);
            });

            ///////////////
            var data = new Date();
            dia = data.getDate().toString();
            month = (data.getMonth() + 1);
            hour = data.getHours().toString();
            minutes = data.getMinutes().toString();

            var getMonth = "";
            if (month.toString().length == 1) {
                getMonth = "0"+month;
            }else{
                getMonth = month;
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

            var dataAtual = data.getFullYear()+"-"+getMonth+"-"+data.getDate()+"T"+getHour+":"+getMinutes;

            $('#programmed_at').val(dataAtual);
            ///////////////
        })
        function analisarArquivo(){
            var campanha = $("#id_campaign").val();
            var arquivo = $("#file").val();

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
                                    console.log("1");
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
                                    console.log("2");
                                    // previewLista();
                                    $('#btn_salvar span').removeClass('spinner-border spinner-border-sm');
                                    $('#btn_salvar').prop('disabled', false);
                                    $('#btn_salvar').html('Arquivo Analisado');
                                    $('#btn_salvar').hide();
                                    // $('#btn_prev').show();
                                    $('.div_agendamento').show();

                                    var textNotification = '<strong>Arquivo:</strong> Análise concluída!';
                                    showNotification('alert-success', textNotification, 'top', 'right', '', '');
                                    return false;

                                }
                                else if(data.error){
                                    console.log("3");
                                    $('#btn_salvar span').removeClass('spinner-border spinner-border-sm');
                                    $('#btn_salvar').prop('disabled', false);
                                    $('#btn_salvar').html('Analisar Arquivo');

                                    var textNotification = '<strong>Erro:</strong> Formato de arquivo inválido!';
                                    showNotification('alert-danger', textNotification, 'top', 'right', '', '');
                                    return false;

                                }
                                else{
                                    console.log("4");
                                    $('#btn_salvar').prop('disabled', false);
                                    $('#btn_salvar').html('Analisar Arquivo');
                                    $('#btn_salvar').hide();
                                    $('.div_agendamento').show();
                                    // $('#btn_prev').show();
                                    // previewLista();
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
                                    // wizard.smartWizard('next');

                                    var textNotification = '<strong>Arquivo:</strong> Erros encontrados!';
                                    showNotification('alert-warning', textNotification, 'top', 'right', '', '');
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
        function cancelarArquivo(){

            var campanha = $("#id_campaign").val();
            var arquivo = $("#file").val();

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
                            $(".custom-file-label").html('Escolher arquivo');
                            $('#file').val("");
                            $('#erros').hide();
                            $('#valida_erro').val("");
                            $('.div_agendamento').hide();
                            $('#btn_salvar').html('Analisar Arquivo');
                            $('#btn_salvar').show()
                            localStorage.setItem('file_name_genion', "");
                        }
                    });
                }

            }
            else{
                swal("Arquivo", "Não encontrado.", "warning");
            }

        }
        function subirLista(){

            var id_campanha = $("#id_campaign").val();
            var arquivo = $("#file").val();
            var check_envio_sms = false;
            var check_agendamento_sms = true;
            var file_name_genion = "";
            if (localStorage.getItem('file_name_genion')) {
                var file_name_genion = localStorage.getItem('file_name_genion');
            }
            var dataAgendada = $('#programmed_at').val();
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

                var dataAtual = data.getFullYear()+"-"+getMonth+"-"+data.getDate()+"T"+getHour+":"+getMinutes;

                if (dataAgendada < dataAtual) {
                    var textNotification = '<strong>Data agendada não pode ser menor que data atual.</strong>';
                    showNotification('alert-warning', textNotification, 'top', 'right', '', '');
                    return false;
                }
            }

            $('#btn_concluir_upload').prop('disabled', 'disabled');
            $('#btn_concluir_upload').html('Salvando...');
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
                formData.append('check_envio_sms', false);
                formData.append('check_agendamento_sms', true);
                formData.append('date_schedule', dataAgendada);
                formData.append('action', 'subirLista');
                $.ajax({
                    url: '{{route("uploadshipping.uploadlist")}}',
                    type: 'POST',
                    data: formData,
                    processData: false, // tell jQuery not to process the data
                    contentType: false, // tell jQuery not to set contentType
                    success: function (response) {
                        console.log(response);
                        localStorage.setItem('file_name_genion', "");
                        if(response.error == 1){
                            $('#btn_concluir_upload span').removeClass('spinner-border spinner-border-sm');
                            $('#btn_concluir_upload').prop('disabled', false);
                            $('#btn_concluir_upload').html('Salvar');

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
                            $('#btn_concluir_upload').html('Salvar');
                            return false
                        }

                        swal(
                            'Sucesso',
                            'Agendamento cadastrada com sucesso',
                            'success'
                        );

                        setTimeout(() => {
                        changepage("{{route('program.index')}}")
                    }, 2000);
                    }
                });
            }
        }
        function nextValidErrors(){
            var erro = $("#valida_erro").val();

            if (typeof erro === 'undefined' || erro == "") {
                subirLista();
            }else{
                swal({
                    title: "Confirmar agendamento?",
                    // text: "Lista possui "+ erro +((erro <= 1)? " dado inválido " : " dados inválidos "),
                    text: "Ao continuar com o agendamento, os dados inválidos serão descartados e permanecerão para envio somente os registros válidos.",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    confirmButtonText: "Sim",
                    cancelButtonText: "Não",
                }, function (isConfirm) {
                    if (isConfirm) {
                        subirLista();
                    } else {
                        swal.close();
                        return false;
                    }
                });
            }

        }
    </script>
<i class="fa-solid fa-calendar-days"></i>
