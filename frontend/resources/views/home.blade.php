@php
use App\Http\Controllers\HomeController;
    $application = HomeController::startapplication();
    $user = $application['data']['user'];
@endphp
<style type="text/css">
    .ct-bar{fill:none;stroke-width:15px;stroke: #5f76e8;}

    body {
        background: transparent
    }

    #hovered-region,
    #clicked-region {
        background: #fdc16a;
        border: 1px solid #e5eabd;
        color: #e4b045;
        display: inline-block;
        font: normal 10pt 'Ubuntu Mono';
        margin-right: 3px;
        padding: 4px 8px;
        text-align: center;
        width: 170px
    }

    #hovered-region span,
    #clicked-region span {
        color: #e4e770;
        font-weight: bold
    }

    #brazil-map {
        margin: 10px auto;
        height: 190px;
        width: 400px
    }

    @media screen and (max-width: 1395px) and (min-width: 991px) {
        #brazil-map {
            margin-left: -80px;
        }
    }

    @media screen and (max-width: 420px) {
        #brazil-map {
            margin-left: -80px;
        }
    }
    .loader {
        margin-right: 1rem;
        border: 6px solid #f3f3f3;
        border-top: 6px solid #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 2s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
{{-- DATE RANGE PICKER CSS --}}
<link rel="stylesheet" href="{{ asset('assets/libs/daterangepicker/daterangepicker.css') }}">


{{-- INICIO PAGINA --}}
<div class="page-wrapper">

    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">
                    @php
                        if(date('H') >= 0 && date('H') <= 11)
                            echo 'Bom dia';
                        else if (date('H') >= 12 && date('H') <= 18)
                            echo'Boa tarde';
                        else
                            echo'Boa noite';
                    @endphp
                    {{ $user['name'] ?? '' }}!
                </h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-5 align-self-center">
                <div class="customize-input float-right" style="flex-direction: row;display: flex;">
                    <div id="loader-dash" class="loader" style="display: none;"></div>
                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="dashboard_home" class="container-fluid"></div>

</div>
{{-- FIM PAGINA --}}


<style type="text/css">
    .ct-bar{fill:none;stroke-width:15px;stroke: #5f76e8;}

    body {
        background: transparent
    }

    #hovered-region,
    #clicked-region {
        background: #fdc16a;
        border: 1px solid #e5eabd;
        color: #e4b045;
        display: inline-block;
        font: normal 10pt 'Ubuntu Mono';
        margin-right: 3px;
        padding: 4px 8px;
        text-align: center;
        width: 170px
    }

    #hovered-region span,
    #clicked-region span {
        color: #e4e770;
        font-weight: bold
    }

    #brazil-map {
        margin: 10px auto;
        height: 190px;
        width: 400px
    }

    @media screen and (max-width: 1395px) and (min-width: 991px) {
        #brazil-map {
            margin-left: -80px;
        }
    }

    @media screen and (max-width: 420px) {
        #brazil-map {
            margin-left: -80px;
        }
    }
</style>

{{-- DATE RANGE PICKER CSS --}}
<link rel="stylesheet" href="{{ asset('assets/libs/daterangepicker/daterangepicker.css') }}">

{{-- DATE RANGE PICKER JS --}}
<script src="{{ asset('assets/libs/daterangepicker/pt-br.js') }}"></script>
<script src="{{ asset('assets/libs/daterangepicker/daterangepicker.js') }}"></script>
{{-- NOTIFICATION --}}
<script src="{{asset('assets/plugins/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{asset('assets/plugins/ui/notifications.js')}}"></script>

<script type="text/javascript">

    $(function() {

        var start = moment();
        var end = moment();

        function cb(start, end) {
            if(start.format('DD/MM/YYYY') == end.format('DD/MM/YYYY')){
                $('#reportrange span').html(start.format('DD/MM/YYYY'));
            }else{
                $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            }

            var date_init = start.format("YYYY-MM-DD");
            var date_end = end.format("YYYY-MM-DD");

            let date1 = new Date(date_init);
            let date2 = new Date(date_end);
            let diffTime = Math.abs(date2 - date1);
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if(diffDays > 30){
                let textNotification = "<b>Aviso:</b> Data não pode ultrapassar o limite de 30 dias.";
                showNotification('alert-warning', textNotification, 'top', 'right', '', '');
                return false;
            }

            var formData = new FormData();

            formData.append('date_init', date_init);
            formData.append('date_end', date_end);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#loader-dash').show()
            $('#reportrange').css('pointer-events', 'none');
            $.ajax({
                url: '{{route("home.dashData")}}',
                type: 'POST',
                data: formData,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                success: function (response) {
                    if (response.error == 500) {
                        swal(
                            `${response.data.title}`,
                            `${response.data.message}`,
                            'error'
                        );
                        $('#loader-dash').hide()
                        $('#reportrange').css('pointer-events', 'auto');
                        return false
                    }
                    $('#dashboard_home').html(response);
                    $('#loader-dash').hide()
                    $('#reportrange').css('pointer-events', 'auto');
                }
            });

        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Último 7 dias': [moment().subtract(6, 'days'), moment()],
            'Último 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

    });

</script>


