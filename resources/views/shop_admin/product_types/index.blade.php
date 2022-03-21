@extends('shop_admin.app')
@section('title' , __('messages.show_product_types'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.show_product_types') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="without-print" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ __('messages.product_type') }}</th>
                            <th class="text-center">{{ __('messages.edit') }}</th>
                            <th class="text-center">{{ __('messages.delete') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['types'] as $type)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ App::isLocale('en') ? $type->type_en : $type->type_ar }}</td>
                                <td class="text-center blue-color"><a
                                        href="{{ route('product_type.edit', $type->id) }}"><i
                                            class="far fa-edit"></i></a></td>
                                <td class="text-center blue-color">
                                    @if(count($type->products) > 0)
                                        {{ __('messages.category_has_products') }}
                                    @else
                                        <a onclick='return confirm("{{ __('messages.are_you_sure') }}");'
                                           href="{{ route('product_type.delete', $type->id) }}"><i
                                                class="far fa-trash-alt"></i></a>
                                    @endif
                                </td>
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
