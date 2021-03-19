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
                </ol>
            </nav>
        </div>
        <div class="col-md-6 h-100">
          <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url("detailEditPage/{$detailID}") }}">訂單設定</a></li>
                  <li class="breadcrumb-item"><a href="{{ url("detailOrderPay/{$detailID}") }}">付款紀錄</a></li>
                  <li class="breadcrumb-item"><a href="{{ url("detailOrderAdmin/{$detailID}") }}">項目統計</a></li>
                  <li class="breadcrumb-item"><a href='{{ url("order/{$detailID}/{$shopID}") }}' class="alert-link">管理者代訂</a></li>
              </ol>
          </nav>
      </div>
        <table class="table">
            <thead>
              <tr>
                <th scope="col">商品名</th>
                <th scope="col">價格</th>
                <th scope="col">數量</th>
                <th scope="col">備註</th>

              </tr>
            </thead>
            <form action="{{ url('/editOrder') }}" method="post">
            <tbody>
              {!! csrf_field() !!}      
                <tr >
                    <td>{{$orders->product_name}}</td>
                    <td>{{'$'.$orders->product_price}}</td>
                    <td><input id="amount" name="amount" type="number" value='{{$orders->amount}}' min="0" max="100"></td>
                    <td><input id="ps" name="ps" value='{{$orders->ps}}'></td>
                </tr>
            </tbody>
            <td>
              <input id="id" name="id" type="hidden" value="{{$orders->id}}">
              訂購者<input id="user" name="user" value="{{$orders->user}}" required=""> 
              <button id="submit" name="submit" class="btn btn-sm btn-default">送出</button>
            </td>
            </form>
          </table>
        </div>
    </div>

