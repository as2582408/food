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
    <form method="POST" action="{{ url('add') }}" class="form-horizontal" role="form">

    <table>
          <tr>
            {!! csrf_field() !!}
              <td id="items">
            商店名<input id="shop" name="shop" required="">
                <br><br>
              1.   
              <input id="items1" name="items[]" required="">
              價格<input id="price1" name="price[]" required="">
              <br><br>
              2.   
              <input id="items2" name="items[]" required="">
              價格<input id="price2" name="price[]" required="">
              <br>   
              </td>
              <input id="add_items" name="add_items" type="button"  class="bt-add" value="新增商品欄位" style="margin-top:10px">
              <input id="add" name="add" type="submit"  value="確認"  >
          </tr>
    </table>
</form>
  </div>
<script>
  	var i = 2;

    $('#add_items').click(function(){
    var o = '價格'
		$items = $('#price'+i);
		i++;
		$items.after('<br><br>'+i+'.    <input id="items' + i + '" name="items[]" ' + 'required="" >'+o+'<input id="price' + i + '" name="price[]" ' + 'required="" >');
	})
</script>

