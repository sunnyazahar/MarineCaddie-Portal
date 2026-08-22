{{--
  Mobile viewport chrome safety — load AFTER page styles so list-page
  `height: 100vh !important` cannot clip header / pagination footer when
  the browser address bar is visible (100vh > visible area on Android/iOS).
--}}
<style>
    @media (max-width: 991.98px) {
        html {
            height: var(--mc-app-vh, 100svh) !important;
        }

        .header-navbar.pcoded-header,
        .navbar.header-navbar {
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            max-width: 100vw !important;
            height: calc(var(--mc-header-h, 4rem) + env(safe-area-inset-top, 0px)) !important;
            min-height: calc(var(--mc-header-h, 4rem) + env(safe-area-inset-top, 0px)) !important;
            padding-top: env(safe-area-inset-top, 0px) !important;
            box-sizing: border-box !important;
            transform: none !important;
        }

        .pcoded-main-container {
            padding-top: calc(var(--mc-header-h, 4rem) + env(safe-area-inset-top, 0px)) !important;
        }

        .pcoded-navbar {
            top: calc(var(--mc-header-h, 4rem) + env(safe-area-inset-top, 0px)) !important;
        }

        body[class*="-list-page"],
        body.stocks-list-page,
        body.pickup-work-list-page {
            height: var(--mc-app-vh, 100svh) !important;
            max-height: var(--mc-app-vh, 100svh) !important;
        }

        [class*="-list-card"] {
            height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px)) !important;
            max-height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px)) !important;
        }

        body.stock-bulk-footer-visible [class*="-list-card"],
        body.stock-bulk-footer-visible .stocks-list-card {
            height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px) - 56px) !important;
            max-height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px) - 56px) !important;
        }

        .pagination-sticky-footer {
            flex-shrink: 0 !important;
            position: relative !important;
            bottom: auto !important;
            height: auto !important;
            max-height: none !important;
            min-height: 52px !important;
            padding-bottom: max(10px, env(safe-area-inset-bottom, 0px)) !important;
            box-sizing: border-box !important;
            overflow: visible !important;
            z-index: 30 !important;
        }

        .pagination-sticky-footer .list-pagination-meta,
        .pagination-sticky-footer .list-pagination-links {
            overflow: visible !important;
        }
    }
</style>
