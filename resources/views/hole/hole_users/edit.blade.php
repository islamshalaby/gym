@extends('hole.app')
@section('title' , __('messages.edit'))
@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.edit') }}</h4>
                    </div>
                </div>
            </div>
            <form method="post" action="{{route('halls.update',$data->id)}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.hole_name_ar') }}</label>
                    <input required type="text" name="name" value="{{$data->name}}" class="form-control" id="name">
                </div>
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.hole_name_en') }}</label>
                    <input required type="text" name="name_en" value="{{$data->name_en}}" class="form-control"
                           id="name">
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
                    <label for="exampleFormControlTextarea1">{{ __('messages.about_hole_ar') }}</label>
                    <textarea class="form-control" name="about_hole" id="exampleFormControlTextarea1" rows="4">
                        {{$data->about_hole}}
                    </textarea>
                </div>
                <div class="form-group mb-4">
                    <label for="exampleFormControlTextarea1">{{ __('messages.about_hole_en') }}</label>
                    <textarea class="form-control" name="about_hole_en" id="exampleFormControlTextarea1" rows="4">
                        {{$data->about_hole_en}}
                    </textarea>
                </div>
                <div class="form-group mb-4 mt-3">
                    <label for="exampleFormControlFile1">{{ __('messages.logo') }}</label>
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
                            <input type="file" name="logo" class="custom-file-container__custom-file__custom-file-input"
                                   accept="image/*">
                            <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                        </label>
                        <div class="custom-file-container__image-preview">
                        </div>
                    </div>
                </div>
                <h4>{{ __('messages.cover') }}</h4>
                <div class="row">
                    <div class="col-md-2 product_image">
                        <img style="width: 50%" src="{{image_cloudinary_url()}}{{ $data->cover }}"/>
                    </div>
                </div>
                <div class="custom-file-container" data-upload-id="myFirstImage">
                    <label>{{ __('messages.upload') }} ({{ __('messages.single_image') }}) <a
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
                <h4>{{ __('messages.appointment') }}</h4>
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="plan_price"> &nbsp; </label>
                        <div class="form-check pl-0">
                            <div class="custom-control custom-checkbox checkbox-info">
                                <input type="checkbox" @if($time_male != null) checked
                                       @endif class="custom-control-input" name="male" value="male" id="male_hole">
                                <label class="custom-control-label"
                                       for="male_hole">{{ __('messages.male_hole') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" id="special1_cont">
                        <label for="plan_price">{{ __('messages.from') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr" @if($time_male != null) value="{{$time_male->time_from}}"
                                   @endif name="male_hole_from" class="form-control flatpickr flatpickr-input active"
                                   type="text">
                        </div>
                    </div>
                    <div class="col-md-4" id="special2_cont">
                        <label for="plan_price">{{ __('messages.to') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr_2" @if($time_male != null) value="{{$time_male->time_from}}"
                                   @endif name="male_hole_to" class="form-control flatpickr flatpickr-input active"
                                   type="text">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="plan_price"> &nbsp; </label>
                        <div class="form-check pl-0">
                            <div class="custom-control custom-checkbox checkbox-info">
                                <input type="checkbox" @if($time_female != null) checked
                                       @endif class="custom-control-input" name="female" value="female"
                                       id="female_hole">
                                <label class="custom-control-label"
                                       for="female_hole">{{ __('messages.female_hole') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" id="special1_cont">
                        <label for="plan_price">{{ __('messages.from') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr_3" @if($time_female != null) value="{{$time_female->time_from}}"
                                   @endif name="female_hole_from" class="form-control flatpickr flatpickr-input active"
                                   type="text">
                        </div>
                    </div>
                    <div class="col-md-4" id="special2_cont">
                        <label for="plan_price">{{ __('messages.to') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr_4" @if($time_female != null) value="{{$time_female->time_from}}"
                                   @endif name="female_hole_to" class="form-control flatpickr flatpickr-input active"
                                   type="text">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="plan_price"> &nbsp; </label>
                        <div class="form-check pl-0">
                            <div class="custom-control custom-checkbox checkbox-info">
                                <input type="checkbox" @if($time_mix != null) checked
                                       @endif class="custom-control-input" name="mix" value="mix" id="mix_hole">
                                <label class="custom-control-label" for="mix_hole">{{ __('messages.mix_hole') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" id="special1_cont">
                        <label for="plan_price">{{ __('messages.from') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr_5" @if($time_mix != null) value="{{$time_mix->time_to}}"
                                   @endif name="mix_hole_from" class="form-control flatpickr flatpickr-input active"
                                   type="text">
                        </div>
                    </div>
                    <div class="col-md-4" id="special2_cont">
                        <label for="plan_price">{{ __('messages.to') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr_6" @if($time_mix != null) value="{{$time_mix->time_to}}"
                                   @endif name="mix_hole_to" class="form-control flatpickr flatpickr-input active"
                                   type="text">
                        </div>
                    </div>
                </div>
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
