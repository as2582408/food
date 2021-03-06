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
        $shop = Shop::all();
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
            'shop' => 'required|max:255|unique:shop,shop_name|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
            'addUser' => 'required|max:255|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
        ]);
        //檢查商品
        $itemPattern = "/^[A-Za-z0-9\x7f-\xffA]+$/";
        $i = $itemsNum;
        $j = 1;
        foreach($request->items as $item) {
            if(!isset($item) || !preg_match($itemPattern, $item, $matches)){
                return redirect('/shop')->withErrors('商品格式錯誤，請勿輸入特殊符號');
            }
            if($j != $i && $request->items[$j] == $item){
                return redirect('/shop')->withErrors('請勿輸入重複商品');
            }
            $j++;
        }
        //檢查價格
        foreach($request->price as $priceCheck) {
            if(!isset($item) || !is_numeric($priceCheck)){
                return redirect('/shop')->withErrors('價格格式錯誤，請輸入數字');
            }
        }
        $check = Shop::where('shop_name', $request->shop)->first();
        if (isset($check)) {
            return redirect('/shop')->withErrors('商店已存在');
        }

        $shopId = Shop::insertGetId(['shop_name' => $request->shop, 'add_user' => $request->addUser]);

        foreach($request->items as $key => $item)
        {
            Product::create([
                'shop_id' => $shopId,
                'product_name' => $item,
                'product_price' => $price[$key],
                'product_status' => 'Y'
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
        
        return view('newDetail',['id' => $id, 'shopName' => $shop]);
    }
    public function newDetail(Request $request)
    {

        $endtime = $request->enddate .' '.$request->endtime.':00';

        $this->validate($request, [
            'password' => 'required|regex:/^[A-Za-z0-9]+$/',
            'openUser' => 'required|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
        ]);
        
        Detail::create([
            'shop_id' => $request->id,
            'date' => date("Y-m-d H:i:s"),
            'end_time' => $endtime,
            'password' => bcrypt($request->password),
            'status' => 'Y',
            'openUser' => $request->openUser
        ])->save();

        return redirect('/');
    }

    public function orderPage($id, $shopId, $admin) 
    {
        if( !is_numeric($id) || !is_numeric($shopId)) {
            return redirect('/');
        }
        $detail = Detail::find($id);
        //會員超時無法進入
        if($admin == 'user' && $detail->end_time < date("Y-m-d H:i:s")) {
            return redirect('/')->withErrors('該訂單已關閉');
        }
        //管理員要有指定的session才能登入
        if($admin == 'admin' && !Session::has('edit'.$id) ) {
            return redirect('/')->withErrors('認證過期，請重新登入');;
        }
        $shop = Shop::find($shopId);
        $products = Product::where('shop_id',$shopId)->get();
        return view('order',['products' => $products, 'detailId' => $id, 'shopName' => $shop, 'adcheck' => $admin]);
    }

    public function detailOrder($id) 
    {
        if( !is_numeric($id)) {
            return redirect('/');
        }
        $orders = DB::select('SELECT product_name,user,SUM(amount) as amount,product_price,order_id FROM `order` WHERE detail_id = ? GROUP BY product_name,user,product_price,order_id
        ',[$id]);
        $shopid = Detail::find($id);
        $product = Product::where('shop_id',$shopid->shop_id)->get();
        $shop = Shop::find($shopid->shop_id);

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
        return view('detailOrder', ['orders' => $newOrder, 'products' => $newProduct, 'id' => $id, 'shopName' => $shop]);

    } 
    public function detailOrderUser($id) 
    {
        if( !is_numeric($id)) {
            return redirect('/');
        }
        $users = DB::select('SELECT user,product_name,SUM(amount) as amount, (product_price * SUM(amount)) as total,order_id FROM `order` WHERE detail_id = ? GROUP BY user,product_name,product_price,order_id
        ',[$id]);
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
        $shopid = Detail::find($id);
        $shop = Shop::find($shopid->shop_id);
        
        return view('detailOrderUser', ['users' => $newUser, 'id' => $id, 'price' => $price, 'shopName' => $shop]);

    } 

    public function allOrder($id) 
    {
        if( !is_numeric($id)) {
            return redirect('/');
        }
        $orders = Order::where('detail_id',$id)->join('product','order.order_id', '=', 'product.id')->get();
        $shopid = Detail::find($id);
        $shop = Shop::find($shopid->shop_id);
        return view('allOrder', ['orders' => $orders,'id' => $id, 'shopName' => $shop]);
    } 

    public function detailEditPassword($id)
    {
        if( !is_numeric($id)) {
            return redirect('/');
        }
        $shopid = Detail::find($id);
        $shop = Shop::find($shopid->shop_id);

        return view('detailEditPassword',['id' => $id, 'shopName' => $shop]);
    }

    public function detailEditPage(Request $request) 
    {   
        $detail = Detail::find($request->id);
        if(Hash::check($request->password, $detail->password))
        {
            session()->put('edit'.$request->id, 'Y');
            $shopid = Detail::find($request->id);
            $shop = Shop::find($shopid->shop_id);

            return view('editDetail',['detail' => $detail, 'id' => $request->id, 'shopName' => $shop]);
        }
        return redirect('detailEditPassword/'.$request->id)->withErrors('密碼錯誤');
    }
    public function getDetailEditPage($id) 
    {   
        if(!is_numeric($id)) {
            return redirect('/');
        }
        if (!Session::has('edit'.$id))
        {
            return redirect('/')->withErrors('認證過期，請重新登入');
        }
        $detail = Detail::find($id);
        $shop = Shop::find($detail->shop_id);

        return view('editDetail',['detail' => $detail, 'id' => $id, 'shopName' => $shop]);
    }
    public function detailEdit(Request $request)
    {
        $old = Detail::find($request->id);
        $endtime = $request->enddate .' '.$request->endtime.':00';
        $url = 'detailEditPage/'.$request->id;
        
        Detail::where('id' ,$request->id)->update([
            'end_time' => $endtime,
        ]);
        return redirect($url);

    }
    public function detailOrderAdmin($id) 
    {
        if(!is_numeric($id)) {
            return redirect('/');
        }
        if (!Session::has('edit'.$id))
        {
            return redirect('/')->withErrors('認證過期，請重新登入');
        }
        $orders = Order::where('detail_id',$id)->get();
        $shopID = Detail::find($id);
        $shop = Shop::find($shopID->shop_id);

        return view('adminAllOrder', ['orders' => $orders,'id' => $id,'shopID' => $shopID->shop_id, 'shopName' => $shop]);

    }
    public function detailOrderUserAdmin($id) 
    {
        if(!is_numeric($id)) {
            return redirect('/');
        }
        if (!Session::has('edit'.$id))
        {
            return redirect('/')->withErrors('認證過期，請重新登入');;
        }
        $shopID = Detail::find($id);
        $shop = Shop::find($shopID->shop_id);

        $users = DB::select('SELECT user,product_name,SUM(amount) as amount, (product_price * SUM(amount)) as total, order_id FROM `order` WHERE detail_id = ? GROUP BY user,product_name,product_price,order_id',[$id]);
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
        
        return view('detailOrderUserAdmin', ['users' => $newUser, 'id' => $id, 'price' => $price, 'shopID' => $shopID->shop_id, 'shopName' => $shop]);

    } 
    public function detailOrderAdminList($id) 
    {
        if (!is_numeric($id)) {
            return redirect('/');
        }
        if (!Session::has('edit'.$id))
        {
            return redirect('/')->withErrors('認證過期，請重新登入');;
        }
        $orders = DB::select('SELECT product_name,user,SUM(amount) as amount,product_price,order_id FROM `order` WHERE detail_id = ? GROUP BY product_name,user,product_price,order_id
        ',[$id]);
        $shopid = Detail::find($id);
        $shop = Shop::find($shopid->shop_id);
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
        return view('detailOrderAdminList', ['orders' => $newOrder, 'products' => $newProduct, 'id' => $id,'shopID' => $shopid->shop_id, 'shopName' => $shop]);
    }

    public function detailOrderAdminListPay($id) 
    {
        if (!is_numeric($id)) {
            return redirect('/');
        }
        if (!Session::has('edit'.$id))
        {
            return redirect('/')->withErrors('認證過期，請重新登入');;
        }
        $orders = DB::select('SELECT product_name,user,SUM(amount) as amount,product_price,order_id FROM `order` WHERE detail_id = ? GROUP BY product_name,user,product_price,order_id
        ',[$id]);
        $shopid = Detail::find($id);
        $shop = Shop::find($shopid->shop_id);
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
        return view('detailOrderAdminListPay', ['orders' => $newOrder, 'products' => $newProduct, 'id' => $id,'shopID' => $shopid->shop_id, 'shopName' => $shop]);
    }
    public function detailOrderUserAdminPay($id) 
    {
        if(!is_numeric($id)) {
            return redirect('/');
        }
        if (!Session::has('edit'.$id))
        {
            return redirect('/')->withErrors('認證過期，請重新登入');;
        }
        $shopID = Detail::find($id);
        $shop = Shop::find($shopID->shop_id);

        $users = DB::select('SELECT user,product_name,SUM(amount) as amount, (product_price * SUM(amount)) as total, order_id FROM `order` WHERE detail_id = ? GROUP BY user,product_name,product_price,order_id',[$id]);
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
        
        return view('detailOrderUserAdminPay', ['users' => $newUser, 'id' => $id, 'price' => $price, 'shopID' => $shopID->shop_id, 'shopName' => $shop]);

    } 
    public function editOrder(Request $request) 
    {
        
        $this->validate($request, [
            'user' => 'required|max:255|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
            'ps' => 'max:255|regex:/^[A-Za-z0-9\x7f-\xffA]+$/',
        ]);
        if (isset($request->amount) && !is_numeric($request->amount)) {
            return redirect('/')->withErrors('數目格式錯誤，請輸入數字');
        }
        
        Order::where('id', $request->id)->update([
            'user' => $request->user,
            'ps' => $request->ps,
            'amount' => $request->amount,
        ]);
        return redirect()->back()->withErrors('修改成功');;
    }

    public function editOrderPage($id) 
    {
        if (!is_numeric($id)) {
            return redirect('/');
        }
        $orders = Order::find($id);
        if (!isset($orders) || !Session::has('edit'.$orders->detail_id))
        {
            return redirect('/')->withErrors('認證過期，請重新登入');;
        }
        $shopID = Detail::find($orders->detail_id);
        $shop = Shop::find($shopID->shop_id);

        if (!Session::has('edit'.$shopID->id) || !is_numeric($id))
        {
            return redirect('/');
        }

        return view('editOrder', ['orders' => $orders,'id' => $id,'shopID' => $shopID->shop_id,'detailID' => $shopID->id, 'shopName' => $shop]);
    }

    public function orderPay($id) 
    {   
        if (!is_numeric($id)) {
            return redirect('/');
        }
        if (!Session::has('edit'.$id) || !is_numeric($id))
        {
            return redirect('/')->withErrors('認證過期，請重新登入');
        }

        $orders = Order::where('detail_id',$id)->get();
        if(!isset($orders)) {
            return redirect('/');
        }
        $shopID = Detail::find($id);
        $shop = Shop::find($shopID->shop_id);
        
        return view('detailOrderPay', ['orders' => $orders,'id' => $id, 'shopID' => $shopID->shop_id, 'shopName' => $shop]);
    }
    public function historyPage()
    {
        $shop = Detail::select(['shop.shop_name','detail.id','shop.id as shopId','end_time','openUser'])
        ->join('shop', 'shop.id', '=', 'detail.shop_id')
        ->get();
        return view('history', ['shop' => $shop]);
    }

    public function getOrderStatus($id) 
    {   
        if (!is_numeric($id)) {
            return '404';
        }
        $orders = Order::select('id','status','product_price','amount')->where('detail_id',$id)->get();
        return $orders->toJson();
    }

    public function allOrderAjax($id) 
    {   
        if ( !is_numeric($id)) {
            return '404';
        }
        $orders = Order::where('detail_id',$id)->join('product','order.order_id', '=', 'product.id')->get();
        return $orders->toJson();
    }

    public function changOrderStatus($id) 
    {   
        if (!is_numeric($id)) {
            return false;
        }
        $orderStatus = Order::select('status')->where('id', $id)->first();
        $status = ($orderStatus->status == 'N') ? 'Y' : 'N'; 
        Order::where('id', $id)->update(['status' => $status]);
        return $status;
    }

    public function allDetail() 
    {   
        $shop = Detail::select(['shop.shop_name','detail.id','shop.id as shopId','end_time','openUser'])
        ->where('end_time', '>', Now())        
        ->join('shop', 'shop.id', '=', 'detail.shop_id')
        ->get();

        return $shop->toJson();
    }
    public function hisAllDetail() 
    {   
        $shop = Detail::select(['shop.shop_name','detail.id','shop.id as shopId','end_time','openUser'])
        ->join('shop', 'shop.id', '=', 'detail.shop_id')
        ->orderBy('end_time', 'asc')
        ->get();

        return $shop->toJson();
    }

    public function delOrder($id) 
    {   
        $detailID = Order::find($id);
        if (!Session::has('edit'.$detailID->detail_id) || !is_numeric($id))
        {
            return redirect('/');
        }
        Order::where('id', $id)->delete();
        return redirect()->back()->withErrors('刪除成功');
    }

    public function shopContent($id)
    {
        if (!is_numeric($id))
        {
            return redirect('/');
        }

        $products = Product::where('shop_id', $id)->get();
        $shop = Shop::find($id);
        return view('shopContent', ['products' => $products, 'shopName' => $shop]);
    }

    public function editShop($id)
    {
        if (!is_numeric($id))
        {
            return redirect('shop');
        }

        $products = Product::where('shop_id', $id)->get();
        $shop = Shop::find($id);

        return view('editShop', ['products' => $products, 'shopID' => $id, 'shopName' => $shop]);
    }
    public function delete($id)
    {
        if (!is_numeric($id))
        {
            return redirect('shop');
        }

        Product::where('id', $id)->delete();
        Order::where('order_id',$id)->delete();

        return redirect()->back()->withErrors('刪除成功');
    }
    public function addProducts(Request $request)
    {
        if(!isset($request->items) || !isset($request->price)) {
            return redirect()->back()->withErrors('參數錯誤，請勿留白');
        }
        $itemsNum = count($request->items);
        $priceNum = count($request->price);
        $price = $request->price;

        if($itemsNum != $priceNum){
            return redirect()->back()->withErrors('參數錯誤，請勿留白');
        };
        //檢查商品
        $itemPattern = "/^[A-Za-z0-9\x7f-\xffA]+$/";
        $i = $itemsNum;
        $j = 1;
        foreach($request->items as $item) {
            if(!isset($item) || !preg_match($itemPattern, $item, $matches)){
                return redirect()->back()->withErrors('商品格式錯誤，請勿輸入特殊符號');
            }
            if($j != $i && $request->items[$j] == $item){
                return redirect()->back()->withErrors('請勿輸入重複商品');
            }
            $j++;
        }
        //檢查價格
        foreach($request->price as $priceCheck) {
            if(!isset($item) || !is_numeric($priceCheck)){
                return redirect()->back()->withErrors('價格格式錯誤，請輸入數字');
            }
        }
        
        foreach($request->items as $item) {
            $products = Product::select('product_name')->where([['shop_id', $request->id],['product_name',$item]])->first();
            if(isset($products)){
                return redirect()->back()->withErrors('請勿輸入重複商品');
            }
        }

        foreach($request->items as $key => $item)
        {
            Product::create([
                'shop_id' => $request->id,
                'product_name' => $item,
                'product_price' => $price[$key]
            ])->save();
        }
        $url = 'shopContent/'.$request->id;
        return redirect($url)->withErrors('新增成功');
    }

    public function ajaxDetailOrderUser($id)
    {
        $users = DB::select('SELECT user,order_id FROM `order` WHERE detail_id = ? AND ps != "" GROUP BY user,product_name,product_price,order_id
        ',[$id]);

        return $users;
    }

    public function ajaxOrderUser($id)
    {
        $idarr = mb_split('-', $id);
        $ps = DB::select('SELECT ps,amount FROM `order` WHERE user = ? and order_id = ?',[$idarr[0], $idarr[1]]);
        return $ps;
    }

    public function AjaxGetOrderStatus($id) 
    {   
        if (!is_numeric($id)) {
            return '404';
        }
        $orders = DB::select('SELECT product_name,user,SUM(amount) as amount,product_price,order_id FROM `order` WHERE detail_id = ? AND status="Y" GROUP BY product_name,user,product_price,order_id
        ',[$id]);
        return $orders;
    }

    public function AjaxChangGetOrderStatus($id) 
    {   
        $idarr = mb_split('-', $id);
        $re = Order::where([['order_id', $idarr[1]],['user',$idarr[0]]])->update(['status' => 'Y']);

        return $re;
    }

    public function AjaxChangGetOrderStatusY($id) 
    {   
        $idarr = mb_split('-', $id);
        $re = Order::where([['order_id', $idarr[1]],['user',$idarr[0]]])->update(['status' => 'N']);

        return $re;
    }


    //
    public function AjaxGetOrderStatusUser($id) 
    {   
        if (!is_numeric($id)) {
            return '404';
        }
        $orders = DB::select('SELECT user FROM `order` WHERE detail_id = 6 AND status="Y" GROUP BY user',[$id]);
        return $orders;
    }
    public function AjaxChangGetOrderStatusUser($id) 
    {   
        $idarr = mb_split('-', $id);
        $re = Order::where([['detail_id', $idarr[1]],['user',$idarr[0]]])->update(['status' => 'Y']);

        return $re;
    }

    public function AjaxChangGetOrderStatusUserY($id) 
    {   
        $idarr = mb_split('-', $id);
        $re = Order::where([['detail_id', $idarr[1]],['user',$idarr[0]]])->update(['status' => 'N']);

        return $re;
    }

    public function test(Request $request) 
    {
        $itemsNum = count($request->items);
        $priceNum = count($request->price);
        $price = $request->price;

        if($itemsNum != $priceNum){
            return '參數錯誤，請勿留白';
        };
        //檢查商品
        $itemPattern = "/^[A-Za-z0-9\x7f-\xffA]+$/";
        $i = $itemsNum;
        $j = 1;
        if(!isset($request->shop) || !preg_match($itemPattern, $request->shop, $matches)){
            return '商店格式錯誤，請勿輸入特殊符號';
        }
        if(!isset($request->addUser) || !preg_match($itemPattern, $request->addUser, $matches)){
            return '人員格式錯誤，請勿輸入特殊符號';
        }
        foreach($request->items as $item) {
            if(!isset($item)) {
                return '商品格式錯誤，請勿留白';
            }
            if(!preg_match($itemPattern, $item, $matches)){
                return '商品格式錯誤，請勿輸入特殊符號';
            }
            if($j != $i && $request->items[$j] == $item){
                return '請勿輸入重複商品';
            }
            $j++;
        }
        //檢查價格
        foreach($request->price as $priceCheck) {
            if(!isset($item) || !is_numeric($priceCheck)){
                return '價格格式錯誤，請輸入數字';
            }
        }
        $check = Shop::where('shop_name', $request->shop)->first();
        if (isset($check)) {
            return '商店已存在';
        }

        $shopId = Shop::insertGetId(['shop_name' => $request->shop, 'add_user' => $request->addUser]);

        foreach($request->items as $key => $item)
        {
            Product::create([
                'shop_id' => $shopId,
                'product_name' => $item,
                'product_price' => $price[$key],
                'product_status' => 'Y'
            ])->save();
        }
        return '200';
        }
}
