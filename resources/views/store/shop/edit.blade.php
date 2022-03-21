@extends('store.app')
@php
    $page_title = __('messages.edit_store') . $data['store']['name'];
@endphp
@section('title' , $page_title)
@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ $page_title }}</h4>
                    </div>
                </div>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="list-unstyled mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="" autocomplete="do-not-show-ac" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="">{{ __('messages.current_logo') }}</label><br>
                        <img src="{{image_cloudinary_url()}}{{ $data['store']['logo'] }}"/>
                    </div>
                    <div class="custom-file-container" data-upload-id="myFirstImage">
                        <label>{{ __('messages.upload') }} ({{ __('messages.logo') }}) <a href="javascript:void(0)"
                                                                                          class="custom-file-container__image-clear"
                                                                                          title="Clear Image">x</a></label>
                        <label class="custom-file-container__custom-file">
                            <input type="file" name="logo" class="custom-file-container__custom-file__custom-file-input"
                                   accept="image/*">
                            <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                        </label>
                        <div class="custom-file-container__image-preview"></div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="">{{ __('messages.current_cover') }}</label><br>
                        <img src="{{image_cloudinary_url()}}{{ $data['store']['cover'] }}"/>
                    </div>
                    <div class="custom-file-container" data-upload-id="mySecondImage">
                        <label>{{ __('messages.upload') }} ({{ __('messages.cover') }}) <a href="javascript:void(0)"
                                                                                           class="custom-file-container__image-clear"
                                                                                           title="Clear Image">x</a> (
                            349x133 )</label>
                        <label class="custom-file-container__custom-file">
                            <input type="file" name="cover"
                                   class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                            <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                        </label>
                        <div class="custom-file-container__image-preview"></div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">{{ __('messages.name_ar') }}</label>
                        <input required type="text" name="name_ar" class="form-control" id="email"
                               placeholder="{{ __('messages.name_ar') }}" value="{{$data['store']['name_ar']}}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">{{ __('messages.name_en') }}</label>
                        <input required type="text" name="name_en" class="form-control" id="email"
                               placeholder="{{ __('messages.name_en') }}" value="{{$data['store']['name_en']}}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">{{ __('messages.desc_ar') }}</label>
                        <input required type="text" name="desc_ar" value="{{$data['store']['desc_ar']}}" class="form-control" id="desc_ar"
                               placeholder="{{ __('messages.desc_ar') }}" >
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">{{ __('messages.desc_en') }}</label>
                        <input required type="text" name="desc_en" value="{{$data['store']['desc_en']}}" class="form-control" id="desc_en"
                               placeholder="{{ __('messages.desc_en') }}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">{{ __('messages.phone') }}</label>
                        <input required type="text" name="phone" class="form-control" id="phone"
                               placeholder="{{ __('messages.phone') }}" value="{{$data['store']['phone']}}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">{{ __('messages.email') }}</label>
                        <input required type="text" name="email" class="form-control" id="email"
                               placeholder="{{ __('messages.email') }}" value="{{ $data['store']['email'] }}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="min_order_cost">{{ __('messages.min_order_cost') }}</label>
                        <input type="number" step="any" min="0" name="min_order_cost" class="form-control"
                               id="min_order_cost" placeholder="{{ __('messages.min_order_cost') }}"
                               value="{{ $data['store']['min_order_cost'] }}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password">{{ __('messages.password') }}</label>
                        <input type="password" autocomplete="do-not-show-ac" class="form-control" id="password"
                               name="password" placeholder="{{ __('messages.password') }}" value="">
                    </div>
                    <input type="submit" value="{{ __('messages.edit') }}" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
@endsection
