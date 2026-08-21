/**
 * App shell — mobile sidebar + header options (vanilla JS).
 */
(function () {
    var SIDEBAR_OPEN = 'mc-sidebar-open';
    var HEADER_OPTIONS_OPEN = 'mc-header-options-open';

    function qs(sel, root) {
        return (root || document).querySelector(sel);
    }

    function qsa(sel, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(sel));
    }

    function isSmall() {
        return window.matchMedia('(max-width: 991px)').matches;
    }

    function openSidebar() {
        document.body.classList.add(SIDEBAR_OPEN);
    }

    function closeSidebar() {
        document.body.classList.remove(SIDEBAR_OPEN);
    }

    function toggleSidebar() {
        document.body.classList.toggle(SIDEBAR_OPEN);
    }

    function setHeaderOptions(open) {
        document.body.classList.toggle(HEADER_OPTIONS_OPEN, !!(open && isSmall()));
    }

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    onReady(function () {
        // Old Pcoded preloader — script.js used to fade this; hide immediately on Tailwind v2.
        qsa('.theme-loader').forEach(function (el) {
            el.style.display = 'none';
            el.remove();
        });

        var collapse = qs('#mobile-collapse');
        if (collapse) {
            collapse.addEventListener('click', function (e) {
                e.preventDefault();
                setHeaderOptions(false);
                toggleSidebar();
            });
        }

        qsa('.pcoded-overlay-box').forEach(function (el) {
            el.addEventListener('click', closeSidebar);
        });

        qsa('.mobile-options').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (!isSmall()) return;
                closeSidebar();
                setHeaderOptions(!document.body.classList.contains(HEADER_OPTIONS_OPEN));
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeSidebar();
                setHeaderOptions(false);
            }
        });

        window.addEventListener('resize', function () {
            if (!isSmall()) {
                closeSidebar();
                setHeaderOptions(false);
            }
        });
    });
})();
