<?php
namespace App\Http\Controllers\Shop_admin;

use App\Http\Controllers\Controller;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;
use App\ProductMultiOption;
use App\ProductProperty;
use App\ProductImage;
use App\ControlOffer;
use App\SubCategory;
use App\HomeSection;
use App\HomeElement;
use App\OptionValue;
use App\Category;
use App\Product;
use App\Brand;
use App\Setting;
use App\Shop;
use Cloudinary;

class ProductController extends Controller{
    // show products
    public function show(Request $request) {
        $data['categories'] = Category::where('deleted', 0)->orderBy('id', 'desc')->get();
        $data['brands'] = Brand::where('deleted', 0)->orderBy('id', 'desc')->get();
        $data['stores'] = Shop::where('status', 1)->orderBy('id', 'desc')->get();
        if($request->expire){
            $data['products'] = Product::where('deleted', 0)->where('store_id', auth()->guard('shop')->user()->id)->where('remaining_quantity' , '<' , 10)->orderBy('id' , 'desc')->get();
            $data['expire'] = 'soon';
        }else{
            $data['products'] = Product::where('deleted', 0)->where('store_id', auth()->guard('shop')->user()->id)->orderBy('id' , 'desc')->get();
            $data['expire'] = 'no';
        }


        $data['encoded_products'] = json_encode($data['products']);
        return view('shop_admin.products.index', ['data' => $data]);
    }
    public function make_free_gift($id) {
        $product = Product::whereId($id)->first();

        if($product->free_gift == 1){
            $data['free_gift'] = 0 ;
        }else{
            $data['free_gift'] = 1 ;
        }
        Product::whereId($product->id)->update($data);
        return back();
    }

    // fetch category brands
    public function fetch_category_brands(Category $category) {
        $rows = $category->brands;

        $data = json_decode(($rows));

        return response($data, 200);
    }

    // fetch brand sub categories
    public function fetch_brand_sub_categories(Brand $brand) {
        $rows = $brand->subCategories;

        $data = json_decode(($rows));

        return response($data, 200);
    }

    // fetch sub category products
    public function sub_category_products(SubCategory $subCategory) {
        $rows = Product::where('sub_category_id', $subCategory->id)->with('images', 'category')->get();
        $data = json_decode(($rows));

        return response($data, 200);
    }

    // edit get
    public function EditGet(Product $product) {
        $data['product'] = $product;
        $data['barcode'] = uniqid();
        $data['categories'] = Category::where('deleted', 0)->orderBy('id', 'desc')->get();
        $data['category'] = Category::findOrFail($data['product']['category_id']);
        $data['stores'] = Shop::get();
        $data['options'] = [];
        $data['product_options'] = [];
        $data['Home_sections'] = HomeSection::where('type', 4)->get();
        $data['Home_sections_ids'] = HomeSection::where('type', 4)->pluck('id');
        $data['elements'] = HomeElement::where('element_id', $product->id)->whereIn('home_id', $data['Home_sections_ids'])->pluck('home_id')->toArray();
        $data['property_values'] = $data['product']->values()->select('option_values.id', 'option_values.option_id')->get();
        return view('shop_admin.products.edit', ['data' => $data]);
    }

