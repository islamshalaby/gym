@extends('coach.app')
@section('title' , __('messages.coach_details'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">

                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.coach_details') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered mb-4">
                        <tbody>
                        <tr>
                            <td class="label-table"> {{ __('messages.name') }}</td>
                            <td> @if(app()->getLocale() == 'ar') {{ $data->name }} @else {{ $data->name_en }} @endif </td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.email') }} </td>
                            <td>{{ $data->email }}</td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.phone') }} </td>
                            <td>{{ $data->phone }}</td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.age') }} </td>
                            <td>{{ $data->age }}</td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.exp_years') }} </td>
                            <td>{{ $data->exp }}</td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.gender') }} </td>
                            <td>
                                @if( $data->gender == 'male')
                                    {{ __('messages.male') }}
                                @else
                                    {{ __('messages.female') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.about_coach') }} </td>
                            <td> @if(app()->getLocale() == 'ar') {{ $data->about_coach }} @else {{ $data->about_coach_en }} @endif </td>
                        </tr>
                        </tbody>
                    </table>
                    <h4>{{ __('messages.appointment') }}</h4>
                    <table class="table table-bordered mb-4">
                        <thead>
                        <tr>
                            <th class="text-center">{{ __('messages.from') }}</th>
                            <th class="text-center">{{ __('messages.to') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($time_works as $row)
                            <tr>
                                <td class="text-center">{{ date('g:i a', strtotime($row->time_from )) }}</td>
                                <td class="text-center">{{ date('g:i a', strtotime($row->time_to)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <h4>{{ __('messages.image') }}</h4>
                    <br>
                    <div class="row">
                        <div class="col-md-2 product_image">
                            <img style="width: 100%"
                                 src="{{image_cloudinary_url()}}{{ $data->image}}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



