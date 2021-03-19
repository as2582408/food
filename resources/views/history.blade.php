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
        <table class="table">
            <thead>
              <tr>
                <th scope="col">發起人</th>
                <th scope="col">餐廳名稱</th>
                <th scope="col">結束時間</th>
                <th scope="col">管理</th>
              </tr>
            </thead>
            <tbody>
              <tr id = 'tr'>
              </tr>
            </tbody>
          </table>
        </div>
    </div>
    <script> 
      $.ajax({
      url: "/hisAllDetail",
      type: "GET",
      dataType: "text",
      cache: false,
      success: function(response) {
          var arr = JSON.parse(response);
          var forEachIt = arr.forEach(function(item, index, array){
              console.log(item);

              $tr = $('#tr');
              var oao = '<tr><td>'+item.openUser+'</td>'
              oao += '<td><a href="{{ url("detailOrder") }}/'+item.id+'">'+item.shop_name+'</a></td>';
              oao += '<td>'+item.end_time+'</td>'
              oao += '<td><a href="{{ url("detailEditPassword") }}/'+item.id+'"class="alert-link">管理</a></td></tr>';
              $tr.after(oao);
          });
      },
      error: function(){
          console.log('哪裡怪怪的');
          } 
  });
  </script>