    // edit post
    public function EditPost(Request $request, Product $product) {
        $request->validate([
            'barcode' => 'unique:products,barcode,' . $product->id . '|max:255|nullable',
            'stored_number' => 'unique:products,stored_number,' . $product->id . '|max:255|nullable',
            'title_en' => 'required',
            'title_ar' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            'category_id' => 'required'
        ]);
        $product_post = $request->except(['images', 'option', 'value_en', 'value_ar', 'home_section', 'option_id', 'property_value_id', 'multi_option_id', 'multi_option_value_id', 'total_quatity', 'remaining_quantity', 'final_price', 'total_amount', 'remaining_amount', 'price_after_discount', 'barcodes', 'stored_numbers']);


        if (isset($request->home_section) && !empty($request->home_section)) {
            $data['Home_sections_ids'] = HomeSection::where('type', 4)->pluck('id')->toArray();
            $data['elements'] = HomeElement::where('element_id', $product->id)->whereIn('home_id', $data['Home_sections_ids'])->select('id')->first();
            if (!empty($data['elements'])) {
                $data['product_element'] = HomeElement::findOrFail($data['elements']['id']);

                $data['product_element']->update(['home_id'=>$request->home_section]);
            }else {
                HomeElement::create(['home_id'=>$request->home_section, 'element_id' => $product->id]);
            }

        }

        if (isset($product_post['offer'])) {
            $price_before = (int)$product_post['price_before_offer'];
            $discount_value = (int)$product_post['offer_percentage'] / 100;
            $price_value = $price_before * $discount_value;
            $product_post['final_price'] = $price_before - $price_value;
        }
        if (isset($product_post['offer'])) {
            $product_post['offer'] = 1;
        }else {
            $product_post['offer'] = 0;
            $product_post['offer_percentage'] = 0;
            $product_post['price_before_offer'] = 0;
        }
        $product->update($product_post);
        if ( $images = $request->file('images') ) {
            foreach ($images as $image) {
                $image_name = $image->getRealPath();
                $img = Cloudinary::upload($image_name);
                // $imagereturned = Cloudder::getResult();
                $image_id = $img->getPublicId();
                $image_format = $img->getExtension();
                $image_new_name = $image_id.'.'.$image_format;
                ProductImage::create(["image" => $image_new_name, "product_id" => $product->id]);
            }
            $productImages = ProductImage::where('product_id', $product->id)->select('id', 'main')->get();
            for ($t =0; $t < count($productImages); $t ++) {
                if ($t == 0) {
                    $productImages[$t]->update(['main' => 1]);
                }else {
                    $productImages[$t]->update(['main' => 0]);
                }
            }
        }

        if (isset($product->options) && count($product->options) > 0) {
            $product->options()->delete();
        }

        if (isset($request->option_id)
            && count($request->option_id) > 0
            && isset($request->property_value_id)
            && count($request->property_value_id) > 0
            && count($request->option_id) == count($request->property_value_id)) {
            if (count($product->productProperties) > 0) {
                $product->productProperties()->delete();
            }
            for ($i = 0; $i < count($request->option_id); $i ++) {
                $post_option['product_id'] = $product->id;
                $post_option['option_id'] = $request->option_id[$i];
                if ($request->property_value_id[$i] != "empty") {
                    if ($request->property_value_id[$i] == 0) {
                        $option_val = OptionValue::create([
                            'option_id' => $request->option_id[$i],
                            'value_en' => $request->another_option_en[$i],
                            'value_ar' => $request->another_option_ar[$i]
                        ]);
                        $post_option['value_id'] = $option_val["id"];
                        ProductProperty::create($post_option);
                    }else {
                        $post_option['value_id'] = $request->property_value_id[$i];
                        ProductProperty::create($post_option);
                    }
                }
            }
        }


        if (isset($request->offer)) {
            $price_before = (double)$request->price_before_offer;
            $discount_value = (double)$request->offer_percentage / 100;
            $price_value = $price_before * $discount_value;
            $selected_prod_data['final_price'] = $price_before - $price_value;
        }

        if (!isset($request->offer)) {
            $selected_prod_data['final_price'] = $request->price_before_offer;
        }

        if (isset($request->offer)) {
            $selected_prod_data['offer'] = 1;
            $selected_prod_data['offer_percentage'] = (double)$request->offer_percentage;
        }else {
            $selected_prod_data['offer'] = 0;
            $selected_prod_data['offer_percentage'] = 0;
            $selected_prod_data['price_before_offer'] = $request->price_before_offer;
        }
        $selected_prod_data['total_quatity'] = $request->total_quatity;
        $selected_prod_data['remaining_quantity'] = $request->remaining_quantity;
        $selected_prod_data['multi_options'] = 0;
        $product->update($selected_prod_data);


        return redirect()->route('shops.products.show');

    }

    // fetch category products
    public function fetch_category_products(Category $category) {
        $rows = Product::where('category_id', $category->id)->with('images', 'category', 'store', 'multiOptionss', 'mainImage')->get();
        $data = json_decode(($rows));
        // dd($data[0]->main_image);
        return response($data, 200);
    }

    // fetch brand products
    public function fetch_brand_products(Brand $brand) {
        $rows = Product::where('brand_id', $brand->id)->with('images', 'category')->get();
        $data = json_decode(($rows));


        return response($data, 200);
    }

    // delete product image
    public function delete_product_image(ProductImage $productImage) {
        $image = $productImage->image;
        $publicId = substr($image, 0 ,strrpos($image, "."));
        Cloudder::delete($publicId);
        $productImage->delete();

        return redirect()->back();
    }

    // details
    public function details(Product $product) {
        $data['product'] = $product;

        return view('shop_admin.products.details', ['data' => $data]);
    }

    // delete
    public function delete(Product $product) {

        $product->update(['deleted' => 1]);
        $control_offer = ControlOffer::where('offer_id', $product->id)->get();
        if (!empty($control_offer)) {
            for ($n = 0; $n < count($control_offer); $n ++) {
                $control_offer[$n]->delete();
            }
        }

        $home_section = HomeSection::where('type', 4)->pluck('id')->toArray();
        $home_element = HomeElement::whereIn('home_id', $home_section)->where('element_id', $product->id)->get();
        if (!empty($home_element)) {
            for ($i =0; $i < count($home_element); $i ++) {
                $home_element[$i]->delete();
            }

        }

        return redirect()->back();
    }

