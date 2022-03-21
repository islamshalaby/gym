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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            <span>{{ __('messages.home') }}</span>
                        </div>
                    </a>
                </li>
                @if(in_array(4 , Auth::user()->custom['admin_permission']))
                    <li class="menu halls">
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
                                <span>{{ __('messages.holes') }}
                                    @if( count($rates) > 0 )
                                        <span class="unreadcount" title="{{ __('messages.new_rates') }}">
                                            <span class="insidecount">
                                                {{count($rates)}}
                                            </span>
                                        </span>
                                    @endif
                                </span>
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
                                <li class="active create">
                                    <a href="{{route('halls.create')}}"> {{ __('messages.add') }} </a>
                                </li>
                            @endif
                            <li class="show">
                                <a href="{{route('halls.show')}}"> {{ __('messages.show') }}
                                    @if( count($rates) > 0 )
                                        <span class="unreadcount" title="{{ __('messages.new_rates') }}">
                                            <span class="insidecount">
                                                {{count($rates)}}
                                            </span>
                                        </span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if(in_array(13 , Auth::user()->custom['admin_permission']))
                    <li class="menu famous_holes">
                        <a href="{{route('famous_holes')}}" class="dropdown-toggle first-link">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-airplay">
                                    <path
                                        d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path>
                                    <polygon points="12 15 17 21 7 21 12 15"></polygon>
                                </svg>
                                <span>{{ __('messages.famous_holes') }}</span>
                            </div>
                        </a>
                    </li>
                @endif
                <li class="menu reserv_data">
                    <a href="{{route('reserv_data.types')}}" class="dropdown-toggle first-link">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-users">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>{{ __('messages.reserv_data') }}</span>
                        </div>
                    </a>
                </li>
                <li class="menu all_rates">
                    <a href="{{route('halls.all_rates')}}" class="dropdown-toggle first-link">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <span>{{ __('messages.all_rates') }}</span>
                        </div>
                    </a>
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
