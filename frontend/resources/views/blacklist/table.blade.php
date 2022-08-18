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

<div class="table-responsive-sm  table-responsive-md">
    <table id="tab-blacklists" class="table table-striped table-bordered no-wrap">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Data de Criação</th>
                <th>Download</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($blacklists as $blacklist)
                <tr id="tr">
                    <td>{{$blacklist['mailing_file_original']}}</td>
                    <td>{{Carbon::parse($blacklist['created_at'])->format('d/m/Y')}}</td>
                    <th style="text-align: -webkit-center;">
                        <i class='fas fa-download material-icons' style="cursor:pointer" id="file-{{$blacklist['id']}}" onclick="downloadFile('{{$blacklist['id']}}', '{{$blacklist['mailing_file_original']}}', '{{$blacklist['created_at']}}', this)" data-load-id="file-load-{{$blacklist['id']}}"></i>
                        <div id="file-load-{{$blacklist['id']}}" class="loader" style="display: none;"></div>
                    </th>
                    <td>
                        <button type='button' class='btn btn-secondary' style='margin-left: 5px;' id="btn-delele-lote-{{$blacklist['id']}}" onclick="deleteLote('{{$blacklist['id']}}', '{{$blacklist['mailing_file_original']}}', '{{$blacklist['created_at']}}')">Deletar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function(){
        $('#tab-blacklists').DataTable({
            "order": [[ 1, "asc" ]]
        });
    });

    function deleteLote(id, mailing_file_original, created_at) {
        $("#btn-delele-lote-"+id).text('Deletando...');
        $("#btn-delele-lote-"+id).prop('disabled', true);

        var formData = new FormData();
        formData.append('mailing_file_original', mailing_file_original);
        formData.append('created_at', created_at);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{route("blacklist.destroyLote")}}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(result) {
                if (result.error == 500) {
                    swal(
                        `${result.data.title}`,
                        `${result.data.message}`,
                        'error'
                    );
                    changepage("{{route('blacklist.index')}}");
                    return false
                }
                if (result['error']) {
                    swal(
                        'Falha',
                        'Erro ao excluir arquivo blacklist',
                        'warning'
                    );
                }
                swal(
                    'Sucesso',
                    'Arquivo blacklist excluído com sucesso',
                    'success'
                );
                changepage("{{route('blacklist.index')}}");
            },
            error: function (result) {
                swal(
                    'Falha',
                    'Erro ao excluir arquivo blacklist',
                    'warning'
                );
                $(`#btn-delele-lote-${id}`).text('Deletar');
                $(`#btn-delele-lote-${id}`).prop('disabled', false);
            }
        });
    }
</script>
