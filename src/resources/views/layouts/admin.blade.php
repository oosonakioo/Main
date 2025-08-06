<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE | {{ trans('setting.company-name') }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/dataTables/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/colorbox/colorbox.css') }}">
    <link rel="stylesheet" href="{{ asset('js/admin/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/admin/css/skins/skin-black.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/bootstrap-datepicker/css/bootstrap-datepicker3.css') }}">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker/locales/bootstrap-datepicker.th.min.js') }}"></script>
    <script src="{{ asset('js/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/colorbox/jquery.colorbox-min.js') }}"></script>
    <script src="{{ asset('js/admin/js/app.min.js') }}"></script>

    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <script>
      var pathelfinder = '{{ config('setting.path-elfinder') }}';
    </script>

    @if ($lang == 'th')
      <script>
        $.extend(true, $.fn.dataTable.defaults, {
          "language": {
              "sProcessing": "กำลังดำเนินการ...",
              "sLengthMenu": "แสดง_MENU_ แถว",
              "sZeroRecords": "ไม่พบข้อมูล",
              "sInfo": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
              "sInfoEmpty": "แสดง 0 ถึง 0 จาก 0 แถว",
              "sInfoFiltered": "(กรองข้อมูล _MAX_ ทุกแถว)",
              "sInfoPostFix": "",
              "sSearch": "ค้นหา:",
              "sUrl": "",
              "oPaginate": {
                "sFirst": "เริ่มต้น",
                "sPrevious": "ก่อนหน้า",
                "sNext": "ถัดไป",
                "sLast": "สุดท้าย"
              }
            }
          });
      </script>
    @endif

    <style type="text/css">
    .issue-row:hover {
        cursor: pointer;
    }
    </style>
</head>
<body class="hold-transition skin-black sidebar-mini">

@if ($errors->has('token_error'))
  {{ $errors->first('token_error') }}
@endif

<div class="wrapper">

    <header class="main-header">

        <a href="{{ url('admin') }}" class="logo">
            <span class="logo-mini"><b>A</b>LT</span>
            <span class="logo-lg"><b>Admin</b>LTE</span>
        </a>


        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="user user-menu">
                        <a href="{{ Helper::url('admin/profile') }}">
                            <img src="{{ asset('js/admin/img/avatar5.png') }}" class="user-image" alt="User Image">
                            <span class="hidden-xs">{{ studly_case(Auth::user()->name) }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/logout') }}" title="{{ trans('admin.logout') }}"><i
                                    class="fa fa-power-off"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            @include('layouts.admin-nav')
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @yield('header')
                <small>@yield('description')</small>
            </h1>
            <ol class="breadcrumb">
                {{--<li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                <li class="active">Here</li>--}}
                @yield('menu')
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <span class="pull-left">Copyright &copy; 2015 <a href="#">AdminLTE</a>.</span> All rights reserved.

        <div class="pull-right">
            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                <a rel="alternate" hreflang="{{$localeCode}}"
                   href="{{LaravelLocalization::getLocalizedURL($localeCode) }}">
                    {{ $properties['native'] }}
                </a> &nbsp;
            @endforeach
        </div>
    </footer>
    <script type="text/javascript" src="{{ asset('src/packages/barryvdh/elfinder/js/standalonepopup.js') }}"></script>
</div>
<!-- ./wrapper -->
@yield('script')
</body>
</html>
