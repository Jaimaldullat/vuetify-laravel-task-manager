<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Manager</title>
    <link href="{{ mix('css/app.css') }}" type="text/css" rel="stylesheet"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
</head>
<body>
    <div id="app">
    </div>
    <script src="{{ mix('js/main.js') }}" type="text/javascript"></script>
</body>
</html>
