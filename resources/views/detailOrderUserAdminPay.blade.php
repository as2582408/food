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
                <li class="breadcrumb-item"><a href="{{ url("detailEditPage/{$id}") }}">訂單設定</a></li>
                <li class="breadcrumb-item"><a href="{{ url("detailOrderPay/{$id}") }}">付款紀錄</a></li>
                <li class="breadcrumb-item"><a href="{{ url("detailOrderAdmin/{$id}") }}">項目統計</a></li>
                <li class="breadcrumb-item"><a href='{{ url("order/{$id}/{$shopID}/admin") }}' class="alert-link">管理者代訂</a></li>
            </ol>
        </nav>
    </div>

    <table class="table table-sm">
        <thead>
            <div>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url("detailOrderPay/{$id}") }}">詳細清單</a></li>
                  <li class="breadcrumb-item"><a href="{{ url("detailOrderAdminListPay/{$id}") }}">品項排序</a></li>
                  <li class="breadcrumb-item"><a href="{{ url("detailOrderUserAdminPay/{$id}") }}">訂購人排序</a></li>
                </ol>
            </div>
        <tr>
            <th scope="col">訂購人</th>
            <th scope="col">總價</th>
            <th scope="col">商品</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($users as $user_name => $products)
            <tr>
                <th scope="row" id="{{$user_name.'-'}}{{$id}}" style='background-color:#00BB00' onclick="changN('{{$user_name.'-'}}{{$id}}')">{{$user_name}}</th>
                <th scope="row">{{'$'.$price[$user_name]}}</th>

                @foreach ($products as $product)
                    <td>{{$product->product_name}}{{' x '.$product->amount}}</td>
                @endforeach
            </tr>
            @endforeach
    </tbody>
    </table>
</div>
<script> 
    var id = {{$id}}
    $.ajax({
        url: "/AjaxGetOrderStatusUser/"+id,
        type: "GET",
        dataType: "text",
        cache: false,
        success: function(response) {
            var arr = JSON.parse(response);
            console.log(arr);
        var forEachIt = arr.forEach(function(item, index, array){
            var tdid = item.user+'-'+id;
            document.getElementById(tdid).style['background-color'] = 'red';
            document.getElementById(tdid).removeAttribute('onclick');
            document.getElementById(tdid).setAttribute('onclick','changY("'+tdid+'")')
        });
        },
        error: function(){
            console.log('哪裡怪怪的');
            } 
    });

    function changN(id){
            console.log(id,status);
        $.ajax({
            url: "/AjaxChangGetOrderStatusUser/"+id,
            type: "GET",
            dataType: "text",
            cache: false,
            success: function(response) {
                    document.getElementById(id).style['background-color'] = 'red';
                    document.getElementById(id).removeAttribute('onclick');
                    document.getElementById(id).setAttribute('onclick','changY("'+id+'")')
            },
            error: function(){
                console.log('哪裡怪怪的');
        	    } 
            });
    }

    function changY(id){
            console.log(id,status);
        $.ajax({
            url: "/AjaxChangGetOrderStatusUserY/"+id,
            type: "GET",
            dataType: "text",
            cache: false,
            success: function(response) {
                    document.getElementById(id).style['background-color'] = '#00BB00';
                    document.getElementById(id).removeAttribute('onclick');
                    document.getElementById(id).setAttribute('onclick','changN("'+id+'")')
            },
            error: function(){
                console.log('哪裡怪怪的');
        	    } 
            });
    }
</script>
