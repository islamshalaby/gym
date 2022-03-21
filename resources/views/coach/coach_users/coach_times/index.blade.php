@extends('coach.app')
@section('title' , __('messages.avilable_times'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.avilable_times') }}</h4>
                    </div>
                </div>
{{--                <div class="row">--}}
{{--                    <div class="col-md-6 col-sm-6 col-12">--}}
{{--                        <a class="btn btn-info"--}}
{{--                           href="{{route('coach_times.create',$id)}}"> {{ __('messages.add') }} </a>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th class="text-center blue-color">Id</th>
                        <th class="text-center blue-color">{{ __('messages.from') }}</th>
                        <th class="text-center blue-color">{{ __('messages.to') }}</th>
                        @if(Auth::user()->update_data)
                            <th class="text-center">{{ __('messages.edit') }}</th>
                        @endif
{{--                        @if(Auth::user()->delete_data)--}}
{{--                            <th class="text-center">{{ __('messages.delete') }}</th>--}}
{{--                        @endif--}}
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; ?>
                    @foreach ($data as $row)
                        <tr>
                            <td class="text-center blue-color"><?=$i;?></td>
                            <td class="text-center">
                                {{date('g:i a', strtotime($row->time_from))}}
                            </td>
                            <td class="text-center">
                                {{date('g:i a', strtotime($row->time_to))}}
                            </td>
                            @if(Auth::user()->update_data)
                                <td class="text-center blue-color">
                                    <a href="{{route('coach_times.edit',$row->id)}}"><i class="far fa-edit"></i></a>
                                </td>
                            @endif
{{--                            @if(Auth::user()->delete_data)--}}
{{--                                <td class="text-center blue-color"><a--}}
{{--                                        onclick="return confirm('{{ __('messages.are_you_sure') }}');"--}}
{{--                                        href="{{ route('coach_times.delete', $row->id) }}"><i--}}
{{--                                            class="far fa-trash-alt"></i></a></td>--}}
{{--                            @endif--}}
                            <?php $i++; ?>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{--    add model--}}
    <div id="add_new_Modal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.add') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <form action="{{route('reserv.types.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
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
    <div id="edit_Modal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.edit') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <form action="{{route('reserv.types.update')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-md-6" id="special1_cont">
                                <label for="plan_price">{{ __('messages.from') }}</label>
                                <div class="form-group mb-0">
                                    <input id="timeFlatpickr_4" name="time_from"
                                           class="form-control flatpickr flatpickr-input active" type="text">
                                </div>
                            </div>
                            <div class="col-md-6" id="special2_cont">
                                <label for="plan_price">{{ __('messages.to') }}</label>
                                <div class="form-group mb-0">
                                    <input id="timeFlatpickr_3" name="time_to"
                                           class="form-control flatpickr flatpickr-input active" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal">
                            <i class="flaticon-cancel-12"></i> {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.edit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $("#edit_btn").click(function () {
                $("#timeFlatpickr_4").val($(this).data('from'));
                $("#timeFlatpickr_3").val($(this).data('to'));
            });
        });
    </script>
@endsection
