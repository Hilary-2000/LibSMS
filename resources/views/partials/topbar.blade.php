{{-- Top bar shared by every page: logo, search, notifications, user menu. --}}
            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="." class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('images/brand/ladybird-mark-on-light.png') }}" alt="Ladybird" height="36">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('images/brand/ladybird-logo-on-light.png') }}" alt="Ladybird" height="40">
                                </span>
                            </a>

                            <a href="." class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('images/brand/ladybird-mark-on-dark.png') }}" alt="Ladybird" height="36">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('images/brand/ladybird-logo-on-dark.png') }}" alt="Ladybird" height="40">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                    </div>

                    <div class="d-flex">

                        <div class="dropdown d-inline-block d-lg-none ms-2">
                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">
        
                                <form class="p-3">
                                    <div class="form-group m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- What's new (opens by itself once after each release) --}}
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item noti-icon waves-effect d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#whatsNewModal" title="What's new">
                                <i class="bx bx-gift"></i>
                                <span class="d-none d-md-inline ms-1 font-size-13 fw-medium">What's new</span>
                                @if (\App\Services\WhatsNew::unseen())
                                    <span class="badge bg-danger rounded-pill" data-whats-new-dot>new</span>
                                @endif
                            </button>
                        </div>

                        {{-- Light / Dark / System --}}
                        <div class="d-flex align-items-center ms-1 me-1">
                            @include('partials.theme_switch')
                        </div>

                        <div class="dropdown d-none d-lg-inline-block ms-1">
                            <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                                <i class="bx bx-fullscreen"></i>
                            </button>
                        </div>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-bell {{$notification_count > 0 ? 'bx-tada' : ''}}"></i>
                                <span class="badge bg-danger rounded-pill">{{$notification_count > 99 ? "99+" : $notification_count}}</span>
                            </button>

                            {{-- notification details --}}
                            <x-notification :notifications="$notifications"/>
                        </div>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user" src="https://lsims.ladybirdsmis.com/sims/{{(session()->get("user_details")->profile_loc != null && strlen(trim(session()->get("user_details")->profile_loc)) > 0) ? session()->get("user_details")->profile_loc : "images/dp.png"}}"
                                    alt="Header Avatar">
                                <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{session("gender") == "M" ? "Mr." : "Ms."}} {{explode(" ",ucwords(strtolower(session("fullname"))))[0]}}</span>
                                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="/Logout"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span></a>
                            </div>
                        </div>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                                <i class="bx bx-cog bx-spin"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </header>
@include('partials.whats_new')
