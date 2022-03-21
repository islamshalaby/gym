@extends('admin.app')
@section('title' , __('messages.points_packages'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.points_packages') }}</h4>
                    </div>
                </div>
                <form action="{{route('update.points.settings')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xl-7 mx-auto">
                            <div class="form-group">
                                <h4>{{ __('messages.number_dolar') }}</h4>
                                <input type="number" name="points" class="form-control" id="h-text1"
                                       aria-describedby="h-text1" value="{{settings()->points}}">
                            </div>
                            @if(Auth::user()->update_data)
                                <div class="form-group">
                                    <button type="submit" name="time"
                                            class="btn btn-success">{{ __('messages.edit') }}</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <a class="btn btn-primary" data-toggle="modal"
                           data-target="#add_new_package_Modal">{{ __('messages.add') }}</a>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-center blue-color">Id</th>
                            <th class="text-center blue-color">{{ __('messages.points') }}</th>
                            <th class="text-center blue-color">{{ __('messages.value') }}</th>

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
                                <td class="text-center blue-color">{{$row->points}}</td>
                                <td class="text-center blue-color">{{$row->price}}</td>
                                @if(Auth::user()->update_data)
                                    <td class="text-center blue-color">
                                        <a id="btn_update" data-package-id="{{$row->id}}" data-points="{{$row->points}}"
                                           data-price="{{$row->price}}" data-toggle="modal"
                                           data-target="#edit_package_Modal"><i class="far fa-edit"></i></a></td>
                                @endif
                                @if(Auth::user()->delete_data)
                                    <td class="text-center blue-color"><a
                                            onclick="return confirm('{{ __('messages.are_you_sure') }}');"
                                            href="{{ route('points_packages.delete', $row->id) }}"><i
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
        {{--    add new package--}}
        <div id="add_new_package_Modal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('messages.add_new_package') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <form action="{{route('points_packages.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-4">
                                <label for="plan_price">{{ __('messages.points') }}</label>
                                <input required type="number" min="0" name="points" class="form-control">
                            </div>
                            <div class="form-group mb-4">
                                <label for="plan_price">{{ __('messages.price') }}</label>
                                <input required type="number" min="0" step="any" name="price" class="form-control">
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
        {{--    edit package--}}
        <div id="edit_package_Modal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('messages.edit_package') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <form action="{{route('points_packages.update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-4">
                                <label for="plan_price">{{ __('messages.points') }}</label>
                                <input required id="txt_package_id" type="hidden" min="0" name="id"
                                       class="form-control">
                                <input required id="txt_points" type="number" min="0" name="points"
                                       class="form-control">
                            </div>
                            <div class="form-group mb-4">
                                <label for="plan_price">{{ __('messages.price') }}</label>
                                <input required id="txt_price" type="number" min="0" step="any" name="price"
                                       class="form-control">
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
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '#btn_update', function () {
                price = $(this).data('price');
                points = $(this).data('points');
                package_id = $(this).data('package-id');
                $("#txt_price").val(price);
                $("#txt_points").val(points);
                $("#txt_package_id").val(package_id);
            });
        });
    </script>
@endsection
