<nav class="navbar header-navbar pcoded-header">
    <div class="navbar-wrapper">

        <div class="navbar-logo">
            <a class="mobile-menu" id="mobile-collapse" href="#!">
                <i class="feather icon-menu"></i>
            </a>
            <a style="font-size:20px; font-weight:700">
                <!-- <img class="img-fluid" src="../files/assets/images/logo.png" alt="Theme-Logo" /> -->
                <span style="color: #002d5b;">Marine</span><span style="color: #359DDA;">Caddie</span>
            </a>
            <a class="mobile-options">
                <i class="feather icon-more-horizontal"></i>
            </a>
        </div>

        <div class="navbar-container">
            <ul class="nav-left">
                <li class="header-search">
                    <div class="main-search morphsearch-search">
                        <div class="input-group m-l-10">
                            @php
                                $breadcrumbItems = \App\Support\BreadcrumbBuilder::items();
                            @endphp
                            <nav class="app-breadcrumb mt-2" aria-label="Breadcrumb">
                                @foreach ($breadcrumbItems as $index => $crumb)
                                    @if ($index > 0)
                                        <span class="app-breadcrumb-sep">/</span>
                                    @endif
                                    @if (!empty($crumb['url']) && !$loop->last)
                                        <a href="{{ $crumb['url'] }}" class="app-breadcrumb-link">{{ $crumb['label'] }}</a>
                                    @else
                                        <span class="app-breadcrumb-current">{{ $crumb['label'] }}</span>
                                    @endif
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="nav-right">
                <li class="header-notification mc-notif-wrap">
                    <div class="dropdown-primary dropdown">
                        @php
                            $notifService = app(\App\Services\UserNotificationService::class);
                            $notifUser = Auth::user();
                            $notifCounts = $notifUser
                                ? $notifService->countsForUser($notifUser)
                                : ['all' => 0, 'comments' => 0, 'pickups' => 0, 'costs' => 0, 'other' => 0];
                            $notifUnread = (int) ($notifCounts['all'] ?? 0);
                        @endphp
                        <div class="dropdown-toggle" data-toggle="dropdown" id="mc-notif-toggle">
                            <i class="feather icon-bell"></i>
                            <span class="badge bg-c-pink mc-notif-badge" @if($notifUnread < 1) style="display:none" @endif>{{ $notifUnread > 99 ? '99+' : $notifUnread }}</span>
                        </div>
                        <div class="dropdown-menu mc-notif-panel" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            <div class="mc-notif-header">
                                <h6>Notifications</h6>
                                <button type="button" class="mc-notif-mark-all" id="mc-notif-mark-all">Mark all as read</button>
                            </div>
                            <div class="mc-notif-tabs" role="tablist">
                                <button type="button" class="mc-notif-tab active" data-category="all">
                                    All <span class="mc-notif-tab-count" data-count-for="all">{{ $notifCounts['all'] }}</span>
                                </button>
                                <button type="button" class="mc-notif-tab" data-category="comments">
                                    Comments <span class="mc-notif-tab-count" data-count-for="comments">{{ $notifCounts['comments'] }}</span>
                                </button>
                                <button type="button" class="mc-notif-tab" data-category="pickups">
                                    Pick ups <span class="mc-notif-tab-count" data-count-for="pickups">{{ $notifCounts['pickups'] }}</span>
                                </button>
                                <button type="button" class="mc-notif-tab" data-category="other">
                                    Other <span class="mc-notif-tab-count" data-count-for="other">{{ $notifCounts['other'] + ($notifCounts['costs'] ?? 0) }}</span>
                                </button>
                            </div>
                            <div class="mc-notif-list" id="mc-notif-list">
                                <div class="mc-notif-empty">Loading notifications…</div>
                            </div>
                            <div class="mc-notif-footer">
                                <button type="button" class="mc-notif-load-more" id="mc-notif-load-more">Load more</button>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="user-profile header-notification">
                    <div class="dropdown-primary dropdown">
                        <div class="dropdown-toggle" data-toggle="dropdown" style="display: flex; align-items: center; gap: 8px;">
                            <span style="display: inline-flex; flex-direction: column; line-height: 1.15; text-align: left;">
                                <span style="font-weight: 600;">{{ Auth::user()->name ?? 'User' }}</span>
                                @if(!empty(Auth::user()?->role))
                                    <span style="font-size: 11px; font-weight: 500; opacity: 0.75;">({{ Auth::user()->role }})</span>
                                @endif
                            </span>
                            <i class="feather icon-chevron-down"></i>
                        </div>
                        <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn"
                            data-dropdown-out="fadeOut">
                            <li>
                                <a href="#!">
                                    <span style="font-size: 12px; font-weight: 600;">
                                    {{ Auth::user()->name ?? '' }}
                                    <hr style="margin: 0; padding: 0;">
                                    {{ Auth::user()->email ?? '' }} </span>
                                </a>
                            </li>
                           
                            <li>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                             document.getElementById('logout-form').submit();">
                                    <i class="feather icon-log-out"></i> Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>

                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
@include('partials.notifications-panel-assets')
