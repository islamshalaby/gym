@extends('admin.app')
@section('title' , __('messages.ad_edit'))
@push('scripts')
    <script>
        $("select#users").on("change", function () {
            $('select#products').html("")
            var userId = $(this).find("option:selected").val();
            console.log(userId)
            $.ajax({
                url: "/admin-panel/ads/fetchproducts/" + userId,
                type: 'GET',
                success: function (data) {
                    $('.productsParent').show()
                    $('select#products').prop("disabled", false)
                    data.forEach(function (product) {
                        $('select#products').append(
                            "<option value='" + product.id + "'>" + product.title + "</option>"
                        )
                    })
                }
            })
        })
        var user = $("select#users option:selected").val()
        @if ($data['ad']['type'] == 'id')
        $.ajax({
            url: "/admin-panel/ads/fetchproducts/" + user,
            type: 'GET',
            success: function (data) {
                $('.productsParent').show()
                $('select#products').prop("disabled", false)
                var productId = "{{ $data['ad']['content'] }}"
                data.forEach(function (product) {
                    var selected = ""
                    if (productId == product.id) {
                        selected = "selected"
                    }
                    $('select#products').append(
                        "<option " + selected + " value='" + product.id + "'>" + product.title + "</option>"
                    )
                })
            }
        })
        @endif
        $("#ad_type").on("change", function () {
            if (this.value == 'link') {
                $(".outside").show()
                $('.productsParent').hide()
                $('select#products').prop("disabled", true)
                $(".outside input").prop("disabled", false)
                $(".coach").hide()
                $(".hall").hide()
            } else if (this.value == 'hall') {
                $(".hall").show()
                $('.productsParent').hide()
                $('select#products').prop("disabled", true)
                $(".hall input").prop("disabled", false)
                $(".outside").hide()
                $(".coach").hide()
            } else if (this.value == 'coach') {
                $(".coach").show()
                $('.productsParent').hide()
                $('select#products').prop("disabled", true)
                $(".coach input").prop("disabled", false)
                $(".outside").hide()
                $(".hall").hide()
            }
        })
    </script>
@endpush

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.ad_edit') }}</h4>
                    </div>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="">{{ __('messages.current_image') }}</label><br>
                        <img src="{{image_cloudinary_url()}}{{ $data['ad']['image'] }}"/>
                    </div>
                    <div class="custom-file-container" data-upload-id="myFirstImage">
                        <label>{{ __('messages.change_image') }} ({{ __('messages.single_image') }}) <a
                                href="javascript:void(0)" class="custom-file-container__image-clear"
                                title="Clear Image">x</a></label>
                        <label class="custom-file-container__custom-file">
                            <input type="file" name="image"
                                   class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                            <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                        </label>
                        <div class="custom-file-container__image-preview"></div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="title_ar">{{ __('messages.title_ar') }}</label>
                        <input type="text" value="{{ $data['ad']['title_ar'] }}" name="title_ar" class="form-control"
                               id="title_ar">
                    </div>
                    <div class="form-group mb-4">
                        <label for="title_en">{{ __('messages.title_en') }}</label>
                        <input type="text" name="title_en" value="{{ $data['ad']['title_en'] }}" class="form-control"
                               id="title_en">
                    </div>
                    <div class="form-group mb-4">
                        <label for="exampleFormControlTextarea1">{{ __('messages.desc_ar') }}</label>
                        <textarea class="form-control" name="desc_ar" id="exampleFormControlTextarea1"
                                  rows="3">{{ $data['ad']['desc_ar'] }}</textarea>
                    </div>
                    <div class="form-group mb-4">
                        <label for="exampleFormControlTextarea1">{{ __('messages.desc_en') }}</label>
                        <textarea class="form-control" name="desc_en" id="exampleFormControlTextarea1"
                                  rows="3">{{ $data['ad']['desc_en'] }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="sel1">{{ __('messages.ad_type') }}</label>
                        <select id="ad_type" name="type" class="form-control">
                            <option selected>{{ __('messages.select') }}</option>
                            <option
                                {{ $data['ad']['type'] == 'link' ? 'selected' : '' }} value="link">{{ __('messages.outside_the_app') }}</option>
                            <option
                                {{ $data['ad']['type'] == 'hall' ? 'selected' : '' }} value="hall">{{ __('messages.hall') }}</option>
                            <option
                                {{ $data['ad']['type'] == 'coach' ? 'selected' : '' }} value="coach">{{ __('messages.coach') }}</option>
                            {{--<option {{ $data['ad']['type'] == 'seller' ? 'selected' : '' }} value="seller">{{ __('messages.seller') }}</option>--}}
                        </select>
                    </div>
                    <div @if($data['ad']['type'] == 'link') @else style="display: none"
                         @endif class="form-group mb-4 outside">
                        <label for="link">{{ __('messages.link') }}</label>
                        <input type="text" value="{{$data['ad']['content']}}" name="content" class="form-control"
                               id="link"
                               placeholder="{{ __('messages.link') }}" value="">
                    </div>
                    <div @if($data['ad']['type'] == 'hall') @else style="display: none" @endif class="form-group hall">
                        <label for="sel1">{{ __('messages.halls') }}</label>
                        <select id="hall_cmb" name="hall" class="form-control">
                            <option selected>{{ __('messages.select') }}</option>
                            @foreach($data['halls'] as $row)
                                @if($row->id == $data['ad']['content'])
                                    <option selected value="{{$row->id}}">{{ $row->hallname }}</option>
                                @else
                                    <option value="{{$row->id}}">{{ $row->hallname }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div @if($data['ad']['type'] == 'coach') @else style="display: none"
                         @endif class="form-group coach">
                        <label for="sel1">{{ __('messages.coaches') }}</label>
                        <select id="hall_cmb" name="coach" class="form-control">
                            <option selected>{{ __('messages.select') }}</option>
                            @foreach($data['coaches'] as $row)
                                @if($row->id == $data['ad']['content'])
                                    <option selected value="{{$row->id}}">{{ $row->coachname }}</option>
                                @else
                                    <option value="{{$row->id}}">{{ $row->coachname }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="submit" value="{{ __('messages.edit') }}" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
@endsection
