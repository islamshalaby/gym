@extends('shop_admin.app')
@php
    $governorateName = App::isLocale('en') ? $data['governorate']->title_en : $data['governorate']->title_ar;
@endphp

@section('title' , __('messages.add_by_governorates') . " ( " . $governorateName . " ) ")

@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">

                        <h4>{{ __('messages.add_by_governorates') . " ( " . $governorateName . " ) " }}</h4>
                        <div class="alert alert-outline-primary mb-4" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                            <i class="flaticon-cancel-12 close" data-dismiss="alert"></i> {{ __('messages.on_submit') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="without-print" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>{{ __('messages.delivery_cost') . " ( " . __('messages.dinar') . " )" }}</th>
                            <th>{{ __('messages.estimated_arrival_time') . " ( " . __('messages.minutes') . " ) " }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['stores'] as $store)
                            @if( $store->id == Auth::guard('shop')->user()->id)
                            <tr>
                                <td colspan="3">
                                    <form method="post">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="store_id" value="{{ $store->id }}"/> <input
                                                type="hidden" name="governorate_id"
                                                value="{{ $data['governorate_id'] }}"/>
                                            <div class="form-group col-sm-4 mb-4">
                                                <input required type="number" step="any" min="0" name="delivery_cost"
                                                       class="form-control" id="delivery_cost"
                                                       placeholder="{{ __('messages.delivery_cost') }}" value="">
                                            </div>
                                            <div class="form-group  col-sm-4 mb-4">
                                                <input required type="number" step="any" min="0"
                                                       name="estimated_arrival_time" class="form-control"
                                                       id="estimated_arrival_time"
                                                       placeholder="{{ __('messages.estimated_arrival_time') }}"
                                                       value="">
                                            </div>
                                            <div class="form-group  col-sm-4 mb-4">
                                                <input type="submit" value="{{ __('messages.save') }}"
                                                       class="btn btn-primary">
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <?php $i++; ?>
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
