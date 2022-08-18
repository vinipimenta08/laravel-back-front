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
                        <h2>Atualização de Tarifação</h2>
                        <form action method="POST" id="idNovoRegistro" autocomplete="off">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Cliente:</b>
                                    <div class="form-group ">
                                        <select id="id_client" name="id_client" class="form-control">
                                            <option value="">Selecione</option>
                                            @if ($clients)
                                                @foreach ($clients as $client)
                                                    <option value="{{$client['id']}}" {{$custome['id_client'] == $client['id'] ? 'selected' : ''}} >{{$client['name']}}</option>
                                                @endforeach
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
                                        <input type="number" step=".01" name="value" value="{{$custome['value']}}" id="value" class="form-control">
                                        <span class="error-validate"><p id="validate-value"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <a type="button" class="btn btn-light" onclick="changepage('{{route('customer.index')}}')">Cancelar</a>
                                    <button type="submit" id="btn-submit-form-customer" class="btn btn-primary waves-effect">Aplicar Alteração</button>
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
            $('#idNovoRegistro').submit(function(e){
                e.preventDefault();
                var originalText = $('#btn-submit-form-customer').text();

                $('#btn-submit-form-customer').text('Alterando...');
                $('#btn-submit-form-customer').prop('disabled', true);
                var formdata = new FormData($("form[id='idNovoRegistro']")[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "{{route('customer.atualizar', ['customer' => $custome['id']])}}",
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
                            $('#btn-submit-form-customer').text(originalText);
                            $('#btn-submit-form-customer').prop('disabled', false);
                            return false
                        }
                        if (result['error'] == 100) {
                            Object.keys(result.message).forEach(key => {
                                $(('#validate-'+key)).text(result.message[key].join(' / '));
                            });
                            $('.error-validate').show();
                            $('#btn-submit-form-customer').text(originalText);
                            $('#btn-submit-form-customer').prop('disabled', false);
                            return false;
                        }
                        swal(
                            'Sucesso',
                            'Tarifação alterada com sucesso',
                            'success'
                        );
                        $('#btn-submit-form-customer').text(originalText);
                        $('#btn-submit-form-customer').prop('disabled', false);
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao alterar tarifação',
                            'warning'
                        );
                        $('#btn-submit-form-customer').text(originalText);
                        $('#btn-submit-form-customer').prop('disabled', false);
                    }
                })

            });
        })
    </script>
