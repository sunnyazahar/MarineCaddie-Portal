{{--
  Mobile chrome safety — AFTER page styles.
  Only remaps locked LIST shells (100vh → svh). Never locks html/body on
  edit/create/other pages so document scroll keeps working.
--}}
<style>
    @media (max-width: 991.98px) {
        /* Non-list pages: always allow page scroll */
        html,
        body:not([class*="-list-page"]):not(.stocks-list-page):not(.pickup-work-list-page) {
            height: auto !important;
            max-height: none !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
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

        /* List pages only — viewport lock with svh so footer is not clipped */
        body[class*="-list-page"],
        body.stocks-list-page,
        body.pickup-work-list-page {
            height: var(--mc-app-vh, 100svh) !important;
            max-height: var(--mc-app-vh, 100svh) !important;
            overflow: hidden !important;
        }

        body[class*="-list-page"] [class*="-list-card"],
        body.stocks-list-page [class*="-list-card"],
        body.pickup-work-list-page [class*="-list-card"] {
            height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px)) !important;
            max-height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px)) !important;
        }

        body.stock-bulk-footer-visible.stocks-list-page .stocks-list-card {
            height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px) - 56px) !important;
            max-height: calc(var(--mc-app-vh, 100svh) - var(--mc-header-h, 4rem) - env(safe-area-inset-top, 0px) - 56px) !important;
        }

        /* Inner table scroll must work when page scroll is locked */
        body[class*="-list-page"] [class*="-table-area"],
        body.stocks-list-page [class*="-table-area"],
        body.pickup-work-list-page [class*="-table-area"],
        body[class*="-list-page"] .table-scroll-wrapper,
        body.stocks-list-page .table-scroll-wrapper,
        body.pickup-work-list-page .table-scroll-wrapper,
        body[class*="-list-page"] .dataTables_scrollBody,
        body.stocks-list-page .dataTables_scrollBody,
        body.pickup-work-list-page .dataTables_scrollBody,
        body[class*="-list-page"] .list-ajax-table-wrapper,
        body.stocks-list-page .list-ajax-table-wrapper {
            overflow-x: auto !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            min-height: 0 !important;
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
    }
</style>
