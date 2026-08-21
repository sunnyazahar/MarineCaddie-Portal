@props([
    'title',
    'subtitle' => null,
    'icon' => 'ti-layout-list-thumb',
    'count' => null,
    'countLabel' => 'records',
])

@once('lists.page-header-styles')
    @push('styles')
        <style>
            .list-page-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
                flex-shrink: 0;
                padding: 10px 14px;
                margin: 0 0 6px;
                background: linear-gradient(135deg, #f0fafb 0%, #ffffff 55%, #f8fafc 100%);
                border: 1px solid #d9eef0;
                border-left: 4px solid #008080;
                border-radius: 8px;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            }
            .list-page-header-main {
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 0;
            }
            .list-page-header-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                border-radius: 8px;
                background: #008080;
                color: #fff;
                font-size: 16px;
                flex-shrink: 0;
                box-shadow: 0 2px 6px rgba(0, 128, 128, 0.28);
            }
            .list-page-header-copy {
                min-width: 0;
            }
            .list-page-header-title {
                margin: 0;
                font-size: 16px;
                font-weight: 800;
                color: #0f172a;
                line-height: 1.2;
                letter-spacing: 0.01em;
            }
            .list-page-header-subtitle {
                margin: 2px 0 0;
                font-size: 12px;
                font-weight: 600;
                color: #64748b;
                line-height: 1.3;
            }
            .list-page-header-aside {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                margin-left: auto;
            }
            .list-page-header-count {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 5px 10px;
                border-radius: 999px;
                background: #e6f5f5;
                border: 1px solid #b7e0e0;
                color: #0f5f5f;
                font-size: 12px;
                font-weight: 800;
                white-space: nowrap;
            }
            .list-page-header-count strong {
                font-weight: 800;
                color: #008080;
            }
            .list-page-header-actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }
            @media (max-width: 991.98px) {
                .list-page-header {
                    padding: 8px 10px;
                    margin-bottom: 4px;
                }
                .list-page-header-title {
                    font-size: 15px;
                }
                .list-page-header-subtitle {
                    display: none;
                }
            }
        </style>
    @endpush
@endonce

<div {{ $attributes->class(['list-page-header']) }} data-list-page-header="1">
    <div class="list-page-header-main">
        <span class="list-page-header-icon" aria-hidden="true">
            <i class="{{ $icon }}"></i>
        </span>
        <div class="list-page-header-copy">
            <h1 class="list-page-header-title">{{ $title }}</h1>
            @if ($subtitle)
                <p class="list-page-header-subtitle">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    <div class="list-page-header-aside">
        @if ($count !== null)
            <span class="list-page-header-count">
                <strong>{{ number_format((int) $count) }}</strong> {{ $countLabel }}
            </span>
        @endif

        @if (isset($actions))
            <div class="list-page-header-actions">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
