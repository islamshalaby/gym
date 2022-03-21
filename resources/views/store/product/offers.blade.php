@extends('store.app')

@section('title' , __('messages.show_offers'))

@push('styles')
    <style>
        .table > tbody > tr > td,
        .table > thead > tr > th {
            font-size : 10px
        }
        .dropdown-menu {
            height: 100px;
            overflow: auto
        }
    </style>

@section('content')
<div id="badgeCustom" class="col-lg-12 mx-auto layout-spacing">
    <div class="statbox widget box box-shadow">

    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-4">
                            <img style="height: 100px;"
                                 src="{{image_cloudinary_url()}}{{$data['offer_image']}}">
                        </div>
                        
                    </div>

                    @if(Auth::user()->add_data && Auth::user()->update_data)
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <a class="btn btn-primary" data-toggle="modal"
                        data-target="#slider_image_Modal">{{ __('messages.edit_offer_image') }}</a>
                    </div>
                    @endif
                </div>
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('messages.show_offers') }}
                    </h4>

                </div>
            </div>
            <div id="slider_image_Modal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('messages.edit_offer_image') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                        <form action="{{route('shops.products.update_image')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="container">
                                    <div id="ads_check" class="row" >
                                        
                                        <div class="custom-file-container" data-upload-id="myFirstImage">
                                            <label>{{ __('messages.change_image') }} ({{ __('messages.single_image') }}) <a
                                                    href="javascript:void(0)" class="custom-file-container__image-clear"
                                                    title="Clear Image">x</a></label>
                                            <label class="custom-file-container__custom-file">
                                                <input type="file" name="offer_image"
                                                       class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                                            </label>
                                            <div class="custom-file-container__image-preview"></div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="modal-footer">
                                <input type="submit" value="{{ __('messages.edit') }}" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table id="html5-extension" class="table table-hover non-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th class="hide_col">{{ __('messages.image') }}</th>
                            <th>{{ __('messages.product_title') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.total_quatity') }}</th>
                            <th>{{ __('messages.remaining_quantity') }}</th>
                            <th>{{ __('messages.sold_quantity') }}</th>
                            <th>{{ __('messages.price_before_discount') }}</th>
                            <th>{{ __('messages.price_after_discount') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['products'] as $product)
                            <tr>
                                <td><?=$i;?></td>
                                <td class="hide_col"><img src="{{image_cloudinary_url()}}{{ isset($product->mainImage->image) ? $product->mainImage->image : '' }}"  /></td>
                                <td>{{ App::isLocale('en') ? $product->title_en : $product->title_ar }}</td>
                                <td>{{ App::isLocale('en') ?  $product->category->title_en : $product->category->title_ar }}</td>
                                <td>{{ $product->multi_options == 0 ? $product->total_quatity : $product->multiOptions->sum("total_quatity") }}</td>
                                <td>{{ $product->multi_options == 0 ? $product->remaining_quantity : $product->multiOptions->sum("remaining_quantity") }}</td>
                                <td>{{ $product->sold_count }}</td>
                                <td>
                                    {{ $product->offer == 1 ? $product->price_before_offer . " " . __('messages.dinar') : $product->final_price . " " . __('messages.dinar') }}
                                </td>
                                <td>
                                    {{ $product->final_price . " " . __('messages.dinar') }}
                                </td>
                                <td class="text-center blue-color">
                                    
                                    <a href="{{route('products.action.offer',[$product->id, 0])}}"
                                        class="btn btn-danger  mb-2 mr-2 rounded-circle" title=""
                                        data-original-title="Tooltip using BUTTON tag">
                                        <i class="far fa-heart"></i>
                                    </a>
                                </td>

                                <?php $i++; ?>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                    <td></td>
                    <tfoot>
                </table>
            </div>
        </div>
        {{-- <div class="paginating-container pagination-solid">
            <ul class="pagination">
                <li class="prev"><a href="{{$data['categories']->previousPageUrl()}}">Prev</a></li>
                @for($i = 1 ; $i <= $data['categories']->lastPage(); $i++ )
                    <li class="{{ $data['categories']->currentPage() == $i ? "active" : '' }}"><a href="/admin-panel/categories/show?page={{$i}}">{{$i}}</a></li>
                @endfor
                <li class="next"><a href="{{$data['categories']->nextPageUrl()}}">Next</a></li>
            </ul>
        </div>   --}}

    </div>

@endsection
