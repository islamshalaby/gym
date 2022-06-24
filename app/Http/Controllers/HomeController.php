<?php

namespace App\Http\Controllers;

use App\Balance_package;
use App\Coach;
use App\Hole;
use App\Main_ad;
use App\Plan_details;
use App\Points_package;
use App\Product_view;
use App\Reservation;
use App\Setting;
use App\Shop;
use App\User;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\Category;
use App\Ad;
use App\Product;
use App\ProductImage;
use App\Favorite;
use Carbon\Carbon;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['exchange_points', 'points_packages', 'balance_packages', 'gethome', 'app_home', 'check_ad', 'main_ad', 'getOfferImage']]);
    }

    public function gethome(Request $request)
    {
        //        --------------------------------------------- begin scheduled functions --------------------------------------------------------
        $expired = Reservation::where('status', 'start')->whereDate('expire_date', '<', Carbon::now())->get();
        foreach ($expired as $row) {
            $product = Reservation::find($row->id);
            $product->status = 'ended';
            $product->save();
        }
        //        --------------------------------------------- end scheduled functions --------------------------------------------------------
        $data['slider'] = Ad::select('id', 'image', 'type', 'content')->where('place', 1)->get();
        $data['ads'] = Ad::select('id', 'image', 'type', 'content')->where('place', 2)->get();
        $data['categories'] = Category::select('id', 'image', 'title_ar as title')->where('deleted', 0)->get();
        $data['offers'] = Product::where('offer', 1)->where('status', 1)->where('deleted', 0)->where('publish', 'Y')->select('id', 'title', 'price', 'type')->get();
        for ($i = 0; $i < count($data['offers']); $i++) {
            $data['offers'][$i]['image'] = ProductImage::where('product_id', $data['offers'][$i]['id'])->select('image')->first()['image'];
            $user = auth()->user();
            if ($user) {
                $favorite = Favorite::where('user_id', $user->id)->where('product_id', $data['offers'][$i]['id'])->first();
                if ($favorite) {
                    $data['offers'][$i]['favorite'] = true;
                } else {
                    $data['offers'][$i]['favorite'] = false;
                }
            } else {
                $data['offers'][$i]['favorite'] = false;
            }
            // $data['offers'][$i]['favorite'] = false;

        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function app_home(Request $request)
    {
        $lang = $request->lang;
        $user = auth()->user();

        if ($lang == 'ar'){
            $data['slider_ads'] = Ad::select('id', 'image', 'type', 'title_ar as title', 'desc_ar as content', 'content as target')->get();
            $data['famous_halls'] = Hole::select('id', 'cover', 'logo', 'name as name', 'started_price', 'rate')
                ->where('famous', '1')
                ->where('status', 'active')
                ->where('deleted', '0')
                ->orderBy('sort', 'asc')
                ->get()
                ->map(function ($halls) use ($user) {
                    if ($user == null) {
                        $halls->favorite = false;
                    } else {
                        $fav = Favorite::where('user_id', $user->id)->where('product_id', $halls->id)->where('type', 'hall')->first();
                        if ($fav == null) {
                            $halls->favorite = false;
                        } else {
                            $halls->favorite = true;
                        }
                    }
                    return $halls;
                });
            $data['famous_coaches'] = Coach::select('id', 'image', 'available', 'name as name')
                ->where('famous', '1')
                ->where('is_confirm', 'accepted')
                ->where('status', 'active')
                ->where('deleted', '0')
                ->orderBy('sort', 'asc')
                ->get()
                ->map(function ($coaches) use ($user) {
                    if ($user == null) {
                        $coaches->favorite = false;
                    } else {
                        $fav = Favorite::where('user_id', $user->id)->where('product_id', $coaches->id)->where('type', 'coach')->first();
                        if ($fav == null) {
                            $coaches->favorite = false;
                        } else {
                            $coaches->favorite = true;
                        }
                    }
                    return $coaches;
                });
            $data['famous_store'] = Shop::select('id', 'logo as image', 'name_ar as title')
                ->where('famous', '1')
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
        }else{
            $data['slider_ads'] = Ad::select('id', 'image', 'type', 'title_en as title', 'desc_en as content', 'content as target')->get();
            $data['famous_halls'] = Hole::select('id', 'cover', 'logo', 'name_en as name', 'started_price', 'rate')
                ->where('famous', '1')
                ->where('status', 'active')
                ->where('deleted', '0')
                ->orderBy('sort', 'asc')
                ->get()
                ->map(function ($halls) use ($user) {
                    if ($user == null) {
                        $halls->favorite = false;
                    } else {
                        $fav = Favorite::where('user_id', $user->id)->where('product_id', $halls->id)->where('type', 'hall')->first();
                        if ($fav == null) {
                            $halls->favorite = false;
                        } else {
                            $halls->favorite = true;
                        }
                    }
                    return $halls;
                });
            $data['famous_coaches'] = Coach::select('id', 'image', 'available', 'name_en as name')
                ->where('famous', '1')
                ->where('is_confirm', 'accepted')
                ->where('status', 'active')
                ->where('deleted', '0')
                ->orderBy('sort', 'asc')
                ->get()
                ->map(function ($coaches) use ($user) {
                    if ($user == null) {
                        $coaches->favorite = false;
                    } else {
                        $fav = Favorite::where('user_id', $user->id)->where('product_id', $coaches->id)->where('type', 'coach')->first();
                        if ($fav == null) {
                            $coaches->favorite = false;
                        } else {
                            $coaches->favorite = true;
                        }
                    }
                    return $coaches;
                });
            $data['famous_store'] = Shop::select('id', 'logo as image', 'name_en as title')
                ->where('famous', '1')
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }
//nasser code
    // main ad page
    public function main_ad(Request $request)
    {
        $data = Main_ad::select('image', 'type', 'content')->where('deleted', '0')->inRandomOrder()->take(1)->get();
        if (count($data) == 0) {
            $response = APIHelpers::createApiResponse(true, 406, 'no ads available',
                'لا يوجد اعلانات', null, $request->lang);
            return response()->json($response, 406);
        }
        foreach ($data as $image) {
            $image['image'] = $image->image;
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $image, $request->lang);
        return response()->json($response, 200);
    }

    public function check_ad(Request $request)
    {
        $ads = Main_ad::select('image')->where('deleted', '0')->get();
        if (count($ads) > 0) {
            $data['show_ad'] = true;
        } else {
            $data['show_ad'] = false;
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function balance_packages(Request $request)
    {
        if ($request->lang == 'en') {
            $data['packages'] = Balance_package::where('status', 'show')->select('id', 'name_en as title', 'price', 'amount', 'desc_en as desc')->orderBy('title', 'desc')->get();
        } else {
            $data['packages'] = Balance_package::where('status', 'show')->select('id', 'name_ar as title', 'price', 'amount', 'desc_ar as desc')->orderBy('title', 'desc')->get();
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function points_packages(Request $request)
    {
        $user_id = auth()->user()->id;
        $data['my_points'] = User::select('points')->where('id', $user_id)->first();
        $data['packages'] = Points_package::where('deleted', '0')->select('id', 'points', 'price')->orderBy('id', 'desc')->get();
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function exchange_points(Request $request, $id)
    {
        $package = Points_package::where('id', $id)->where('deleted', '0')->first();
        if ($package != null) {
            $user = auth()->user();
			
            if ($package->points <= $user->points) {
                $user = User::where('id', $user->id)->first();
                $user->my_wallet = $user->my_wallet + $package->price;
                $user->payed_balance = $user->payed_balance + $package->price;
                $user->points = $user->points - $package->points;
                $user->save();
                $data['my_points'] = User::select('points')->where('id', $user->id)->first();
                $data['packages'] = Points_package::where('deleted', '0')->select('id', 'points', 'price')->orderBy('id', 'desc')->get();
                $response = APIHelpers::createApiResponse(false, 200, 'points exchanged successfully', 'تم استبدال النقاط بالمبلغ', $data, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, 'There are not enough points',
                    'لا يوجد نقاط كافية للاستبدال', (object)[], $request->lang);
                return response()->json($response, 200);
            }
        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'chosen package not exists',
                'الباقة المختاره غير موجوده', null, $request->lang);
            return response()->json($response, 406);
        }
    }

    // get offer image
    public function getOfferImage(Request $request) {
        $offer_image = Setting::where('id', 1)->select('offer_image')->first()['offer_image'];
        if ($offer_image == null) {
            $offer_image = "";
        }

        $response = APIHelpers::createApiResponse(false , 200 ,  '', 'jj' , $offer_image, $request->lang );
        return response()->json($response , 200);
    }
}
