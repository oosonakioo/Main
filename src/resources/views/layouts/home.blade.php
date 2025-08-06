<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

        <meta name="keyword" content="@yield('keyword', $keyword)">
        <meta name="description" content="@yield('description', $desc)">
        <title>@yield('title', $title)</title>

        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit:100,400,500,600,700">
        <link rel="stylesheet" type="text/css" href="{{ asset('bootstrap/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('js/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/nanoscroller.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.bxslider.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('css/web-custom.css') }}" />

        <script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>
        <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/validator.js') }}"></script>
        <script src="{{ asset('js/jquery.nanoscroller.min.js') }}"></script>
        <script src="{{ asset('js/jquery.bxslider.min.js') }}"></script>
        <script src="{{ asset('js/html5lightbox.js') }}"></script>
        <!-- <script src="js/modernizr.min.js"></script> -->

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!--[if lte IE 9]>
          <link rel="stylesheet" type="text/css" href="{{ asset('css/web-custom-ie.css') }}" />
        <![endif]-->

    </head>

    <body>

        @if (session('completed'))
            <div class="container alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ session('completed') }}
            </div>
        @endif

      	@include('layouts.home-header')
      	@yield('content')
      	@include('layouts.home-footer')
        @include('layouts.googleanalytics')

    </body>
</html>
