
<style type="text/css">
    .error-validate{
        color: red;
        display: none;
        font-size: 13px;
    }
</style>
    <div class="page-wrapper">
        <!-- CARD ATUALIZA MENU -->
        <!-- zero configuration -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                
                <div class="card">
                    <div class="card-body">
                        <h2>Atualização de Menu</h2>
                        <form action method="POST" id="idNovoRegistro" autocomplete="off">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Nome:</b>
                                    <div class="form-group">
                                        <input type="text" name="name" id="name" class="form-control" value="{{$menu['name']}}">
                                        <span class="error-validate"><p id="validate-name"></p></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Link:</b>
                                    <div class="form-group">
                                        <input type="text" name="href" id="href" class="form-control" value="{{$menu['href']}}">
                                        <span class="error-validate"><p id="validate-href"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Local do icone:</b>
                                    <div class="form-group">
                                        <select id="locate-icon" name="locate-icon" class="form-control">
                                            <option value="">Selecione</option>
                                            <option value="fontawesome" {{$menu['locate-icon'] == "fontawesome" ? 'selected' : ''}}>Fontawesome</option>
                                            <option value="feather-icon" {{$menu['locate-icon'] == "feather-icon" ? 'selected' : ''}}>Feather-Icon</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Icone:</b>
                                    <div class="form-group">
                                        <input type="text" name="icon" id="icon" class="form-control" value="{{$menu['icon']}}">
                                        <span class="error-validate"><p id="validate-icon"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Tipo:</b>
                                    <div class="form-group">
                                        <input type="text" name="slug" id="slug" class="form-control" value="{{$menu['slug']}}">
                                        <span class="error-validate"><p id="validate-slug"></p></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <b>Sequencia:</b>
                                    <div class="form-group">
                                        <input type="text" name="sequence" id="sequence" class="form-control" value="{{$menu['sequence']}}">
                                        <span class="error-validate"><p id="validate-sequence"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="div-perfil">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <b>Perfil Acesso:</b>
                                    <div class="form-group ">
                                        <select id="roles" name="roles[]" class="form-control" multiple>
                                            @if ($profiles)
                                                @foreach ($profiles as $profile)
                                                    <option value="{{$profile['name']}}" {{in_array($profile['name'], $rules) ? 'selected' : ''}}>{{$profile['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="error-validate"><p id="validate-roles"></p></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <a type="button" class="btn btn-light" onclick="changepage('{{route('menu.index')}}')">Cancelar</a>
                                    <button type="submit" id="btn-submit-form-user" class="btn btn-primary waves-effect">Aplicar Alteração</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM CARD ATUALIZA MENU -->
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
                    url: "{{route('menu.atualizar', ['menu' => $menu['id']])}}",
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
                        if (result['error']) {
                            $('#btn-submit-form-user').text(originalText);
                            $('#btn-submit-form-user').prop('disabled', false);
                            Object.keys(result.message).forEach(key => {
                                $(('#validate-'+key)).text(result.message[key].join(' / '));
                            });
                            $('.error-validate').show();
                            return false;
                        }
                        swal(
                            'Sucesso',
                            'Menu alterado com sucesso',
                            'success'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    },
                    error: function (result) {
                        swal(
                            'Falha',
                            'Erro ao alterar menu',
                            'warning'
                        );
                        $('#btn-submit-form-user').text(originalText);
                        $('#btn-submit-form-user').prop('disabled', false);
                    }
                })

            });
        })
    </script>