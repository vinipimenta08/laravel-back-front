<?php

header("Content-type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=layout_lista.csv"); // informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
header( "Content-Transfer-Encoding: binary");

echo 'CELULAR;MENSAGEM_SMS;TITULO_EVENTO;DATA_INICIO;DESCRICAO;LOCALIZACAO;IDENTIFICADOR;CORINGA_1;CORINGA_2
11999999999;Parabéns você formalizou um acordo clique no link e deixe programado um lembrete na sua agenda do seu celular;ACORDO XXX;dd/mm/YYYY;Acordo Parcelado;PAGAVEL PREFENCIALMENTE NAS LOTÉRICAS;000000000;CORINGA1;CORINGA2';

?>
