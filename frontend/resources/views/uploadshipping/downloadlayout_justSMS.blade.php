<?php

header("Content-type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=layout_lista.csv"); // informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
header( "Content-Transfer-Encoding: binary");

echo 'CELULAR;MENSAGEM_SMS;
11999999999;Segue a linha digitavel para o pagamento: xxxxxxxxxxxxxxxxxxxxxxxxxx';

?>
