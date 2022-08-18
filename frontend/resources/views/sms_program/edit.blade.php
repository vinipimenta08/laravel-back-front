<style type="text/css">
    .error-validate{
        color: red;
        display: none;
        font-size: 13px;
    }
</style>
    <div class="page-wrapper">
        <!-- CARD ATUALIZA TARIFAÇÃO -->
        <!-- zero configuration -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body">
                        <h2>Atualização de Agendamento</h2>
                        <form action method="POST" id="idNovoRegistro" autocomplete="off">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Centro de Custo:</b>
                                    <div class="form-group ">
                                        <input type="text" name="id_campaign" value="{{$smsProgram['name_campaign']}}" id="id_campaign" class="form-control" disabled>
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
                                                <input type="file" class="custom-file-input required" accept=".csv" value="{{$smsProgram['mailing_file_original']}}" id="file" name="file" disabled />
                                                <label class="custom-file-label" for="file">{{$smsProgram['mailing_file_original']}}</label>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Data Agendamento:</b>
                                    <div class="form-group">
                                        <input type="datetime-local" name="programmed_at" value="{{$smsProgram['programmed_at']}}" id="programmed_at" class="form-control">
                                        <span class="error-validate"><p id="validate-programmed_at"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <a type="button" class="btn btn-light" onclick="changepage('{{route('program.index')}}')">Cancelar</a>
                                    <!-- <button type="submit" id="btn-submit-form-program" class="btn btn-primary waves-effect">Aplicar Alteração</button> -->
                                    <button class="btn btn-primary" type="button" id="btn_update_upload" onclick="update_program('<?php echo $smsProgram['id'] ?>')">
                                        <span role="" aria-hidden="true"></span>
                                        Aplicar Alteração
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM CARD ATUALIZA TARIFAÇÃO -->
    </div>

    <script>

        $(document).ready(function() {
            var data = new Date("<?php echo $smsProgram['programmed_at'] ?>");
            day = data.getDate().toString();
            month = (data.getMonth() + 1);
            hour = data.getHours().toString();
            minutes = data.getMinutes().toString();

            var getDay = "";
            if (day.toString().length == 1) {
                getDay = "0"+day;
            }else{
                getDay = day;
            }

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

            var dataAtual = data.getFullYear()+"-"+getMonth+"-"+getDay+"T"+getHour+":"+getMinutes;

            $('#programmed_at').val(dataAtual);

        })

        function update_program(id){
            var id = id;
            var dataAgendada = $('#programmed_at').val();

            var originalText = $('#btn_update_upload').text();

            var data = new Date();
            day = data.getDate().toString();
            month = (data.getMonth() + 1);
            hour = data.getHours().toString();
            minutes = data.getMinutes().toString();

            var getDay = "";
            if (day.toString().length == 1) {
                getDay = "0"+day;
            }else{
                getDay = day;
            }

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

            var dataAtual = data.getFullYear()+"-"+getMonth+"-"+getDay+"T"+getHour+":"+getMinutes;

            if (dataAgendada < dataAtual) {
                var textNotification = '<strong>Data agendada não pode ser menor que data atual.</strong>';
                showNotification('alert-warning', textNotification, 'top', 'right', '', '');
                return false;
            }

            $('#btn_update_upload').prop('disabled', 'disabled');
            $('#btn_update_upload').html('Alterando...');
            $('#btn_update_upload span').addClass('spinner-border spinner-border-sm');

            var formData = new FormData();
            formData.append('id', id);
            formData.append('programmed_at', dataAgendada);
            $.ajax({
                url: '{{route("program.update")}}',
                type: 'POST',
                data: formData,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
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
                        $('#btn_update_upload').text(originalText);
                        $('#btn_update_upload').prop('disabled', false);
                        return false
                    }
                    if (result['error']) {
                        $('#btn_update_upload').text(originalText);
                        $('#btn_update_upload').prop('disabled', false);
                        Object.keys(result.message).forEach(key => {
                            $(('#validate-'+key)).text(result.message[key].join(' / '));
                        });
                        $('.error-validate').show();
                        return false;
                    }
                    swal(
                        'Sucesso',
                        'Agendamento alterado com sucesso',
                        'success'
                    );
                    $('#btn_update_upload').text(originalText);
                    $('#btn_update_upload').prop('disabled', false);
                },
                error: function (result) {
                    swal(
                        'Falha',
                        'Erro ao alterar agendamento',
                        'warning'
                    );
                    $('#btn_update_upload').text(originalText);
                    $('#btn_update_upload').prop('disabled', false);
                }
            });
            return false;
        }

    </script>
