<div class="table-responsive-sm  table-responsive-md table-responsive">
    <table id="tab-user" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>CELULAR</th>
                <th>MENSAGEM_SMS</th>
                @if (!$validClientJustSMS)
                    <th>TITULO_EVENTO</th>
                    <th>DATA_INICIO</th>
                    <th>DESCRICAO</th>
                    <th>LOCALIZACAO</th>
                    <th>IDENTIFICADOR</th>
                    <th>CORINGA_1</th>
                    <th>CORINGA_2</th>
                @endif
            </tr>
        </thead>
        @foreach ($customs as $custom)
            <tbody>
                <tr>
                    <td>{{isset($custom["phone"]) ? $custom["phone"] : ""}}</td>
                    <td>{{isset($custom["message_sms"]) ? $custom["message_sms"] : ""}}</td>
                @if (!$validClientJustSMS)
                    <td>{{isset($custom["title"]) ? $custom["title"] : ""}}</td>
                    <td>{{isset($custom["date_event"]) ? $custom["date_event"] : ""}}</td>
                    <td>{{isset($custom["description"]) ? $custom["description"] : ""}}</td>
                    <td>{{isset($custom["location"]) ? $custom["location"] : ""}}</td>
                    <td>{{isset($custom["identification"]) ? $custom["identification"] : ""}}</td>
                    <td>{{isset($custom["joker_one"]) ? $custom["joker_one"] : ""}}</td>
                    <td>{{isset($custom["joker_two"]) ? $custom["joker_two"] : ""}}</td>
                @endif
                </tr>
            </tbody>
        @endforeach
    </table>
</div>
