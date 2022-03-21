@extends('store.app')
@php
    $areaName = App::isLocale('en') ? $data['area']->title_en : $data['area']->title_ar;
@endphp
@section('title' , __('messages.add_by_areas')  . " ( " . $areaName . " ) ")

@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.add_by_areas') . " ( " . $areaName . " ) " }}</h4>
                        {{--  @if($data['show_add'])
                        <a class="btn btn-primary" href="{{ route('areas.add.delivercost', $data['area']['id']) }}">{{ __('messages.add') }}</a>
                        @endif  --}}
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="without-print" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ __('messages.store') }}</th>
                            <th>{{ __('messages.delivery_cost') . " ( " . __('messages.dinar') . " )" }}</th>
                            <th>{{ __('messages.estimated_arrival_time') . " ( " . __('messages.minutes') . " ) " }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['stores'] as $store)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ $store->name }}  </td>
                                <td colspan="3">
                                    <form method="post">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="store_id" value="{{ $store->id }}"/><input
                                                type="hidden" name="area_id" value="{{ $data['area_id'] }}"/>
                                            <div class="form-group col-sm-4 mb-4">
                                                <input required type="number" step="any" min="0" name="delivery_cost"
                                                       class="form-control" id="delivery_cost"
                                                       placeholder="{{ __('messages.delivery_cost') }}"
                                                       value="{{ $store->deliveryByarea($data['area_id']) ? $store->deliveryByarea($data['area_id'])->delivery_cost : '' }}">
                                            </div>
                                            <div class="form-group  col-sm-4 mb-4">
                                                <input required type="number" step="any" min="0"
                                                       name="estimated_arrival_time" class="form-control"
                                                       id="estimated_arrival_time"
                                                       placeholder="{{ __('messages.estimated_arrival_time') }}"
                                                       value="{{ $store->deliveryByarea($data['area_id']) ? $store->deliveryByarea($data['area_id'])->estimated_arrival_time : '' }}">
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
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
