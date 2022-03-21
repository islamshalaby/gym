@extends('hole_admin.app')
@section('title' , __('messages.user_details'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">

                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.user_details') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered mb-4">
                        <tbody>
                        <tr>
                            <td class="label-table"> {{ __('messages.user_name') }}</td>
                            <td>{{ $data['user']['name'] }}</td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.user_phone') }} </td>
                            <td>{{ $data['user']['phone'] }}</td>
                        </tr>
                        <tr>
                            <td class="label-table"> {{ __('messages.user_email') }} </td>
                            <td> {{ $data['user']['email'] }} </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection



