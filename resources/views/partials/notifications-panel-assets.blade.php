<style>
    .mc-notif-wrap .dropdown-menu.mc-notif-panel {
        width: 460px;
        max-width: calc(100vw - 24px);
        padding: 0;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.16);
        right: -8px;
        top: 58px;
        left: auto;
        overflow: hidden;
    }
    .mc-notif-wrap .dropdown-menu.mc-notif-panel:after { display: none; }
    .mc-notif-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px 10px;
        background: #fff;
    }
    .mc-notif-header h6 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #002d5b;
    }
    .mc-notif-mark-all {
        border: 0;
        background: transparent;
        color: #2f80ed;
        font-size: 12px;
        font-weight: 500;
        padding: 0;
        cursor: pointer;
    }
    .mc-notif-mark-all:hover { text-decoration: underline; }
    .mc-notif-tabs {
        display: flex;
        gap: 2px;
        background: #eef1f5;
        padding: 0 8px;
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        overflow-x: auto;
    }
    .mc-notif-tab {
        flex: 0 0 auto;
        border: 0;
        background: transparent;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        padding: 10px 10px 8px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        white-space: nowrap;
    }
    .mc-notif-tab.active {
        color: #002d5b;
        border-bottom-color: #002d5b;
    }
    .mc-notif-tab-count {
        display: inline-block;
        min-width: 16px;
        padding: 1px 6px;
        margin-left: 4px;
        border-radius: 3px;
        background: #f39c12;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        line-height: 1.4;
        vertical-align: middle;
    }
    .mc-notif-tab-count:empty,
    .mc-notif-tab-count[data-zero="1"] { display: none; }
    .mc-notif-list {
        max-height: 380px;
        overflow-y: auto;
        background: #fff;
    }
    .mc-notif-item {
        display: flex;
        gap: 12px;
        padding: 12px 14px 12px 12px;
        border-bottom: 1px solid #edf0f4;
        position: relative;
        background: #fff;
    }
    .mc-notif-item.is-unread { box-shadow: inset 4px 0 0 #f39c12; }
    .mc-notif-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #002d5b;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
        font-size: 14px;
    }
    .mc-notif-body { flex: 1; min-width: 0; }
    .mc-notif-time {
        font-size: 12px;
        font-weight: 700;
        color: #002d5b;
        margin-bottom: 4px;
    }
    .mc-notif-message {
        margin: 0;
        font-size: 12px;
        line-height: 1.45;
        color: #334155;
        word-break: break-word;
    }
    .mc-notif-message a {
        color: #2f80ed !important;
        font-weight: 600;
        text-decoration: none;
    }
    .mc-notif-message a:hover { text-decoration: underline; }
    .mc-notif-more {
        border: 0;
        background: transparent;
        color: #94a3b8;
        padding: 0 2px;
        cursor: pointer;
        align-self: flex-start;
        font-size: 16px;
        line-height: 1;
    }
    .mc-notif-empty {
        padding: 28px 16px;
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
    }
    .mc-notif-footer {
        text-align: center;
        padding: 10px 12px 12px;
        background: #fff;
        border-top: 1px solid #edf0f4;
    }
    .mc-notif-load-more {
        border: 0;
        background: transparent;
        color: #2f80ed;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
    }
    .mc-notif-load-more:disabled {
        color: #94a3b8;
        cursor: default;
        text-decoration: none;
    }
    .mc-notif-load-more:hover:not(:disabled) { text-decoration: underline; }
</style>

<script>
@php
    $mcNotifInitialCounts = $notifCounts ?? [
        'all' => 0,
        'comments' => 0,
        'pickups' => 0,
        'other' => 0,
    ];
    if (isset($notifCounts['costs'])) {
        $mcNotifInitialCounts['other'] = (int) ($mcNotifInitialCounts['other'] ?? 0) + (int) $notifCounts['costs'];
    }
