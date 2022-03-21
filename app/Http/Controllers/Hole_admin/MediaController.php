<?php

namespace App\Http\Controllers\Hole_admin;

use App\Hole_media;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cloudinary;

class MediaController extends Controller
{

    // get all contact us messages
    public function index()
    {
        $id = auth()->guard('hole')->user()->id;
        $data = Hole_media::where('hole_id', $id)->orderBy('created_at', 'desc')->get();
        return view('hole_admin.media.index', compact('data'));
    }

    public function store(Request $request)
    {
        $id = auth()->guard('hole')->user()->id;
        $data = $this->validate(\request(),
            [
                'images' => 'required'
            ]);

        $data_image['hole_id'] = $id;
        $i = 0;
        foreach ($request->images as $image) {
            $extension = $image->getClientOriginalExtension();

            $list_video_ext = array('flv', 'mp4', 'm3u8', 'ts', '3gp', 'mov', 'avi', 'wmv');
            if (in_array($extension, $list_video_ext)) {
                $story = $image->getRealPath();
                if ($image->getSize()) {
                    $uploadedFileUrl = $this->uploadFromApi($image);
                    $image_id2 = $uploadedFileUrl->getPublicId();
                    $image_format2 = $uploadedFileUrl->getExtension();
                    $image_new_story = $image_id2 . '.' . $image_format2;
                    $data_image['image'] = $image_new_story;
                    $data_image['type'] = 'video';
                    if (count($request->thumbnail) > 0) {
                        $thumbImage = Cloudinary::upload($request->thumbnail[$i]->getRealPath());
                        $publicThumb = $thumbImage->getPublicId();
                        $formatThumb = $thumbImage->getExtension();
                        $data_image['thumbnail'] = $publicThumb . '.' . $formatThumb;
                    }
                }
            } else {
                $image_name = $image->getRealPath();
                $thumbImage = Cloudinary::upload($image_name);
                $publicThumb = $thumbImage->getPublicId();
                $formatThumb = $thumbImage->getExtension();
                $data_image['image'] = $publicThumb . '.' . $formatThumb;
            }
            Hole_media::create($data_image);
            $i ++;
        }
        session()->flash('success', trans('messages.added_s'));
        return back();
    }

    public function store_video(Request $request)
    {
        $id = auth()->guard('hole')->user()->id;
        $data = $this->validate(\request(),
            [
                'video' => 'required'
            ]);
        $story = $request->file('video')->getRealPath();
        if ($request->file('video')->getSize()) {
            $uploadedFileUrl = $this->upload($request->file('video'));
            $image_id2 = $uploadedFileUrl->getPublicId();
            $image_format2 = $uploadedFileUrl->getExtension();
            $image_new_story = $image_id2 . '.' . $image_format2;
            $data['image'] = $image_new_story;
        }
        $data_image['hole_id'] = $id;
        Hole_media::create($data);
        session()->flash('success', trans('messages.added_s'));
        return back();
    }

    public function destroy(Request $request)
    {
        Hole_media::where('id', $request->media_id)->delete();
        session()->flash('success', trans('messages.deleted_s'));
        return redirect()->back();
    }
}
