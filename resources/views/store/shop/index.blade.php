@extends('store.app')
@section('title' , __('messages.stores'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.stores') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.email') }}</th>
                            <th class="text-center">{{ __('messages.famous_stores') }}</th>
                            <th class="text-center">{{ __('messages.details') }}</th>
                            @if(Auth::user()->update_data)
                                <th class="text-center">{{ __('messages.edit') }}</th>
                            @endif
                            @if(Auth::user()->delete_data)
                                <th class="text-center">{{ __('messages.block_active') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['shops'] as $shop)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ $shop->name }}</td>
                                <td>{{ $shop->email }}</td>
                                <td class="text-center blue-color">
                                    @if($shop->famous == '1' )
                                        <a href="{{route('shop.make_famous',$shop->id)}}"
                                           class="btn btn-danger  mb-2 mr-2 rounded-circle"
                                           title="{{ __('messages.famous') }}"
                                           data-original-title="Tooltip using BUTTON tag">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    @else
                                        <a href="{{route('shop.make_famous',$shop->id)}}"
                                           class="btn btn-dark  mb-2 mr-2 rounded-circle"
                                           title="{{ __('messages.not_famous') }}"
                                           data-original-title="Tooltip using BUTTON tag">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center blue-color"><a href="{{ route('shops.details', $shop->id) }}"><i
                                            class="far fa-eye"></i></a></td>
                                @if(Auth::user()->update_data)
                                    <td class="text-center blue-color"><a href="{{ route('shops.edit', $shop->id) }}"><i
                                                class="far fa-edit"></i></a></td>
                                @endif
                                @if(Auth::user()->delete_data)
                                    @if($shop->status == 1)
                                        <td class="text-center blue-color">
                                            <a onclick='return confirm("{{ __('messages.are_you_sure') }}");'
                                               href="{{ route('shops.action', [$shop->id, 2]) }}"><span
                                                    class="badge badge-danger">{{ __('messages.block') }}</span></a>
                                        </td>
                                    @else
                                        <td class="text-center blue-color">
                                            <a onclick='return confirm("{{ __('messages.are_you_sure') }}");'
                                               href="{{ route('shops.action', [$shop->id, 1]) }}">
                                                <span class="badge badge-success">{{ __('messages.active') }}</span>
                                            </a>
                                        </td>
                                    @endif
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