@endphp
(function () {
    function boot($) {
        if (!$ || !$('#mc-notif-list').length) return;

        var state = { category: 'all', offset: 0, limit: 10, loading: false, hasMore: false };
        var urls = {
            list: @json(route('notifications.index')),
            markAll: @json(route('notifications.mark-all-read')),
            markReadBase: @json(url('/notifications'))
        };
        var csrf = @json(csrf_token());
        var initialCounts = @json($mcNotifInitialCounts);

        function escapeHtml(str) {
            return String(str == null ? '' : str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function iconClass(icon) {
            if (icon === 'pickup') return 'feather icon-package';
            if (icon === 'cost') return 'feather icon-credit-card';
            if (icon === 'other') return 'feather icon-alert-circle';
            return 'feather icon-message-circle';
        }

        function renderMessage(item) {
            var msg = escapeHtml(item.message || '');
            if (item.link_label && item.link_url) {
                var label = escapeHtml(item.link_label);
                var link = '<a href="' + escapeHtml(item.link_url) + '">' + label + '</a>';
                var bracketed = '[' + label + ']';
                if (msg.indexOf(bracketed) !== -1) {
                    msg = msg.split(bracketed).join('[' + link + ']');
                } else if (msg.indexOf(label) !== -1) {
                    msg = msg.split(label).join(link);
                }
            }
            return msg;
        }

        function updateCounts(counts) {
            if (!counts) return;
            var other = parseInt(counts.other || 0, 10) + parseInt(counts.costs || 0, 10);
            var mapped = {
                all: parseInt(counts.all || 0, 10),
                comments: parseInt(counts.comments || 0, 10),
                pickups: parseInt(counts.pickups || 0, 10),
                other: other
            };
            ['all', 'comments', 'pickups', 'other'].forEach(function (key) {
                var value = mapped[key] || 0;
                var $badge = $('.mc-notif-tab-count[data-count-for="' + key + '"]');
                $badge.text(value > 0 ? value : '');
                $badge.attr('data-zero', value > 0 ? '0' : '1');
            });
            var unread = mapped.all;
            var $bell = $('.mc-notif-badge');
            if (unread > 0) {
                $bell.text(unread > 99 ? '99+' : unread).show();
            } else {
                $bell.hide();
            }
        }

        function renderItems(items, append) {
            var $list = $('#mc-notif-list');
            if (!append) $list.empty();
            if (!items.length && !append) {
                $list.html('<div class="mc-notif-empty">No notifications for this filter.</div>');
                return;
            }
            items.forEach(function (item) {
                var html = ''
                    + '<div class="mc-notif-item' + (item.is_read ? '' : ' is-unread') + '" data-id="' + item.id + '">'
                    +   '<div class="mc-notif-icon"><i class="' + iconClass(item.icon) + '"></i></div>'
                    +   '<div class="mc-notif-body">'
                    +     '<div class="mc-notif-time">' + escapeHtml(item.time) + '</div>'
                    +     '<p class="mc-notif-message">' + renderMessage(item) + '</p>'
                    +   '</div>'
                    +   '<button type="button" class="mc-notif-more" title="Mark as read" data-id="' + item.id + '">⋮</button>'
                    + '</div>';
                $list.append(html);
            });
        }

        function loadNotifications(append) {
            if (state.loading) return;
            state.loading = true;
            if (!append) {
                $('#mc-notif-list').html('<div class="mc-notif-empty">Loading notifications…</div>');
                state.offset = 0;
            }
            $.ajax({
                url: urls.list,
                method: 'GET',
                data: { category: state.category, offset: state.offset, limit: state.limit },
                success: function (res) {
                    var items = (res && res.notifications) ? res.notifications : [];
                    renderItems(items, !!append);
                    updateCounts(res.counts);
                    state.hasMore = !!(res && res.has_more);
                    state.offset += items.length;
                    var $btn = $('#mc-notif-load-more');
                    if (state.hasMore) {
                        $btn.prop('disabled', false).text('Load more').show();
                    } else {
                        $btn.prop('disabled', true).text('No more notifications').show();
                    }
                },
                error: function () {
                    if (!append) $('#mc-notif-list').html('<div class="mc-notif-empty">Could not load notifications.</div>');
                },
                complete: function () { state.loading = false; }
            });
        }

        $(document).on('click', '.mc-notif-tab', function (e) {
            e.preventDefault(); e.stopPropagation();
            $('.mc-notif-tab').removeClass('active');
            $(this).addClass('active');
            state.category = $(this).data('category');
            loadNotifications(false);
        });

        $(document).on('click', '#mc-notif-mark-all', function (e) {
            e.preventDefault(); e.stopPropagation();
            $.ajax({
                url: urls.markAll,
                method: 'POST',
                data: { _token: csrf },
                success: function (res) {
                    updateCounts(res.counts);
                    loadNotifications(false);
                }
            });
        });

        $(document).on('click', '#mc-notif-load-more', function (e) {
            e.preventDefault(); e.stopPropagation();
            if (!state.hasMore || state.loading) return;
            loadNotifications(true);
        });

        $(document).on('click', '.mc-notif-item a', function (e) {
            e.stopPropagation();
            // Let the browser follow the mapped page URL.
        });

        $(document).on('click', '.mc-notif-more', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).data('id') || $(this).closest('.mc-notif-item').data('id');
            if (!id) return;
            $.ajax({
                url: urls.markReadBase + '/' + id + '/read',
                method: 'POST',
                data: { _token: csrf },
                success: function (res) {
                    updateCounts(res.counts);
                    $('.mc-notif-item[data-id="' + id + '"]').removeClass('is-unread');
                }
            });
        });

        $(document).on('click', '.mc-notif-item.is-unread', function (e) {
            if ($(e.target).closest('a, .mc-notif-more').length) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).data('id');
            if (!id) return;
            $.ajax({
                url: urls.markReadBase + '/' + id + '/read',
                method: 'POST',
                data: { _token: csrf },
                success: function (res) {
                    updateCounts(res.counts);
                    $('.mc-notif-item[data-id="' + id + '"]').removeClass('is-unread');
                }
            });
        });

        $(document).on('click', '.mc-notif-panel', function (e) {
            if ($(e.target).closest('a').length) {
                return;
            }
            e.stopPropagation();
        });

        $('#mc-notif-toggle').on('click', function () {
            setTimeout(function () { loadNotifications(false); }, 50);
        });

        updateCounts(initialCounts);
    }

    if (window.jQuery) {
        boot(window.jQuery);
    } else {
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery) boot(window.jQuery);
        });
    }
})();
</script>
