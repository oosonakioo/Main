<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE | {{ trans('setting.company-name') }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/font-awesome/css/font-awesome.min.css') }}">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <style>
        body, html,
        .vertical-center {
            height: 100%;
        }

        .vertical-center {
            display: flex;
            align-items: center;
        }

        .jumbotron {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="jumbotron vertical-center">
    @yield('content')
</div>
<script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
</body>
</html>
