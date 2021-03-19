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
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('addShop') }}">添加店家</a></li>
        </ol>
    </nav>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">新增店家人員</th>
            <th scope="col">餐廳名稱</th>
            <th scope="col">編輯</th>
            <th scope="col">開啟新訂單</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($shop as $item)
            <tr >
                <th scope="col">{{$item->add_user}}</th>
                <td><a href='{{ url("shopContent/{$item->id}") }}' class="alert-link">{{$item->shop_name}}</a></td>
                <td><a href='{{ url("editShop/{$item->id}") }}' class="alert-link">編輯</a></td>
                <td><a href='{{ url("newDetail/{$item->id}") }}' class="alert-link">新訂單</a></td>
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
