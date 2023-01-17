<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>پنل مدیریت پولکس</title>
  <link rel="icon" href="{{asset('assets/img/fav-icon.ico')}}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{asset('assets/css/adminlte.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/bootstrap-rtl.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/custom-style.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/icons.min.css')}}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper" id="arzineh">
  <header-panel></header-panel>
  <sidebar-panel :admin="{{ json_encode(auth()->user()) }}"></sidebar-panel>
  <div class="content-wrapper">
    <div class="container-fluid">
      <loader-panel></loader-panel>
    </div>
  </div>
</div>
<script src="{{asset('assets/js/vue.js')}}"></script>
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/js/adminlte.js')}}"></script>
</body>
</html>
