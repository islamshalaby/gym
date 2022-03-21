@extends('shop_admin.app')
@section('title' , __('messages.update_profile') )
@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.update_profile') }}</h4>
                    </div>
                </div>
                @if (session('status'))
                    <div class="alert alert-danger mb-4" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">x</button>
                        <strong>Error!</strong> {{ session('status') }} </button>
                    </div>
                @endif
                <form method="post" action="">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="name_ar">{{ __('messages.name_ar') }}</label>
                        <input required type="text" name="name_ar" class="form-control" id="name_ar"
                               value="{{ $data['name_ar'] }}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="name_en">{{ __('messages.name_en') }}</label>
                        <input required type="text" name="name_en" class="form-control" id="name_en"
                               value="{{ $data['name_en'] }}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">{{ __('messages.manager_email') }}</label>
                        <input required type="Email" class="form-control" id="email" name="email"
                               value="{{ $data['email'] }}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password">{{ __('messages.password') }}</label>
                        <input type="password" class="form-control" id="password" name="password" value="">
                    </div>
                    <br>
                    <input type="submit" value="{{ __('messages.edit') }}" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
@endsection
