<!-- CARD LISTA CUSTO CAMPANHA -->
@php
    use Carbon\Carbon;
@endphp
<style>
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
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive-sm  table-responsive-md">
                    <table id="tab-reports" class="table table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>Centro de Custo</th>
                                <th>Data Envio</th>
                                <th>Download</th>
                                <th>Resposta Cliente</th>
                                <th>Log Erro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($reports))
                                @foreach ($reports as $report)
                                    <tr>
                                        <td>
                                            {{$report['name']}}
                                        </td>
                                        <td>
                                            {{Carbon::parse($report['created_at'])->format('d/m/Y')}}
                                        </td>
                                        <th style="text-align: -webkit-center;">
                                            <i class='fas fa-download material-icons' style="cursor:pointer" id="list-{{$report['id']}}" onclick="downloadList('{{$report['id']}}', '{{Carbon::parse($report['created_at'])->format('Y-m-d')}}', this)" data-load-id="list-load-{{$loop->index}}"></i>
                                            <div id="list-load-{{$loop->index}}" class="loader" style="display: none;"></div>
                                        </th>
                                        <th style="text-align: -webkit-center;">
                                            <i class='fas fa-download material-icons' style="cursor:pointer" id="reply-{{$report['id']}}" onclick="downloadReplySms('{{$report['id']}}', '{{Carbon::parse($report['created_at'])->format('Y-m-d')}}', this)" data-load-id="reply-load-{{$loop->index}}"></i>
                                            <div id="reply-load-{{$loop->index}}" class="loader" style="display: none;"></div>
                                        </th>
                                        <th style="text-align: -webkit-center;">
                                            <i class='fas fa-download material-icons' style="cursor:pointer" id="error-{{$report['id']}}" onclick="downloadErrors('{{$report['id']}}', '{{Carbon::parse($report['created_at'])->format('Y-m-d')}}', this)" data-load-id="error-load-{{$loop->index}}"></i>
                                            <div id="error-load-{{$loop->index}}" class="loader" style="display: none;"></div>
                                        </th>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                            @if (count($reports) > 0)
                                <tfoot>
                                    <tr>
                                        <th colspan="2"></th>
                                        <th style="text-align: -webkit-center;">
                                            <i class='fas fa-download material-icons' id="list-all" style="cursor:pointer" onclick="downloadList()" data-load-id="list-load-all"></i>
                                            <div id="list-load-all" class="loader" style="display: none;"></div>
                                        </th>
                                        <th style="text-align: -webkit-center;">
                                            <i class='fas fa-download material-icons' id="reply-all" style="cursor:pointer" onclick="downloadReplySms()" data-load-id="reply-load-all"></i>
                                            <div id="reply-load-all" class="loader" style="display: none;"></div>
                                        </th>
                                        <th style="text-align: -webkit-center;">
                                            <i class='fas fa-download material-icons' id="error-all" style="cursor:pointer" onclick="downloadErrors()" data-load-id="error-load-all"></i>
                                            <div id="error-load-all" class="loader" style="display: none;"></div>
                                        </th>
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