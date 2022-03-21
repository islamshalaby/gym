<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\Visitor;
use App\Product;
use App\Cart;
use App\Favorite;
use App\ProductImage;
use App\UserAddress;
use App\Area;
use App\Shop;
use App\DeliveryArea;
use App\Setting;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['create', 'add', 'get', 'getcartcount', 'changecount', 'delete']]);
    }

    public function index()
    {
        //
    }

    // create visitor
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required',
            'fcm_token' => 'required',
            'type' => 'required' // 1 -> iphone ---- 2 -> android
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, 'Missing Required Fields', 'بعض الحقول مفقودة', null, $request->lang);
            return response()->json($response, 406);
        }

        $last_visitor = Visitor::where('unique_id', $request->unique_id)->first();
        if ($last_visitor) {
            $visitor = $last_visitor;
        } else {
            $visitor = new Visitor();
            $visitor->unique_id = $request->unique_id;
            $visitor->fcm_token = $request->fcm_token;
            $visitor->type = $request->type;
            $visitor->save();
        }


        $response = APIHelpers::createApiResponse(false, 200, '', '', $visitor, $request->lang);
        return response()->json($response, 200);
    }

    // add to cart
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required',
            'product_id' => 'required|exists:products,id',
            'product_number' => 'required|numeric|min:0|not_in:0'
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, 'Missing Required Fields or product does not exist', 'بعض الحقول مفقودة او المنتج غير موجود', null, $request->lang);
            return response()->json($response, 406);
        }

        $product = Product::find($request->product_id);


        $visitor = Visitor::where('unique_id', $request->unique_id)->first();
        if ($visitor) {

            $cart = Cart::where('visitor_id', $visitor->id)->where('product_id', $request->product_id)->first();
            if ($product->remaining_quantity < 1) {
                $response = APIHelpers::createApiResponse(true, 406, 'The remaining amount of the product is not enough', 'الكميه المتبقيه من المنتج غير كافيه', null, $request->lang);
                return response()->json($response, 406);
            }


            if ($cart) {
                $count = $cart->count;
                $cart->count = $count + $request->product_number;
                $cart->save();
            } else {
                $cart = new Cart();
                $cart->count = $request->product_number;
                $cart->product_id = $request->product_id;
                $cart->visitor_id = $visitor->id;
                $cart->store_id = $product->store_id;
                $cart->save();
            }


            $response = APIHelpers::createApiResponse(false, 200, '', '', $cart, $request->lang);
            return response()->json($response, 200);

        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'This Unique Id Not Registered', 'This Unique Id Not Registered', null, $request->lang);
            return response()->json($response, 406);
        }

    }

    // get cart
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, 'Missing Required Fields', 'بعض الحقول مفقودة', null, $request->lang);
            return response()->json($response, 406);
        }

        $visitor = Visitor::where('unique_id', $request->unique_id)->first();
        if ($visitor) {
            $visitor_id = $visitor['id'];
            $cart = Cart::where('visitor_id', $visitor_id)->select('product_id as id', 'count')->get();
            $data['subtotal_price'] = "0";
            for ($i = 0; $i < count($cart); $i++) {
                if ($request->lang == 'en') {
                    $product = Product::with('store')
                        ->select('title_en as title', 'final_price', 'price_before_offer', 'id', 'free', 'store_id', 'offer', 'offer_percentage')
                        ->where('id', $cart[$i]['id'])->first();
                } else {
                    $product = Product::with('store')->select('title_ar as title', 'final_price', 'price_before_offer', 'id', 'free', 'store_id', 'offer', 'offer_percentage')->where('id', $cart[$i]['id'])->first();
                }

                if (auth()->user()) {
                    $user_id = auth()->user()->id;
                    $prevfavorite = Favorite::where('product_id', $cart[$i]['id'])->where('user_id', $user_id)->where('type', 'product')->first();
                    if ($prevfavorite) {
                        $cart[$i]['favorite'] = true;
                    } else {
                        $cart[$i]['favorite'] = false;
                    }

                } else {
                    $cart[$i]['favorite'] = false;
                }

                $cart[$i]['final_price'] = number_format((float)$product['final_price'], 3, '.', '');
                $cart[$i]['price_before_offer'] = number_format((float)$product['price_before_offer'], 3, '.', '');
                $cart[$i]['offer_percentage'] = $product['offer_percentage'];
                $cart[$i]['offer'] = $product['offer'];
                $productCount = $cart[$i]['count'];
                if ($product['free'] && $productCount > 2) {
                    $productCount = $productCount - 1;
                }
                $sBPrice = $data['subtotal_price'] + ($product['final_price'] * $productCount);
                $data['subtotal_price'] = number_format((float)$sBPrice, 3, '.', '');
                $cart[$i]['title'] = $product['title'];
                $cart[$i]['free'] = $product['free'];
                $cart[$i]['store_name'] = $product->store->name;
                $cart[$i]['store_id'] = $product->store->id;
                $cart[$i]['image'] = ProductImage::select('image')->where('product_id', $cart[$i]['id'])->first()['image'];
            }

            $data['cart'] = $cart;
            $data['count'] = count($cart);

            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);

        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'This Unique Id Not Registered', 'This Unique Id Not Registered', null, $request->lang);
            return response()->json($response, 406);
        }
    }

    // get cart count
    public function getcartcount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, 'Missing Required Fields', 'بعض الحقول مفقودة', null, $request->lang);
            return response()->json($response, 406);
        }

        $visitor = Visitor::where('unique_id', $request->unique_id)->first();
        if ($visitor) {
            $visitor_id = $visitor['id'];
            $cart = Cart::where('visitor_id', $visitor_id)->select('product_id as id', 'count')->get();
            $count['count'] = count($cart);

            $response = APIHelpers::createApiResponse(false, 200, '', '', $count, $request->lang);
            return response()->json($response, 200);

        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'This Unique Id Not Registered', 'This Unique Id Not Registered', null, $request->lang);
            return response()->json($response, 406);
        }
    }

    // change count
    public function changecount(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'unique_id' => 'required',
            'product_id' => 'required|exists:products,id',
            'new_count' => 'required'
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, 'Missing Required Fields or product does not exist', 'بعض الحقول مفقودة او المنتج غير موجود', null, $request->lang);
            return response()->json($response, 406);
        }

        $visitor = Visitor::where('unique_id', $request->unique_id)->first();

        $product = Product::find($request->product_id);
        if ($product->remaining_quantity < $request->new_count) {
            $response = APIHelpers::createApiResponse(true, 406, 'The remaining amount of the product is not enough', 'الكميه المتبقيه من المنتج غير كافيه', null, $request->lang);
            return response()->json($response, 406);
        }


        if ($visitor) {

            $cart = Cart::where('product_id', $request->product_id)->where('visitor_id', $visitor->id)->first();


            if (isset($cart->count)) {
                $cart->count = $request->new_count;
                $cart->save();
                $response = APIHelpers::createApiResponse(false, 200, '', '', $cart, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, 'This product is not exist in cart', 'هذا المنتج غير موجود بالعربة', null, $request->lang);
                return response()->json($response, 406);
            }
        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'This Unique Id Not Registered', 'This Unique Id Not Registered', null, $request->lang);
            return response()->json($response, 406);
        }

    }

    // remove from cart
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required',
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, 'Missing Required Fields', 'بعض الحقول مفقودة', null, $request->lang);
            return response()->json($response, 406);
        }

        $visitor = Visitor::where('unique_id', $request->unique_id)->first();
        if ($visitor) {

            $cart = Cart::where('product_id', $request->product_id)->where('visitor_id', $visitor->id)->first();

            $cart->delete();

            $response = APIHelpers::createApiResponse(false, 200, '', '', null, $request->lang);
            return response()->json($response, 200);

        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'This Unique Id Not Registered', 'This Unique Id Not Registered', null, $request->lang);
            return response()->json($response, 406);
        }
    }

    // get cart before order
    public function get_cart_before_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required'
        ]);


        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, 'Missing Required Fields', 'بعض الحقول مفقودة', null, $request->lang);
            return response()->json($response, 406);
        }


        //dd(auth()->user()->id);
        $visitor = Visitor::where('unique_id', $request->unique_id)->first();
        $address = UserAddress::where('id', auth()->user()->main_address_id)->first();
        $area = Area::where('id', $address['area_id'])->select('title_' . $request->lang . ' as area')->first()['area'];

        if ($visitor) {
            $visitor_id = $visitor['id'];
            $cart = Cart::where('visitor_id', $visitor_id)->select('product_id', 'count')->get();
            $stores = [];
            for ($i = 0; $i < count($cart); $i++) {
                array_push($stores, $cart[$i]->product->store_id);
            }
            $get_stores = Shop::select('name_' . $request->lang . ' as name', 'id')->whereIn('id', $stores)->get();
            $sub_total_price = "0";
            $delivery_cost = "0";
            for ($n = 0; $n < count($get_stores); $n++) {
                $data['cart'][$n]['shipment_number'] = $n + 1;
                $delivery = DeliveryArea::select('delivery_cost', 'arrival_from', 'arrival_to')->where('area_id', $address['area_id'])->where('store_id', $get_stores[$n]['id'])->first();

                if (!isset($delivery['delivery_cost'])) {
                    $delivery = Setting::find(1);
                }
                $data['cart'][$n]['products'] = [];
                $store_products = [];
                $delivery_cost = $delivery_cost + $delivery['delivery_cost'];
                $storeTotalPrice = 0;
                $storeSubTotalPrice = 0;
                for ($k = 0; $k < count($cart); $k++) {

                    $product = Product::select('id', 'title_' . $request->lang . ' as title', 'free', 'store_id', 'final_price', 'price_before_offer')
                        ->where('id', $cart[$k]['product_id'])->first()->makeHidden(['store', 'mainImage']);
                    // var_dump($product['id']);
                    $product['count'] = $cart[$k]['count'];
                    $product['store_name'] = $product->store->name_en;
                    if (isset($product->mainImage->image)) {
                        $product['image'] = $product->mainImage->image;
                    } else {
                        $product['image'] = "";
                    }
                    if ($product['store_id'] == $get_stores[$n]['id']) {
                        $sub_total_price = $sub_total_price + ($product['final_price'] * $cart[$k]['count']);
                        $storeSubTotalPrice = $storeSubTotalPrice + ($product['final_price'] * $cart[$k]['count']);
                        $storeTotalPrice = $storeTotalPrice + ($product['final_price'] * $cart[$k]['count']);
                    }
                    if ($product['store_id'] == $get_stores[$n]['id']) {
                        array_push($store_products, $product['id']);
                        array_push($data['cart'][$n]['products'], $product);
                    }
                }
                $data['cart'][$n]['delivery_cost'] = $delivery['delivery_cost'];
                $data['cart'][$n]['sub_total_cost'] = (string)$storeSubTotalPrice;
                $total = $storeTotalPrice + $delivery['delivery_cost'];
                $data['cart'][$n]['total_cost'] = (string)$total;

                if ($delivery['arrival_from'] == null) {
                    $data['cart'][$n]['min_estimated_time'] = "";
                } else {
                    $data['cart'][$n]['min_estimated_time'] = $delivery['arrival_from'];
                }
                if ($delivery['arrival_to'] == null) {
                    $data['cart'][$n]['max_estimated_time'] = "";
                } else {
                    $data['cart'][$n]['max_estimated_time'] = $delivery['arrival_to'];
                }
            }
            // $data['count'] = count($cart);
            $data['subtotal_price'] = (string)$sub_total_price;
            $data['delivery_cost'] = (string)$delivery_cost;
            $ttotal = $delivery_cost + $sub_total_price ;
            $data['total_cost'] = (string)$ttotal;
            if ($address == null) {
                $data['address'] = (object)[];
            } else {
                $data['address'] = $address;
            }
            $data['address']['area'] = $area;
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);

        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'This Unique Id Not Registered', 'This Unique Id Not Registered', null, $request->lang);
            return response()->json($response, 406);
        }
    }
}
