@extends('coach.app')
@section('title' , __('messages.add_avilable_times'))
@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <a class="btn btn-info"
                           href="{{route('coaches.times',$id)}}">{{ __('messages.avilable_times') }}</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.add_avilable_times') }}</h4>
                    </div>
                </div>
            </div>
            @if (session('status'))
                <div class="alert alert-danger mb-4" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">x</button>
                    <strong>Error!</strong> {{ session('status') }} </button>
                </div>
            @endif

            <form method="post" action="{{route('coach_times.store')}}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="coach_id" value="{{$id}}">
                <div class="form-group row">
                    <div class="col-md-6" id="special1_cont">
                        <label for="plan_price">{{ __('messages.from') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr" name="time_from"
                                   class="form-control flatpickr flatpickr-input active" type="text">
                        </div>
                    </div>
                    <div class="col-md-6" id="special2_cont">
                        <label for="plan_price">{{ __('messages.to') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr_2" name="time_to"
                                   class="form-control flatpickr flatpickr-input active" type="text">
                        </div>
                    </div>
                </div>
                <input type="submit" value="{{ __('messages.add') }}" class="btn btn-primary">
            </form>
        </div>
    </div>
@endsection
