<?php
namespace App\Http\Controllers\Shop_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductType;

class ProductTypeController extends Controller{

    // index
    public function show() {
        $data['types'] = ProductType::orderBy('id', 'desc')->get();
        return view('shop_admin.product_types.index', ['data' => $data]);
    }

    // add get
    public function AddGet() {
        return view('shop_admin.product_types.create');
    }

    // add post
    public function AddPost(Request $request) {
        $post = $request->all();
        ProductType::create($post);
        return redirect()->route('product_type.index');
    }

    // edit get
    public function EditGet(ProductType $type) {
        $data['type'] = $type;

        return view('shop_admin.product_types.edit', ['data' => $data]);
    }

    // edit post
    public function EditPost(Request $request, ProductType $type) {
        $post = $request->all();

        $type->update($post);

        return redirect()->route('product_type.index');
    }

    // delete
    public function delete(ProductType $type) {
        $type->delete();

        return redirect()->back();
    }

}
