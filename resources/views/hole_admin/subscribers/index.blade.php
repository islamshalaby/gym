@extends('hole_admin.app')
@if(request()->segment(3) == 'start')
    @section('title' , __('messages.subscribers_current'))
@elseif(request()->segment(3) == 'ended')
    @section('title' , __('messages.subscribers_ended'))
@endif
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        @if(request()->segment(3) == 'start')
                            <h4>{{ __('messages.subscribers_current') }}</h4>
                        @elseif(request()->segment(3) == 'ended')
                            <h4>{{ __('messages.subscribers_ended') }}</h4>
                        @endif
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <a class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-center">{{ __('messages.booking_number') }}</th>
                            <th class="text-center">{{ __('messages.user_name') }}</th>
                            <th class="text-center">{{ __('messages.booking_name') }}</th>
                            <th class="text-center">{{ __('messages.booking_date') }}</th>
                            <th class="text-center">{{ __('messages.end_date') }}</th>
                            <th class="text-center">{{ __('messages.personal_data') }}</th>
                            <th class="text-center">{{ __('messages.status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data as $row)
                            <tr>
                                <td class="text-center">{{$row->id}}</td>
                                <td class="text-center"><a
                                        href="{{route('subscription.user_data',$row->user_id)}}">{{ $row->User->name }}</a>
                                </td>
                                <td class="text-center"> @if(app()->getLocale() == 'ar') {{ $row->Booking->name_ar }} @else {{ $row->Booking->name_en }} @endif </td>
                                <td class="text-center"> {{date('Y-m-d', strtotime($row->created_at))}}</td>
                                <td class="text-center"> {{date('Y-m-d', strtotime($row->expire_date))}}</td>
                                <td class="text-center">
                                    <div class="dropdown custom-dropdown">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink2"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-more-horizontal">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink2">
                                            <br>
                                            <table id="html5-extension" class="table table-hover non-hover"
                                                   style="width: 300px;">
                                                <tbody>
                                                @php $options = \App\Reservation_option::where('reservation_id',$row->id)->get(); @endphp
                                                @foreach($options as $reserve_data)
                                                    <tr>
                                                        <td class="text-center">
                                                            <h6>
                                                                @if(app()->getLocale() == 'ar')
                                                                    {{$reserve_data->Type->title_ar}}
                                                                @else
                                                                    {{$reserve_data->Type->title_en}}
                                                                @endif
                                                            </h6>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($reserve_data->goal_id > 0)
                                                                @if(count($reserve_data->Type->Goals)  > 0 )
                                                                    @if(app()->getLocale() == 'ar')
                                                                        {{$reserve_data->Goal->title_ar}}
                                                                    @else
                                                                        {{$reserve_data->Goal->title_en}}
                                                                    @endif
                                                                @else
                                                                    {{$reserve_data->goal_id}}
                                                                @endif
                                                            @else
                                                                {{$reserve_data->goal_id}}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center blue-color">
                                    @if($row->status == 'start')
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-dark btn-sm">{{ __('messages.current_reservayion') }}</button>
                                            <button type="button"
                                                    class="btn btn-dark btn-sm dropdown-toggle dropdown-toggle-split"
                                                    id="dropdownMenuReference5" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false" data-reference="parent">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                     class="feather feather-chevron-down">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuReference5">
                                                <a class="dropdown-item" href="{{route('subscription.end',$row->id)}}"
                                                   style="color: red; text-align: center;">{{ __('messages.end_subscription') }}</a>
                                                {{--                                                <div class="dropdown-divider"></div>--}}
                                                {{--                                                <a class="dropdown-item" href="javascript:void(0);" id="re_new_btn" data-reserve-id="{{$row->id}}" data-toggle="modal" data-target="#choose_booking"--}}
                                                {{--                                                   style="color: #2196f3; text-align: center;">{{ __('messages.resubscribe') }}</a>--}}
                                            </div>
                                        </div>
                                    @elseif($row->status == 'ended')
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-dark btn-sm">{{ __('messages.booking_ended') }}</button>
                                            <button type="button"
                                                    class="btn btn-dark btn-sm dropdown-toggle dropdown-toggle-split"
                                                    id="dropdownMenuReference5" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false" data-reference="parent">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                     class="feather feather-chevron-down">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </button>
                                            {{--                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuReference5">--}}
                                            {{--                                                <a class="dropdown-item" href="javascript:void(0);" id="re_new_btn" data-reserve-id="{{$row->id}}" data-toggle="modal" data-target="#choose_booking"--}}
                                            {{--                                                   style="color: #2196f3; text-align: center;">{{ __('messages.resubscribe') }}</a>--}}
                                            {{--                                            </div>--}}
                                        </div>
                                    @endif
                                </td>
                                <?php $i++; ?>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </a>
            </div>
        </div>
    </div>
    <div id="choose_booking" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.resubscribe') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <form action="{{route('subscription.re_new')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="reserv_id" id="txt_reserv_id">
                    <div class="modal-body">
                        <div class="widget widget-table-one">
                            <div class="widget-heading">
                                <h5 class="">{{ __('messages.choose_booking') }}</h5>
                            </div>
                            <div class="widget-content">
                                @foreach($bookings as $row)
                                    <div class="transactions-list">
                                        <div class="t-item">
                                            <div class="t-company-name">
                                                <div class="t-icon">
                                                    <label
                                                        class="new-control new-checkbox new-checkbox-rounded checkbox-info">
                                                        <input type="radio" name="booking_id" value="{{$row->id}}"
                                                               class="new-control-input">
                                                        <span class="new-control-indicator"></span> -
                                                    </label>
                                                </div>
                                                <div class="t-name">
                                                    <h4> @if(app()->getLocale() == 'ar') {{$row->name_ar}} @else {{$row->name_en}} @endif
                                                        &nbsp; ( {{$row->months_num}} {{ __('messages.month') }} ) </h4>
                                                    <p class="meta-date"> @if(app()->getLocale() == 'ar') {{$row->title_ar}} @else {{$row->title_en}} @endif </p>
                                                </div>
                                            </div>
                                            <div class="t-rate rate-inc">
                                                <p>
                                                    <span> @if($row->is_discount == '1') {{ $row->discount_price }} @else {{ $row->price }} @endif </span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                         class="feather feather-dollar-sign">
                                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                                        <path
                                                            d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                                    </svg>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="plan_price"> &nbsp; </label>
                            <div class="form-check pl-0">
                                <div class="custom-control custom-checkbox checkbox-info">
                                    <input type="checkbox" checked class="custom-control-input" name="add_money"
                                           value="add_money" id="add_money">
                                    <label class="custom-control-label"
                                           for="add_money">{{ __('messages.add_money_to_income') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal">
                            <i class="flaticon-cancel-12"></i> {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.renew') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '#re_new_btn', function () {
                $("#txt_reserv_id").val($(this).data('reserve-id'));
            });
        });
    </script>
@endsection

