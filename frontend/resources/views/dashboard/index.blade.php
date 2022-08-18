

    {{-- CARD CENTRO DE CUSTO/ENVIOS/VISUALIZADOS --}}
    <div class="card-group">
        <div class="card border-right">
            <div class="card-body">
                <div class="d-flex d-lg-flex d-md-block align-items-center">
                    <div>
                        <div class="d-inline-flex align-items-center">
                            <h2 class="text-dark mb-1 font-weight-medium">{{number_format($cardTotal['sended'], 0, ',', '.')}}</h2>
                            <span class="badge bg-primary font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none">
                                @if ($cardTotal['imported'])
                                    @php
                                        echo round((($cardTotal['sended'] / $cardTotal['imported']) * 100), 2)
                                    @endphp
                                @else
                                    {{$cardTotal['imported']}}
                                @endif
                                %
                            </span>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Enviados</h6>
                    </div>
                    <div class="ml-auto mt-md-3 mt-lg-0">
                        <span class="opacity-7 text-muted"><i data-feather="send"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-right">
            <div class="card-body">
                <div class="d-flex d-lg-flex d-md-block align-items-center">
                    <div>
                        <div class="d-inline-flex align-items-center">
                            <h2 class="text-dark mb-1 w-100 text-truncate font-weight-medium">{{number_format($cardTotal['opening'], 0, ',', '.')}}</h2>
                            <span class="badge bg-danger font-12 text-white font-weight-medium badge-pill ml-2 d-md-none d-lg-block">
                                @if ($cardTotal['imported'])
                                    @php
                                        echo round((($cardTotal['opening'] / $cardTotal['imported']) * 100), 2)
                                    @endphp
                                @else
                                    {{$cardTotal['imported']}}
                                @endif
                                %
                            </span>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Visualizados
                        </h6>
                    </div>
                    <div class="ml-auto mt-md-3 mt-lg-0">
                        <span class="opacity-7 text-muted"><i data-feather="eye"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-right">
            <div class="card-body">
                <div class="d-flex d-lg-flex d-md-block align-items-center">
                    <div>
                        <div class="d-inline-flex align-items-center">
                            <h2 class="text-dark mb-1 font-weight-medium">{{number_format($cardTotal['imported'], 0, ',', '.')}}</h2>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Registros</h6>
                    </div>
                    <div class="ml-auto mt-md-3 mt-lg-0">
                        <span class="opacity-7 text-muted"><i data-feather="folder"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex d-lg-flex d-md-block align-items-center">
                    <div>
                        <h2 class="text-dark mb-1 font-weight-medium">{{number_format($cardTotal['campaigns'], 0, ',', '.')}}</h2>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Centro de Custos</h6>
                    </div>
                    <div class="ml-auto mt-md-3 mt-lg-0">
                        <span class="opacity-7 text-muted"><i data-feather="list"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- FIM CARD CENTRO DE CUSTO/ENVIOS/VISUALIZADOS --}}

    <div class="row">
        <div class="col-lg-4 col-md-12">
            {{-- INICIO CARD GRAFICO ENVIOS --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Envios</h4>
                    <div id="campaign-v2" class="mt-2" style="height:283px; width:100%;"></div>
                    <ul class="list-style-none mb-0">
                        <li class="mt-2">
                            <i class="fas fa-circle text-primary font-10 mr-2"></i>
                            <span class="text-muted">Enviados</span>
                            <span class="text-dark float-right font-weight-medium">{{$cardTotal['sended']}}</span>
                        </li>
                        <li class="mt-2">
                            <i class="fas fa-circle text-danger font-10 mr-2"></i>
                            <span class="text-muted">Visualizado</span>
                            <span class="text-dark float-right font-weight-medium">{{$cardTotal['opening']}}</span>
                        </li>
                        <li class="mt-2">
                            <i class="fas fa-circle text-cyan font-10 mr-2"></i>
                            <span class="text-muted">Respostas</span>
                            <span class="text-dark float-right font-weight-medium">{{$cardTotal['reply']}}</span>
                        </li>
                    </ul>
                </div>
            </div>
            {{-- FIM CARD GARFICO ENVIOS --}}
        </div>
        <div class="col-lg-4 col-md-12">
            {{-- INICIO CARD GRAFICO RESPOSTA --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Respostas</h4>
                    <div class="net-income mt-4 position-relative" style="height:310px;"></div>
                    <ul class="list-inline text-center mt-4 mb-2">
                        <li class="list-inline-item text-muted font-italic">Semana</li>
                    </ul>
                </div>
            </div>
            {{-- FIM CARD GARFICO RESPOSTA --}}
        </div>
        <div class="col-lg-4 col-md-12">
            {{-- INICIO CARD GRAFICO MAPAS --}}
            <div class="card" style="height: 461px;">
                <div class="card-body">
                    <h4 class="card-title mb-4">Localização</h4>
                    <div class="" style="height:180px">
                        <div id="brazil-map"></div>
                    </div>
                    <div class="indices" style="margin-top: 25px;">

                        @php
                            $count=0;
                        @endphp

                        @if ($states['data'])

                            @foreach ($states['data'] as $state)

                                @php
                                    if ($count == 0) {
                                        $color = '#1f3fdb';
                                    }elseif ($count == 1) {
                                        $color = '#ff4f70';
                                    }elseif ($count == 2) {
                                        $color = '#01caf1';
                                    }elseif ($count == 3) {
                                        $color = '#22ca80';
                                    }
                                @endphp


                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-right">
                                        <span class="text-muted font-14">{{ $state['estado'] }}</span>
                                    </div>
                                    <div class="col-5">
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar role="progressbar" style="width: {{ $state['percent'] }}%;background-color: {{ $color }};"
                                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 text-left">
                                        <span class="mb-0 font-14 text-dark font-weight-medium">{{ round($state['percent'], 0) }}%</span>
                                    </div>
                                </div>


                                @php
                                    $count++;
                                @endphp

                            @endforeach

                        @else

                                <div class="row mb-3 align-items-center">
                                <div class="col-12 text-center">
                                    <span class="text-muted font-14">Nenhum dado para ser exibido.</span>
                                </div>
                            </div>

                            @php
                                $count++;
                            @endphp

                        @endif

                        @if ($count < 4)

                            @while ($count < 4)

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-right">
                                        <span class="text-muted font-14" style="color: #fff!important;">-</span>
                                    </div>
                                </div>

                                @php
                                    $count++;
                                @endphp

                            @endwhile

                        @endif

                    </div>
                </div>
            </div>
            {{-- FIM CARD GRAFICO MAPAS --}}
        </div>
    </div>

    {{-- CARD LISTA CUSTO CAMPANHA --}}
    @if ($listCustom['data'])
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive-sm table-responsive-md" style="padding-bottom: 20px;">
                    <table id="zero_config" class="table table-striped no-wrap">
                        <thead style="background-color: #fff;">
                            <tr>
                            @if ($user_acount['id_profile'] == 1)
                                <th>Job</th>
                            @endif
                                <th>Envio</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($listCustom['data'] as $list)
                                @if (isset($list['campaign']['name']) != "")
                                
                                    <tr>
                                    @if ($user_acount['id_profile'] == 1)
                                        <th class="table-id-td align-middle" style="text-align: center;">
                                            <span class="fa-stack text-default fa-lg">
                                                <i class="fa fa-circle fa-stack-2x" data-original-title="" title=""></i>
                                                <i class="fas fa-envelope fa-stack-1x text-white" data-original-title="" title=""></i>
                                            </span> <h5 class="text-center">{{ $list['name_client'] }}</h5>
                                        </th>
                                    @endif
                                        <td>
                                            <div class="inline-block align-middle">
                                                <strong class="text-gray-dark text-fixed text-fixed--400 text-uppercase">{{ $list['campaign']['name'] }}</strong>
                                                <br>
                                                @if ($list['sended'] > 0)
                                                    <strong>Envio:</strong>
                                                    @php
                                                        $data_format = str_replace('-','/',$list['sended_at']);
                                                        $data_entrada = date('d/m/Y',  strtotime($data_format));
                                                        $hora_entrada = date('H:i:s',  strtotime($data_format));
                                                    @endphp
                                                    <span class="no-wrap font-worksans-300">{{ $data_entrada }} às {{ $hora_entrada}}</span>
                                                @else
                                                    <strong>Importado</strong>
                                                @endif
                                            </div>
                                        </td>
                                        <td class='table-times align-middle' colspan='2'>
                                            <div class="row">
                                                <div class="flex-fill flex-wrap text-center">
                                                        <p class="m-0 heading-small">Base</p>
                                                        <strong>{{ $list['base'] }}</strong>
                                                </div>
                                                <div class="flex-fill flex-wrap text-center">
                                                        <p class="m-0 heading-small">Importados</p>
                                                        <strong>{{ $list['imported'] }}</strong>
                                                    </div>
                                                    <div class="flex-fill flex-wrap text-center">
                                                        <p class="m-0 heading-small">Falhas</p>
                                                        <strong>{{ $list['failed'] }}</strong>
                                                    </div>
                                                    <div class="flex-fill flex-wrap text-center">
                                                        <p class="m-0 heading-small">Enviados</p>
                                                        <strong>{{ $list['sended'] }}</strong>
                                                    </div>
                                                    <div class="flex-fill flex-wrap text-center">
                                                        <p class="m-0 heading-small">Respostas</p>
                                                        <strong>{{ $list['reply'] }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    {{-- FIM CARD LISTA CUSTO CAMPANHA --}}

{{-- GRAFICO ENVIO --}}
<script src="{{ asset('assets/extra-libs/c3/c3.min.js') }}"></script>
<script src="{{ asset('assets/extra-libs/c3/d3.min.js') }}"></script>
{{-- GRAFICO RESPOSTA --}}
<script src="{{ asset('assets/libs/chartist/dist/chartist.min.js') }}"></script>
<script src="{{ asset('assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script>
{{-- GRAFICO MAPA --}}
<script src="{{ asset('assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('assets/extra-libs/jvector/jquery-jvectormap-1.2.2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/extra-libs/jvector/brazil.js') }}" type="text/javascript"></script>

<script type="text/javascript">

    $(document).ready(function () {

        // ==============================================================
        // Campaign
        // ==============================================================

        @php
            if($cardTotal['sended'] == 0 && $cardTotal['opening'] == 0 && $cardTotal['reply'] == 0){
                $vazio = 100;
            }else{
                $vazio = 0;
            }
        @endphp

        var chart1 = c3.generate({
            bindto: '#campaign-v2',
            data: {
                columns: [
                    ['Enviados', "{{  $cardTotal['sended'] }}" ],
                    ['Visualizado', "{{ $cardTotal['opening'] }}"],
                    ['Resposta', "{{ $cardTotal['reply'] }}"],
                    ["Vazio", "{{ $vazio }}"]
                ],

                type: 'donut',
                tooltip: {
                    show: true
                }
            },
            donut: {
                label: {
                    show: false
                },
                title: 'SMS',
                width: 18
            },

            legend: {
                hide: true
            },
            color: {
                pattern: [
                    '#5f76e8',
                    '#ff4f70',
                    '#01caf1',
                    "#edf2f6"
                ]
            }
        });

        d3.select('#campaign-v2 .c3-chart-arcs-title').style('font-family', 'Rubik');



        // ==============================================================
        // income
        // ==============================================================
        var data = {
            labels: ['{{ $last_days['dayofweek_1'] }}', '{{ $last_days['dayofweek_2'] }}', '{{ $last_days['dayofweek_3'] }}', '{{ $last_days['dayofweek_4'] }}', '{{ $last_days['dayofweek_5'] }}', '{{ $last_days['dayofweek_6'] }}', '{{ $last_days['dayofweek_7'] }}'],
            series: [
                [ '{{ $last_days['response_day_1'] }}'  , '{{ $last_days['response_day_2'] }}', '{{ $last_days['response_day_3'] }}', '{{ $last_days['response_day_4'] }}', '{{ $last_days['response_day_5'] }}', '{{ $last_days['response_day_6'] }}', '{{ $last_days['response_day_7'] }}']
            ]
        };

        var options = {
            axisX: {
                showGrid: false
            },
            seriesBarDistance: 1,
            chartPadding: {
                top: 1,
                right: 15,
                bottom: 0,
                left: 0
            },
            plugins: [
                Chartist.plugins.tooltip()
            ],
            width: '100%'
        };

        var responsiveOptions = [
            ['screen and (max-width: 640px)', {
                seriesBarDistance: 5,
                axisX: {
                    labelInterpolationFnc: function (value) {
                        return value[0];
                    }
                }
            }]
        ];
        new Chartist.Bar('.net-income', data, options, responsiveOptions);



        // ==============================================================
        // Maps
        // ==============================================================
        var map_settings = {
            map: 'brazil',
            zoomButtons: false,
            zoomMax: 1,
            regionStyle: {
                initial: {
                    'fill-opacity': 0.9,
                    stroke: '#000',
                    'stroke-width': 100,
                    'stroke-opacity': 1
                },
                hover: {
                    fill: '#8971ea'
                }
            },
            backgroundColor: 'transparent',
            series: {
                regions: [{
                    values: {
                        // Região Norte
                        ac: '#e9ecef',
                        am: '#e9ecef',
                        ap: '#e9ecef',
                        pa: '#e9ecef',
                        ro: '#e9ecef',
                        rr: '#e9ecef',
                        to: '#e9ecef',
                        // Região Nordeste
                        al: '#e9ecef',
                        ba: '#e9ecef',
                        ce: '#e9ecef',
                        ma: '#e9ecef',
                        pb: '#e9ecef',
                        pe: '#e9ecef',
                        pi: '#e9ecef',
                        rn: '#e9ecef',
                        se: '#e9ecef',
                        // Região Centro-Oeste
                        df: '#e9ecef',
                        go: '#e9ecef',
                        ms: '#e9ecef',
                        mt: '#e9ecef',
                        // Região Sudeste
                        es: '#e9ecef',
                        mg: '#e9ecef',
                        rj: '#e9ecef',
                        sp: '#e9ecef',
                        // Região Sul
                        pr: '#e9ecef',
                        rs: '#e9ecef',
                        sc: '#e9ecef',

                        @php
                            $count_color=0;
                            foreach ($states['data'] as $state) {
                                $count_color++;
                                if ($count_color == 1) {
                                    echo strtolower($state['estado']).": '#1f3fdb',";
                                }elseif ($count_color == 2) {
                                    echo strtolower($state['estado']).": '#ff4f70',";
                                }
                                elseif ($count_color == 3) {
                                    echo strtolower($state['estado']).": '#01caf1',";
                                }
                                elseif ($count_color == 4) {
                                    echo strtolower($state['estado']).": '#22ca80',";
                                }
                            }
                        @endphp

                    },
                    attribute: 'fill'
                }]
            },
            container: $('#brazil-map')
        };

        map = new jvm.WorldMap($.extend(true, {}, map_settings));

    });

</script>
