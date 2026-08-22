{{-- Shared list pagination footer (in-flow at bottom of list card; bold teal accent). --}}
<style>
    .pagination-sticky-footer {
        position: relative !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;
        flex-shrink: 0;
        box-sizing: border-box !important;
        width: 100%;
        padding: 10px 16px !important;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-top: 2px solid #008080;
        z-index: 20;
        margin: 0 !important;
        box-shadow: 0 -3px 12px rgba(15, 23, 42, 0.06);
        min-height: 52px;
        height: 52px;
        max-height: 52px;
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .list-pagination-meta {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: 0.01em;
        white-space: nowrap;
    }
    .list-pagination-meta strong {
        font-weight: 800;
        color: #008080;
    }
    .list-pagination-links {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-left: auto;
    }
    .list-pagination-links .pagination {
        margin: 0 !important;
        gap: 4px;
        display: flex;
        align-items: center;
    }
    .pagination-sticky-footer .pagination .page-link {
        color: #0f172a !important;
        font-size: 13px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #dbe3ee;
        background: #fff;
        line-height: 1.2;
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }
    .pagination-sticky-footer .pagination .page-link:hover {
        background: #e6f5f5 !important;
        border-color: #008080 !important;
        color: #008080 !important;
    }
    .pagination-sticky-footer .pagination .page-item.active .page-link {
        background-color: #008080 !important;
        border-color: #008080 !important;
        color: #fff !important;
        font-weight: 800;
        box-shadow: 0 2px 6px rgba(0, 128, 128, 0.35);
    }
    .pagination-sticky-footer .pagination .page-item.disabled .page-link {
        color: #94a3b8 !important;
        background: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        font-weight: 600;
    }
    @media (max-width: 991.98px) {
        .pagination-sticky-footer {
            padding: 8px 12px !important;
            height: auto !important;
            min-height: 52px;
            max-height: none;
        }
    }
    @media (max-width: 575.98px) {
        .pagination-sticky-footer {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            min-height: 0;
            padding: 10px 12px max(10px, env(safe-area-inset-bottom)) !important;
        }
        .list-pagination-meta {
            white-space: normal;
            text-align: center;
            font-size: 12px;
            line-height: 1.35;
        }
        .list-pagination-links {
            margin-left: 0;
            justify-content: center;
            width: 100%;
        }
        .list-pagination-links .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
</style>
