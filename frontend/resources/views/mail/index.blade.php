
@component('mail::message')
<h1> Redefinição de senha do usuario</h1>

<br>
<b>
    Olá,
</b><br>
<p>
    Você está recebendo este e-mail, porque alguém solicitou recentemente uma alteração de senha da sua conta OPTIN.
    <br/>Se foi você, clique no botão abaixo para redefinir sua senha.
</p>

@component('mail::button', ['url' => $url ])
    Redefinir Senha
@endcomponent

<p>
    Se você não solicitou uma alteração de senha, nenhuma ação adicional é necessária.
</p>

<h6>
    Este é um e-mail automático enviado pela Genion Technology e expira em 24 horas, favor não responder.
</h6>

@endcomponent