    // fetch category options
    public function fetch_category_options(Category $category) {
        $rows = $category->optionsWithValues;

        $data = json_decode(($rows));

        return response($data, 200);
    }

    // fetch sub category multi options
    public function fetch_sub_category_multi_options(Category $category) {
        $rows = $category->multiOptionsWithValues;
        $data = json_decode(($rows));

        return response($data, 200);
    }

    // product search
    public function product_search(Request $request) {
        $data['categories'] = Category::where('deleted', 0)->orderBy('id', 'desc')->get();
        if (isset($request->name)) {
            $data['products'] = Product::with('images')->where('title_en', 'like', '%' . $request->name . '%')
                ->orWhere('title_ar', 'like', '%' . $request->name . '%')->get();
            return view('shop_admin.products.searched_products', ['data' => $data]);
        }else {
            return view('shop_admin.products.product_search', ['data' => $data]);
        }
    }

    // update quantity
    public function update_quantity(Request $request, Product $product) {
        $total_quatity = (int)$request->remaining_quantity + (int)$product->total_quatity;
        $remaining_quantity = (int)$request->remaining_quantity + (int)$product->remaining_quantity;
        $product->update(['total_quatity' => $total_quatity, 'remaining_quantity' => $remaining_quantity]);

        return redirect()->back();
    }

    // update quantity
    public function update_quantity_m_option(Request $request, ProductMultiOption $option) {
        $product = Product::find($option->product_id);
        $product->update([
            'total_quatity' => (int)$request->remaining_quantity + (int)$product->total_quatity,
            'remaining_quantity' => (int)$request->remaining_quantity + (int)$product->remaining_quantity
        ]);

        $total_quatity = (int)$request->remaining_quantity + (int)$option->total_quatity;
        $remaining_quantity = (int)$request->remaining_quantity + (int)$option->remaining_quantity;
        $option->update(['total_quatity' => $total_quatity, 'remaining_quantity' => $remaining_quantity]);

        return redirect()->back();
    }

    // add get
    public function addGet(Request $request) {
        $data['categories'] = Category::where('deleted', 0)->orderBy('id', 'desc')->get();
        $data['Home_sections'] = HomeSection::where('type', 4)->get();
        $data['stores'] = Shop::get();
        $data['barcode'] = uniqid();

        if (isset($request->cat)) {
            $data['cat'] = Category::findOrFail($request->cat);
        }

        return view('shop_admin.products.create', ['data' => $data]);
    }

    // add post
    public function addPost(Request $request) {
        $request->validate([
            'barcode' => 'unique:products,barcode|max:255|nullable',
            'stored_number' => 'unique:products,stored_number|max:255|nullable',
            'title_en' => 'required',
            'title_ar' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            'category_id' => 'required'
        ]);

        $product_post = $request->except(['images', 'option', 'value_en', 'value_ar', 'home_section', 'option_id', 'property_value_id', 'multi_option_id', 'multi_option_value_id', 'total_quatity', 'remaining_quantity', 'final_price', 'total_amount', 'remaining_amount', 'price_after_discount', 'barcodes', 'stored_numbers']);
        $product_post['store_id'] = auth()->guard('shop')->user()->id;
        $createdProduct = Product::create($product_post);

        if (isset($request->home_section)) {
            HomeElement::create(['home_id' => $request->home_section, 'element_id' => $createdProduct['id']]);
        }

        if ( $images = $request->file('images') ) {
            $g = 0;
            foreach ($images as $image) {
                $image_name = $image->getRealPath();
                $img = Cloudinary::upload($image_name);
                // $imagereturned = Cloudder::getResult();
                $image_id = $img->getPublicId();
                $image_format = $img->getExtension();
                $image_new_name = $image_id.'.'.$image_format;
                $mainImage = 0;
                if ($g == 0) {
                    $mainImage = 1;
                }
                ProductImage::create(["image" => $image_new_name, "product_id" => $createdProduct['id'], "main" => $mainImage]);
                $g ++;
            }

            $productImages = ProductImage::where('product_id', $createdProduct['id'])->select('id', 'main')->get();
            for ($t =0; $t < count($productImages); $t ++) {
                if ($t == 0) {
                    $productImages[$t]->update(['main' => 1]);
                }else {
                    $productImages[$t]->update(['main' => 0]);
                }
            }
        }

        if (isset($request->option_id)
            && count($request->option_id) > 0
            && isset($request->property_value_id)
            && count($request->property_value_id) > 0
            && count($request->option_id) == count($request->property_value_id)) {
            for ($i = 0; $i < count($request->option_id); $i ++) {
                $post_option['product_id'] = $createdProduct['id'];
                $post_option['option_id'] = $request->option_id[$i];
                if ($request->property_value_id[$i] != "empty") {
                    if ($request->property_value_id[$i] == 0) {
                        $option_val = OptionValue::create([
                            'option_id' => $request->option_id[$i],
                            'value_en' => $request->another_option_en[$i],
                            'value_ar' => $request->another_option_ar[$i]
                        ]);
                        $post_option['value_id'] = $option_val["id"];
                        ProductProperty::create($post_option);
                    }else {
                        $post_option['value_id'] = $request->property_value_id[$i];
                        ProductProperty::create($post_option);
                    }
                }
            }
        }

        $selected_product = Product::where('id', $createdProduct['id'])->first();


        if (isset($request->offer)) {
            $price_before = (double)$request->price_before_offer;
            $discount_value = (double)$request->offer_percentage / 100;
            $price_value = $price_before * $discount_value;
            $selected_prod_data['final_price'] = $price_before - $price_value;
        }

        if (!isset($request->offer)) {
            $selected_prod_data['final_price'] = $request->price_before_offer;
        }

        if (isset($request->offer)) {
            $selected_prod_data['offer'] = 1;
            $selected_prod_data['offer_percentage'] = (double)$request->offer_percentage;
        }else {
            $selected_prod_data['offer'] = 0;
            $selected_prod_data['offer_percentage'] = 0;
            $selected_prod_data['price_before_offer'] = $request->price_before_offer;
        }
        $selected_prod_data['total_quatity'] = $request->total_quatity;
        $selected_prod_data['remaining_quantity'] = $request->remaining_quantity;
        $selected_product->update($selected_prod_data);


        return redirect()->route('shops.products.show')
            ->with('success', __('messages.created_successfully'));
    }

