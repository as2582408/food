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
    <div class="col-md-6 h-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url("detailOrder/{$id}") }}">品項排序</a></li>
              <li class="breadcrumb-item"><a href="{{ url("detailOrderUser/{$id}") }}">訂購人排序</a></li>
              <li class="breadcrumb-item"><a href="{{ url("allOrder/{$id}") }}">詳細清單</a></li>
            </ol>
        </nav>
    </div>
    <table class="table table-sm">
        <thead>
        <tr>
            <th scope="col">訂購人</th>
            <th scope="col">總價</th>
            <th scope="col">商品</th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
            @foreach ($users as $user_name => $products)
            <tr>
                <th scope="row">{{$user_name}}</th>
                <th scope="row">{{'$'.$price[$user_name]}}</th>

                @foreach ($products as $product)
                    <td id ="{{$user_name.'-'}}{{$product->order_id}}">{{$product->product_name}}{{' x '.$product->amount}}
                    </td>
                @endforeach
            </tr>
            @endforeach
    </tbody>
    </table>
</div>
<script> 
    var id = {{$id}}
    var retext;
    $.ajax({
        url: "/ajaxDetailOrderUser/"+id,
        type: "GET",
        dataType: "text",
        cache: false,
        success: function(response) {
            var arr = JSON.parse(response);
        var forEachIt = arr.forEach(function(item, index, array){
            var id = item.user+'-'+item.order_id;
            document.getElementById(id).style['color'] = 'blue';
            document.getElementById(id).setAttribute('onclick','get("'+id+'")')
        });
        },
        error: function(){
            console.log('哪裡怪怪的');
            } 
    });

    function get(id){
    $.ajax({
        url: "/ajaxOrderUser/"+id,
        type: "GET",
        dataType: "text",
        cache: false,
        success: function(response) {
            retext = document.getElementById(id).innerHTML;
            var text = document.getElementById(id).innerHTML;
            var arr = JSON.parse(response);
            var forEachIt = arr.forEach(function(item, index, array){
                text = text+'<br>x'+ item.amount +' '+item.ps;
            });
            document.getElementById(id).innerHTML = text;
            document.getElementById(id).removeAttribute('onclick');
            document.getElementById(id).setAttribute('onclick','reset("'+id+'")')
        },
        error: function(){
            console.log('哪裡怪怪的');
            } 
        });
    }
    
    function reset(id){
        document.getElementById(id).innerHTML = retext;
        document.getElementById(id).removeAttribute('onclick');
        document.getElementById(id).setAttribute('onclick','get("'+id+'")')
    }
</script>
