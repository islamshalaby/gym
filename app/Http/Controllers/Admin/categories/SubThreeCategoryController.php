<?php

namespace App\Http\Controllers\Admin\categories;
use App\Http\Controllers\Admin\AdminController;
use Cloudinary;
use Illuminate\Http\Request;
use App\SubThreeCategory;

class SubThreeCategoryController extends AdminController
{

    public function index()
    {

    }
    public function create($id)
    {
        return view('admin.categories.sub_catyegory.sub_two_category.sub_three_category.create',compact('id'));
    }
    public function store(Request $request)
    {
        $data = $this->validate(\request(),
            [
                'sub_category_id' => 'required',
                'title_ar' => 'required',
                'title_en' => 'required',
                'image' => 'required'
            ]);
        $image_name = $request->file('image')->getRealPath();
        $imagereturned = Cloudinary::upload($image_name);
        $image_id = $imagereturned->getPublicId();
        $image_format = $imagereturned->getExtension();
        $image_new_name = $image_id . '.' . $image_format;
        $data['image'] = $image_new_name;
        SubThreeCategory::create($data);
        session()->flash('success', trans('messages.added_s'));
        return redirect( route('sub_three_cat.show',$request->sub_category_id));
    }
    public function show($id)
    {
        $cat_id = $id;
        $data = SubThreeCategory::where('sub_category_id',$id)->where('deleted','0')->get();
        return view('admin.categories.sub_catyegory.sub_two_category.sub_three_category.index',compact('data','cat_id'));
    }

    public function edit($id) {
        $data = SubThreeCategory::where('id',$id)->first();
        return view('admin.categories.sub_catyegory.sub_two_category.sub_three_category.edit', compact('data'));
    }
    public function update(Request $request, $id) {
        $model = SubThreeCategory::where('id',$id)->first();
        $data = $this->validate(\request(),
            [
                'title_ar' => 'required',
                'title_en' => 'required'
            ]);
        if($request->file('image')){
            $image_name = $request->file('image')->getRealPath();
            $imagereturned = Cloudinary::upload($image_name);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_name = $image_id . '.' . $image_format;
            $data['image'] = $image_new_name;
        }
        SubThreeCategory::where('id',$id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return redirect( route('sub_three_cat.show',$model->sub_category_id));
    }
    public function destroy($id)
    {
        $data['deleted'] = "1";
        SubThreeCategory::where('id',$id)->update($data);
        session()->flash('success', trans('messages.deleted_s'));
        return back();
    }
}
