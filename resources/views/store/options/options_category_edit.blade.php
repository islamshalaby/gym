@extends('store.app')
@section('title' , __('messages.edit_properties_category'))
@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.edit_properties_category') }}</h4>
                    </div>
                </div>
                <form action="" method="post">
                    @csrf

                    <div class="form-group mb-4">
                        <label for="title_en">{{ __('messages.title_en') }}</label>
                        <input required type="text" name="title_en" class="form-control" id="title_en"
                               placeholder="{{ __('messages.title_en') }}" value="{{ $data['category']['title_en'] }}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="title_ar">{{ __('messages.title_ar') }}</label>
                        <input required type="text" name="title_ar" class="form-control" id="title_ar"
                               placeholder="{{ __('messages.title_ar') }}" value="{{ $data['category']['title_ar'] }}">
                    </div>
                    <input type="submit" value="{{ __('messages.submit') }}" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
@endsection
