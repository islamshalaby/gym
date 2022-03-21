<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product_feature;
use App\Product_view;
use App\ProductImage;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\Hole_time_work;
use App\Hole_booking;
use App\Hole_branch;
use App\Hole_media;
use App\Favorite;
use App\Shop;
use App\Hole;
use App\Product;
use App\Rate;
use App\User;
use App\Setting;

class ShopsController extends Controller
{
    public $personal_data = [];

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['product_details','all_shops', 'details', 'getOfferImage', 'filter_by_cat','offer_products']]);

    }

    public function all_shops(Request $request)
    {
        $lang = $request->lang;
        $user = auth()->user();
        $data['categories'] = Category::where('deleted', 0)->select('id', 'title_' . $request->lang . ' as title', 'image')->get()->map(function ($cat) use ($request) {
            $cat->selected = false;
            if ($request->category && $request->category != 0 && $request->category == $cat->id) {
                $cat->selected = true;
            }
            return $cat;
        })->toArray();
        $allTitle = "All";
        if ($request->lang == 'ar') {
            $allTitle = "الكل";
        }
        $selected = false;
        if ($request->category == 0) {
            $selected = true;
        }
        $all = [
            "id" => 0,
            "title" => $allTitle,
            "image" => "",
            "selected" => $selected
        ];
        array_unshift($data['categories'], $all);

        $data['shops'] = Shop::select('id', 'name_' . $lang . ' as title', 'desc_' . $lang . ' as description', 'logo', 'cover')
            ->where('famous', '1')
            ->where('status', 1)
            ->orderBy('sort', 'asc');
        if ($request->category && $request->category != 0) {
            $productCategories = Product::where('deleted', 0)->where('hidden', 0)->where('category_id', $request->category)->pluck('store_id');
            $data['shops'] = $data['shops']->whereIn('id', $productCategories);
        }
        $data['shops'] = $data['shops']->get()
            ->map(function ($shops) use ($user) {
                if ($user == null) {
                    $shops->favorite = false;
                } else {
                    $fav = Favorite::where('user_id', $user->id)->where('product_id', $shops->id)->where('type', 'shop')->first();
                    if ($fav == null) {
                        $shops->favorite = false;
                    } else {
                        $shops->favorite = true;
                    }
                }
                return $shops;
            });

        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function details(Request $request, $id)
    {
        $lang = $request->lang;
        $user = auth()->user();
        Session::put('api_lang', $lang);
        $offer_image = Setting::where('id', 1)->select('offer_image')->first()['offer_image'];
        $shop = Shop::select('id', 'cover', 'logo', 'name_' . $lang . ' as title')->find($id)->makeHidden('name');
        if ($shop != null) {
            $exist_free_product = Product::where('store_id', $id)->where('deleted', 0)->where('hidden', 0)->where('free', 1)->first();
            if($exist_free_product){
                $shop['free'] = 1;
            }else{
                $shop['free'] = 0;
            }
            if ($user == null) {
                $data['favorite'] = false;
            } else {
                $fav = Favorite::where('user_id', $user->id)->where('product_id', $id)->where('type', 'shop')->first();
                if ($fav == null) {
                    $data['favorite'] = false;
                } else {
                    $data['favorite'] = true;
                }
            }
            $data['basic'] = $shop;
            $data['offer_image'] = $offer_image;
            $first_cat_id = Category::select('id', 'image', 'title_' . $lang . ' as title')
                ->where('deleted', 0)
                ->orderBy('created_at')
                ->first();
            $data['categories'] = Category::select('id', 'image', 'title_' . $lang . ' as title')
                ->where('deleted', 0)
                ->orderBy('created_at')
                ->get()->map(function ($data) use ($first_cat_id) {
//                    if ($data->id == $first_cat_id->id) {
//                        $data->selected = true;
//                    } else {
                        $data->selected = false;
//                    }
                    return $data;
                })->toArray();

            //add all to categories
            $title = 'All';
            if ($lang == 'ar') {
                $title = 'الكل';
            }
            $all = new \StdClass;
            $all->id = 0;
            $all->image = null;
            $all->title = $title;
            $all->selected = true;
            array_unshift($data['categories'], $all);


            $data['products'] = Product::select('id', 'title_' . $lang . ' as title','description_' . $lang . ' as description', 'final_price', 'price_before_offer', 'offer', 'offer_percentage', 'category_id')
                ->where('store_id', $id)
                ->where('deleted', 0)
                ->where('hidden', 0)
                ->get()->map(function ($data) use ($user) {
                    $data->image = $data->mainImage->image;
                    if ($user == null) {
                        $data->favorite = false;
                    } else {
                        $fav = Favorite::where('user_id', $user->id)->where('product_id', $data->id)->where('type', 'product')->first();
                        if ($fav == null) {
                            $data->favorite = false;
                        } else {
                            $data->favorite = true;
                        }
                    }
                    return $data;
                })->makeHidden('mainImage');

        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }
    public function offer_products(Request $request, $id)
    {
        $lang = $request->lang;
        $user = auth()->user();
        Session::put('api_lang', $lang);
        $shop = Shop::select('id', 'cover', 'logo', 'name_' . $lang . ' as title')->find($id)->makeHidden('name');
        if ($shop != null) {
            $data['products'] = Product::select('id', 'title_' . $lang . ' as title', 'final_price', 'price_before_offer', 'offer', 'offer_percentage', 'category_id')
                ->where('store_id', $id)
                ->where('deleted', 0)
                ->where('free', 1)
                ->where('hidden', 0)
                ->get()->map(function ($data) use ($user) {
                    $data->image = $data->mainImage->image;
                    if ($user == null) {
                        $data->favorite = false;
                    } else {
                        $fav = Favorite::where('user_id', $user->id)->where('product_id', $data->id)->where('type', 'product')->first();
                        if ($fav == null) {
                            $data->favorite = false;
                        } else {
                            $data->favorite = true;
                        }
                    }
                    return $data;
                })->makeHidden('mainImage');

        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function filter_by_cat(Request $request, $id)
    {
        $lang = $request->lang;
        $user = auth()->user();
        Session::put('api_lang', $lang);
        $shop = Shop::select('id', 'cover', 'logo', 'name_' . $lang . ' as title')->find($id)->makeHidden('name');
        if ($shop != null) {
            if ($user == null) {
                $data['favorite'] = false;
            } else {
                $fav = Favorite::where('user_id', $user->id)->where('product_id', $id)->where('type', 'shop')->first();
                if ($fav == null) {
                    $data['favorite'] = false;
                } else {
                    $data['favorite'] = true;
                }
            }
            $data['basic'] = $shop;
            $first_cat_id = Category::select('id', 'image', 'title_' . $lang . ' as title')
                ->where('deleted', 0)
                ->orderBy('created_at')
                ->first();
            $data['categories'] = Category::select('id', 'image', 'title_' . $lang . ' as title')
                ->where('deleted', 0)
                ->has('products', '>', 0)
                ->orderBy('created_at')
                ->get()->map(function ($data) use ($request) {
                    if ($data->id == $request->category_id) {
                        $data->selected = true;
                    } else {
                        $data->selected = false;
                    }
                    return $data;
                })->toArray();
            //add all to categories
            $title = 'All';
            if ($lang == 'ar') {
                $title = 'الكل';
            }
            $all = new \StdClass;
            $all->id = 0;
            $all->image = null;
            $all->title = $title;
            if( $request->category_id == 0){
                $all->selected = true;
            }else{
                $all->selected = false;
            }
            array_unshift($data['categories'], $all);
            if ($request->category_id != 0) {
                $data['products'] = Product::select('id', 'title_' . $lang . ' as title', 'final_price', 'price_before_offer', 'offer', 'offer_percentage', 'category_id')
                    ->where('store_id', $id)
                    ->where('category_id', $request->category_id)
                    ->where('deleted', 0)
                    ->where('hidden', 0)
                    ->get()->map(function ($data) use ($user) {
                        $data->image = $data->mainImage->image;
                        if ($user == null) {
                            $data->favorite = false;
                        } else {
                            $fav = Favorite::where('user_id', $user->id)->where('product_id', $data->id)->where('type', 'product')->first();
                            if ($fav == null) {
                                $data->favorite = false;
                            } else {
                                $data->favorite = true;
                            }
                        }
                        return $data;
                    })->makeHidden('mainImage');
            } else {
                $data['products'] = Product::select('id', 'title_' . $lang . ' as title', 'final_price', 'price_before_offer', 'offer', 'offer_percentage', 'category_id')
                    ->where('store_id', $id)
                    ->where('deleted', 0)
                    ->where('hidden', 0)
                    ->get()->map(function ($data) use ($user) {
                        $data->image = $data->mainImage->image;
                        if ($user == null) {
                            $data->favorite = false;
                        } else {
                            $fav = Favorite::where('user_id', $user->id)->where('product_id', $data->id)->where('type', 'product')->first();
                            if ($fav == null) {
                                $data->favorite = false;
                            } else {
                                $data->favorite = true;
                            }
                        }
                        return $data;
                    })->makeHidden('mainImage');
            }
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function product_details(Request $request)
    {

        $user = auth()->user();
        $lang = $request->lang;
        Session::put('lang', $lang);
        $data = Product::with('images')->with('category_name')
            ->select('id', 'title_' . $lang . ' as title', 'description_' . $lang . ' as description', 'remaining_quantity', 'final_price', 'price_before_offer',
                'offer', 'offer_percentage', 'category_id','free')
            ->where('deleted', 0)
            ->where('hidden', 0)
            ->find($request->id)->makeHidden(['store_id', 'store', 'productProperties', 'category_id', 'type']);
        $options = [];
        for ($i = 0; $i < count($data->productProperties); $i++) {
            $single = [
                'option' => $data->productProperties[$i]->property['title_en'],
                'value' => $data->productProperties[$i]->values['value_en']
            ];
            array_push($options, $single);
        }
        $data['options'] = $options;
        if ($user) {
            $favorite = Favorite::where('user_id', $user->id)->where('product_id', $data->id)->where('type', 'product')->first();
            if ($favorite) {
                $data->favorite = true;
            } else {
                $data->favorite = false;
            }
        } else {
            $data->favorite = false;
        }
        //for related products
        $related = Product::with('mainImage')->where('category_id', $data->category_id)
            ->where('id', '!=', $data->id)
            ->select('id', 'title_' . $lang . ' as title', 'final_price', 'price_before_offer', 'offer', 'offer_percentage', 'category_id','free')
            ->where('deleted', 0)
            ->where('hidden', 0)
            ->limit(4)
            ->get();
        for ($i = 0; $i < count($related); $i++) {
            if ($user) {
                $favorite = Favorite::where('user_id', $user->id)->where('product_id', $related[$i]['id'])->where('type', 'product')->first();
                if ($favorite) {
                    $related[$i]['favorite'] = true;
                } else {
                    $related[$i]['favorite'] = false;
                }
            } else {
                $related[$i]['favorite'] = false;
            }
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', array('product' => $data, 'related' => $related), $request->lang);
        return response()->json($response, 200);
    }
}
