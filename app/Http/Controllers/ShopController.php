<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Shop;
use App\Detail;
use App\Order;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ShopController extends Controller
{
    public function index()
    {
        $shop = Detail::select(['shop.shop_name','detail.id','shop.id as shopId','end_time'])
        ->where('end_time', '>', Now())        
        ->join('shop', 'shop.id', '=', 'detail.shop_id')
        ->get();
        return view('welcome', ['shop' => $shop]);
    }
    public function shopIndex()
    {
        $shop = Shop::select(['shop_name','id'])->get();
        return view('shop', ['shop' => $shop]);
    }

    public function addShop()
    {
        return view('addShop');
    }

    public function postAddShop(Request $request)
    {
        $itemsNum = count($request->items);
        $priceNum = count($request->price);
        $price = $request->price;

        if($itemsNum != $priceNum){
            return redirect('/')->withErrors('參數錯誤，請勿留白');
        };
        //檢查商店名稱
        $this->validate($request, [
            'shop' => 'required|max:255|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
        ]);
        //檢查商品
        $itemPattern = "/^[A-Za-z0-9\x7f-\xffA]+$/";
        foreach($request->items as $item) {
            if(!isset($item) || !preg_match($itemPattern, $item, $matches)){
                return redirect('/shop')->withErrors('商品格式錯誤，請勿輸入特殊符號');
            }
        }
        //檢查價格
        foreach($request->price as $priceCheck) {
            if(!isset($item) || !is_numeric($priceCheck)){
                return redirect('/shop')->withErrors('價格格式錯誤，請輸入數字');
            }
        }

        $shopId = Shop::insertGetId(['shop_name' => $request->shop]);

        foreach($request->items as $key => $item)
        {
            Product::create([
                'shop_id' => $shopId,
                'product_name' => $item,
                'product_price' => $price[$key]
            ])->save();
        }
        return redirect('shop');

    }

    public function newDetailPage($id)
    {
        $shop = Shop::find($id);

        if(!isset($shop) || !is_numeric($id)) {
            return redirect('/');
        };
       
        return view('newDetail',['id' => $id]);
    }
    public function newDetail(Request $request)
    {

        $uptime = $request->update .' '.$request->uptime.':00';
        $endtime = $request->enddate .' '.$request->endtime.':00';

        if($uptime > $endtime) {
            return redirect('/')->withErrors('時間錯誤，結束日期不能早於開始日期');
        }
        $this->validate($request, [
            'password' => 'required|regex:/^[A-Za-z0-9]+$/',
            'openUser' => 'required|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
        ]);
        
        Detail::create([
            'shop_id' => $request->id,
            'date' => date("Y-m-d H:i:s"),
            'up_time' => $uptime,
            'end_time' => $endtime,
            'password' => bcrypt($request->password),
            'status' => 'Y',
            'openUser' => $request->openUser
        ])->save();

        return redirect('/');
    }

    public function orderPage($id, $shopId) 
    {
        if( !is_numeric($id) || !is_numeric($shopId)) {
            return redirect('/');
        }
        $products = Product::where('shop_id',$shopId)->get();
        return view('order',['products' => $products, 'detailId' => $id]);
    }

    public function addOrder(Request $request) 
    {
        $products = Product::select('product.id','product.product_name','product.product_price')        
        ->join('detail', 'product.shop_id', '=', 'detail.shop_id')
        ->get();

        foreach($products as $product) {
            //彈性變數
            $amount = 'amount'.$product->id;
            $ps = 'ps'.$product->id;

            $itemPattern = "/^[A-Za-z0-9\x7f-\xffA]+$/";
            $this->validate($request, [
                'name' => 'required|max:255|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
            ]);

            if(isset($request->$ps) && !preg_match($itemPattern, $request->$ps, $matches)) {
                return redirect('/')->withErrors('備註格式錯誤，請勿輸入特殊符號');
            }
            if(isset($request->$amount) && !is_numeric($request->$amount)) {
                return redirect('/')->withErrors('數目格式錯誤，請輸入數字');
            }
            //如果商品存在數量才進行動作
            if(isset($request->$amount)) {
                    $Pss = (isset($request->$ps)) ? $request->$ps : '';
                    Order::create([
                        'detail_id' => $request->id,
                        'order_id' => $product->id,
                        'user' => $request->name,
                        'amount' => $request->$amount,
                        'status' => 'Y',
                        'ps' => $Pss,
                        'product_name' => $product->product_name,
                        'product_price' => $product->product_price,
                    ])->save();
            }
        }
        return redirect('/');
    }

    public function detailOrder($id) 
    {
        if( !is_numeric($id)) {
            return redirect('/');
        }
        $orders = DB::select('SELECT product_name,user,SUM(amount) as amount,product_price FROM `order` WHERE detail_id = ? GROUP BY product_name,user,product_price
        ',[$id]);
        $shopid = Detail::find($id);
        $product = Product::where('shop_id',$shopid->shop_id)->get();
        $newOrder = [];
        //將訂單依商品分類
        foreach($orders as $order)
        {
            $key = $order->product_name;
            $newOrder[$key][] = $order;
        }
        //商品價格
        $newProduct = [];
        foreach($product as $products)
        {
            $key = $products->product_name;
            $newProduct[$key][] = $products;
        }
        return view('detailOrder', ['orders' => $newOrder, 'products' => $newProduct, 'id' => $id]);

    } 
    public function detailOrderUser($id) 
    {
        if( !is_numeric($id)) {
            return redirect('/');
        }
        $users = DB::select('SELECT user,product_name,SUM(amount) as amount, (product_price * SUM(amount)) as total FROM `order` WHERE detail_id = ? GROUP BY user,product_name,product_price',[$id]);
        $newUser = [];
        $price = [];
        $total = 0;
        //依造使用者分類
        foreach($users as $user)
        {
            $key = $user->user;
            $newUser[$key][] = $user;
        } 
        //計算總金額
        $key = '';
        foreach($users as $user)
        {
            if($key == '') $key = $user->user;

            if($user->user == $key) {
                $total += $user->total;
            } else {
                $key = $user->user;
                $total = $user->total;
            }
            $price[$key] = $total;

        }
        
        return view('detailOrderUser', ['users' => $newUser, 'id' => $id, 'price' => $price]);

    } 

    public function allOrder($id) 
    {
        if( !is_numeric($id)) {
            return redirect('/');
        }
        $orders = Order::where('detail_id',$id)->join('product','order.order_id', '=', 'product.id')->get();
        return view('allOrder', ['orders' => $orders,'id' => $id]);
    } 

    public function detailEditPassword($id)
    {
        if( !is_numeric($id)) {
            return redirect('/');
        }
        return view('detailEditPassword',['id' => $id]);
    }

    public function detailEditPage(Request $request) 
    {   
        $detail = Detail::find($request->id);
        if(Hash::check($request->password, $detail->password))
        {
            session()->put('edit'.$request->id, 'Y');
            return view('editDetail',['detail' => $detail, 'id' => $request->id]);
        }
        return redirect('/');
    }
    public function getDetailEditPage($id) 
    {   
        if (!Session::has('edit'.$id) || !is_numeric($id))
        {
            return redirect('/');
        }
        $detail = Detail::find($id);
        return view('editDetail',['detail' => $detail, 'id' => $id]);
    }
    public function detailEdit(Request $request)
    {
        $old = Detail::find($request->id);
        $uptime = $request->update .' '.$request->uptime.':00';
        $endtime = $request->enddate .' '.$request->endtime.':00';

        if($uptime > $endtime) {
            return redirect('/')->withErrors('時間錯誤，結束日期不能早於開始日期');
        }
        
        Detail::where('id' ,$request->id)->update([
            'up_time' => $uptime,
            'end_time' => $endtime,
        ])->save();

        return redirect('/');

    }
    public function detailOrderAdmin($id) 
    {
        if (!Session::has('edit'.$id) || !is_numeric($id))
        {
            return redirect('/');
        }
        $orders = Order::where('detail_id',$id)->get();
        $shopID = Detail::find($id);

        return view('adminAllOrder', ['orders' => $orders,'id' => $id,'shopID' => $shopID->shop_id]);

    }

    public function editOrder(Request $request) 
    {
        
        $this->validate($request, [
            'user' => 'required|max:255|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
            'ps' => 'max:255|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
        ]);
        if(isset($request->amount) && !is_numeric($request->amount)) {
            return redirect('/')->withErrors('數目格式錯誤，請輸入數字');
        }
        
        Order::where('id', $request->id)->update([
            'user' => $request->user,
            'ps' => $request->ps,
            'amount' => $request->amount,
        ]);
        return redirect('/')->withErrors('修改成功');;
    }

    public function editOrderPage($id) 
    {
        $orders = Order::find($id);
        if (!isset($orders) || !Session::has('edit'.$orders->detail_id) || !is_numeric($id))
        {
            return redirect('/');
        }
        $shopID = Detail::find($orders->detail_id);
        if (!Session::has('edit'.$shopID->id) || !is_numeric($id))
        {
            return redirect('/');
        }

        return view('editOrder', ['orders' => $orders,'id' => $id,'shopID' => $shopID->shop_id]);
    }

    public function orderPay($id) 
    {   
        if (!Session::has('edit'.$id) || !is_numeric($id))
        {
            return redirect('/');
        }

        $orders = Order::where('detail_id',$id)->get();
        if(!isset($orders)) {
            return redirect('/');
        }
        $shopID = Detail::find($id);
        
        return view('detailOrderPay', ['orders' => $orders,'id' => $id, 'shopID' => $shopID->shop_id]);
    }
    public function historyPage()
    {
        $shop = Detail::select(['shop.shop_name','detail.id','shop.id as shopId','end_time'])
        ->join('shop', 'shop.id', '=', 'detail.shop_id')
        ->get();
        return view('history', ['shop' => $shop]);
    }

    public function getOrderStatus($id) 
    {   
        if( !is_numeric($id)) {
            return '404';
        }
        $orders = Order::select('id','status','product_price','amount')->where('detail_id',$id)->get();
        return $orders->toJson();
    }

    public function changOrderStatus($id,$status) 
    {   
        $status = ($status == 'N') ? 'Y' : 'N'; 
        $orders = Order::where('id', $id)->update(['status' => $status]);
        return $orders;
    }

    public function allDetail() 
    {   
        $shop = Detail::select(['shop.shop_name','detail.id','shop.id as shopId','end_time','openUser'])
        ->where('end_time', '>', Now())        
        ->join('shop', 'shop.id', '=', 'detail.shop_id')
        ->get();

        return $shop->toJson();
    }
}
