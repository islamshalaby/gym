@extends('hole_admin.app')
@section('title' , __('messages.hall_data'))
@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.hall_data') }}</h4>
                    </div>
                </div>
            </div>
            <form method="post" action="{{route('hall.update',$data->id)}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.hole_name') }}</label>
                    <input required type="text" name="name" value="{{$data->name}}" class="form-control" id="name">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.email') }}</label>
                    <input required type="Email" value="{{$data->email}}" class="form-control" id="email" name="email">
                </div>
                <div class="form-group mb-4">
                    <label for="password">{{ __('messages.password') }}</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="form-group mb-4">
                    <label for="phone">{{ __('messages.phone') }}</label>
                    <input required type="phone" value="{{$data->phone}}" name="phone" class="form-control" id="phone">
                </div>
                <div class="form-group mb-4">
                    <label for="exampleFormControlTextarea1">{{ __('messages.about_hole') }}</label>
                    <textarea class="form-control" name="about_hole" id="exampleFormControlTextarea1" rows="4">
                        {{$data->about_hole}}
                    </textarea>
                </div>
{{--                <div class="form-group mb-4 mt-3">--}}
{{--                    <label for="exampleFormControlFile1">{{ __('messages.logo') }}</label>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-2 product_image">--}}
{{--                            <img style="width: 50%" src="{{image_cloudinary_url()}}{{ $data->logo }}"/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="custom-file-container" data-upload-id="mySecondImage">--}}
{{--                        <label>{{ __('messages.upload') }} ({{ __('messages.single_image') }}) <a--}}
{{--                                href="javascript:void(0)" class="custom-file-container__image-clear"--}}
{{--                                title="Clear Image">x</a></label>--}}
{{--                        <label class="custom-file-container__custom-file">--}}
{{--                            <input type="file" name="logo" class="custom-file-container__custom-file__custom-file-input"--}}
{{--                                   accept="image/*">--}}
{{--                            <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>--}}
{{--                            <span class="custom-file-container__custom-file__custom-file-control"></span>--}}
{{--                        </label>--}}
{{--                        <div class="custom-file-container__image-preview">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <h4>{{ __('messages.cover') }}</h4>--}}
{{--                <div class="row">--}}
{{--                    <div class="col-md-2 product_image">--}}
{{--                        <img style="width: 50%" src="{{image_cloudinary_url()}}{{ $data->cover }}"/>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="custom-file-container" data-upload-id="myFirstImage">--}}
{{--                    <label>{{ __('messages.upload') }} ({{ __('messages.single_image') }})</label>--}}
{{--                    <label class="custom-file-container__custom-file">--}}
{{--                        <input type="file" name="cover"--}}
{{--                               class="custom-file-container__custom-file__custom-file-input" accept="image/*">--}}
{{--                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>--}}
{{--                        <span class="custom-file-container__custom-file__custom-file-control"></span>--}}
{{--                    </label>--}}
{{--                    <div class="custom-file-container__image-preview"></div>--}}
{{--                </div>--}}





                <div class="form-group mb-4 mt-3">
                    <h4>{{ __('messages.logo') }}</h4>
                    <div class="row">
                        <div class="col-md-2 product_image">
                            <img style="width: 50%" src="{{image_cloudinary_url()}}{{ $data->logo }}"/>
                        </div>
                    </div>
                    <div class="custom-file-container" data-upload-id="mySecondImage">
                        <label>{{ __('messages.upload') }} ({{ __('messages.single_image') }}) <a
                                href="javascript:void(0)" class="custom-file-container__image-clear"
                                title="Clear Image">x</a></label>
                        <label class="custom-file-container__custom-file">
                            <input type="file" name="logo"
                                   class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                            <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                        </label>
                        <div class="custom-file-container__image-preview">

                        </div>
                    </div>
                </div>
                <<h4>{{ __('messages.cover') }}</h4>
                <div class="row">
                    <div class="col-md-2 product_image">
                        <img style="width: 50%" src="{{image_cloudinary_url()}}{{ $data->cover }}"/>
                    </div>
                </div>
                <div class="custom-file-container" data-upload-id="myFirstImage">
                    <label>{{ __('messages.upload') }} ({{ __('messages.multiple_images') }}) <a
                            href="javascript:void(0)" class="custom-file-container__image-clear"
                            title="Clear Image">x</a></label>
                    <label class="custom-file-container__custom-file">
                        <input type="file" name="cover"
                               class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                    </label>
                    <div class="custom-file-container__image-preview"></div>
                </div>
                <h4>{{ __('messages.story') }}</h4>
                <video width="200" height="200" controls>
                    <source src="https://res.cloudinary.com/dsibvtsiv/video/upload/v1621843606/{{ $data->story }}" type="video/mp4">
                    <source src="movie.ogg" type="video/ogg">
                    Your browser does not support the video tag.
                </video>
                <div class="custom-file-container" data-upload-id="myThirdImage">
                    <label>{{ __('messages.upload') }} ({{ __('messages.multiple_images') }}) <a
                            href="javascript:void(0)" class="custom-file-container__image-clear"
                            title="Clear Image">x</a></label>
                    <label class="custom-file-container__custom-file">
                        <input type="file" name="story"
                               class="custom-file-container__custom-file__custom-file-input" accept="video/*">
                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                    </label>
                    <div class="custom-file-container__image-preview"></div>
                </div>

{{--                <h4>{{ __('messages.story') }}</h4>--}}
{{--                <div class="custom-file-container" data-upload-id="myFirstImage">--}}
{{--                    <label>{{ __('messages.upload') }} ({{ __('messages.single_image') }}) </label>--}}
{{--                    <label class="custom-file-container__custom-file">--}}
{{--                        <input type="file" name="story"--}}
{{--                               class="custom-file-container__custom-file__custom-file-input" accept="video/*">--}}
{{--                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>--}}
{{--                        <span class="custom-file-container__custom-file__custom-file-control"></span>--}}
{{--                    </label>--}}
{{--                    <div class="custom-file-container__image-preview"></div>--}}
{{--                </div>--}}
                <input type="submit" value="{{ __('messages.edit') }}" class="btn btn-primary">
            </form>

        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var i = 0;
            $("#addButton").click(function () {
                var html = '';

                html += ' <div id="" class="form-group row">';
                html += ' ';
                //error here
                html += '<div class="col-sm-3 "><input  name="branches[' + i + '][title_ar]" type="text" class="form-control"></div>';
                html += '<div class="col-sm-3 "><input  name="branches[' + i + '][title_en]" type="text" class="form-control"></div>';
                html += '<div class="col-sm-3 "><input  name="branches[' + i + '][longitude]" type="text" class="form-control"></div>';
                html += '<div class="col-sm-3 "><input  name="branches[' + i + '][latitude]" type="text" class="form-control"></div>';
                html += "</div>";
                $('#parent').append(html);

                i++;
            });
        });
    </script>
@endsection
