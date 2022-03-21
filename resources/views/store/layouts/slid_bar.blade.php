<!--  BEGIN MAIN CONTAINER  -->
<div class="main-container" id="container">
    <div class="overlay"></div>
    <div class="search-overlay"></div>
    <!--  BEGIN SIDEBAR  -->
    @php $rates =  \App\Rate::where('type','hall')->where('admin_approval',2)->get(); @endphp
    <div class="sidebar-wrapper sidebar-theme">
        <nav id="sidebar">
            <div class="shadow-bottom"></div>
            <ul class="list-unstyled menu-categories" id="accordionExample">
                <li class="menu categories">
                    <a href="/admin-panel" class="dropdown-toggle first-link">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-home">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>{{ __('messages.home') }}</span>
                        </div>
                    </a>
                </li>
                @if(in_array(4 , Auth::user()->custom['admin_permission']))
                    <li class="menu shops">
                        <a href="#halls" data-active="true" data-toggle="collapse" aria-expanded="true"
                           class="dropdown-toggle first-link">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-users">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span>{{ __('messages.stores') }} </span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-chevron-right">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </div>
                        </a>
                        <ul class="collapse submenu list-unstyled show" id="halls" data-parent="#accordionExample">
                            @if(Auth::user()->add_data)
                                <li class="active add">
                                    <a href="{{route('shops.create')}}"> {{ __('messages.add') }} </a>
                                </li>
                            @endif
                            <li class="active show">
                                <a href="{{route('shops.show')}}"> {{ __('messages.show') }}</a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if(in_array(13 , Auth::user()->custom['admin_permission']))
                    <li class="menu shops_famous">
                        <a href="{{route('shops_famous')}}" class="dropdown-toggle first-link">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-airplay">
                                    <path
                                        d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path>
                                    <polygon points="12 15 17 21 7 21 12 15"></polygon>
                                </svg>
                                <span>{{ __('messages.famous_stores') }}</span>
                            </div>
                        </a>
                    </li>
                @endif
                @if(in_array(19 , Auth::user()->custom['admin_permission']))
                    <li class="menu products">
                        <a href="#products" data-active="true" data-toggle="collapse" aria-expanded="true"
                           class="dropdown-toggle first-link">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-users">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span>{{ __('messages.products') }} </span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-chevron-right">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </div>
                        </a>
                        <ul class="collapse submenu list-unstyled show" id="products" data-parent="#accordionExample">
                            {{-- @if(Auth::user()->add_data)
                                <li class="active show">
                                    <a href="{{route('shops.products.show')}}"> {{ __('messages.add') }} </a>
                                </li>
                            @endif --}}
                            <li class="active show">
                                <a href="{{route('shops.products.show')}}"> {{ __('messages.show_products') }} </a>
                            </li>
                            <li class="active offers">
                                <a href="{{route('shops.products.offers')}}"> {{ __('messages.offers') }}</a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li class="menu categories">
                    <a href="#categories" data-active="true" data-toggle="collapse" aria-expanded="true"
                       class="dropdown-toggle first-link">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-layers">
                                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                <polyline points="2 17 12 22 22 17"></polyline>
                                <polyline points="2 12 12 17 22 12"></polyline>
                            </svg>
                            <span>{{ __('messages.categories') }}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled show" id="categories" data-parent="#accordionExample">
                        <li class="active add">
                            <a href="{{route('shop.categories.create')}}"> {{ __('messages.add') }} </a>
                        </li>
                        <li class="show">
                            <a href="{{route('shop.categories.show')}}"> {{ __('messages.show') }} </a>
                        </li>
                    </ul>
                </li>
                <li class="menu properties">
                    <a href="#properties" data-active="true" data-toggle="collapse" aria-expanded="true"
                       class="dropdown-toggle first-link">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-check-square">
                                <polyline points="9 11 12 14 22 4"></polyline>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            <span>{{ __('messages.properties') }}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled show" id="properties" data-parent="#accordionExample">
                        <li class="active add">
                            <a href="{{ route('options.add') }}"> {{ __('messages.add') }} </a>
                        </li>
                        <li class="show">
                            <a href="{{ route('options.index') }}"> {{ __('messages.show') }} </a>
                        </li>
                    </ul>
                </li>
                <li class="menu areas">
                    <a href="#areas" data-active="true" data-toggle="collapse" aria-expanded="true"
                       class="dropdown-toggle first-link">
                        <div class="">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                                 fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span>{{ __('messages.areas_governorates') }}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled show" id="areas" data-parent="#accordionExample">
                        @if(Auth::user()->add_data)
                            <li class="active add-governorates">
                                <a href="{{ route('areas.governorates.add') }}"> {{ __('messages.add_governorate') }} </a>
                            </li>
                        @endif
                        <li class="show-governorates">
                            <a href="{{ route('areas.governorates.index') }}"> {{ __('messages.show_governorates') }} </a>
                        </li>
                        @if(Auth::user()->add_data)
                            <li class="active add">
                                <a href="{{ route('areas.add') }}"> {{ __('messages.add_area') }} </a>
                            </li>
                        @endif
                        <li class="show">
                            <a href="{{ route('areas.index') }}"> {{ __('messages.show_areas') }} </a>
                        </li>

                    </ul>
                </li>
                <li class="menu deliver-cost">
                    <a href="#deliver-cost" data-active="true" data-toggle="collapse" aria-expanded="true"
                       class="dropdown-toggle first-link">
                        <div class="">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                                 fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span>{{ __('messages.delivery_costs') }}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled show" id="deliver-cost" data-parent="#accordionExample">
                        @if(Auth::user()->add_data)
                            <li class="active deliverycost">
                                <a href="{{ route('areas.byArea.delivercost') }}"> {{ __('messages.add_by_areas') }} </a>
                            </li>
                            <li class="active add-by-governorate">
                                <a href="{{ route('areas.add.deliveryCostByGovernorate') }}"> {{ __('messages.add_by_governorates') }} </a>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
    <!--  END SIDEBAR  -->
    <!--  BEGIN CONTENT AREA  -->
    <div id="content" class="main-content">
        <div class="layout-px-spacing">
            <div class="row layout-top-spacing">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">

