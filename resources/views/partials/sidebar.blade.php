{{--
    Left sidebar shared by every page.
    Usage: @include('partials.sidebar', ['active' => 'Circulation'])
    `active` = the menu entry to highlight on pages that are not themselves menu links
    (e.g. /Circulation/check-out/5 highlights "Circulation"). Menu entries are only shown when the
    user has the privilege; an empty privilege list means full access (enforced on the server by
    App\Http\Middleware\LibraryAccess).
--}}
@php
    $lib_priv = \App\Http\Middleware\LibraryAccess::parsePrivileges(optional(session()->get("user_details"))->library_previleges);
    $can = fn ($privilege) => count($lib_priv) == 0 || in_array($privilege, $lib_priv, true);
    $active = $active ?? null;
    // kept for pages that still call isPresent() in their own markup (e.g. change_lib_previleges)
    if (!function_exists('isPresent')) { function isPresent($array,$needle){
        if ($array == null) {
            return false;
        }
        for ($index=0; $index < count($array); $index++) {
            if ($array[$index] == $needle) {
                return true;
            }
        }
        return false;
    } }
@endphp
            <div class="vertical-menu">

                <div data-simplebar class="h-100">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li class="menu-title" key="t-menu">Menu</li>
                            {{-- dashboard --}}
                            <li class="">
                                <a href="/Dashboard" class="waves-effect">
                                    <i class="bx bx-home-circle"></i>
                                    <span key="t-chat">Dashboard</span>
                                </a>
                            </li>
                            @if ($can("Acquisition"))
                                <li @if ($active === "Acquisitions") class="mm-active" @endif>
                                    <a href="/Acquisitions" class="waves-effect">
                                        <i class="bx bx-add-to-queue"></i>
                                        <span key="t-file-manager">Acquisitions</span>
                                    </a>
                                </li>
                            @endif
                            @if ($can("Cataloging"))
                                <li @if ($active === "Cataloging") class="mm-active" @endif>
                                    <a href="/Cataloging" class="waves-effect">
                                        <i class="bx bx-file"></i>
                                        <span key="t-file-manager">Cataloging</span>
                                    </a>
                                </li>
                            @endif
                            @if ($can("Circulation"))
                                {{-- Circulation: everything about lending, returning and reserving books --}}
                                <li @if ($active === "Circulation") class="mm-active" @endif>
                                    <a href="javascript: void(0);" class="waves-effect has-arrow" aria-expanded="{{ $active === 'Circulation' ? 'true' : 'false' }}">
                                        <i class="bx bx-rotate-left"></i>
                                        <span key="t-file-manager">Circulation</span>
                                    </a>
                                    <ul class="sub-menu {{ $active === 'Circulation' ? 'mm-collapse mm-show' : '' }}" aria-expanded="{{ $active === 'Circulation' ? 'true' : 'false' }}">
                                        <li><a href="/Circulation/check-out"><i class="bx bx-log-out-circle"></i> Lend a book</a></li>
                                        <li><a href="/Circulation"><i class="bx bx-history"></i> Borrowing history <small class="text-muted d-block ms-4">return a book</small></a></li>
                                        <li><a href="/Circulation/Stats"><i class="bx bx-stats"></i> Statistics</a></li>
                                        <li><a href="/Circulation/Reservations"><i class="bx bx-time-five"></i> Reservations</a></li>
                                    </ul>
                                </li>
                            @endif
                            @if ($can("Acquisition"))
                                <li>
                                    <a href="/Acquisitions/Stock-take" class="waves-effect">
                                        <i class="bx bx-list-check"></i>
                                        <span>Stock-take</span>
                                    </a>
                                </li>
                            @endif
                            @if ($can("Report"))
                                <li>
                                    <a href="/Reports" class="waves-effect">
                                        <i class="bx bxs-report"></i>
                                        <span key="t-file-manager">Reports</span>
                                    </a>
                                </li>
                            @endif
                            @if ($can("Settings"))
                                <li class="">
                                    <a href="javascript: void(0);" class="waves-effect has-arrow " aria-expanded="false">
                                        <i class="bx bx-cog"></i>
                                        <span key="t-jobs">Settings</span>
                                    </a>
                                    <ul class="sub-menu" aria-expanded="false" style="height: 0px;">
                                        <li><a href="/Settings/User-mgmt" key="t-job-list"><i class="bx bxs-user"></i> User Management</a></li>
                                        <li><a href="/Settings/Library-mgmt" key="t-job-list"><i class="bx bxs-book"></i> Library Management</a></li>
                                        <li><a href="/Settings/Audit-log"><i class="bx bx-history"></i> Audit Log</a></li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
