
<!-- CARD LISTA CUSTO CAMPANHA -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive-sm  table-responsive-md">
                <table id="tab-financy" class="table table-striped table-bordered no-wrap">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Quantidade envio</th>
                            <th>Fatura</th>
                        </tr>
                    </thead>

                    @if ($sended)
                        <tbody>

                            @php
                                $tot_envio = 0;
                                $tot_custo = 0;
                            @endphp

                            @foreach ($sended as $send)
                                <tr id="tr-{{ $send['id_client'] }}">
                                    <td>{{ $send['clients'] }}</td>
                                    <td>{{ $send['sended'] }}</td>
                                    <td>{{ 'R$ '.number_format($send['valueSended'], 2, ',', '.') }}</td>
                                </tr>
                                @php
                                    $tot_envio = $tot_envio + $send['sended'];
                                    $tot_custo = $tot_custo + $send['valueSended'];
                                @endphp
                            @endforeach

                        </tbody>

                        <tfoot>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td>{{ $tot_envio }}</td>
                                <td>{{ 'R$ '.number_format($tot_custo, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif

                </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM CARD LISTA CUSTO CAMPANHA -->
