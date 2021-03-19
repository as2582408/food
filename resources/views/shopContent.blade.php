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
    </div>

    <table class="table table-sm">
        <thead>
        <tr>
            <th scope="col">商品名</th>
            <th scope="col">價格</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr>
                <th scope="row">{{$product->product_name}}</th>
                <th scope="row">{{'$'.$product->product_price}}</th>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script> 
        var shop = [];
        $items = $('#start');
        $items.after('<tr id="start"> <th scope=row>1</th> <td>Mark</td> <td>Otto</td> </tr>');
</script>
