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
                <li class="breadcrumb-item"><a href='{{ url("order/{$id}/{$shopID}") }}' class="alert-link">管理者代訂</a></li>
            </ol>
        </nav>
    </div>
    <table class="table table-sm">
        <thead>
        <tr>
            <th scope="col">商品名</th>
            <th scope="col">價格</th>
            <th scope="col">訂購者</th>
            <th scope="col">數量</th>
            <th scope="col">備註</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($orders as  $order)
            <tr>
                <th scope="row" id={{$order->id}} onclick="chang('{{$order->id}}','{{$order->status}}')">{{$order->product_name}}</th>
                <td id = 'price{{$order->id}}'>{{$order->product_price}}</td>
                <td>{{$order->user}}</td>
                <td id = 'amount{{$order->id}}'>{{$order->amount}}</td>
                <td>{{$order->ps}}</td>
            </tr>
            @endforeach
            <tr>
                <th>未付款金額</th>
                <td id = "noPay"></td>
                <th>已付款金額</th>
                <td id = "Pay"></td>
            </tr>
    </tbody>
    </table>
</div>
<script> 
        var id = {{$id}}
        $.ajax({
            url: "/food/public/getOrderStatus/"+id,
            type: "GET",
            dataType: "text",
            cache: false,
            success: function(response) {
                var arr = JSON.parse(response);
                var noPay = 0;
                var Pay = 0;
                var forEachIt = arr.forEach(function(item, index, array){
                    if(item.status == 'Y') {
                        var color = 'red';
                    } else {
                        var color = 'green';
                    }
                    if(item.status == 'Y') {
                        noPay += (item.product_price * item.amount);
                    } else {
                        Pay += (item.product_price * item.amount);
                    }
                    document.getElementById(item.id).style['background-color'] = color;
                });
                document.getElementById('noPay').innerHTML = noPay;
                document.getElementById('Pay').innerHTML = Pay;
            },
            error: function(){
                console.log('哪裡怪怪的');
        	    } 
        });

        function chang(id,status){
            var oldNoPay = document.getElementById('noPay').innerHTML
            var oldPay = document.getElementById('Pay').innerHTML
            var price =  document.getElementById('price'+id).innerHTML
            var amount =  document.getElementById('amount'+id).innerHTML
        $.ajax({
            url: "/food/public/changOrderStatus/"+id+'/'+status,
            type: "GET",
            dataType: "text",
            cache: false,
            success: function(response) {
                if(status == 'Y') {
                        var color = 'green';
                    } else {
                        var color = 'red';
                }
                document.getElementById(id).style['background-color'] = color;
                if(status == 'Y') {
                        oldNoPay = (oldNoPay - (price * amount));
                        oldPay = parseInt(oldPay) + parseInt(price * amount);
                        console.log(oldPay,oldNoPay);
                        console.log(document.getElementById('21'));
                        document.getElementById('noPay').innerHTML = oldNoPay;
                        document.getElementById('Pay').innerHTML = oldPay;
                    } else {
                        oldNoPay = parseInt(oldNoPay) + parseInt(price * amount);
                        oldPay = (oldPay - (price * amount));
                        console.log(oldPay,oldNoPay);

                        document.getElementById('noPay').innerHTML = oldNoPay;
                        document.getElementById('Pay').innerHTML = oldPay;
                }
            },
            error: function(){
                console.log('哪裡怪怪的');
        	    } 
            });
    }
</script>