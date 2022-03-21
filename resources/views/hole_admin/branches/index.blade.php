@extends('hole_admin.app')
@section('title' , __('messages.branches'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <h4>{{ __('messages.branches') }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <a class="btn btn-info" href="{{route('branches.create')}}"> {{ __('messages.add') }} </a>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <a class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">{{ __('messages.branch_ar') }}</th>
                            <th class="text-center">{{ __('messages.branch_en') }}</th>
                            <th class="text-center">{{ __('messages.edit') }}</th>
                            <th class="text-center">{{ __('messages.delete') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data as $row)
                            <tr>
                                <td class="text-center"><?=$i;?></td>
                                <td class="text-center">{{ $row->title_ar }}</td>
                                <td class="text-center">{{ $row->title_en }}</td>
                                <td class="text-center blue-color">
                                    <a href="{{route('branches.edit',$row->id)}}">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </td>
                                <td class="text-center blue-color"><a
                                        onclick="return confirm('{{ __('messages.delete_confirmation') }}');"
                                        href="{{route('branches.delete',$row->id)}}"><i
                                            class="far fa-trash-alt"></i></a></td>
                                <?php $i++; ?>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '#btn_send', function () {
                user_id = $(this).data('user');
                $("#txt_user_id").val(user_id);
            });
        });
    </script>
@endsection

