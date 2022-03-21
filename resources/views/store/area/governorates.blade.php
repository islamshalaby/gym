@extends('store.app')

@section('title' , __('messages.show_governorates'))

@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.show_governorates') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="without-print" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ __('messages.governorate') }}</th>
                            <th class="text-center">{{ __('messages.areas') }}</th>
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
                        @foreach ($data['governorates'] as $governorate)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ App::isLocale('en') ? $governorate->title_en : $governorate->title_ar }}</td>
                                <td>
                                    @if (count($governorate->areas) > 0)
                                        @foreach ($governorate->areas as $area)
                                            <a href="{{ route('areas.details', $area->id) }}" target="_blank">
                                                <span style="margin-bottom: 5px"
                                                      class="badge outline-badge-info">{{ App::isLocale('en') ? $area->title_en : $area->title_ar }}</span>
                                            </a>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-center blue-color"><a
                                        href="{{ route('areas.governorates.details', $governorate->id) }}"><i
                                            class="far fa-eye"></i></a></td>
                                @if(Auth::user()->update_data)
                                    <td class="text-center blue-color"><a
                                            href="{{ route('areas.governorates.edit', $governorate->id) }}"><i
                                                class="far fa-edit"></i></a></td>
                                @endif
                                @if(Auth::user()->delete_data)
                                    <td class="text-center blue-color">
                                        @if (count($governorate->areas) > 0)
                                            {{ __('messages.governorate_has_areas') }}
                                        @else
                                            <a onclick='return confirm("{{ __('messages.are_you_sure') }}");'
                                               href="{{ route('areas.governorates.delete', $governorate->id) }}"><i
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
