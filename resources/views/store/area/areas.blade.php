@extends('store.app')

@section('title' , __('messages.show_areas'))

@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.show_areas') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="without-print" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ __('messages.area_title') }}</th>
                            <th class="text-center">{{ __('messages.governorate') }}</th>
                            <th class="text-center">{{ __('messages.details') }}</th>
                            @if(Auth::user()->update_data)
                                <th class="text-center">{{ __('messages.edit') }}</th>
                            @endif
                            @if(Auth::user()->delete_data)
                                <th class="text-center">{{ __('messages.delete') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['areas'] as $area)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ App::isLocale('en') ? $area->title_en : $area->title_ar }}</td>
                                <td class="text-center">
                                    <a href="{{ route('areas.governorates.details', $area->governorate_id) }}"
                                       target="_blank">{{ App::isLocale('en') ? $area->governorate->title_en : $area->governorate->title_ar }}</a>
                                </td>
                                <td class="text-center blue-color"><a href="{{ route('areas.details', $area->id) }}"><i
                                            class="far fa-eye"></i></a></td>
                                @if(Auth::user()->update_data)
                                    <td class="text-center blue-color"><a href="{{ route('areas.edit', $area->id) }}"><i
                                                class="far fa-edit"></i></a></td>
                                @endif
                                @if(Auth::user()->delete_data)
                                    <td class="text-center blue-color">
                                        @if(count($area->stores) > 0)
                                            {{ __('messages.area_delivery_added') }}
                                        @else
                                            <a onclick='return confirm("{{ __('messages.are_you_sure') }}");'
                                               href="{{ route('areas.delete', $area->id) }}"><i
                                                    class="far fa-trash-alt"></i></a>
                                        @endif
                                    </td>
                                @endif
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
