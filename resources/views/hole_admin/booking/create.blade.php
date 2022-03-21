@extends('hole_admin.app')
@section('title' , __('messages.add'))
@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.add') }}</h4>
                    </div>
                </div>
            </div>
            <form method="post" action="{{route('booking.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.booking_name_ar') }}</label>
                    <input required type="text" name="name_ar" class="form-control" id="name_ar">
                </div>
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.booking_name_en') }}</label>
                    <input required type="text" name="name_en" class="form-control" id="name_en">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.details_easy_ar') }}</label>
                    <input required type="text" class="form-control" id="title_ar" name="title_ar">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.details_easy_en') }}</label>
                    <input required type="text" class="form-control" id="title_en" name="title_en">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.price') }}</label>
                    <input required type="number" step="any" class="form-control" id="txt_price" name="price">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.bookng_month_num') }}</label>
                    <input required type="number" min="1" class="form-control" id="txt_months_num" name="months_num">
                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="plan_price"> &nbsp; </label>
                        <div class="form-check pl-0">
                            <div class="custom-control custom-checkbox checkbox-info">
                                <input type="checkbox" class="custom-control-input" name="cb_discount" value="discount"
                                       id="discount">
                                <label class="custom-control-label" for="discount">{{ __('messages.discount') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" id="discount_cont" style="display: none;">
                        <label for="plan_price">{{ __('messages.discount') }}</label>
                        <input type="number" step="any" id="txt_discount" name="discount" class="form-control">
                    </div>
                    <div class="col-md-4" id="discount_price_cont" style="display: none;">
                        <label for="plan_price">{{ __('messages.discount_price') }}</label>
                        <input type="number" step="any" readonly id="txt_discount_price" name="discount_price"
                               class="form-control">
                    </div>
                </div>
                <div class="card m-b-20">
                    <div class="card-title" style="padding-right: 20px;">
                        <strong> {{ __('messages.plan_features') }} </strong>

                    </div>
                    <div class="card-header" style="padding-right: 20px;">
                        <button class="btn btn-primary" type='button' value='Add Button' id='addButton'>
                            <i class="fa fa-plus"></i></button>
                    </div>
                    <div class="card-body parent" style='text-align:right' id="parent">

                        <div class="panel" style='text-align:right'>
                            <div id="" class="form-group row">
                                <div class='col-sm-5'><label>{{ __('messages.advance_name_ar') }}</label></div>
                                <div class='col-sm-5'><label>{{ __('messages.advance_name_en') }}</label></div>
                                <div class='col-sm-2'><label>{{ __('messages.delete') }}</label></div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="submit" value="{{ __('messages.add') }}" class="btn btn-primary">
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function myFun(j) {
            document.getElementById(j).style.display = 'none';
            document.getElementById('txt_name_ar' + j).value = '';
            document.getElementById('txt_name_en' + j).value = '';
        }
        $(document).ready(function () {
            var i = 0;
            $("#addButton").click(function () {
                var options = '';
                var html = '';
                html += " <div id='" + i + "' class='form-group row'>";
                html += "<div class='col-sm-5'> <input name='rows[" + i + "][name_ar]' class='form-control' id='txt_name_ar" + i + "' type='text' ></div>";
                html += "<div class='col-sm-5'> <input name='rows[" + i + "][name_en]' class='form-control' id='txt_name_en" + i + "' type='text' ></div>";
                html += "<div class='col-sm-2'> <button onclick='myFun(" + i + ")' class='btn btn-danger' type='button' data-t-id=" + i + "   id='delete'>" +
                    "                                        <i class='fa fa-trash'></i></button></div>";
                html += "</div>";
                $('#parent').append(html);
                i++;
            });
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
