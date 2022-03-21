<?php
namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Admin\AdminController;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;
use App\Category;
use Cloudinary;

class CategoryController extends AdminController{
    // type : get -> to add new
    public function AddGet(){
        return view('store.categories.create');
    }
    // type : post -> add new category
    public function AddPost(Request $request){
        $cover = $request->file('image')->getRealPath();
        $imagereturned = Cloudinary::upload($cover);
        $image_id = $imagereturned->getPublicId();
        $image_format = $imagereturned->getExtension();
        $image_new_name = $image_id . '.' . $image_format;

        $category = new Category();
        $category->title_en = $request->title_en;
        $category->title_ar = $request->title_ar;
        $category->image = $image_new_name;
        $category->save();

        session()->flash('success', trans('messages.added_s'));
        return redirect(route('shop.categories.show'));
    }
    // get all categories
    public function show(){
        $data['categories'] = Category::where('deleted' , 0)->orderBy('id' , 'desc')->get();
        return view('store.categories.index' , ['data' => $data]);
    }
    // get edit page
    public function EditGet(Request $request){
        $data['category'] = Category::find($request->id);
        return view('store.categories.edit' , ['data' => $data ]);
    }
    // edit category
    public function EditPost(Request $request){
        $category = Category::find($request->id);
        if($request->file('image')){
//            $image = $category->image;
//            $publicId = substr($image, 0 ,strrpos($image, "."));
//            if($publicId != null ){
//                Cloudder::delete($publicId);
//            }
            $cover = $request->file('image')->getRealPath();
            $imagereturned = Cloudinary::upload($cover);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_name = $image_id . '.' . $image_format;
            $category->image = $image_new_name;
        }
        $category->title_en = $request->title_en;
        $category->title_ar = $request->title_ar;
        $category->save();
        return redirect(route('shop.categories.show'));
    }
    // delete category
    public function delete(Request $request){
        $category = Category::find($request->id);
        $category->deleted = 1;
        $category->save();
        return redirect()->back();
    }

    // details
    public function details(Category $category) {
        $data['category'] = $category;
        return view('store.categories.category_details', ['data' => $data]);
    }
}
