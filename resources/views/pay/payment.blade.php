<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>تراکنش پرداخت از طریق درگاه</title>
  <link rel="stylesheet" href="{{asset('assets/css/adminlte.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/bootstrap-rtl.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/custom-style.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/icons.min.css')}}">
  <style>
    body{
      width: 100vw;
      height: 100vh;
      background: #003b6d;
    }
    .wrapper-pay{
      width: 100%;
      height: 100%;
      max-width: 500px;
      padding: 18px;
      margin: auto
    }
    .inner-pay{
      width: 100%;
      height: 100%;
      background: #fff;
      border-radius: 18px;
      text-align: center;
      position: relative;
    }
    .back-btn{
      padding: 1rem;
      background: #003b6d;
      border-radius: 8px;
      color: #fff;
      position: absolute;
      bottom: 30px;
      left: 50%;
      transform: translate(-50% , -50%);
      
    }
    .back-btn:hover{
      color: #fff
    }
    .text-suc{
      color: #52cc4e;
    }
    .text-err{
      color: #ff4757;
    }
  </style>
</head>
<body>
  <div class="wrapper-pay">
    <div class="inner-pay">
      @if($status == 1)
      <div class="text-suc py-5">
        <h3 class="">تراکنش موفق</h3>
      </div>
      @endif
      @if($status == 0)
      <div class="text-err py-5">
        <h3 class="">تراکنش ناموفق</h3>
      </div>
      @endif
      
      
      @if($status == 0)
        <div class="text-err py-2">
          <p class="">{{ $msg }}</p>
        </div>

        @if($time)
        <p>تاریخ و زمان ---- {{$time}}</p>
        @endif
      @endif

      @if($status == 1)
      <div class="text-suc py-2">
        <p class="">{{ $msg }}</p>
      </div>
      
      @if($time)
      <p>تاریخ و زمان ---- {{$time}}</p>
      @endif
      <p>شناسه تراکنش ---- {{$transaction_id}}</p>
      @endif
    </div>
  </div>
</body>
</html>