@extends('coach.app')

@section('title' , __('messages.add_new_coach'))

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.add_new_coach') }}</h4>
                    </div>
                </div>
            </div>
            @if (session('status'))
                <div class="alert alert-danger mb-4" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">x</button>
                    <strong>Error!</strong> {{ session('status') }} </button>
                </div>
            @endif

            <form method="post" action="{{route('coaches.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.name_ar') }}</label>
                    <input required type="text" name="name" class="form-control" id="name">
                </div>
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.name_en') }}</label>
                    <input required type="text" name="name_en" class="form-control" id="name">
                </div>

                <div class="form-group mb-4">
                    <label for="email">{{ __('messages.email') }}</label>
                    <input required type="Email" class="form-control" id="email" name="email">
                </div>
                <div class="form-group mb-4">
                    <label for="password">{{ __('messages.password') }}</label>
                    <input required type="password" class="form-control" id="password" name="password">
                </div>
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.phone') }}</label>
                    <input required type="tel" name="phone" class="form-control" id="name">
                </div>
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.age') }}</label>
                    <input required type="number" min="1" name="age" class="form-control" id="name">
                </div>
                <div class="form-group mb-4">
                    <label for="name">{{ __('messages.exp_years') }}</label>
                    <input required type="number" min="0" name="exp" class="form-control" id="name">
                </div>
                <label for="name">{{ __('messages.work_days') }}</label>
                <div class="form-group row">
                    <div class="col-md-6" id="special1_cont">
                        <label for="plan_price">{{ __('messages.from') }}</label>
                        <div class="form-group mb-0">
                            <select required class="form-control" name="work_day_from" id="cmb_work_day_from">
                                <option selected disabled>{{ __('messages.choose_day') }}</option>
                                @foreach ($days as $row)
                                    @if( app()->getLocale() == 'en')
                                        <option value="{{ $row->id }}">{{ $row->name_en }}</option>
                                    @else
                                        <option value="{{ $row->id }}">{{ $row->name_ar }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" id="special2_cont">
                        <label for="plan_price">{{ __('messages.to') }}</label>
                        <div class="form-group mb-0">
                            <select required class="form-control" name="work_day_to" id="cmb_work_day_to">
                                <option selected disabled>{{ __('messages.choose_day') }}</option>
                                @foreach ($days as $row)
                                    @if( app()->getLocale() == 'en')
                                        <option value="{{ $row->id }}">{{ $row->name_en }}</option>
                                    @else
                                        <option value="{{ $row->id }}">{{ $row->name_ar }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <label for="name">{{ __('messages.avilable_times') }}</label>
                <div class="form-group row">
                    <div class="col-md-6" id="special1_cont">
                        <label for="plan_price">{{ __('messages.from') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr" name="time_from" required
                                   class="form-control flatpickr flatpickr-input active" type="text">
                        </div>
                    </div>
                    <div class="col-md-6" id="special2_cont">
                        <label for="plan_price">{{ __('messages.to') }}</label>
                        <div class="form-group mb-0">
                            <input id="timeFlatpickr_2" name="time_to" required
                                   class="form-control flatpickr flatpickr-input active" type="text">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="exampleFormControlTextarea1">{{ __('messages.about_coach_ar') }}</label>
                    <textarea class="form-control" name="about_coach" id="exampleFormControlTextarea1"
                              rows="4"></textarea>
                </div>
                <div class="form-group mb-4">
                    <label for="exampleFormControlTextarea1">{{ __('messages.about_coach_en') }}</label>
                    <textarea class="form-control" name="about_coach_en" id="exampleFormControlTextarea1"
                              rows="4"></textarea>
                </div>
                <div class="form-group mb-4 mt-3">
                    <label for="exampleFormControlFile1">{{ __('messages.image') }}</label>
                    <div class="custom-file-container" data-upload-id="myFirstImage">
                        <label>{{ __('messages.upload') }} ({{ __('messages.single_image') }}) <a
                                href="javascript:void(0)" class="custom-file-container__image-clear"
                                title="Clear Image">x</a></label>
                        <label class="custom-file-container__custom-file">
                            <input type="file" required name="image"
                                   class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                            <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                        </label>
                        <div class="custom-file-container__image-preview"></div>
                    </div>
                </div>
                <input type="submit" value="{{ __('messages.add') }}" class="btn btn-primary">
            </form>
        </div>

@endsection
