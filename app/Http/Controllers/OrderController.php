<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserAddress;
use App\Visitor;
use App\Product;
use App\Cart;
use App\Order;
use App\Wallet;
use App\OrderItem;
use App\DeliveryArea;
use Illuminate\Support\Facades\Validator;
use App\Helpers\APIHelpers;
use App\Shop;
use App\ProductMultiOption;
use App\MainOrder;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api' , ['except' => ['excute_pay']]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required',
            'address_id' => 'required',
            'payment_method' => 'required'
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة'  , null , $request->lang);
            return response()->json($response , 406);
        }

        $user_id = auth()->user()->id;
        if (auth()->user()->active == 0) {
            $response = APIHelpers::createApiResponse(true , 406 , 'User is not active' , 'تم حظر المستخدم'  , null , $request->lang);
            return response()->json($response , 406);
        }
        $visitor  = Visitor::where('unique_id' , $request->unique_id)->first();
        $user_id_unique_id = $visitor->user_id;
        $visitor_id = $visitor->id;
        $cart = Cart::where('visitor_id' , $visitor_id)->get();

        if(count($cart) == 0){
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة'  , null , $request->lang);
            return response()->json($response , 406);
        }
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $main_order_number = substr(str_shuffle(uniqid() . $str) , -9);
        $address = UserAddress::where('id', auth()->user()->main_address_id)->first();

        $stores = Shop::join('products', 'products.store_id', '=', 'shops.id')
            ->where('carts.visitor_id', $visitor_id)
            ->leftjoin('carts', function($join) {
                $join->on('carts.product_id', '=', 'products.id');
            })
            ->pluck('shops.id')
            ->toArray();
        $unrepeated_stores1 = array_unique($stores);
        $unrepeated_stores = [];
        foreach ($unrepeated_stores1 as $key => $value) {
			array_push($unrepeated_stores, $value);
		}
        for ($n = 0; $n < count($cart); $n ++) {
            if($cart[$n]->product->remaining_quantity < $cart[$n]['count']){
                $response = APIHelpers::createApiResponse(true , 406 , 'The remaining amount of the product is not enough' , 'الكميه المتبقيه من المنتج غير كافيه'  , null , $request->lang);
                return response()->json($response , 406);
            }
        }
        if($request->payment_method == 2){
            $main_order = MainOrder::create([
                'user_id' => auth()->user()->id,
                'address_id' => $request->address_id,
                'payment_method' => $request->payment_method,
                'main_order_number' => $main_order_number
            ]);
            if (count($stores) > 0) {

                for ($i = 0; $i < count($unrepeated_stores); $i ++) {
                    $store_products = Cart::where('store_id', $unrepeated_stores[$i])->where('visitor_id', $visitor_id)->get();

                    $pluck_products = Cart::where('store_id', $unrepeated_stores[$i])->where('visitor_id', $visitor_id)->pluck('product_id')->toArray();
                    if (count($store_products) > 0) {
                        $subtotal_price = 0;
                        for ($n = 0; $n < count($store_products); $n ++) {
                            if($store_products[$n]->product->remaining_quantity < $store_products[$n]['count']){
                                $d_main_order = MainOrder::find($main_order['id']);
                                $d_main_order->delete();
                                $response = APIHelpers::createApiResponse(true , 406 , 'The remaining amount of the product is not enough' , 'الكميه المتبقيه من المنتج غير كافيه'  , null , $request->lang);
                                return response()->json($response , 406);
                            }
                            $single_product = Product::select('id', 'remaining_quantity')->where('id', $store_products[$n]['product_id'])->first();
                            $single_product->remaining_quantity = $single_product->remaining_quantity - $store_products[$n]['count'];
                            $single_product->save();

                            $subtotal_price = $subtotal_price + ($store_products[$n]->product->final_price * $store_products[$n]['count']);

                        }
                    }

                    $delivery = DeliveryArea::select('delivery_cost', 'arrival_to', 'arrival_from')->where('area_id', $address['area_id'])->where('store_id', $unrepeated_stores[$i])->first();

                    if (!isset($delivery['delivery_cost'])) {
                        $delivery = Setting::find(1);
                    }
                    $total_cost = $delivery['delivery_cost'] + $subtotal_price;

                    $order = Order::create([
                            'user_id' => auth()->user()->id,
                            'address_id' => $request->address_id,
                            'status' => 1,
                            'payment_method' => $request->payment_method,
                            'subtotal_price' => $subtotal_price,
                            'delivery_cost' => $delivery['delivery_cost'],
                            'total_price' => $total_cost,
                            'order_number' => substr(str_shuffle(uniqid() . $str) , -9),
                            'store_id' => $unrepeated_stores[$i],
                            'arrival_from' => $delivery['arrival_from'],
                            'arrival_to' => $delivery['arrival_to'],
                            'main_id' => $main_order['id']
                        ]);

                        for($k = 0; $k < count($store_products); $k++){
                            $product_data = Product::select('final_price', 'price_before_offer')->where('id', $store_products[$k]['product_id'])->first();
                            $order_item =  OrderItem::create([
                                'order_id' => $order->id,
                                'product_id' => $store_products[$k]['product_id'],
                                'price_before_offer' => $product_data['price_before_offer'],
                                'final_price' => $product_data['final_price'],
                                'count' => $store_products[$k]['count']
                            ]);
                            // empty cart
                            Cart::where('product_id',$store_products[$k]['product_id'])->where('visitor_id',$visitor_id)->delete();
                        }
                }
            }
            $u_main_order = MainOrder::find($main_order['id']);
            // dd($main_order->orders->sum('delivery_cost'));
            $u_main_order->update([
                'subtotal_price' => $main_order->orders->sum('subtotal_price'),
                'delivery_cost' => $main_order->orders->sum('delivery_cost'),
                'total_price' => $main_order->orders->sum('total_price')
            ]);
			$data=(object)['url' => ''];
            $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $data , $request->lang);
            return response()->json($response , 200);
        }else if ($request->payment_method == 1) {
            $main_order = MainOrder::create([
                'user_id' => auth()->user()->id,
                'address_id' => $request->address_id,
                'payment_method' => $request->payment_method,
                'main_order_number' => $main_order_number
            ]);
            $ordersIds = [];
            if (count($stores) > 0) {
                $total_price = 0;
                for ($i = 0; $i < count($unrepeated_stores); $i ++) {
                    $store_products = Cart::where('store_id', $unrepeated_stores[$i])->where('visitor_id', $visitor_id)->get();
                    
                    $pluck_products = Cart::where('store_id', $unrepeated_stores[$i])->pluck('product_id')->toArray();
                    if (count($store_products) > 0) {
                        $subtotal_price = 0;
                        for ($n = 0; $n < count($store_products); $n ++) {
                            if($store_products[$n]->product->remaining_quantity < $cart[$n]['count']){
                                $d_main_order = MainOrder::find($main_order['id']);
                                $d_main_order->delete();
                                $response = APIHelpers::createApiResponse(true , 406 , 'The remaining amount of the product is not enough' , 'الكميه المتبقيه من المنتج غير كافيه'  , null , $request->lang);
                                return response()->json($response , 406);
                            }
                            $single_product = Product::select('id', 'remaining_quantity')->where('id', $store_products[$n]['product_id'])->first();
                            $single_product->remaining_quantity = $single_product->remaining_quantity - $store_products[$n]['count'];
                            $single_product->save();
                            $subtotal_price = $subtotal_price + ($store_products[$n]->product->final_price * $store_products[$n]['count']);
                            
                        }
                    }
    
                    $shop = Shop::where('id', $unrepeated_stores[$i])->select('min_order_cost')->first();
                    if ($subtotal_price < $shop['min_order_cost']) {
                        $d_main_order = MainOrder::find($main_order['id']);
                        $d_main_order->delete();
                        $response = APIHelpers::createApiResponse(true , 406 , 'Minimum Order Cost for Store ' . $shop['name'] . ' is ' . $shop['min_order_cost'] . ' KWD' , 'الحد الأدنى لقيمة الطلب من متجر ' . $shop['name'] . ' هو ' . $shop['min_order_cost'] . ' د.ك'  , null , $request->lang);
                        return response()->json($response , 406);
                    }
                    
                    $delivery = DeliveryArea::select('delivery_cost')->where('area_id', $address['area_id'])->where('store_id', $unrepeated_stores[$i])->first();
                    
                    if (!isset($delivery['delivery_cost'])) {
                        $delivery = Setting::find(1);
                    }
                    $total_cost = $delivery['delivery_cost'] + $subtotal_price;
                    $total_price = $total_price + $total_cost;
                    
                    $order = Order::create([
                            'user_id' => auth()->user()->id,
                            'address_id' => $request->address_id,
                            'status' => 1,
                            'payment_method' => $request->payment_method,
                            'subtotal_price' => $subtotal_price,
                            'delivery_cost' => $delivery['delivery_cost'],
                            'total_price' => $total_cost,
                            'order_number' => substr(str_shuffle(uniqid() . $str) , -9),
                            'store_id' => $unrepeated_stores[$i],
                            'arrival_from' => $delivery['arrival_from'],
                            'arrival_to' => $delivery['arrival_to'],
                            'main_id' => $main_order['id']
                        ]);
                    $count = 0;
                    for($k = 0; $k < count($store_products); $k++){
                        
                        $product_data = Product::select('final_price', 'price_before_offer')->where('id', $store_products[$k]['product_id'])->first();
                        
                        $order_item =  OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $store_products[$k]['product_id'],
                            'price_before_offer' => number_format((float)$product_data['price_before_offer'], 3, '.', ''),
                            'final_price' => number_format((float)$product_data['final_price'], 3, '.', ''),
                            'count' => $store_products[$k]['count']
                        ]);
                        
                        $count = $count + $store_products[$k]['count'];
                                              
                    }
                    array_push($ordersIds, $order['id']);
                    
                }
            }
            $u_main_order = MainOrder::find($main_order['id']);
            $user = auth()->user();
            
            if ($user->my_wallet < $total_price) {
                for ($p = 0; $p < count($u_main_order->orders); $p ++) {
                    $u_main_order->orders[$p]->delete();
                }
                $u_main_order->delete();
                $response = APIHelpers::createApiResponse(true , 406 , 'Not Enough wallet balance' , 'رصيد المحفظة لا يكفى' , null , $request->lang);
                return response()->json($response , 406);
            }else {
                for ($p = 0; $p < count($cart); $p++) {
                    $cart[$p]->delete();
                }
                $toPrice = number_format((float)$total_price, 3, '.', '');
                $user->my_wallet = $user->my_wallet - $toPrice;
                $user->save();
            }
            $subTPrice = number_format((float)$main_order->orders->sum('subtotal_price'), 3, '.', '');
            $dCost = number_format((float)$main_order->orders->sum('delivery_cost'), 3, '.', '');
            $tPrice = number_format((float)$main_order->orders->sum('total_price'), 3, '.', '');
            $u_main_order->update([
                'subtotal_price' => $subTPrice,
                'delivery_cost' => $dCost,
                'total_price' => $tPrice
            ]);

            $data = (object)[
                "url" => "",
                'main_order_number' => $u_main_order['main_order_number'],
                'count' => $count,
                'date' => $u_main_order['created_at']->format('Y-m-d'),
                'time' => $u_main_order['created_at']->format('g:i A'),
                'total_cost' => number_format((float)$main_order->orders->sum('total_price'), 3, '.', '')
            ];

            $response = APIHelpers::createApiResponse(false , 200 ,  '' , '' , $data , $request->lang );
            return response()->json($response , 200);
        }else {
            if (count($stores) > 0) {
                $total_price = 0;
                for ($i = 0; $i < count($unrepeated_stores); $i ++) {
                    $store_products = Cart::where('visitor_id',$visitor_id)->where('store_id', $unrepeated_stores[$i])->get();

                    $pluck_products = Cart::where('visitor_id',$visitor_id)->where('store_id', $unrepeated_stores[$i])->pluck('product_id')->toArray();
                    if (count($store_products) > 0) {
                        $subtotal_price = 0;
                        for ($n = 0; $n < count($store_products); $n ++) {
                            if($store_products[$n]->product->remaining_quantity < $store_products[$n]['count']){

                                $response = APIHelpers::createApiResponse(true , 406 , 'The remaining amount of the product is not enough' , 'الكميه المتبقيه من المنتج غير كافيه'  , null , $request->lang);
                                return response()->json($response , 406);
                            }
                            $single_product = Product::select('id', 'remaining_quantity')->where('id', $store_products[$n]['product_id'])->first();
                            $single_product->remaining_quantity = $single_product->remaining_quantity - $store_products[$n]['count'];
                            $single_product->save();
                            if ($store_products[$n]['option_id'] != 0) {
                                $m_option = ProductMultiOption::find($store_products[$n]['option_id']);
                                $subtotal_price = $subtotal_price + ($m_option['final_price'] * $store_products[$n]['count']);
                                $m_option->remaining_quantity = $m_option->remaining_quantity - $store_products[$n]['count'];
                            }else {
                                $subtotal_price = $subtotal_price + ($store_products[$n]->product->final_price * $store_products[$n]['count']);
                            }
                        }
                    }

                    $max_period = Product::join('carts', 'carts.product_id', '=', 'products.id')
                    ->whereIn('products.id', $pluck_products)
                    ->where('carts.visitor_id', $visitor_id)
                    ->select('products.id', DB::raw('MAX(products.order_period) AS max_period'), 'carts.count')
                    ->groupBy('products.id')
                    ->groupBy('carts.count')
                    ->orderBy('max_period', 'desc')
                    ->first();

                    $min_period = Product::join('carts', 'carts.product_id', '=', 'products.id')
                    ->whereIn('products.id', $pluck_products)
                    ->where('carts.visitor_id', $visitor_id)
                    ->select('products.id', DB::raw('MIN(products.order_period) AS min_period'), 'carts.count')
                    ->groupBy('products.id')
                    ->groupBy('carts.count')
                    ->orderBy('min_period', 'asc')
                    ->first();

                    $today = Carbon::now();
                    $current_day = Carbon::now();
                    $max_total_period = $max_period['count'] * $max_period['max_period'];
                    $min_total_period = $min_period['count'] * $min_period['min_period'];
                    if ($max_total_period > $min_total_period) {
                        $to_deliver_date = $today->addDays($max_total_period)->format('Y-m-d');
                        $from_deliver_date = $current_day->addDays($min_total_period)->format('Y-m-d');
                    }else if($max_total_period < $min_total_period) {
                        $from_deliver_date = $today->addDays($max_total_period)->format('Y-m-d');
                        $to_deliver_date = $current_day->addDays($min_total_period)->format('Y-m-d');
                    }else if($max_total_period == $min_total_period) {
                        $from_deliver_date = $today->addDays(1)->format('Y-m-d');
                        $to_deliver_date = $current_day->addDays($max_total_period)->format('Y-m-d');
                    }
                    // dd($to_deliver_date);
                    $delivery = DeliveryArea::select('delivery_cost')->where('area_id', $address['area_id'])->where('store_id', $unrepeated_stores[$i])->first();
                    if (!isset($delivery['delivery_cost'])) {
                        $delivery = Setting::find(1);
                    }
                    $total_cost = $delivery['delivery_cost'] + $subtotal_price;

                    $total_price = $total_price + $total_cost;
                }
            }

            $root_url = $request->root();
        	$user = auth()->user();

            $path='https://apitest.myfatoorah.com/v2/SendPayment';
			$token="bearer rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";

        $headers = array(
            'Authorization:' .$token,
            'Content-Type:application/json'
        );
            $price = $total_price;
            $call_back_url = $root_url."/api/order/excute_pay?user_id=".$user->id."&unique_id=".$request->unique_id."&address_id=".$request->address_id."&payment_method=".$request->payment_method;
            $error_url = $root_url."/api/pay/error";
            $fields =array(
				"CustomerName" => $user->name,
				"NotificationOption" => "LNK",
				"InvoiceValue" => $price,
				"CallBackUrl" => $call_back_url,
				"ErrorUrl" => $error_url,
				"Language" => "AR",
				"CustomerEmail" => $user->email
        	);

            $payload =json_encode($fields);
            $curl_session =curl_init();
            curl_setopt($curl_session,CURLOPT_URL, $path);
            curl_setopt($curl_session,CURLOPT_POST, true);
            curl_setopt($curl_session,CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl_session,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl_session,CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_session,CURLOPT_IPRESOLVE, CURLOPT_IPRESOLVE);
            curl_setopt($curl_session,CURLOPT_POSTFIELDS, $payload);

            $result=curl_exec($curl_session);
			//dd($result);
            curl_close($curl_session);
            $result = json_decode($result);
            // dd($result);
            $data['url'] = $result->Data->InvoiceURL;

            $response = APIHelpers::createApiResponse(false , 200 ,  '' , '' , $data , $request->lang );
            return response()->json($response , 200);
        }

    }

    public function excute_pay(Request $request){
        $user = User::find($request->user_id);
        $user_id = $user->id;
        $visitor  = Visitor::where('unique_id' , $request->unique_id)->first();
        $user_id_unique_id = $visitor->user_id;
        $visitor_id = $visitor->id;
        $cart = Cart::where('visitor_id' , $visitor_id)->get();

        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $main_order_number = substr(str_shuffle(uniqid() . $str) , -9);
        $address = UserAddress::select('area_id')->find($request->address_id);
        $stores = Shop::join('products', 'products.store_id', '=', 'shops.id')
            ->where('carts.visitor_id', $visitor_id)
            ->leftjoin('carts', function($join) {
                $join->on('carts.product_id', '=', 'products.id');
            })
            ->pluck('shops.id')
            ->toArray();
        $unrepeated_stores1 = array_unique($stores);
        $unrepeated_stores = [];
        foreach ($unrepeated_stores1 as $key => $value) {
			array_push($unrepeated_stores, $value);
		}
        $main_order = MainOrder::create([
            'user_id' => $request->user_id,
            'address_id' => $request->address_id,
            'payment_method' => $request->payment_method,
            'main_order_number' => $main_order_number
        ]);
        if (count($stores) > 0) {
            for ($i = 0; $i < count($unrepeated_stores); $i ++) {
                $store_products = Cart::where('store_id', $unrepeated_stores[$i])->where('visitor_id', $visitor_id)->get();

                $pluck_products = Cart::where('store_id', $unrepeated_stores[$i])->pluck('product_id')->toArray();
                if (count($store_products) > 0) {
                    $subtotal_price = 0;
                    for ($n = 0; $n < count($store_products); $n ++) {
                        if($store_products[$n]->product->remaining_quantity < $cart[$n]['count']){
                            $d_main_order = MainOrder::find($main_order['id']);
                            $d_main_order->delete();
                            $response = APIHelpers::createApiResponse(true , 406 , 'The remaining amount of the product is not enough' , 'الكميه المتبقيه من المنتج غير كافيه'  , null , $request->lang);
                            return response()->json($response , 406);
                        }
                        $single_product = Product::select('id', 'remaining_quantity')->where('id', $store_products[$n]['product_id'])->first();
                        $single_product->remaining_quantity = $single_product->remaining_quantity - $store_products[$n]['count'];
                        $single_product->save();
                        if ($store_products[$n]['option_id'] != 0) {
                            $m_option = ProductMultiOption::find($store_products[$n]['option_id']);
                            $subtotal_price = $subtotal_price + ($m_option['final_price'] * $store_products[$n]['count']);
                            $m_option->remaining_quantity = $m_option->remaining_quantity - $store_products[$n]['count'];
                        }else {
                            $subtotal_price = $subtotal_price + ($store_products[$n]->product->final_price * $store_products[$n]['count']);
                        }
                    }
                }

                $max_period = Product::join('carts', 'carts.product_id', '=', 'products.id')
                ->whereIn('products.id', $pluck_products)
                ->where('carts.visitor_id', $visitor_id)
                ->select('products.id', DB::raw('MAX(products.order_period) AS max_period'), 'carts.count')
                ->groupBy('products.id')
                ->groupBy('carts.count')
                ->orderBy('max_period', 'desc')
                ->first();

                $min_period = Product::join('carts', 'carts.product_id', '=', 'products.id')
                ->whereIn('products.id', $pluck_products)
                ->where('carts.visitor_id', $visitor_id)
                ->select('products.id', DB::raw('MIN(products.order_period) AS min_period'), 'carts.count')
                ->groupBy('products.id')
                ->groupBy('carts.count')
                ->orderBy('min_period', 'asc')
                ->first();

                $today = Carbon::now();
                $current_day = Carbon::now();
                $max_total_period = $max_period['count'] * $max_period['max_period'];
                $min_total_period = $min_period['count'] * $min_period['min_period'];
                if ($max_total_period > $min_total_period) {
                    $to_deliver_date = $today->addDays($max_total_period)->format('Y-m-d');
                    $from_deliver_date = $current_day->addDays($min_total_period)->format('Y-m-d');
                }else if($max_total_period < $min_total_period) {
                    $from_deliver_date = $today->addDays($max_total_period)->format('Y-m-d');
                    $to_deliver_date = $current_day->addDays($min_total_period)->format('Y-m-d');
                }else if($max_total_period == $min_total_period) {
                    $from_deliver_date = $today->addDays(1)->format('Y-m-d');
                    $to_deliver_date = $current_day->addDays($max_total_period)->format('Y-m-d');
                }
                // dd($to_deliver_date);
                $delivery = DeliveryArea::select('delivery_cost')->where('area_id', $address['area_id'])->where('store_id', $unrepeated_stores[$i])->first();
                if (!isset($delivery['delivery_cost'])) {
                    $delivery = Setting::find(1);
                }
                $total_cost = $delivery['delivery_cost'] + $subtotal_price;

                $order = Order::create([
                    'user_id' => $request->user_id,
                    'address_id' => $request->address_id,
                    'status' => 1,
                    'payment_method' => $request->payment_method,
                    'subtotal_price' => $subtotal_price,
                    'delivery_cost' => $delivery['delivery_cost'],
                    'total_price' => $total_cost,
                    'order_number' => substr(str_shuffle(uniqid() . $str) , -9),
                    'store_id' => $unrepeated_stores[$i],
                    'arrival_from' => $delivery['arrival_from'],
                    'arrival_to' => $delivery['arrival_to'],
                    'main_id' => $main_order['id']
                ]);

                    for($k = 0; $k < count($store_products); $k++){
                        $product_data = Product::select('final_price', 'price_before_offer')->where('id', $store_products[$k]['product_id'])->first();
                        $order_item =  OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $store_products[$k]['product_id'],
                            'price_before_offer' => $product_data['price_before_offer'],
                            'final_price' => $product_data['final_price'],
                            'count' => $store_products[$k]['count']
                        ]);
                        // empty cart
                        Cart::where('product_id',$store_products[$k]['product_id'])->where('visitor_id',$visitor_id)->delete();
                    }
            }
        }
        $u_main_order = MainOrder::find($main_order['id']);
        $u_main_order->update([
            'subtotal_price' => $main_order->orders->sum('subtotal_price'),
            'delivery_cost' => $main_order->orders->sum('delivery_cost'),
            'total_price' => $main_order->orders->sum('total_price')
        ]);
        return redirect('api/pay/success');
    }

    public function getorders(Request $request){
        $user_id = auth()->user()->id;
        $orders = MainOrder::where('user_id' , $user_id)->select('id' , 'total_price' , 'main_order_number' , 'created_at as date')->orderBy('id' , 'desc')->get();
        $orderDates = MainOrder::where('user_id' , $user_id)->pluck('created_at')->toArray();
        $ordersDays = [];
        for ($k = 0; $k < count($orderDates); $k ++) {
            $ordersDays[$k] = date_format(date_create($orderDates[$k]) , "d-m-Y");
        }

        $unrepeated_days1 = array_unique($ordersDays);
		$unrepeated_days = [];
        foreach ($unrepeated_days1 as $key => $value) {
			array_push($unrepeated_days, $value);
        }
        $data = [];

        for ($n = 0; $n < count($unrepeated_days); $n ++) {
            $dayOrders = [];

            for($i = 0; $i < count($orders); $i++){
                if ($unrepeated_days[$n] == date_format(date_create($orders[$i]['date']), "d-m-Y")) {
                    $items = OrderItem::join('orders','orders.id', '=','order_items.order_id')
                    ->where('main_orders.id', $orders[$i]['id'])
                    ->leftjoin('main_orders', function($join) {
                        $join->on('main_orders.id', '=', 'orders.main_id');
                    })
                    ->select(DB::raw('SUM(order_items.count) as cnt'), 'order_items.product_id as pId')
                    ->groupBy('order_items.count')
                    ->groupBy('order_items.product_id')
                    ->get();

                    $orders[$i]['count'] = $items->sum('cnt');
                    $date = date_create($orders[$i]['date']);

                    $orderDate = date_format($date , "d-m-Y");
                    $dayOrder = (object)[
                        'id' => $orders[$i]['id'],
                        'count' => count($items),
                        'date' => $orderDate,
                        'time' => date('H:i A',strtotime($orders[$i]['date'])),
                        // 'total_price' => $orders[$i]['total_price'],
                        'main_order_number' => $orders[$i]['main_order_number']
                    ];

                    array_push($dayOrders, $dayOrder);
                }

            }
            $data[$n]['day'] = $unrepeated_days[$n];
            if ($unrepeated_days[$n] == Carbon::today()->format('d-m-Y')) {
                $today = 'Today';
                if ($request->lang == 'ar') {
                    $today = 'اليوم';
                }
                $data[$n]['day'] = $today;
            }

            $data[$n]['orders'] = $dayOrders;
        }

        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $data , $request->lang);
        return response()->json($response , 200);
    }

    public function pay_sucess(){
        return "Please wait ...";
    }

    public function pay_error(){
        return "Please wait ...";
    }

    public function orderdetails(Request $request){
        $order_id = $request->id;
        $order = MainOrder::select('id', 'payment_method', 'subtotal_price', 'delivery_cost', 'total_price', 'status', 'main_order_number', 'address_id', 'created_at')->where('id', $order_id)->first()->makeHidden(['address_id', 'orders_with_select', 'created_at']);
        $mainOrderDate = date_create($order['created_at']);
        $order['date'] = date_format($mainOrderDate , "d-m-Y");
        $address = UserAddress::find($order['address_id'])->makeHidden(['area_id', 'area_with_select', 'created_at', 'updated_at']);
        $data['order'] = $order;
        $stores = $order->orders_with_select->makeHidden(['store', 'oItems', 'from_deliver_date', 'to_deliver_date', 'main_id', 'created_at']);
        if (count($stores) > 0) {
            for ($i = 0; $i < count($stores); $i ++) {

                $stores[$i]['shipment_number'] = $i + 1;
				$orderDate = date_create($stores[$i]['created_at']);
//                $details = (object)[
//                    "subtotal_price" => $stores[$i]['subtotal_price'],
//                    "delivery_cost" => $stores[$i]['delivery_cost'],
//                    "total_price" => $stores[$i]['total_price'],
//                    "order_number" => $stores[$i]['order_number'],
//                    "id" => $stores[$i]['id']
//                ];
                $products = [];

                if (count($stores[$i]->oItems) > 0) {
                    for ($n = 0; $n < count($stores[$i]->oItems); $n ++) {
                        $stores[$i]->oItems[$n]['product'] = $stores[$i]->oItems[$n]->product_with_select->makeHidden(['mainImage', 'multi_options']);
                        $stores[$i]->oItems[$n]['product']['store_name'] = $stores[$i]->store->name;
                        $stores[$i]->oItems[$n]['product']['count'] = $stores[$i]->oItems[$n]['count'];
                        $stores[$i]->oItems[$n]['product']['status'] = $stores[$i]->oItems[$n]['status'];
                        $stores[$i]->oItems[$n]['product']['image'] = $stores[$i]->oItems[$n]->product_with_select->mainImage['image'];
                        array_push($products, $stores[$i]->oItems[$n]->product_with_select);
                    }
                }
//                , $details
                array_unshift($products);

                $stores[$i]['products'] = $products;
                // array_push($stores[$i]['products'], $details);
                // dd($stores[$i]['products']);
            }
        }

        $data['stores'] = $stores;

        if($address){
            $address['area'] = $address->area_with_select['title'];
            $data['address'] = $address;
        }else{
            $data['address'] = new \stdClass();
        }

        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $data , $request->lang);
        return response()->json($response , 200);

    }

}
