@extends('store.app')
@section('title' , __('messages.show_properties_categories'))
@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.show_properties_categories') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="without-print" class="table table-hover non-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ __('messages.category_title') }}</th>
                            {{-- <th class="text-center">{{ __('messages.details') }}</th> --}}
                                <th class="text-center">{{ __('messages.edit') }}</th>
                                <th class="text-center">{{ __('messages.delete') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['categories'] as $category)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ App::isLocale('en') ? $category->title_en : $category->title_ar }}</td>
                                {{-- <td class="text-center blue-color"><a href="{{ route('categories.details', $category->id) }}" ><i class="far fa-eye"></i></a></td> --}}
                                <td class="text-center blue-color"><a
                                        href="{{ route('options.categories.edit', $category->id) }}"><i
                                            class="far fa-edit"></i></a></td>
                                <td class="text-center blue-color">
                                    @if(count($category->options) == 0)
                                        <a onclick='return confirm("{{ __('messages.are_you_sure') }}");'
                                           href="{{ route('options.categories.delete', $category->id) }}"><i
                                                class="far fa-trash-alt"></i></a>
                                    @else
                                        {{ __('messages.category_has_properties') }}
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
