@extends('hole.app')
@section('title' , __('messages.hole_details'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.hole_details') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered mb-4">
                        <tbody>
                        <tr>
                            <td class="label-table"> {{ __('messages.hole_name') }}</td>
                            <td> @if(app()->getLocale() == 'ar') {{ $data->name }} @else {{ $data->name_en }} @endif </td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.email') }} </td>
                            <td>{{ $data->email }}</td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.phone') }} </td>
                            <td> {{ $data->phone }} </td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.about_hole') }} </td>
                            <td> @if(app()->getLocale() == 'ar') {{ $data->about_hole }} @else {{ $data->about_hole_en }} @endif  </td>
                        </tr>
                        </tbody>
                    </table>
                    <h4>{{ __('messages.appointment') }}</h4>
                    @php $time_works = \App\Hole_time_work::where('hole_id',$data->id)->get(); @endphp
                    <table class="table table-bordered mb-4">
                        <thead>
                        <tr>
                            <th class="text-center">{{ __('messages.type') }}</th>
                            <th class="text-center">{{ __('messages.from') }}</th>
                            <th class="text-center">{{ __('messages.to') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($time_works as $row)
                            <tr>
                                <td class="text-center">
                                    @if($row->type == 'male')
                                        {{ __('messages.male_hole') }}
                                    @elseif($row->type == 'female')
                                        {{ __('messages.female_hole') }}
                                    @elseif($row->type == 'mix')
                                        {{ __('messages.mix_hole') }}
                                    @endif
                                </td>
                                <td class="text-center">{{ date('g:i a', strtotime($row->time_from )) }}</td>
                                <td class="text-center">{{ date('g:i a', strtotime($row->time_to )) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <h4>{{ __('messages.branches') }}</h4>
                    @php $hole_branches = \App\Hole_branch::where('hole_id',$data->id)->get(); @endphp
                    <table class="table table-bordered mb-4">
                        <thead>
                        <tr>
                            <th class="text-center">{{ __('messages.branch_ar') }}</th>
                            <th class="text-center">{{ __('messages.branch_en') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($hole_branches as $row)
                            <tr>
                                <td class="text-center">{{$row->title_ar}}</td>
                                <td class="text-center">{{$row->title_en}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <h4>{{ __('messages.logo') }}</h4>
                    <div class="row">
                        <div class="col-md-2 product_image">
                            <img style="width: 100%"
                                 src="{{image_cloudinary_url()}}{{ $data->logo }}"/>
                        </div>
                    </div>
                    <h4>{{ __('messages.cover') }}</h4><br>
                    <div class="row">
                        <div style="position : relative" class="col-md-2 product_image">
                            <img width="100%"
                                 src="{{image_cloudinary_url()}}{{ $data->cover }}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



