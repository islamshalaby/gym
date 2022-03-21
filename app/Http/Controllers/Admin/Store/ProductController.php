<?php
namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Hash;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;
use App\Shop;
use App\Category;
use App\Product;
use App\Brand;
use Cloudinary;

class ProductController extends AdminController{
    // show products
    public function show(Request $request) {
        $data['categories'] = Category::where('deleted', 0)->orderBy('id', 'desc')->get();
        $data['brands'] = Brand::where('deleted', 0)->orderBy('id', 'desc')->get();
        $data['stores'] = Shop::where('status', 1)->orderBy('id', 'desc')->get();
        if($request->expire){
            $data['products'] = Product::where('deleted', 0)->where('remaining_quantity' , '<' , 10)->orderBy('id' , 'desc')->get();
            $data['expire'] = 'soon';
        }else{
            $data['products'] = Product::where('deleted', 0)->orderBy('id' , 'desc')->get();
            $data['expire'] = 'no';
        }


        $data['encoded_products'] = json_encode($data['products']);
        return view('store.product.index', ['data' => $data]);
    }

    // fetch category products
    public function fetch_category_products(Category $category) {
        $rows = Product::where('category_id', $category->id)->with('images', 'category', 'store', 'mainImage')->get();
        $data = json_decode(($rows));
        
        return response($data, 200);
    }
}