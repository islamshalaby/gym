@extends('hole.app')
@section('title' , __('messages.holes'))
@push('scripts')
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js" type="text/javascript"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("tbody#sortable").sortable({
            items: "tr",
            placeholder: "ui-state-hightlight",
            update: function () {
                var ids = $('tbody#sortable').sortable("serialize");
                var url = "{{ route('halls.sort') }}";
                $.post(url, ids + "&_token={{ csrf_token() }}");
            }
        });
    </script>
@endpush
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <h4>{{ __('messages.holes') }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <a class="btn btn-info" href="{{route('halls.create')}}"> {{ __('messages.add') }} </a>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <a class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">{{ __('messages.logo') }}</th>
                            <th class="text-center">{{ __('messages.hole_name') }}</th>
                            <th class="text-center">{{ __('messages.email') }}</th>

                            <th class="text-center">{{ __('messages.phone') }}</th>
                            <th class="text-center">{{ __('messages.branches') }}</th>
                            <th class="text-center">{{ __('messages.status') }}</th>
                            <th class="text-center">{{ __('messages.rates') }}</th>
                            <th class="text-center">{{ __('messages.famous_holes') }}</th>
                            <th class="text-center">{{ __('messages.details') }}</th>
                            @if(Auth::user()->update_data)
                                <th class="text-center">{{ __('messages.edit') }}</th>
                            @endif
                            @if(Auth::user()->delete_data)
                                <th class="text-center">{{ __('messages.delete') }}</th>
                            @endif
                            <th class="text-center"></th>
                        </tr>
                        </thead>
                        <tbody id="sortable">
                        <?php $i = 1; ?>
                        @foreach ($data as $row)
                            <tr id="id_{{ $row->id }}">
                                <td class="text-center"><?=$i;?></td>
                                <td class="text-center"><img src="{{image_cloudinary_url()}}{{ $row->logo }}"/></td>
                                <td class="text-center"> @if(app()->getLocale() == 'ar') {{ $row->name }} @else {{ $row->name_en }} @endif </td>
                                <td class="text-center">{{ $row->email }}</td>
                                <td class="text-center">{{ $row->phone }}</td>

                                <td class="text-center blue-color">
                                    <a href="{{route('branches.show_new',$row->id)}}">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round" class="feather feather-layers">
                                                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                                <polyline points="2 17 12 22 22 17"></polyline>
                                                <polyline points="2 12 12 17 22 12"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                </td>
                                <td class="text-center">
                                    @if($row->status == 'active')
                                        <a href="/admin-panel/halls/change_status/unactive/{{$row->id}}">
                                            <span class="badge badge-danger">{{ __('messages.block') }}</span>
                                        </a>
                                    @else
                                        <a href="/admin-panel/halls/change_status/active/{{$row->id}}">
                                            <span class="badge badge-success">{{ __('messages.active') }}</span>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center blue-color">
                                    @php $rates =  \App\Rate::where('order_id',$row->id)->where('type','hall')->where('admin_approval',2)->get(); @endphp
                                    @if( count($rates) > 0 )
                                        <a href="{{route('admin_hall_rates.show',$row->id)}}"
                                           class="btn btn-warning  mb-2 mr-2 rounded-circle"
                                           style="position: absolute;margin-right: -22px;margin-top: -20px;"
                                           data-original-title="Tooltip using BUTTON tag">
                                            @if($row->Rates != null)
                                                {{count($row->Rates)}}
                                            @else
                                                0
                                            @endif
                                        </a>
                                        <span class="unreadcount"
                                              style="position: absolute;margin-top: -27px;margin-right: -28px;"
                                              title="{{ __('messages.new_rates') }}">
                                            <span class="insidecount">
                                                    {{ count($rates) }}
                                            </span>
                                        </span>
                                    @else
                                        <a href="{{route('admin_hall_rates.show',$row->id)}}"
                                           class="btn btn-warning  mb-2 mr-2 rounded-circle"
                                           data-original-title="Tooltip using BUTTON tag">
                                            @if($row->Rates != null)
                                                {{count($row->Rates)}}
                                            @else
                                                0
                                            @endif
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center blue-color">
                                    @if($row->famous == '1' )
                                        <a href="{{route('halls.make_famous',$row->id)}}"
                                           class="btn btn-danger  mb-2 mr-2 rounded-circle"
                                           title="{{ __('messages.famous') }}"
                                           data-original-title="Tooltip using BUTTON tag">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    @else
                                        <a href="{{route('halls.make_famous',$row->id)}}"
                                           class="btn btn-dark  mb-2 mr-2 rounded-circle"
                                           title="{{ __('messages.not_famous') }}"
                                           data-original-title="Tooltip using BUTTON tag">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center blue-color">
                                    <a href="{{route('halls.details',$row->id)}}"><i class="far fa-eye"></i></a>
                                </td>
                                @if(Auth::user()->update_data)
                                    <td class="text-center blue-color">
                                        <a href="{{route('halls.edit',$row->id)}}">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    </td>
                                @endif
                                @if(Auth::user()->delete_data)
                                    <td class="text-center blue-color"><a
                                            onclick="return confirm('{{ __('messages.delete_confirmation') }}');"
                                            href="/admin-panel/halls/delete/{{ $row->id }}"><i
                                                class="far fa-trash-alt"></i></a></td>
                                @endif
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

