<style type="text/css">
    .error-validate{
        color: red;
        display: none;
        font-size: 13px;
    }
</style>
    <div class="page-wrapper">
        <!-- CARD ATUALIZA CENTRO DE CUSTO -->
        <!-- zero configuration -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                
                <div class="card">
                    <div class="card-body">
                        <h2>Atualização de Centro de Custo</h2>
                        <form action method="POST" id="idNovoRegistro" autocomplete="off">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Nome:</b>
                                    <div class="form-group">
                                        <input type="text" name="name" id="name" class="form-control" value="{{$campaign[0]['name']}}">
                                        <span class="error-validate"><p id="validate-name"></p></span>
                                    </div>
                                    @if (count($clients))
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <b>Cliente:</b>
                                                <div class="form-group">
                                                    <select id="id_client" name="id_client" class="form-control">
                                                        @if ($clients)
                                                            @foreach ($clients as $client)
                                                                <option value="{{$client['id']}}" {{$campaign[0]['id_client'] == $client['id'] ? 'selected' : ''}} >{{$client['name']}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>                            
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <a type="button" class="btn btn-light" onclick="changepage('{{route('centrodecusto.index')}}')">Cancelar</a>
                                    <button type="submit" id="btn-submit-form-user" class="btn btn-primary waves-effect">Aplicar Alteração</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM CARD ATUALIZA CENTRO DE CUSTO -->
    </div>

    <script>
        
        $(document).ready(function() {
            $('#idNovoRegistro').submit(function(e){
                e.preventDefault();
                var originalText = $('#btn-submit-form-user').text();

                $('#btn-submit-form-user').text('Alterando...');
                $('#btn-submit-form-user').prop('disabled', true);
                var formdata = new FormData($("form[id='idNovoRegistro']")[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "{{route('centrodecusto.atualizar', ['centrodecusto' => $campaign[0]['id']])}}",
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
                            $('#btn-submit-form-user').text(originalText);
                            $('#btn-submit-form-user').prop('disabled', false);
                            return false
                        }
                        if (result.status == 'error') {
                            Object.keys(result.message).forEach(key => {
                                $(('#validate-'+key)).text(result.message[key].join(' / '));
                            });
                            $('.error-validate').show();
                            $('#btn-submit-form-user').text(originalText);
                            $('#btn-submit-form-user').prop('disabled', false);
                            return false;
                        }
                        swal(
                            'Sucesso',
                            'Centro de Custo alterado com sucesso',
                            'success'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao alterar centro de custo',
                            'warning'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    }
                })

            });
        })
    </script>