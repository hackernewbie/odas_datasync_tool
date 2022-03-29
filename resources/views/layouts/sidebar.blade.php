<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">@lang('translation.Menu')</li>

                {{-- <li>
                    <a href="javascript: void(0);" class="waves-effect">
                        <i class="bx bx-home-circle"></i><span class="badge rounded-pill bg-info float-end">04</span>
                        <span key="t-dashboards">@lang('translation.Dashboards')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="index" key="t-default">@lang('translation.Default')</a></li>
                        <li><a href="dashboard-saas" key="t-saas">@lang('translation.Saas')</a></li>
                        <li><a href="dashboard-crypto" key="t-crypto">@lang('translation.Crypto')</a></li>
                        <li><a href="dashboard-blog" key="t-blog">@lang('translation.Blog')</a></li>
                    </ul>
                </li> --}}

                <li>
                    <a href="{{route('dashboard')}}" class="waves-effects">
                        <i class="bx bx-home-circle"></i>
                        <span key="Sync">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('fascilities')}}" class="waves-effects">
                        <i class='bx bx-plus-medical'></i>
                        <span key="Sync">Facilities</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
