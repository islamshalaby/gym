@extends('hole.app')
@section('title' , __('messages.reserv_data'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.reserv_data') }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <a class="btn btn-info" data-toggle="modal"
                           data-target="#add_new_Modal"> {{ __('messages.add') }} </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th class="text-center blue-color">Id</th>
                        <th class="text-center blue-color">{{ __('messages.name_ar') }}</th>
                        <th class="text-center blue-color">{{ __('messages.name_en') }}</th>
                        <th class="text-center blue-color">{{ __('messages.type') }}</th>
                        <th class="text-center blue-color">{{ __('messages.options') }}</th>
                        @if(Auth::user()->update_data)
                            <th class="text-center">{{ __('messages.edit') }}</th>
                        @endif
                        @if(Auth::user()->delete_data)
                            <th class="text-center">{{ __('messages.delete') }}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; ?>
                    @foreach ($data as $row)
                        <tr>
                            <td class="text-center blue-color"><?=$i;?></td>
                            <td class="text-center">{{ $row->title_ar }}</td>
                            <td class="text-center">{{ $row->title_en }}</td>
                            <td class="text-center">
                                @if($row->type == 'y')
                                    {{ __('messages.mandatory') }}
                                @else
                                    {{ __('messages.choice') }}
                                @endif
                            </td>
                            <td class="text-center blue-color">
                                <a href="{{route('reserv_data.goals',$row->id)}}">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="feather feather-layers">
                                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                            <polyline points="2 17 12 22 22 17"></polyline>
                                            <polyline points="2 12 12 17 22 12"></polyline>
                                        </svg>
                                    </div>
                                </a>
                            </td>
                            @if(Auth::user()->update_data)
                                <td class="text-center blue-color">
                                    <a data-toggle="modal" data-target="#edit_Modal" id="edit_btn"
                                       data-type-id="{{$row->id}}" data-title-ar="{{$row->title_ar}}"
                                       data-title-en="{{$row->title_en}}" data-type="{{$row->type}}"><i
                                            class="far fa-edit"></i></a></td>
                            @endif
                            @if(Auth::user()->delete_data)
                                <td class="text-center blue-color"><a
                                        onclick="return confirm('{{ __('messages.are_you_sure') }}');"
                                        href="{{ route('reserv.types.delete', $row->id) }}"><i
                                            class="far fa-trash-alt"></i></a></td>
                            @endif
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
                        <div class="form-group mb-4">
                            <label for="plan_price">{{ __('messages.name_ar') }}</label>
                            <input required type="text" name="title_ar" class="form-control">
                        </div>
                        <div class="form-group mb-4">
                            <label for="plan_price">{{ __('messages.name_en') }}</label>
                            <input required type="text" name="title_en" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="sel1">{{ __('messages.type') }}</label>
                            <select id="ad_type" name="type" class="form-control">
                                <option value="y">{{ __('messages.mandatory') }}</option>
                                <option value="n">{{ __('messages.choice') }}</option>
                            </select>
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
                        <div class="form-group mb-4">
                            <label for="plan_price">{{ __('messages.name_ar') }}</label>
                            <input required type="hidden" id="txt_type_id" name="id" class="form-control">
                            <input required type="text" id="txt_title_ar" name="title_ar" class="form-control">
                        </div>
                        <div class="form-group mb-4">
                            <label for="plan_price">{{ __('messages.name_en') }}</label>
                            <input required type="text" id="txt_title_en" name="title_en" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="sel1">{{ __('messages.type') }}</label>
                            <select id="cmb_type" name="type" class="form-control">
                                <option value="y">{{ __('messages.mandatory') }}</option>
                                <option value="n">{{ __('messages.choice') }}</option>
                            </select>
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
                $("#txt_type_id").val($(this).data('type-id'));
                $("#txt_title_ar").val($(this).data('title-ar'));
                $("#txt_title_en").val($(this).data('title-en'));
                $("#cmb_type").val($(this).data('type'));
            });
        });
    </script>
@endsection
