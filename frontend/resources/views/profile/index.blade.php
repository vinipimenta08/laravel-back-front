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
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Perfil</li>
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
        
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action method="POST" id="formProfile" autocomplete="off">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label>Nome:</label>
                                                    <input type="text" id="nome" class="form-control" value="{{ $user['name'] }}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label>Email:</label>
                                                    <input type="text" id="email" class="form-control" value="{{$user['email']}}" disabled>
                                                     <input type="text" name="email" class="form-control" value="{{$user['email']}}" style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <b>Senha:</b>
                                                <div class="form-group">
                                                    <input type="password" name="password" id="password" class="form-control">
                                                    <span class="error-validate"><p id="validate-password"></p></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <b>Confirmar Senha:</b>
                                                <div class="form-group">
                                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                                                    <span class="error-validate"><p id="validate-confirm_password"></p></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <button type="submit" id="btn_salvar" class="btn btn-info">Salvar</button>
                                            <a type="button" class="btn btn-light" href="{{route('home.index')}}">Cancelar</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
        </div>
    </div>

<script src="{{asset('assets/plugins/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{asset('assets/plugins/ui/notifications.js')}}"></script>

<script type="text/javascript">
        
    $(document).ready(function() {
        $('#formProfile').submit(function(e){
            e.preventDefault();

            var password = $('#password').val();
            var confirm_password = $('#confirm_password').val();

            if (password == "" && confirm_password == "") {
                let textNotification = "<b>Aviso:</b> Campo senha vazio.";
                showNotification('alert-warning', textNotification, 'top', 'right', '', '');
                return false;
            }

            var originalText = $('#btn_salvar').text();

            $('#btn_salvar').text('Salvando...');
            $('#btn_salvar').prop('disabled', true);
            var formdata = new FormData($("form[id='formProfile']")[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: "{{route('profile.atualizar', ['usuario' => $user['id']])}}",
                data: formdata,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('.error-validate').children().each(function(i, e) {
                        $(e).text('');
                    });
                    if (result.error) {
                        Object.entries(result.message).forEach(element => {
                            $((`#validate-${element[0]}`)).text(element[1].join(' / '));
                        });
                        $('.error-validate').show();
                        $('#btn_salvar').text(originalText);
                        $('#btn_salvar').prop('disabled', false);

                        return false;
                    }
                    swal(
                        'Sucesso',
                        'Perfil alterado com sucesso',
                        'success'
                    );
                    $('#btn_salvar').text(originalText);
                    $('#btn_salvar').prop('disabled', false);
                    $('#password').val("");
                    $('#confirm_password').val("");
                },
                error: function (result) {
                    swal(
                        'Falha',
                        'Erro ao alterar perfil',
                        'warning'
                    );
                    $('#btn_salvar').text(originalText);
                    $('#btn_salvar').prop('disabled', false);
                }
            })

        });
    })

</script>