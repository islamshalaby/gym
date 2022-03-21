<!--  BEGIN MAIN CONTAINER  -->
<div class="main-container" id="container">
    <div class="overlay"></div>
    <div class="search-overlay"></div>
    <!--  BEGIN SIDEBAR  -->
    @php
        $rates =  \App\Rate::where('type','coach')->where('admin_approval',2)->get();
        $new_coaches =  \App\Coach::where('is_confirm','new')->get();
    @endphp
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
                    <li class="menu coaches">
                        <a href="#users" data-active="true" data-toggle="collapse" aria-expanded="true"
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
                                <span>{{ __('messages.coaches') }}
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
                        <ul class="collapse submenu list-unstyled show" id="users" data-parent="#accordionExample">
                            @if(Auth::user()->add_data)
                                <li class="active create">
                                    <a href="{{route('coaches.create')}}"> {{ __('messages.add') }} </a>
                                </li>
                            @endif
                            <li class="new_join">
                                <a href="{{route('coaches.new_join')}}"> {{ __('messages.join_requests') }}
                                    @if( count($new_coaches) > 0 )
                                        <span class="unreadcount" title="{{ __('messages.new_rates') }}">
                                            <span class="insidecount">
                                                {{count($new_coaches)}}
                                            </span>
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li class="show">
                                <a href="{{route('coaches.show')}}"> {{ __('messages.all_accepted') }}
                                    @if( count($rates) > 0 )
                                        <span class="unreadcount" title="{{ __('messages.new_rates') }}">
                                            <span class="insidecount">
                                                {{count($rates)}}
                                            </span>
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li class="rejected">
                                <a href="{{route('coaches.rejected')}}"> {{ __('messages.all_rejected') }}
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif
                @if(in_array(13 , Auth::user()->custom['admin_permission']))
                    <li class="menu famous_holes">
                        <a href="{{route('famous_coaches')}}" class="dropdown-toggle first-link">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-home">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                <span>{{ __('messages.famous_coaches') }}</span>
                            </div>
                        </a>
                    </li>
                @endif
                @if(in_array(19 , Auth::user()->custom['admin_permission']))
                <li class="menu coach_notifications">
                    <a href="#coach_notifications" data-active="true" data-toggle="collapse" aria-expanded="true"
                       class="dropdown-toggle first-link">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-bell">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <span>{{ __('messages.notifications') }}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled show" id="coach_notifications"
                        data-parent="#accordionExample">
                        @if(Auth::user()->add_data)
                            <li class="send">
                                <a href="/admin-panel/coach_notifications/send"> {{ __('messages.send') }} </a>
                            </li>
                        @endif
                        <li class="show">
                            <a href="/admin-panel/coach_notifications/show"> {{ __('messages.show') }} </a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>
        </nav>
    </div>
    <!--  END SIDEBAR  -->
    <!--  BEGIN CONTENT AREA  -->
    <div id="content" class="main-content">
        <div class="layout-px-spacing">
            <div class="row layout-top-spacing">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
