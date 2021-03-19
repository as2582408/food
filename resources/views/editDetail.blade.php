<html>
<head>
    <title>訂餐</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body>
<hr>
    <div class="container">
      @if (count($errors) > 0)
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $errors)
                        <p>{{ $errors }}</p>    
                    @endforeach
                </div>

                @elseif (isset($success))
                <div class="alert alert-success">
                        <p>{{ $success }}</p>    
                </div>
                @endif 
    <div class="row">
        <div class="col-md-6 h-100">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url('/') }}">開放中訂單</a></li>
                  <li class="breadcrumb-item"><a href="{{ url('history') }}">歷史訂單</a></li>
                  <li class="breadcrumb-item"><a href="{{ url('shop') }}">店家</a></li>
                  <li class="breadcrumb-item">{{$shopName->shop_name}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url("detailEditPage/{$id}") }}">訂單設定</a></li>
          <li class="breadcrumb-item"><a href="{{ url("detailOrderPay/{$detail->id}") }}">付款紀錄</a></li>
          <li class="breadcrumb-item"><a href="{{ url("detailOrderAdmin/{$detail->id}") }}">項目統計</a></li>
          <li class="breadcrumb-item"><a href='{{ url("order/{$detail->id}/{$detail->shop_id}/admin") }}' class="alert-link">管理者代訂</a></li>
        </ol>
    </nav>
    <form method="POST" action="{{ url('detailEdit') }}" class="form-horizontal" role="form">
    <table>
          <tr>
            {!! csrf_field() !!}
            開始時間: {{$detail->up_time}}
            <br><br>
            修改開始時間<input id="update" name="update" type="date" required=""><input id="uptime" name="uptime" type="time" required="">
            <br><br>
            結束時間: {{$detail->end_time}}
            <br><br>
            修改結束時間<input id="enddate" name="enddate" type="date" required=""><input id="endtime" name="endtime" type="time" required="">
            <br><br>
            <input type="hidden"  id="id" name="id" value="{{$detail->id}}">
            <input id="add" name="add" type="submit"  value="確認"  >
          </tr>
    </table>
    </form>
</div>

