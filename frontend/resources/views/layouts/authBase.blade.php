<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Optin - Genion</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/genion-icon.png') }}">

    <link rel="stylesheet" href="{{ asset('site/style.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/app.css') }}">

    <script src="{{ asset('site/js/app.js') }}" ></script>
    <!-- Chat Movidesk -->
    <script type="text/javascript">var mdChatClient="A8AA6B8FF9D24054BA76055C1CDF9563";</script>
    <script src="https://chat.movidesk.com/Scripts/chat-widget.min.js"></script>
    <!-- Chat do Movidesk fim -->
</head>
<body>

    <div class="main-wrapper">

        @yield('content')
    </div>


</body>
</html>
