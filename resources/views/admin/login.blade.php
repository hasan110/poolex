<!DOCTYPE html>
<html lang="en"dir="rtl">
 
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ورود به پنل مدیریت</title>
    <link rel="icon" href="{{asset('assets/img/fav-icon.ico')}}">
    <link rel="stylesheet" href="{{asset('assets/css/custom-style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/icons.min.css')}}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
  </head>
 
  <body class="login-body">
    <div class="login-wrapper">

      <div class="login-form">

        <div class="logo">
          {{-- <img class="logo-img" src="{{asset('assets/img/logo-dark.png')}}"> --}}
        </div>

        <div class="form-login">
          <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="label">نام کاربری</div>
            <div class="login-input">
              <span class="input-group-addon"><i class="fa fa-user"></i>
              </span>
              <input name="user_name" value="{{ old('user_name') }}" type="text" class="input">
            </div>
            <div class="label">رمز عبور</div>
            <div class="login-input">
              <span class="input-group-addon"><i class="fa fa-eye"></i>
              </span>
              <input name="password" type="password" class="input">
            </div>
            <div class="row">
              <div class="col-xs-6">
                <button type="submit" class="btn-login">
                  <i class="icon-login"></i>
                  ورود</button>
              </div>
            </div>
            @if(\Session::has('Error'))
              <div class="row mt-3">
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <p>
                      {{\Session::get('Error')}}
                    </p>
                </div>
              </div>
            @endif
            @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
          </form>
        </div>

      </div>

      <div class="login-bg">
        <img class="login-img" src="{{asset('assets/img/login-bg.png')}}">
      </div>
    </div>
  </body>
 
</html>