    // get products by subcat
    public function get_product_by_sub_cat(Request $request) {
        $data['products'] = Product::with('images')->where('deleted' , 0)->where('remaining_quantity' , '<' , 10)->where('category_id', $request->cat)->get();
        $data['cat'] = $request->cat;

        return view('shop_admin.products.searched_products', ['data' => $data]);
    }

    // fetch sub categories by category
    public function fetch_sub_categories_by_category(Category $category) {
        $rows = SubCategory::where('deleted', 0)->where('category_id', $category->id)->get();

        $data = json_decode($rows);
        return response($data, 200);
    }

    // fetch products by store
    public function fetch_products_by_store(Shop $store) {
        $rows = Product::where('deleted', 0)->where('hidden', 0)->where('store_id', $store->id)->with('images', 'category', 'store', 'multiOptionss')->get();
        // dd($rows);
        $data = json_decode($rows);
        return response($data, 200);
    }

    // visibility status product
    public function visibility_status_product(Product $product, $status) {
        $product->update(['hidden' => $status]);
        if ($status == 1) {
            $control_offer = ControlOffer::where('offer_id', $product->id)->get();
            if (!empty($control_offer)) {
                for ($n = 0; $n < count($control_offer); $n ++) {
                    $control_offer[$n]->delete();
                }
            }

            $home_section = HomeSection::where('type', 4)->pluck('id')->toArray();
            $home_element = HomeElement::whereIn('home_id', $home_section)->where('element_id', $product->id)->get();
            if (!empty($home_element)) {
                for ($i =0; $i < count($home_element); $i ++) {
                    $home_element[$i]->delete();
                }

            }
        }



        return redirect()->back();
    }

    public function validate_barcode_unique($type, $text) {

        if ($type == 'barcode') {
            $product = ProductMultiOption::where('barcode', $text)->first();
        }else {
            $product = ProductMultiOption::where('stored_number', $text)->first();
        }


        if (!empty($product)) {
            return response("0", 200);
        }

        return response("1", 200);
    }

    public function review_product(Product $product, $status) {
        $product->update(['reviewed' => $status]);

        return redirect()->back();
    }

    // action free product (add - remove)
    public function actionFreeProduct(Product $product, $status) {

        $product->free = $status;
        $product->save();
        $statusText = __('messages.added_to_offer_buy_two_get_one');
        if ($status == 0) {
            $statusText = __('messages.removed_from_offer_buy_two_get_one');
        }

        return redirect()->route('products.offers')
            ->with('success', $statusText);
    }

    // get offers
    public function getOffers() {
        $data['products'] = Product::where('deleted', 0)->where('free', 1)->where('store_id', auth()->guard('shop')->user()->id)->orderBy('id' , 'desc')->get();

        return view('shop_admin.products.offers', ['data' => $data]);
    }



}
