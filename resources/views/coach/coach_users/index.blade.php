@extends('coach.app')
@section('title' , __('messages.coaches'))
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
                var url = "{{ route('coaches.sort') }}";
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
                        <h4>{{ __('messages.coaches') }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <a class="btn btn-info" href="{{route('coaches.create')}}"> {{ __('messages.add') }} </a>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">{{ __('messages.image') }}</th>
                            <th class="text-center">{{ __('messages.name') }}</th>
                            <th class="text-center">{{ __('messages.email') }}</th>
                            <th class="text-center">{{ __('messages.status') }}</th>
                            <th class="text-center">{{ __('messages.avilable_times') }}</th>
                            <th class="text-center">{{ __('messages.rates') }}</th>
                            <th class="text-center">{{ __('messages.famous_coaches') }}</th>
                            <th class="text-center">{{ __('messages.join_requests') }}</th>
                            <th class="text-center">{{ __('messages.details') }}</th>
                            @if(Auth::user()->update_data)
                                <th class="text-center">{{ __('messages.edit') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody id="sortable">
                        <?php $i = 1; ?>
                        @foreach ($data as $row)
                            <tr id="id_{{ $row->id }}">
                                <td class="text-center"><?=$i;?></td>
                                <td class="text-center">
                                    <img src="{{image_cloudinary_url()}}{{ $row->image }}"/>
                                </td>
                                <td class="text-center"> @if(app()->getLocale() == 'ar') {{ $row->name }} @else {{ $row->name_en }} @endif </td>
                                <td class="text-center">{{ $row->email }}</td>
                                <td class="text-center">
                                    @if($row->status == 'active')
                                        <a href="/admin-panel/coaches/change_status/unactive/{{$row->id}}">
                                            <span class="badge badge-danger">{{ __('messages.block') }}</span>
                                        </a>
                                    @else
                                        <a href="/admin-panel/coaches/change_status/active/{{$row->id}}">
                                            <span class="badge badge-success">{{ __('messages.active') }}</span>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{route('coaches.times',$row->id)}}"
                                       class="btn btn-info  mb-2 mr-2 rounded-circle">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-clock">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                        </div>
                                    </a>
                                </td>
                                <td class="text-center blue-color">
                                    @php $rates =  \App\Rate::where('order_id',$row->id)->where('type','coach')->where('admin_approval',2)->get(); @endphp
                                    @if( count($rates) > 0 )
                                        <a href="{{route('coaches.rates',$row->id)}}"
                                           class="btn btn-warning  mb-2 mr-2 rounded-circle" title=""
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
                                                {{count($rates)}}
                                            </span>
                                        </span>
                                    @else
                                        <a href="{{route('coaches.rates',$row->id)}}"
                                           class="btn btn-warning  mb-2 mr-2 rounded-circle" title=""
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
                                        <a href="{{route('coaches.make_famous',$row->id)}}"
                                           class="btn btn-danger  mb-2 mr-2 rounded-circle"
                                           title="{{ __('messages.famous') }}"
                                           data-original-title="Tooltip using BUTTON tag">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    @else
                                        <a href="{{route('coaches.make_famous',$row->id)}}"
                                           class="btn btn-dark  mb-2 mr-2 rounded-circle"
                                           title="{{ __('messages.not_famous') }}"
                                           data-original-title="Tooltip using BUTTON tag">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center blue-color">
                                    @if($row->is_confirm == 'new')
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-dark btn-sm">{{ __('messages.new_request') }}</button>
                                            <button type="button"
                                                    class="btn btn-dark btn-sm dropdown-toggle dropdown-toggle-split"
                                                    id="dropdownMenuReference5" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false" data-reference="parent">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                     class="feather feather-chevron-down">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuReference5">
                                                <a class="dropdown-item"
                                                   href="{{route('coach.confirm',['id'=>$row->id ,'type'=>'accepted'])}}"
                                                   style="color: limegreen; text-align: center;">{{ __('messages.accept') }}</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item"
                                                   href="{{route('coach.confirm',['id'=>$row->id ,'type'=>'rejected'])}}"
                                                   style="color: red; text-align: center;">{{ __('messages.reject') }}</a>
                                            </div>
                                        </div>
                                    @elseif($row->is_confirm == 'accepted')
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-success btn-sm">{{ __('messages.accepted') }}</button>
                                            <button type="button"
                                                    class="btn btn-success btn-sm dropdown-toggle dropdown-toggle-split"
                                                    id="dropdownMenuReference5" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false" data-reference="parent">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                     class="feather feather-chevron-down">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuReference5">
                                                <a class="dropdown-item"
                                                   href="{{route('coach.confirm',['id'=>$row->id ,'type'=>'rejected'])}}"
                                                   style="color: red; text-align: center;">{{ __('messages.reject') }}</a>
                                            </div>
                                        </div>
                                    @elseif($row->is_confirm == 'rejected')
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm">{{ __('messages.rejected') }}</button>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm dropdown-toggle dropdown-toggle-split"
                                                    id="dropdownMenuReference5" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false" data-reference="parent">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                     class="feather feather-chevron-down">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuReference5">
                                                <a class="dropdown-item"
                                                   href="{{route('coach.confirm',['id'=>$row->id ,'type'=>'accepted'])}}"
                                                   style="color: limegreen; text-align: center;">{{ __('messages.accept') }}</a>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center blue-color">
                                    <a href="{{route('coaches.details',$row->id)}}"><i class="far fa-eye"></i></a>
                                </td>
                                @if(Auth::user()->update_data)
                                    <td class="text-center blue-color">
                                        <a href="{{route('coaches.edit',$row->id)}}">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    </td>
                                @endif
                                <?php $i++; ?>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
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

