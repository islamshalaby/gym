@extends('store.app')
@section('title' , __('messages.request_details'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.store_details') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered mb-4">
                        <tbody>
                        <tr>
                            <td class="label-table"> {{ __('messages.logo') }}</td>
                            <td><img src="{{image_cloudinary_url()}}{{ $data['store']['logo'] }}"/></td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.cover') }}</td>
                            <td><img src="{{image_cloudinary_url()}}{{ $data['store']['cover'] }}"/></td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.name') }}</td>
                            <td>{{ $data['store']->name }}</td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.email') }}</td>
                            <td>{{ $data['store']['email'] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection



