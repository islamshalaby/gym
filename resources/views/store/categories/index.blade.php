@extends('store.app')
@section('title' , __('messages.show_categories'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.show_categories') }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <a class="btn btn-primary" href="{{route('shop.categories.create')}}">{{ __('messages.add') }}</a>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th class="text-center">{{ __('messages.image') }}</th>
                            <th class="text-center">{{ __('messages.category_title') }}</th>
                            <th class="text-center">{{ __('messages.edit') }}</th>
                            <th class="text-center">{{ __('messages.delete') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['categories'] as $category)
                            <tr>
                                <td><?=$i;?></td>
                                <td class="text-center"><img src="{{image_cloudinary_url()}}{{ $category->image }}"/>
                                </td>
                                <td class="text-center">{{ app()->getLocale() == 'en' ? $category->title_en : $category->title_ar }}</td>
                                <td class="text-center blue-color"><a
                                        href="/admin-panel/categories/edit/{{ $category->id }}"><i
                                            class="far fa-edit"></i></a>
                                </td>
                                <td class="text-center blue-color"><a
                                        onclick="return confirm('Are you sure you want to delete this item?');"
                                        href="/admin-panel/categories/delete/{{ $category->id }}"><i
                                            class="far fa-trash-alt"></i></a>
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
