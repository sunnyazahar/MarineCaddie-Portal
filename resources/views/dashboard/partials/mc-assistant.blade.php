@php
    $assistantScope = $dashboard['assistant']['scope'] ?? [];
    $assistantStocksOnly = ($assistantScope['mode'] ?? null) === 'stocks-only';
@endphp

<div
    class="mc-assistant-shell"
    id="mcAssistantShell"
    data-storage-key="mc-assistant:{{ auth()->id() ?? 'guest' }}:{{ $assistantScope['mode'] ?? 'dashboard' }}">
    <div class="mc-assistant-panel" id="mcAssistantPanel" data-role="mc-assistant" aria-hidden="true">
        <div class="mc-assistant-header">
            <div class="mc-assistant-header-copy">
                <p class="mc-assistant-kicker" id="mcAssistantKicker">{{ $assistantStocksOnly ? 'Stock helper' : 'Dashboard helper' }}</p>
                <div class="mc-assistant-heading-row">
                    <h2 class="mc-assistant-title">MC Assistant</h2>
                    <span class="mc-assistant-badge" id="mcAssistantBadge">View only</span>
                </div>
                <p class="mc-assistant-copy" id="mcAssistantCopy">
                    {{ $assistantStocksOnly
                        ? 'Ask in simple language about stocks for a specific field, linked shipment details, or a full stock summary.'
                        : 'Ask in simple language about shipments, stocks, offices, hubs, agents, suppliers, customers, contacts, vessels, users, or administration change logs for a specific field, full details, or a dashboard overview.' }}
                </p>
            </div>
            <button
                type="button"
                class="mc-assistant-close"
                id="mcAssistantClose"
                aria-label="Collapse assistant">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="mc-assistant-thread" id="mcAssistantThread" aria-live="polite"></div>
        <div class="mc-assistant-composer">
            <div class="mc-assistant-input-wrap">
                <textarea
                    class="form-control mc-assistant-input"
                    id="mcAssistantInput"
                    aria-label="MC assistant input"
                    autocomplete="off"
                    spellcheck="false"
                    rows="1"></textarea>
            </div>
            <button type="button" class="mc-assistant-send" id="mcAssistantSend">Send</button>
        </div>
        @if ($assistantStocksOnly)
            <div class="mc-assistant-footer">
                <p class="mc-assistant-help" id="mcAssistantHint">For your role, only stock details and stock summaries are available here.</p>
            </div>
        @endif
    </div>
    <button
        type="button"
        class="mc-assistant-launcher"
        id="mcAssistantLauncher"
        aria-expanded="false"
        aria-controls="mcAssistantPanel">
        <span class="mc-assistant-launcher__pulse" aria-hidden="true"></span>
        <span class="mc-assistant-launcher__copy">
            <span class="mc-assistant-launcher__title">MC Assistant</span>
            <span class="mc-assistant-launcher__status" id="mcAssistantLauncherStatus">Details ready</span>
        </span>
        <span class="mc-assistant-launcher__toggle" id="mcAssistantLauncherToggle">Open</span>
    </button>
</div>
