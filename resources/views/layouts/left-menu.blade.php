<nav class="pcoded-navbar">
    <div class="pcoded-inner-navbar main-menu">
        <div class="pcoded-navigatio-lavel">Navigation</div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{route('dashboard')}}">
                    <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                    <span class="pcoded-mtext">Dashboard</span>
                </a>
            </li>

            <li class="pcoded-hasmenu {{ request()->routeIs('stocks', 'stocks.edit', 'stock-follow-up', 'pickup-work-list', 'create-crr') ? 'active pcoded-trigger pcoded-item-open' : '' }}" data-menu-key="stocks">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="feather icon-sidebar"></i></span>
                    <span class="pcoded-mtext">Stocks</span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ request()->routeIs('stocks', 'stocks.edit') ? 'active' : '' }}">
                        <a href="{{route('stocks')}}">
                            <span class="pcoded-mtext">Stock list</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('stock-follow-up') ? 'active' : '' }}">
                        <a href="{{route('stock-follow-up')}}">
                            <span class="pcoded-mtext">Stock follow-up</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('pickup-work-list') ? 'active' : '' }}">
                        <a href="{{route('pickup-work-list')}}">
                            <span class="pcoded-mtext">Pick up work list</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('create-crr') ? 'active' : '' }}">
                        <a href="{{route('create-crr')}}">
                            <span class="pcoded-mtext">Create CRR</span>
                        </a>
                    </li>
                    <!-- <li class="{{ request()->routeIs('etl-stock-items') ? 'active' : '' }}">
                        <a href="{{route('etl-stock-items')}}">
                            <span class="pcoded-mtext">ETL stock items</span>
                        </a>
                    </li> -->
                </ul>
            </li>
        </ul>
        <ul class="pcoded-item pcoded-left-item">
            <li class="pcoded-hasmenu {{ request()->routeIs('shipments', 'shipments.edit', 'pre-alert-reminders', 'shipment-follow-up', 'cost-follow-up', 'create-shipment') ? 'active pcoded-trigger pcoded-item-open' : '' }}" data-menu-key="shipments">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="icofont icofont-ship" style="font-size: 24px;"></i></span>
                    <span class="pcoded-mtext">Shipments</span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ request()->routeIs('shipments', 'shipments.edit') ? 'active' : '' }}">
                        <a href="{{route('shipments')}}">
                            <span class="pcoded-mtext">All shipments</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('pre-alert-reminders') ? 'active' : '' }}">
                        <a href="{{route('pre-alert-reminders')}}">
                            <span class="pcoded-mtext">Shipment follow up</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('shipment-follow-up') ? 'active' : '' }}">
                        <a href="{{route('shipment-follow-up')}}">
                            <span class="pcoded-mtext">Delivery follow-up</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('cost-follow-up') ? 'active' : '' }}">
                        <a href="{{route('cost-follow-up')}}">
                            <span class="pcoded-mtext">Cost follow-up</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('create-shipment') ? 'active' : '' }}">
                        <a href="{{route('create-shipment')}}">
                            <span class="pcoded-mtext">Create shipment</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="pcoded-hasmenu" style="display: none;" data-menu-key="contacts">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="feather icon-package"></i></span>
                    <span class="pcoded-mtext">Contacts</span>
                </a>
                <ul class="pcoded-submenu">
                    <li class=" ">
                        <a href="session-timeout.html">
                            <span class="pcoded-mtext">Session Timeout</span>
                        </a>
                    </li>
                    <li class=" ">
                        <a href="session-idle-timeout.html">
                            <span class="pcoded-mtext">Session Idle Timeout</span>
                        </a>
                    </li>
                    <li class=" ">
                        <a href="offline.html">
                            <span class="pcoded-mtext">Offline</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="pcoded-hasmenu {{ request()->routeIs('offices.*', 'hub.*', 'agents.*', 'other-companies.*', 'suppliers.*', 'customers.*', 'contacts.*', 'vessels.*', 'administration.change-logs') ? 'active pcoded-trigger pcoded-item-open' : '' }}" data-menu-key="administration">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="feather icon-command"></i></span>
                    <span class="pcoded-mtext">Administration</span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ request()->routeIs('offices.*') ? 'active' : '' }}">
                        <a href="{{ route('offices.index') }}">
                            <span class="pcoded-mtext">Office</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('hub.*') ? 'active' : '' }}">
                        <a href="{{ route('hub.index') }}">
                            <span class="pcoded-mtext">Hubs</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('agents.*') ? 'active' : '' }}">
                        <a href="{{ route('agents.index') }}">
                            <span class="pcoded-mtext">Agents</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('other-companies.*') ? 'active' : '' }}">
                        <a href="{{ route('other-companies.index') }}">
                            <span class="pcoded-mtext">Other companies</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <a href="{{ route('suppliers.index') }}">
                            <span class="pcoded-mtext">Suppliers</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('customers.*', 'contacts.*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}">
                            <span class="pcoded-mtext">Customers</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('vessels.*') ? 'active' : '' }}">
                        <a href="{{ route('vessels.index') }}">
                            <span class="pcoded-mtext">Vessels</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('administration.change-logs') ? 'active' : '' }}">
                        <a href="{{ route('administration.change-logs') }}">
                            <span class="pcoded-mtext">Change logs</span>
                        </a>
                    </li>
                </ul>
            </li>
            @if(auth()->user()?->isAdmin())
            <li class="pcoded-hasmenu {{ request()->routeIs('users.*') ? 'active pcoded-trigger pcoded-item-open' : '' }}" data-menu-key="users">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                    <span class="pcoded-mtext">Users</span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ request()->routeIs('users.index') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}">
                            <span class="pcoded-mtext">Users list</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
        </ul>

    </div>
