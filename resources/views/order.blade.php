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
                  <li class="breadcrumb-item"><a href="{{ url('shop') }}">店家</a></li>
                  <li class="breadcrumb-item"><a href="{{ url('history') }}">歷史訂單</a></li>
                  <li class="breadcrumb-item">{{$shopName->shop_name}}</li>
                </ol>
            </nav>
        </div>
        <table class="table">
            <thead>
              <tr>
                <th scope="col">餐點名稱</th>
                <th scope="col">價格</th>
                <th scope="col">數量</th>
                <th scope="col">備註</th>

              </tr>
            </thead>
            <form action="{{ url('/order') }}" method="post">
            <tbody>
              {!! csrf_field() !!}      
                @foreach ($products as $product)
                <tr >
                    <td>{{$product->product_name}}</td>
                    <td>{{'$'.$product->product_price}}</td>
                    <td><input id="amount{{$product->id}}" name="amount{{$product->id}}" type="number" min="0" max="100"></td>
                    <td><input id="ps{{$product->id}}" name="ps{{$product->id}}"></td>
                </tr>
                @endforeach
            </tbody>
            <input id="id" name="id" type="hidden" value="{{$detailId}}">
            <td>
              訂購者<input id="name" name="name" required=""> 
              <button id="submit" name="submit" class="btn btn-sm btn-default">送出</button>
            </td>
            </form>
          </table>
        </div>
    </div>

