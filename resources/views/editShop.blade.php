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
    <form method="POST" action="{{ url('addProducts') }}" class="form-horizontal" role="form">
      <tr>
          新增商品
          <br><br> 
          {!! csrf_field() !!}
          <td id="items">         
          <div id="div"></div>
          <div id="div1">
          1.   
          <input id="items1" name="items[]" required="">
          價格<input id="price1" name="price[]" required="">
          <br>
          </div>
          </td>
          <input id="id" type="hidden" name="id" value="{{$shopID}}" required="">
          <input id="add_items" name="add_items" type="button"  class="bt-add" value="新增商品欄位" style="margin-top:10px">
          <input id="delitems" name="add_items" type="button"  class="bt-add" value="刪除" style="margin-top:10px">
          <input id="add" name="add" type="submit"  value="確認"  >
      </tr>
    </form>
    <table class="table">
          <thead>
            <tr>
                <th scope="col">商品名</th>
                <th scope="col">價格</th>
                <th scope="col">刪除</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <th scope="row">{{$product->product_name}}</th>
                    <th scope="row">{{'$'.$product->product_price}}</th>
                    <td><a href='{{ url("delete/{$product->id}") }}' class="alert-link">刪除</a></td>
                  </tr>
                @endforeach
            </tbody>
    </table>
  </div>
  <script>
  	var i = 1;

    $('#add_items').click(function(){
      var o = '價格'
		  $items = $('#div'+i);

      if(i < 0) {
        i = 0
        $items = $('#div');
      }
		i++;
		$items.after('<div id="div'+i+'"><br>'+i+'.    <input id="items' + i + '" name="items[]" ' + ' required="" >'+o+'<input id="price' + i + '" name="price[]" ' + ' required="" ></div>');
	})
  
  $('#delitems').click(function(){
		$items = $('#div'+i);
		$items.remove();
    i--
	})
</script>

