@extends('hole_admin.app')
@section('title' , __('messages.booking'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <h4>{{ __('messages.booking') }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <a class="btn btn-info" href="{{route('booking.create')}}"> {{ __('messages.add') }} </a>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <a class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">{{ __('messages.booking_name') }}</th>
                            <th class="text-center">{{ __('messages.details_easy') }}</th>
                            <th class="text-center">{{ __('messages.price') }}</th>
                            <th class="text-center">{{ __('messages.the_discount') }}</th>
                            <th class="text-center">{{ __('messages.discount_price') }}</th>
                            <th class="text-center">{{ __('messages.plan_features') }}</th>
                            <th class="text-center">{{ __('messages.common') }}</th>
                            <th class="text-center">{{ __('messages.edit') }}</th>
                            <th class="text-center">{{ __('messages.delete') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data as $row)
                            <tr>
                                <td class="text-center"><?=$i;?></td>
                                <td class="text-center"> @if(app()->getLocale() == 'ar') {{$row->name_ar}} @else {{$row->name_en}} @endif </td>
                                <td class="text-center"> @if(app()->getLocale() == 'ar') {{$row->title_ar}} @else {{$row->title_en}} @endif </td>
                                <td class="text-center">{{ $row->price }}</td>
                                <td class="text-center">
                                    @if($row->is_discount == '1')
                                        {{ $row->discount }} %
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row->is_discount == '1')
                                        {{ $row->discount_price }}
                                    @endif
                                </td>
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
                                            <div class="title" style="padding-bottom: 20px;">
                                                <a data-booking-id="{{$row->id}}" data-toggle="modal"
                                                   data-target="#zoomup_group_Modal" id="btn_add_detail"
                                                   class="btn btn-success">{{ __('messages.add') }}</a>
                                            </div>
                                            @php $booking_details = \App\Hole_booking_detail::where('booking_id',$row->id)->get(); @endphp
                                            <table id="html5-extension" class="table table-hover non-hover"
                                                   style="width: 300px;">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">{{ __('messages.advance_name') }}</th>
                                                    <th class="text-center">{{ __('messages.delete') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($booking_details as $detail_row)
                                                    <tr>
                                                        <td class="text-center"> @if(app()->getLocale() == 'ar') {{$detail_row->name_ar}} @else {{$detail_row->name_en}} @endif </td>
                                                        <td class="text-center blue-color">
                                                            <a onclick="return confirm('{{ __('messages.delete_confirmation') }}');"
                                                               href="{{route('booking.destroy_detail',$detail_row->id)}}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                     height="24" viewBox="0 0 24 24" fill="none"
                                                                     stroke="currentColor" stroke-width="2"
                                                                     stroke-linecap="round" stroke-linejoin="round"
                                                                     class="feather feather-trash-2 table-cancel">
                                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                                    <path
                                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                                </svg>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center blue-color">
                                    @if($row->common == 1)
                                        <a href="{{route('booking.common',$row->id)}}"
                                           class="btn btn-warning  mb-2 mr-2 rounded-circle" title=""
                                           data-original-title="Tooltip using BUTTON tag">
                                            <i class="far fa-star"></i>
                                        </a>
                                    @else
                                        <a href="{{route('booking.common',$row->id)}}"
                                           class="btn btn-dark  mb-2 mr-2 rounded-circle" title=""
                                           data-original-title="Tooltip using BUTTON tag">
                                            <i class="far fa-star"></i>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center blue-color">
                                    <a href="{{route('booking.edit',$row->id)}}">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </td>
                                <td class="text-center blue-color">
                                    <a onclick="return confirm('{{ __('messages.delete_confirmation') }}');"
                                       href="{{route('booking.destroy',$row->id)}}">
                                        <i class="far fa-trash-alt"></i>
                                    </a>
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
    {{--    model--}}
    <div id="zoomup_group_Modal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.add_new_advanc') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <form action="{{route('booking_detail.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input required type="hidden" id="txt_booking_id" name="booking_id" class="form-control">
                    <div class="modal-body">
                        <div class="form-group mb-4">
                            <label for="plan_price">{{ __('messages.advance_name_ar') }}</label>
                            <input required type="text" name="name_ar" class="form-control">
                        </div>
                        <div class="form-group mb-4">
                            <label for="plan_price">{{ __('messages.advance_name_en') }}</label>
                            <input required type="text" name="name_en" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal">
                            <i class="flaticon-cancel-12"></i> {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '#btn_add_detail', function () {
                $("#txt_booking_id").val($(this).data('booking-id'));
            });
        });
    </script>
@endsection

