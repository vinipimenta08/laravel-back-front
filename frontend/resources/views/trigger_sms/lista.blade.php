
<!-- CARD LISTA CUSTO CAMPANHA -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <h4>
                    <span id="data_span" style="float: right;"></span>
                </h4>
                <div class="table-responsive-sm  table-responsive-md">
                    <table id="zero_config" class="table table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>Centro de Custo</th>
                                <th>SMS Enviado</th>
                                <th>SMS Entregue</th>
                                <th>Processando</th>
                                <th>SMS Erro</th>
                                <th>Link</th>
                                <th>Erro</th>
                                <th>Valor Estimado</th>
                                <th>Valor Final</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>

                        <tbody>

                            @php
                                $errorSend = 0;
                                $link = 0;
                                $processing = 0;
                            @endphp

                            @if ($campaigns)

                                @foreach ($campaigns as $campaign)

                                    @php
                                        $disabled = "";
                                        if($campaign['errorSend'] == 0 && $campaign['link'] == 0 && $campaign['error'] == 0){
                                            $disabled = 'disabled';
                                        }else{
                                            if($campaign['errorSend'] > 0){
                                                $errorSend = 2;
                                            }
                                            if($campaign['link'] > 0){
                                                $link = 3;
                                            }
                                        }
                                    @endphp

                                    <tr id="tr-c">
                                        <td>{{ $campaign['campaigns']['name'] }}</td>
                                        <td>{{ $campaign['sended'] }}</td>
                                        <td>{{ $campaign['delivered'] }}</td>
                                        <td>{{ $campaign['processing'] }}</td>
                                        <td>{{ $campaign['errorSend'] }}</td>
                                        <td>{{ $campaign['link'] }}</td>
                                        <td>{{ $campaign['error'] }}</td>
                                        <td>{{ 'R$ '.number_format($campaign['foreseen'], 2, ',', '.') }}</td>
                                        <td>{{ 'R$ '.number_format($campaign['valueSended'], 2, ',', '.') }}</td>
                                        <td>
                                            <div class='row'>
                                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='padding-right: 5px; padding-left: 5px;'>
                                                    <div class='form-group' style='margin-bottom: 0px;'>
                                                        <select id='status-{{ $campaign['campaigns']['id'] }}-{{ $campaign['campaigns']['id_client'] }}' name='status-{{ $campaign['campaigns']['id'] }}-{{ $campaign['campaigns']['id_client'] }}' class='form-control' {{$disabled}}>
                                                            <option value="">Selecione</option>
                                                            @if ($statuslink)
                                                                @foreach ($statuslink['data'] as $status)
                                                                    @if ($status['id'] == $errorSend)
                                                                        <option value="{{$status['id']}}" >{{$status['status']}}</option>
                                                                    @endif
                                                                    @if ($status['id'] == $link)
                                                                        <option value="{{$status['id']}}" >{{$status['status']}}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class='col-lg-6 col-md-6 col-sm-6 col-xs-6'>
                                                <button id="btn_disparo-{{ $campaign['campaigns']['id']}}" type="button" class="btn btn-info btn-circle" data-toggle="popover" data-content="Disparar SMS" data-placement="bottom" onclick="dispararSMS('{{ $campaign['campaigns']['id'] }}', '{{ $campaign['campaigns']['id_client'] }}')" {{$disabled}}>
                                                    <i class='fas fa-play'></i>
                                                    <span role="" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM CARD LISTA CUSTO CAMPANHA -->

<script type="text/javascript">

    $(document).ready(function() {

        $('#btn_disparo').popover({
            container: 'body',
            html: true,
            trigger: 'hover'
        });

    })

</script>
