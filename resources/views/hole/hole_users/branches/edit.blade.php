@extends('hole.app')
@section('title' , __('messages.edit'))
@section('content')
    <?php
    $lat = $data->latitude;
    $lng = $data->longitude;
    ?>
    {{--    //--------------------------------------------------------}}
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.edit') }}</h4>
                    </div>
                </div>
            </div>
            <form method="post" action="{{route('admin_branches.update',$data->id)}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.branch_ar') }}</label>
                    <input required type="text" name="title_ar" value="{{$data->title_ar}}" class="form-control"
                           id="title_ar">
                </div>
                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.branch_en') }}</label>
                    <input required type="text" class="form-control" value="{{$data->title_en}}" id="title_en"
                           name="title_en">
                </div>
                <h4>{{ __('messages.location_in_map') }}</h4>
                <label>{{ __('messages.to_choose_place') }}</label>
                <div class="form-group row">
                    <div class="card-body parent" style='text-align:right' id="parent">
                        <div id="" class="form-group row">
                            <div class="col-sm-12 ">
                                <div id="us1" style="width:100%;height:400px;"></div>
                            </div>
                            <input required type="hidden" name="latitude" id="lat" value="{{$lat}}">
                            <input required type="hidden" name="longitude" id="lng" value="{{$lng}}">
                        </div>
                    </div>
                </div>
                <input type="submit" value="{{ __('messages.edit') }}" class="btn btn-primary">
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function myMap() {
            var mapProp = {
                center: new google.maps.LatLng({{$lat}},{{$lng}}),
                zoom: 5,
            };
            var map = new google.maps.Map(document.getElementById("us1"), mapProp);
        }
    </script>
    <script
        src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDPN_XufKy-QTSCB68xFJlqtUjHQ8m6uUY&callback=myMap"></script>
    <script src="{{url('/')}}/admin/assets/js/locationpicker.jquery.js"></script>
    <script>
        $('#us1').locationpicker({
            location: {
                latitude: {{$lat}},
                longitude: {{$lng}}
            },
            radius: 300,
            markerIcon: "{{url('/images/map-marker.png')}}",
            inputBinding: {
                latitudeInput: $('#lat'),
                longitudeInput: $('#lng')
            }
        });
    </script>
@endsection