</nav>

<style>
    /* Collapse closed submenus; multiple sections may stay open together */
    .pcoded-navbar .pcoded-hasmenu:not(.pcoded-trigger) > .pcoded-submenu {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
    }

    .pcoded-navbar .pcoded-hasmenu.pcoded-trigger > .pcoded-submenu {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        height: auto !important;
        position: relative !important;
        overflow: visible !important;
    }
</style>

<script>
    (function () {
        var STORAGE_KEY = 'pcoded-open-menus-v3';

        function getSavedOpenKeys() {
            try {
                var raw = localStorage.getItem(STORAGE_KEY);
                var parsed = raw ? JSON.parse(raw) : [];
                return Array.isArray(parsed) ? parsed.map(String) : [];
            } catch (e) {
                return [];
            }
        }

        function saveOpenMenus($) {
            var keys = [];
            $('.pcoded-navbar .pcoded-hasmenu.pcoded-trigger').each(function () {
                var key = $(this).attr('data-menu-key');
                if (key) {
                    keys.push(String(key));
                }
            });
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(keys));
            } catch (e) {}
        }

        function isActiveSection($menu) {
            return $menu.hasClass('active') || $menu.find('> .pcoded-submenu > li.active').length > 0;
        }

        function applyOpenState($) {
            var saved = getSavedOpenKeys();

            $('.pcoded-navbar .pcoded-hasmenu').each(function () {
                var $menu = $(this);
                var key = String($menu.attr('data-menu-key') || '');
                var shouldOpen = isActiveSection($menu) || (key !== '' && saved.indexOf(key) !== -1);

                if (shouldOpen) {
                    $menu.addClass('pcoded-trigger pcoded-item-open');
                    if (isActiveSection($menu)) {
                        $menu.addClass('active');
                    }
                } else {
                    $menu.removeClass('pcoded-trigger pcoded-item-open');
                    if (!isActiveSection($menu)) {
                        $menu.removeClass('active');
                    }
                }
            });

            // Keep current page section remembered as open
            $('.pcoded-navbar .pcoded-hasmenu').each(function () {
                var $menu = $(this);
                if (isActiveSection($menu)) {
                    $menu.addClass('pcoded-trigger pcoded-item-open active');
                }
            });

            saveOpenMenus($);
        }

        function bindManualToggle($) {
            var $menus = $('.pcoded-navbar .pcoded-hasmenu');

            // Strip theme accordion / hover handlers that close sibling menus
            $menus.off('click mouseenter mouseleave');
            $menus.children('a').off('click mouseenter mouseleave');

            $menus.children('a').off('click.pcodedManual').on('click.pcodedManual', function (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                var $menu = $(this).closest('.pcoded-hasmenu');
                // Toggle only this menu — do not collapse other open sections
                $menu.toggleClass('pcoded-trigger pcoded-item-open');
                saveOpenMenus($);

                return false;
            });
        }

        function init($) {
            applyOpenState($);
            bindManualToggle($);

            // Re-apply after theme scripts bind so siblings are not forced closed
            setTimeout(function () {
                applyOpenState($);
                bindManualToggle($);
            }, 400);
        }

        function waitForJQuery(attempt) {
            if (window.jQuery) {
                window.jQuery(function ($) {
                    init($);
                });
                return;
            }
            if (attempt < 80) {
                setTimeout(function () {
                    waitForJQuery(attempt + 1);
                }, 100);
            }
        }

        waitForJQuery(0);
    })();
</script>
