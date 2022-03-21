@extends('store.app')

@section('title' , __('messages.add_by_areas'))

@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.add_by_areas') }}</h4>
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
                            <th>{{ __('messages.area') }}</th>
                            @if(Auth::user()->add_data)
                                <th class="text-center">
                                    {{ __('messages.add') }}
                                </th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['areas'] as $area)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ App::isLocale('en') ? $area->title_en : $area->title_ar }}</td>
                                @if(Auth::user()->add_data)
                                    <td class="text-center blue-color"><a
                                            href="{{ route('areas.add.byArea.delivercost', $area->id) }}"
                                            target="_blank"><i class="far fa-eye"></i></a></td>
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
