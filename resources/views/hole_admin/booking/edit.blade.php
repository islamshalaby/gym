@extends('hole_admin.app')
@section('title' , __('messages.edit'))
@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.edit') }}</h4>
                    </div>
                </div>
            </div>
            <form method="post" action="{{route('booking.new_update',$data->id)}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.booking_name_ar') }}</label>
                    <input required type="text" value="{{$data->name_ar}}" name="name_ar" class="form-control"
                           id="name_ar">
                </div>
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.booking_name_en') }}</label>
                    <input required type="text" value="{{$data->name_en}}" name="name_en" class="form-control"
                           id="name_en">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.details_easy_ar') }}</label>
                    <input required type="text" value="{{$data->title_ar}}" class="form-control" id="title_ar"
                           name="title_ar">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.details_easy_en') }}</label>
                    <input required type="text" value="{{$data->title_en}}" class="form-control" id="title_en"
                           name="title_en">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.price') }}</label>
                    <input required type="number" value="{{$data->price}}" step="any" class="form-control"
                           id="txt_price" name="price">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.bookng_month_num') }}</label>
                    <input required type="number" value="{{$data->months_num}}" min="1" class="form-control"
                           id="txt_months_num" name="months_num">
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="plan_price"> &nbsp; </label>
                        <div class="form-check pl-0">
                            <div class="custom-control custom-checkbox checkbox-info">
                                <input type="checkbox" @if($data->is_discount == 1) checked
                                       @endif class="custom-control-input" name="cb_discount" value="discount"
                                       id="discount">
                                <label class="custom-control-label" for="discount">{{ __('messages.discount') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" id="discount_cont"
                         @if($data->is_discount == 0) style="display: none;" @endif >
                        <label for="plan_price">{{ __('messages.discount') }}</label>
                        <input type="number" step="any" value="{{$data->discount}}" id="txt_discount" name="discount"
                               class="form-control">
                    </div>
                    <div class="col-md-4" id="discount_price_cont"
                         @if($data->is_discount == 0) style="display: none;" @endif >
                        <label for="plan_price">{{ __('messages.discount_price') }}</label>
                        <input type="number" step="any" value="{{$data->discount_price}}" readonly
                               id="txt_discount_price" name="discount_price" class="form-control">
                    </div>
                </div>
                <input type="submit" value="{{ __('messages.edit') }}" class="btn btn-primary">
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('input[id="discount"]').click(function () {
                if ($(this).prop("checked") == true) {
                    $('#discount_cont').show();
                    $('#discount_price_cont').show();
                } else if ($(this).prop("checked") == false) {
                    $('#discount_cont').hide();
                    $('#discount_price_cont').hide();
                }
            });
            var price;
            var discount;
            var final_disc;
            var total;
            var discount_price;
            $(document).on('keyup', '#txt_discount', function () {
                //To View Updated remain value afer pay on view
                discount = document.getElementById("txt_discount").value;
                price = document.getElementById("txt_price").value;
                final_disc = discount / 100;
                total = final_disc * price;
                discount_price = price - total;
                $("#txt_discount_price").val(discount_price);
            });
            $(document).on('keyup', '#txt_price', function () {
                //To View Updated remain value afer pay on view
                discount = document.getElementById("txt_discount").value;
                price = document.getElementById("txt_price").value;
                final_disc = discount / 100;
                total = final_disc * price;
                discount_price = price - total;
                $("#txt_discount_price").val(discount_price);
            });
        });
    </script>
@endsection
