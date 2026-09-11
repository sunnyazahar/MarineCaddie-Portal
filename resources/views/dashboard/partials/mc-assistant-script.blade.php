<script>
    jQuery(function ($) {
        var $shell = $('#mcAssistantShell');
        var $assistant = $('#mcAssistantPanel');
        if (! $shell.length || ! $assistant.length) {
            return;
        }

        var shellNode = $shell[0] || null;
        if (shellNode && document.body && typeof document.body.appendChild === 'function' && shellNode.parentNode !== document.body) {
            document.body.appendChild(shellNode);
        }

        var assistantData = @json($dashboard['assistant']);
        var assistantScope = assistantData.scope && typeof assistantData.scope === 'object'
            ? assistantData.scope
            : {
                role: 'Admin',
                mode: 'dashboard',
                allows: {
                    overview: true,
                    shipments: true,
                    stocks: true,
                    administration: true,
                    services: true,
                    overdueShipments: true,
                    stockFollowUps: true
                }
            };
        var shipments = Array.isArray(assistantData.shipments) ? assistantData.shipments : [];
        var stocks = Array.isArray(assistantData.stocks) ? assistantData.stocks : [];
        var overdueShipments = Array.isArray(assistantData.overdueShipments) ? assistantData.overdueShipments : [];
        var stockFollowUps = Array.isArray(assistantData.stockFollowUps) ? assistantData.stockFollowUps : [];
        var $thread = $('#mcAssistantThread');
        var $input = $('#mcAssistantInput');
        var $launcher = $('#mcAssistantLauncher');
        var $launcherStatus = $('#mcAssistantLauncherStatus');
        var $launcherToggle = $('#mcAssistantLauncherToggle');
        var $kicker = $('#mcAssistantKicker');
        var $badge = $('#mcAssistantBadge');
        var $copy = $('#mcAssistantCopy');
        var $hint = $('#mcAssistantHint');
        var $close = $('#mcAssistantClose');
        var $clear = $('#mcAssistantClear');
        var $send = $('#mcAssistantSend');
        var lookupUrl = @json(route('dashboard.assistant-lookup'));
        var assistantTestingEnabled = @json(app()->environment('testing'));

        function detectInitialLanguage() {
            var candidates = [
                document.documentElement ? document.documentElement.lang : '',
                navigator.language || '',
                navigator.userLanguage || ''
            ];

            for (var index = 0; index < candidates.length; index += 1) {
                var language = String(candidates[index] || '').toLowerCase();

                if (language.indexOf('hi') === 0) {
                    return 'hinglish';
                }

                if (language.indexOf('en') === 0) {
                    return 'english';
                }
            }

            return 'english';
        }

        var state = {
            isOpen: false,
            hasWelcomed: false,
            isBusy: false,
            language: 'english'
        };

        function currentLanguage() {
            return state.language === 'english' ? 'english' : 'hinglish';
        }

        function isEnglishResponse() {
            return currentLanguage() === 'english';
        }

        function choose(hinglishText, englishText) {
            return isEnglishResponse() ? englishText : hinglishText;
        }

        function scopeAllows(area) {
            return !!(assistantScope && assistantScope.allows && assistantScope.allows[area]);
        }

        function isStockOnlyAssistant() {
            return scopeAllows('stocks')
                && ! scopeAllows('shipments')
                && ! scopeAllows('administration')
                && ! scopeAllows('overview');
        }

        function launcherReadyText() {
            return choose('Details ready', 'Details ready');
        }

        function snapshotReadyText() {
            return choose('Snapshot ready', 'Snapshot ready');
        }

        function checkingRecordsText() {
            return choose('Records check kar raha hoon', 'Checking records');
        }

        function syncAssistantChrome(preserveStatus) {
            $kicker.text(isStockOnlyAssistant()
                ? choose('Stock helper', 'Stock copilot')
                : choose('Dashboard helper', 'Dashboard copilot'));
            $badge.text(choose('Sirf dekhne ke liye', 'View only'));
            $copy.text(isStockOnlyAssistant()
                ? choose(
                    'Simple language me stock ka specific field, linked shipment details ya full stock summary puchhiye.',
                    'Ask in simple language about stocks for a specific field, linked shipment details, or a full stock summary.'
                )
                : choose(
                    'Simple language me shipment, stock, office, hub, agent, supplier, customer, contact, vessel, user ya administration change log ka specific field, full details ya dashboard overview puchhiye.',
                    'Ask in simple language about shipments, stocks, offices, hubs, agents, suppliers, customers, contacts, vessels, users, or administration change logs for a specific field, full details, or a dashboard overview.'
                ));
            $input.attr('placeholder', isStockOnlyAssistant()
                ? choose(
                    'Jaise: CN-72656522 stock ka supplier batao',
                    'Example: What is the supplier for CN-72656522 stock?'
                )
                : choose(
                    'Jaise: MarineCaddie Dubai Office ka address kisne change kiya ya sunnyazahar@gmail.com ka user role batao',
                    'Example: Who changed the address for MarineCaddie Dubai Office or what is the role for user sunnyazahar@gmail.com?'
                ));
            $hint.text(isStockOnlyAssistant()
                ? choose(
                    'Aapke role ke liye yahan sirf stock details aur stock summary available hai.',
                    'For your role, only stock details and stock summaries are available here.'
                )
                : choose(
                    'Yahan se sirf details aur summary dekhi ja sakti hai. Create, update ya delete yahan se nahi hoga.',
                    'You can only view details and summaries here. Create, update, and delete actions are not available.'
                ));
            $close.attr('aria-label', choose('Assistant band karo', 'Collapse assistant'));
            $send.text(choose('Bhejo', 'Send'));
            $clear.text(choose('Chat saaf karo', 'Clear chat'));
            $launcherToggle.text(choose(state.isOpen ? 'Band' : 'Kholo', state.isOpen ? 'Close' : 'Open'));

            if (! preserveStatus && ! state.isBusy) {
                $launcherStatus.text(launcherReadyText());
            }
        }

        function normalizeAssistantTypos(value) {
            return String(value || '')
                .replace(/\badress\b/g, 'address')
                .replace(/\baddres\b/g, 'address');
        }

        function normalize(value) {
            return normalizeAssistantTypos(
                String(value || '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, ' ')
                    .trim()
            );
        }

        function languageTokens(value) {
            return String(value || '')
                .toLowerCase()
                .replace(/[^a-z\u0900-\u097f0-9]+/g, ' ')
                .trim()
                .split(/\s+/)
                .filter(Boolean);
        }

        function normalizeCompact(value) {
            return normalize(value).replace(/\s+/g, '');
        }

        function escapeHtml(value) {
            return $('<div></div>').text(String(value == null ? '' : value)).html();
        }

        function formatText(text) {
            return escapeHtml(text).replace(/\n/g, '<br>');
        }

        function formatNumber(value) {
            return new Intl.NumberFormat().format(Number(value || 0));
        }

        function valueOrDash(value) {
            var text = $.trim(String(value || ''));

            return text ? text : '—';
        }

        function hasValue(value) {
            return valueOrDash(value) !== '—';
        }

        function joinValues(values) {
            return (values || []).filter(Boolean).join(', ') || '—';
        }

        function joinMeaningful(values, separator) {
            return (values || []).map(function (value) {
                return hasValue(value) ? $.trim(String(value)) : '';
            }).filter(Boolean).join(separator || ' · ');
        }

        function yesNo(value) {
            return value ? 'Yes' : 'No';
        }

        function containsAny(text, phrases) {
            return (phrases || []).some(function (phrase) {
                return text.indexOf(phrase) !== -1;
            });
        }

        function escapeRegex(value) {
            return String(value || '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function containsIntentPhrase(text, phrase) {
            var normalizedText = normalize(text);
            var normalizedPhrase = normalize(phrase);

            if (! normalizedText || ! normalizedPhrase) {
                return false;
            }

            var pattern = '\\b' + escapeRegex(normalizedPhrase).replace(/\s+/g, '\\s+') + '\\b';

            return new RegExp(pattern).test(normalizedText);
        }

        function containsIntentPhrases(text, phrases) {
            return (phrases || []).some(function (phrase) {
                return containsIntentPhrase(text, phrase);
            });
        }

        function containsAdministrationEntityHint(text) {
            return containsIntentPhrases(text, [
                'office',
                'offices',
                'hub',
                'hubs',
                'agent',
                'agents',
                'supplier',
                'suppliers',
                'customer',
                'customers',
                'contact',
                'contacts',
                'vessel',
                'vessels',
                'imo',
                'user',
                'users',
                'username',
                'portal user',
                'portal users',
                'change log',
                'change logs',
                'administration change log',
                'administration change logs',
                'change history',
                'edit history',
                'activity log',
                'audit log'
            ]);
        }

        function detectResponseLanguage(text) {
            var raw = String(text || '');
            var tokens = languageTokens(raw);
            var hinglishScore = 0;
            var englishScore = 0;
            var hinglishMarkers = [
                'hai', 'hain', 'tha', 'thi', 'the', 'kya', 'ka', 'ki', 'ke', 'kon', 'kaun', 'kitna', 'kitne', 'kitni',
                'batao', 'dikhao', 'dikha', 'dikhta', 'dikhti', 'dikhega', 'dikhegi', 'chahiye', 'sirf', 'poora', 'pura',
                'sab', 'aur', 'se', 'par', 'nahi', 'kyon', 'kyu', 'kaise', 'kab', 'kahan', 'yahan', 'wahan', 'mere',
                'mera', 'meri', 'hamare', 'iska', 'uska', 'kis', 'kisko', 'hua', 'hui', 'rakha', 'add', 'hoga'
            ];
            var englishMarkers = [
                'what', 'who', 'which', 'when', 'where', 'how', 'show', 'tell', 'give', 'details', 'detail', 'summary',
                'overview', 'shipment', 'shipments', 'stock', 'stocks', 'customer', 'supplier', 'status', 'flight',
                'document', 'documents', 'package', 'packages', 'weight', 'value', 'count', 'total', 'list', 'help',
                'please', 'consignee', 'receiver', 'address', 'port', 'location', 'country', 'city', 'district',
                'email', 'manager', 'created', 'updated', 'route', 'service', 'vessel', 'eta', 'etd', 'currency'
            ];
            var englishLinkingWords = [
                'is', 'are', 'was', 'were', 'the', 'for', 'to', 'from', 'of', 'and', 'or', 'can', 'could', 'would',
                'should', 'do', 'does', 'did', 'has', 'have', 'had', 'with', 'about', 'me', 'my', 'show', 'tell',
                'give', 'need', 'want', 'linked'
            ];

            if (/[\u0900-\u097f]/.test(raw)) {
                return 'hinglish';
            }

            if (! tokens.length) {
                return currentLanguage();
            }

            tokens.forEach(function (token) {
                if (hinglishMarkers.indexOf(token) !== -1) {
                    hinglishScore += 2;
                }

                if (englishMarkers.indexOf(token) !== -1) {
                    englishScore += 1;
                }

                if (englishLinkingWords.indexOf(token) !== -1) {
                    englishScore += 1.25;
                }

                if (/^[a-z]+$/.test(token) && token.length >= 4) {
                    englishScore += 0.25;
                }
            });

            if (containsIntentPhrases(raw, ['how many', 'who is', 'what is', 'show me', 'tell me', 'can you'])) {
                englishScore += 2;
            }

            if (containsIntentPhrases(raw, ['kitna', 'kitne', 'kitni', 'kaun', 'kon', 'kya', 'batao', 'dikhao', 'poora', 'pura'])) {
                hinglishScore += 2;
            }

            if (containsIntentPhrases(raw, ['what is', 'who is', 'where is', 'which is', 'can you', 'please show', 'please tell'])) {
                englishScore += 2;
            }

            if (hinglishScore === 0 && englishScore > 0) {
                return 'english';
            }

            if (hinglishScore > englishScore) {
                return 'hinglish';
            }

            if (englishScore > hinglishScore) {
                return 'english';
            }

            return currentLanguage();
        }

        function renderScopeBlockedResponse(scopeKey) {
            var blockedArea = $.trim(String(scopeKey || '')).toLowerCase();
            var title = choose(
                'Yeh request aapke role scope ke bahar hai',
                'This request is outside your role scope'
            );
            var message = isStockOnlyAssistant()
                ? choose(
                    blockedArea === 'overview'
                        ? 'Aapke role ke liye main yahan sirf stock related details bata sakta hoon. Dashboard overview yahan available nahi hai.'
                        : 'Aapke role ke liye main yahan sirf stock related details bata sakta hoon. Shipment ya administration details yahan available nahi hain.',
                    blockedArea === 'overview'
                        ? 'For your role, I can only help with stock-related details here. The dashboard overview is not available.'
                        : 'For your role, I can only help with stock-related details here. Shipment or administration details are not available.'
                )
                : choose(
                    'Main sirf unhi records par answer de sakta hoon jo aapke role scope me aate hain.',
                    'I can only answer from records that are within your role scope.'
                );
            var examples = isStockOnlyAssistant()
                ? (isEnglishResponse()
                    ? '<code>What is the supplier for CN-72656522 stock</code>, <code>How many packages are in CN-72656522</code>, <code>Show the complete summary for CN-72656522</code>.'
                    : '<code>CN-72656522 stock ka supplier batao</code>, <code>CN-72656522 me kitne packages hain</code>, <code>CN-72656522 ka complete summary batao</code>.')
                : '';

            return {
                kind: 'out-of-scope',
                status: choose('Scope check ready', 'Scope check ready'),
                html: '' +
                    '<strong>' + escapeHtml(title) + '</strong>' +
                    '<p>' + escapeHtml(message) + '</p>' +
                    (examples
                        ? '<p><strong>' + escapeHtml(choose('Aap yeh puchh sakte hain:', 'You can ask:')) + '</strong> ' + examples + '</p>'
                        : '')
            };
        }

        function isReadOnlyActionRequest(text) {
            if (! containsIntentPhrases(text, [
                'create',
                'save crr',
                'save shipment',
                'save stock',
                'submit',
                'update shipment',
                'update stock',
                'edit shipment',
                'edit stock',
                'delete shipment',
                'delete stock',
                'remove shipment',
                'remove stock',
                'cancel shipment',
                'cancel stock',
                'new crr',
                'new shipment',
                'bana do',
                'banao'
            ])) {
                return false;
            }

            if (detectShipmentCreationCountWindow(text)) {
                return false;
            }

            return ! containsIntentPhrases(text, [
                'creation date',
                'created date',
                'created by',
                'creator',
                'when created',
                'when was shipment created',
                'when was this shipment created',
                'kab create hua',
                'kab banaya gaya',
                'kisne create kiya',
                'kisne banaya'
            ]);
        }

        function hasShipmentCreationCountIntent(text) {
            var normalizedText = normalize(text);

            if (! normalizedText) {
                return false;
            }

            if (! queryHasCountIntent(normalizedText)) {
                return false;
            }

            if (! containsIntentPhrases(normalizedText, ['shipment', 'shipments'])) {
                return false;
            }

            return containsIntentPhrases(normalizedText, [
                'created',
                'create',
                'create hua',
                'create hue',
                'create huye',
                'create kiya',
                'new shipment',
                'new shipments',
                'added shipment',
                'added shipments'
            ]);
        }

        function detectShipmentCreationCountWindow(text) {
            var normalizedText = normalize(text);
            var explicitWindowMatch;
            var days;

            if (! hasShipmentCreationCountIntent(normalizedText)) {
                return null;
            }

            if (containsIntentPhrases(normalizedText, ['today', 'aaj'])) {
                return {
                    key: 'today',
                    days: 1
                };
            }

            if (containsIntentPhrases(normalizedText, ['this month', 'current month', 'is month', 'iss month', 'is mahine', 'iss mahine'])) {
                return {
                    key: '30',
                    days: 30
                };
            }

            explicitWindowMatch = normalizedText.match(/\b(?:last|past|previous|recent|pichle|pichhle|beete|pichale)\s+(\d+)\s+(?:day|days|din)\b/);
            if (explicitWindowMatch) {
                days = Number(explicitWindowMatch[1]);

                if (Number.isFinite(days) && days > 0) {
                    return {
                        key: String(days),
                        days: days
                    };
                }
            }

            explicitWindowMatch = normalizedText.match(/\b(\d+)\s+(?:day|days|din)\b/);
            if (explicitWindowMatch && containsIntentPhrases(normalizedText, ['last', 'past', 'previous', 'pichle', 'pichhle', 'beete', 'pichale'])) {
                days = Number(explicitWindowMatch[1]);

                if (Number.isFinite(days) && days > 0) {
                    return {
                        key: String(days),
                        days: days
                    };
                }
            }

            return null;
        }

        function isTodayShipmentCreationCountRequest(text) {
            var requestedWindow = detectShipmentCreationCountWindow(text);

            return !!requestedWindow && requestedWindow.key === 'today';
        }

        function isCancelledShipmentSummaryRequest(text) {
            return containsIntentPhrases(text, ['cancelled shipment', 'cancelled shipments', 'canceled shipment', 'canceled shipments'])
                || (containsIntentPhrases(text, ['cancelled', 'canceled']) && containsIntentPhrases(text, ['shipment', 'shipments']));
        }

        function isTransportDetailsRequest(text) {
            var normalizedText = normalize(text);

            if (! normalizedText) {
                return false;
            }

            if (containsIntentPhrases(normalizedText, [
                'flight detail',
                'flight details',
                'transport detail',
                'transport details',
                'leg detail',
                'leg details',
                'awb detail',
                'awb details',
                'mawb detail',
                'mawb details',
                'mbl detail',
                'mbl details',
                'carrier detail',
                'carrier details',
                'cmr detail',
                'cmr details',
                'bill of lading'
            ])) {
                return true;
            }

            return containsIntentPhrases(normalizedText, ['flight', 'awb', 'mawb', 'mbl', 'carrier', 'leg', 'transport', 'cmr', 'bill of lading'])
                && containsIntentPhrases(normalizedText, ['detail', 'details', 'summary', 'batao', 'dikhao']);
        }

        function isFullRecordRequest(text) {
            return containsIntentPhrases(text, [
                'complete detail',
                'complete details',
                'complete summary',
                'full detail',
                'full details',
                'full summary',
                'poora detail',
                'poora details',
                'poora summary',
                'pura detail',
                'pura details',
                'pura summary',
                'sab detail',
                'sab details',
                'all detail',
                'all details',
                'all field',
                'all fields'
            ]);
        }

        function isGenericRecordSummaryRequest(text) {
            return containsIntentPhrases(text, [
                'detail',
                'details',
                'summary',
                'summery',
                'overview',
                'snapshot',
                'info',
                'information'
            ]);
        }

        function queryHasCountIntent(text) {
            return containsIntentPhrases(text, ['kitna', 'kitne', 'kitni', 'how many', 'count', 'total', 'number of']);
        }

        function queryHasListIntent(text) {
            return containsIntentPhrases(text, [
                'kaun se',
                'kaunse',
                'kaunsa',
                'which',
                'list',
                'name',
                'names',
                'number',
                'numbers'
            ]);
        }

        function matchesExactIntent(text, phrases) {
            var normalizedText = normalize(text);

            return (phrases || []).some(function (phrase) {
                return normalizedText === normalize(phrase);
            });
        }

        function isServiceSummaryRequest(text) {
            return matchesExactIntent(text, ['service', 'services'])
                || containsIntentPhrases(text, [
                    'service summary',
                    'service mix',
                    'show service mix',
                    'shipment mix by service',
                    'service wise shipment mix',
                    'service-wise shipment mix'
                ]);
        }

        function isShipmentSummaryRequest(text) {
            return matchesExactIntent(text, ['shipment', 'shipments'])
                || containsIntentPhrases(text, [
                    'shipment summary',
                    'shipment summaries',
                    'shipment overview',
                    'shipment details',
                    'show shipment summary',
                    'show shipments',
                    'all shipments',
                    'active shipments',
                    'pre alert',
                    'pre-alert',
                    'irregularit',
                    'shipment ka',
                    'shipment batao'
                ]);
        }

        function isStockSummaryRequest(text) {
            return matchesExactIntent(text, ['stock', 'stocks', 'crr'])
                || containsIntentPhrases(text, [
                    'stock summary',
                    'stock summaries',
                    'stock overview',
                    'stock details',
                    'show stock summary',
                    'show stocks',
                    'all stocks',
                    'active stocks',
                    'urgent stocks',
                    'stock ka',
                    'stock batao'
                ]);
        }

        function containsAdministrationFieldHint(text) {
            return containsIntentPhrases(text, [
                'address',
                'office address',
                'primary address',
                'billing address',
                'invoice address',
                'email',
                'emails',
                'phone',
                'phone number',
                'mobile',
                'role',
                'user role',
                'username',
                'login',
                'assigned office',
                'assigned offices',
                'assigned hub',
                'assigned hubs',
                'assigned agent',
                'assigned agents',
                'assigned supplier',
                'assigned suppliers',
                'otp blocked',
                'otp blocked until',
                'blocked until',
                'contact person',
                'contact name',
                'manager',
                'responsible manager',
                'account manager',
                'created by',
                'creator',
                'who added',
                'who created',
                'kisne add kiya',
                'kisne create kiya',
                'kisne banaya',
                'updated by',
                'modified by',
                'who updated',
                'who modified',
                'last update',
                'last modified',
                'city',
                'country',
                'zip code',
                'zipcode',
                'postal code',
                'port code',
                'vat number',
                'eori',
                'imo',
                'vessel names',
                'which vessels',
                'total vessels',
                'kitne vessels',
                'kaun se vessels',
                'what changed',
                'what was changed',
                'who changed',
                'who modified',
                'who updated',
                'changed by',
                'change history',
                'last change',
                'latest change',
                'last modification',
                'last modification field',
                'last modified field',
                'last edited by',
                'last modified by',
                'change description',
                'record name',
                'which record',
                'entity',
                'which entity',
                'module',
                'change date',
                'last change date',
                'kab change hua',
                'change kab hua',
                'kisne change kiya',
                'kisne modify kiya',
                'kisne update kiya'
            ]);
        }

        function looksLikeAdministrationNameQuery(text) {
            var raw = $.trim(String(text || ''));
            var tokens = languageTokens(raw);

            if (! raw || tokens.length < 2 || tokens.length > 10) {
                return false;
            }

            if (extractLookupTerms(raw).length > 0) {
                return false;
            }

            if (
                isServiceSummaryRequest(raw) ||
                isShipmentSummaryRequest(raw) ||
                isStockSummaryRequest(raw) ||
                containsIntentPhrases(raw, [
                    'overview',
                    'dashboard summary',
                    'overdue arrivals',
                    'follow up stocks',
                    'follow-up stocks',
                    'help',
                    'what can you',
                    'tum kya kar'
                ])
            ) {
                return false;
            }

            return containsIntentPhrases(raw, [
                'shipping',
                'services',
                'cargo',
                'logistics',
                'marine',
                'agency',
                'group',
                'company',
                'trading',
                'ltd',
                'llc',
                'pte',
                'bv',
                'b v'
            ]);
        }

        function isAdministrationLookupRequest(text) {
            var raw = $.trim(String(text || ''));

            if (! raw) {
                return false;
            }

            if (extractLookupTerms(raw).length > 0) {
                return false;
            }

            if (containsIntentPhrases(raw, [
                'shipment ka',
                'shipment ke',
                'shipment ki',
                'stock ka',
                'stock ke',
                'stock ki'
            ])) {
                return false;
            }

            return containsAdministrationEntityHint(raw)
                || containsAdministrationFieldHint(raw)
                || looksLikeAdministrationNameQuery(raw);
        }

        function matchesIntent(text, includePhrases, excludePhrases) {
            if (! containsIntentPhrases(text, includePhrases || [])) {
                return false;
            }

            return ! ((excludePhrases || []).length && containsIntentPhrases(text, excludePhrases || []));
        }

        function setComposerBusy(isBusy) {
            state.isBusy = isBusy;
            $input.prop('disabled', isBusy);
            $clear.prop('disabled', isBusy);
            $send.prop('disabled', isBusy);
        }

        function extractLookupTerms(text) {
            var unique = {};
            var terms = [];

            function push(term) {
                var value = $.trim(String(term || ''));
                var key = normalizeCompact(value);

                if (! key || key.length < 3 || unique[key]) {
                    return;
                }

                unique[key] = true;
                terms.push(value);
            }

            (String(text || '').match(/\b[A-Za-z0-9]{2,}(?:-[A-Za-z0-9]+)+\b/g) || []).forEach(push);
            (String(text || '').match(/\b\d{5,}\b/g) || []).forEach(push);

            return terms;
        }

        function isDetailResponse(response) {
            return response && (
                response.kind === 'compound-response' ||
                response.kind === 'shipment-compound-detail' ||
                response.kind === 'stock-detail' ||
                response.kind === 'shipment-detail' ||
                response.kind === 'shipment-transport-detail' ||
                response.kind === 'shipment-field-detail' ||
                response.kind === 'stock-field-detail' ||
                response.kind === 'office-detail' ||
                response.kind === 'hub-detail' ||
                response.kind === 'agent-detail' ||
                response.kind === 'supplier-detail' ||
                response.kind === 'customer-detail' ||
                response.kind === 'contact-detail' ||
                response.kind === 'vessel-detail' ||
                response.kind === 'user-detail' ||
                response.kind === 'office-field-detail' ||
                response.kind === 'hub-field-detail' ||
                response.kind === 'agent-field-detail' ||
                response.kind === 'supplier-field-detail' ||
                response.kind === 'customer-field-detail' ||
                response.kind === 'contact-field-detail' ||
                response.kind === 'vessel-field-detail' ||
                response.kind === 'user-field-detail' ||
                response.kind === 'change-log-detail' ||
                response.kind === 'change-log-field-detail'
            );
        }

        function shouldTryRemoteLookup(text, response) {
            if (state.isBusy || ! $.trim(String(text || ''))) {
                return false;
            }

            if (response && (response.kind === 'read-only' || response.kind === 'out-of-scope')) {
                return false;
            }

            if (isAdministrationLookupRequest(text)) {
                return true;
            }

            if (extractLookupTerms(text).length > 0) {
                return ! isDetailResponse(response);
            }

            return response && response.kind === 'clarify';
        }

        function rememberLookupItem(type, item) {
            if (! item || ! item.number) {
                return;
            }

            var list = type === 'shipment' ? shipments : stocks;
            var existingIndex = list.findIndex(function (entry) {
                return normalizeCompact(entry.number) === normalizeCompact(item.number);
            });

            if (existingIndex === -1) {
                list.unshift(item);
                return;
            }

            list[existingIndex] = item;
        }

        function addMessage(role, message, isHtml) {
            var label = 'MC Assistant';

            if (role === 'user') {
                label = detectResponseLanguage(message) === 'english' ? 'You' : 'Aap';
            }

            var body = isHtml ? message : formatText(message);
            var html = '' +
                '<div class="mc-assistant-message mc-assistant-message--' + role + '">' +
                    '<div class="mc-assistant-message__label">' + label + '</div>' +
                    '<div class="mc-assistant-message__bubble">' + body + '</div>' +
                '</div>';

            $thread.append(html);
            $thread.scrollTop($thread[0].scrollHeight);
        }

        function shouldApplyViewportLift(hiddenBottom) {
            if (hiddenBottom <= 120) {
                return false;
            }

            var assistantNode = $assistant[0] || null;
            var activeElement = document.activeElement || null;

            if (! assistantNode || ! activeElement || typeof assistantNode.contains !== 'function' || ! assistantNode.contains(activeElement)) {
                return false;
            }

            var tagName = String(activeElement.tagName || '').toLowerCase();

            return /^(input|textarea|select)$/.test(tagName) || !!activeElement.isContentEditable;
        }

        function syncViewportOffset() {
            var shellNode = $shell[0] || null;

            if (! shellNode || ! shellNode.style || typeof shellNode.style.setProperty !== 'function') {
                return;
            }

            var hiddenBottom = 0;
            var visualViewport = window.visualViewport || null;

            if (visualViewport && typeof visualViewport.height === 'number') {
                var offsetTop = typeof visualViewport.offsetTop === 'number' ? visualViewport.offsetTop : 0;
                hiddenBottom = Math.max(0, Math.round(window.innerHeight - (visualViewport.height + offsetTop)));
            }

            var keyboardLift = shouldApplyViewportLift(hiddenBottom) ? hiddenBottom : 0;

            shellNode.style.setProperty('--mc-assistant-visual-offset', keyboardLift + 'px');
        }

        function setLauncher(isOpen) {
            state.isOpen = isOpen;
            syncViewportOffset();

            if (! isOpen) {
                var assistantNode = $assistant[0] || null;
                var activeElement = document.activeElement || null;

                if (assistantNode && activeElement && typeof assistantNode.contains === 'function' && assistantNode.contains(activeElement) && typeof activeElement.blur === 'function') {
                    activeElement.blur();
                }
            }

            $shell.toggleClass('is-open', isOpen);
            $launcher.attr('aria-expanded', isOpen ? 'true' : 'false');
            $assistant.attr('aria-hidden', isOpen ? 'false' : 'true');
            syncAssistantChrome(true);

            if (isOpen) {
                window.setTimeout(function () {
                    $input.trigger('focus');
                }, 40);

                return;
            }

            if ($launcher.length && $launcher[0] && typeof $launcher[0].focus === 'function') {
                window.setTimeout(function () {
                    $launcher[0].focus();
                }, 0);
            }
        }

        function forceClosedLauncherState() {
            state.isOpen = false;
            $shell.removeClass('is-open');
            $launcher.attr('aria-expanded', 'false');
            $assistant.attr('aria-hidden', 'true');
        }

        function shipmentSearchText(item) {
            var documentNames = (item.documents || []).map(function (document) {
                return document.name;
            });
            var transportBits = (item.transportLegs || []).map(function (leg) {
                return [
                    leg.title,
                    leg.referenceLabel,
                    leg.reference,
                    leg.carrierLabel,
                    leg.carrier,
                    leg.departurePort,
                    leg.departureDate,
                    leg.arrivalDate,
                    leg.arrivalTime,
                    leg.note
                ].join(' ');
            });

            return [
                item.number,
                item.status,
                item.departure,
                item.departurePort,
                item.service,
                item.additionalService,
                item.vessel,
                item.customer,
                item.consignee,
                item.consigneePort,
                item.contactPerson,
                item.customerReference,
                joinValues(item.linkedStocks),
                item.accountManager,
                joinValues(documentNames),
                item.transportHeading,
                transportBits.join(' ')
            ].join(' ');
        }

        function stockSearchText(item) {
            return [
                item.number,
                item.status,
                item.priority,
                item.vessel,
                item.customer,
                item.supplier,
                item.hubAgent,
                joinValues(item.linkedShipments),
                item.acceptance
            ].join(' ');
        }

        function recordMatches(items, query, searchTextCallback) {
            var compactQuery = normalizeCompact(query);

            if (! compactQuery) {
                return [];
            }

            return (items || []).map(function (item, index) {
                var primary = normalizeCompact(item.number);
                var haystack = normalizeCompact(searchTextCallback(item));
                var score = -1;

                if (primary === compactQuery) {
                    score = 500;
                } else if (primary && compactQuery.indexOf(primary) !== -1) {
                    score = 460;
                } else if (primary.indexOf(compactQuery) === 0) {
                    score = 420;
                } else if (haystack.indexOf(compactQuery) !== -1) {
                    score = 300;
                }

                return {
                    item: item,
                    score: score,
                    index: index
                };
            }).filter(function (entry) {
                return entry.score >= 0;
            }).sort(function (left, right) {
                if (left.score !== right.score) {
                    return right.score - left.score;
                }

                return left.index - right.index;
            });
        }

        function findShipmentByNumber(query) {
            var compactQuery = normalizeCompact(query);
            var matches = shipments.filter(function (item) {
                var number = normalizeCompact(item.number);
                return number && (number === compactQuery || compactQuery.indexOf(number) !== -1);
            });

            return matches.length === 1 ? matches[0] : null;
        }

        function findStockByNumber(query) {
            var compactQuery = normalizeCompact(query);
            var matches = stocks.filter(function (item) {
                var number = normalizeCompact(item.number);
                return number && (number === compactQuery || compactQuery.indexOf(number) !== -1);
            });

            return matches.length === 1 ? matches[0] : null;
        }

        function cleanAssistantClause(text) {
            return $.trim(String(text || '')
                .replace(/^[\s,;:.-]+/, '')
                .replace(/[\s,;:.-]+$/, '')
            );
        }

        function splitFieldIntentClauses(text) {
            var parts = String(text || '')
                .split(/\s+(?:and|aur|&)\s+/i)
                .map(cleanAssistantClause)
                .filter(Boolean);

            return parts.length ? parts : [cleanAssistantClause(text)];
        }

        function looksLikeStandaloneAssistantClause(text) {
            var raw = cleanAssistantClause(text);
            var normalizedText = normalize(raw);

            if (! raw || ! normalizedText) {
                return false;
            }

            if (extractLookupTerms(raw).length > 0) {
                return true;
            }

            if (containsAdministrationEntityHint(raw) || looksLikeAdministrationNameQuery(raw)) {
                return true;
            }

            if (isReadOnlyActionRequest(normalizedText)) {
                return true;
            }

            if (isCancelledShipmentSummaryRequest(normalizedText) || detectShipmentCreationCountWindow(normalizedText)) {
                return true;
            }

            if (containsAny(normalizedText, ['help', 'what can you', 'kya kar', 'capabilit', 'madad', 'samjha'])) {
                return true;
            }

            if (containsAny(normalizedText, [
                'overdue',
                'late arrival',
                'past deadline',
                'delay',
                'der',
                'follow up',
                'follow-up',
                'followup',
                'unaccepted',
                'awaiting acceptance',
                'pickup queue',
                'pending accept'
            ])) {
                return true;
            }

            if (isServiceSummaryRequest(raw) || isShipmentSummaryRequest(raw) || isStockSummaryRequest(raw)) {
                return true;
            }

            return containsAny(normalizedText, ['overview', 'dashboard', 'snapshot', 'kpi', 'metrics', 'everything', 'overall', 'sab kuch']);
        }

        function splitStandaloneQuestionSegments(text) {
            var segments = [];

            String(text || '')
                .split(/[?\n]+/)
                .map(cleanAssistantClause)
                .filter(Boolean)
                .forEach(function (segment) {
                    var parts = segment
                        .split(/\s+(?:and|aur|&)\s+/i)
                        .map(cleanAssistantClause)
                        .filter(Boolean);

                    if (parts.length > 1 && parts.every(looksLikeStandaloneAssistantClause)) {
                        segments = segments.concat(parts);
                        return;
                    }

                    segments.push(segment);
                });

            return segments.filter(Boolean);
        }

        function responseIdentityKey(response) {
            return [
                response && response.kind ? String(response.kind) : '',
                normalizeForTest(response && response.html ? response.html : '')
            ].join('|');
        }

        function renderCombinedResponses(responses, kind, status) {
            var seen = {};
            var uniqueResponses = [];

            (responses || []).forEach(function (response) {
                var key;

                if (! response || ! response.html) {
                    return;
                }

                key = responseIdentityKey(response);

                if (seen[key]) {
                    return;
                }

                seen[key] = true;
                uniqueResponses.push(response);
            });

            if (! uniqueResponses.length) {
                return null;
            }

            if (uniqueResponses.length === 1) {
                return uniqueResponses[0];
            }

            return {
                kind: kind || 'compound-response',
                status: status || choose('Combined answer ready', 'Combined answer ready'),
                html: '<div class="mc-assistant-response-stack">'
                    + uniqueResponses.map(function (response) {
                        return '<div class="mc-assistant-response-stack__item">' + String(response.html || '') + '</div>';
                    }).join('')
                    + '</div>'
            };
        }

        function textParagraphs(lines) {
            return (lines || []).filter(function (line) {
                return $.trim(String(line || '')) !== '';
            }).map(function (line) {
                return '<p>' + escapeHtml(line) + '</p>';
            }).join('');
        }

        function displayValue(value, emptyText, englishEmptyText) {
            return hasValue(value)
                ? $.trim(String(value))
                : (isEnglishResponse()
                    ? (englishEmptyText || emptyText || 'not added yet')
                    : (emptyText || 'abhi add nahi hai'));
        }

        function describeCount(value, singular, plural) {
            var total = Number(value || 0);
            return formatNumber(total) + ' ' + (total === 1 ? singular : plural);
        }

        function countVerb(value, singular, plural) {
            return Number(value || 0) === 1 ? singular : plural;
        }

        function buildShipmentAddress(item) {
            var address = joinMeaningful([
                item.consigneeAddress,
                item.consigneeCity,
                item.consigneeDistrict,
                item.consigneeZip,
                item.consigneeCountry
            ], ', ');

            return hasValue(address) ? address : choose('abhi complete address add nahi hai', 'the complete address is not added yet');
        }

        function metricList(rows) {
            return '<ul>' + rows.map(function (row) {
                return '<li><strong>' + escapeHtml(row.label) + ':</strong> ' + escapeHtml(row.value) + '</li>';
            }).join('') + '</ul>';
        }

        function section(title, body) {
            return '<p><strong>' + escapeHtml(title) + '</strong></p>' + body;
        }

        function renderShipmentDocuments(items) {
            if (! items.length) {
                return '';
            }

            return '<ul>' + items.map(function (document) {
                var visibility = document.internal
                    ? choose('internal copy', 'internal copy')
                    : choose('shared copy', 'shared copy');

                return '<li><strong>' + escapeHtml(document.name) + ':</strong> '
                    + escapeHtml(choose(
                        'Type ' + displayValue(document.type) + ' hai, yeh ' + visibility + ' hai aur ' + displayValue(document.addedAt) + ' ko add hua tha.',
                        'Type is ' + displayValue(document.type) + ', this is an ' + visibility + ', and it was added on ' + displayValue(document.addedAt) + '.'
                    ))
                    + '</li>';
            }).join('') + '</ul>';
        }

        function renderShipmentStockItems(items) {
            if (! items.length) {
                return '<p>' + escapeHtml(choose(
                    'Is shipment ke saath abhi koi linked stock item visible nahi hai.',
                    'No linked stock item is visible for this shipment right now.'
                )) + '</p>';
            }

            return '<ul>' + items.map(function (stock) {
                var parts = [
                    choose('Hub ', 'Hub ') + displayValue(stock.hub),
                    choose('vessel ', 'vessel ') + displayValue(stock.vessel),
                    hasValue(stock.poNumber) ? 'PO ' + stock.poNumber : choose('PO number abhi add nahi hai', 'PO number is not added yet'),
                    choose('supplier ', 'supplier ') + displayValue(stock.supplier),
                    describeCount(stock.packages || 0, 'package', 'packages'),
                    hasValue(stock.weight) ? stock.weight + choose(' kg weight', ' kg weight') : choose('weight abhi add nahi hai', 'weight is not added yet'),
                    hasValue(stock.cbm) ? stock.cbm + ' CBM' : choose('CBM abhi add nahi hai', 'CBM is not added yet'),
                    hasValue(stock.value) ? choose('value ', 'value ') + stock.value : choose('value abhi add nahi hai', 'value is not added yet'),
                    choose('status ', 'status ') + displayValue(stock.status)
                ];

                return '<li><strong>' + escapeHtml(stock.number) + ':</strong> '
                    + escapeHtml(parts.join(', ') + choose(' dikh raha hai.', ' is visible.'))
                    + '</li>';
            }).join('') + '</ul>';
        }

        function formatRepackedNarrative(items, weight) {
            if (items == null && ! hasValue(weight)) {
                return choose('abhi add nahi hai', 'not added yet');
            }

            var summary = joinMeaningful([
                items == null ? '' : formatNumber(items) + ' item',
                hasValue(weight) ? weight + ' kg' : ''
            ], choose(' aur ', ' and '));

            return summary ? summary + choose(' hai', ' is available') : choose('abhi add nahi hai', 'not added yet');
        }

        function buildShipmentNarrative(item) {
            var lines = [];

            lines.push(
                choose(
                    'Shipment number ' + displayValue(item.number, 'abhi visible nahi hai') + ' hai. Abhi iska status ' + displayValue(item.status, 'Unknown') + ' hai.',
                    'Shipment number ' + displayValue(item.number, 'abhi visible nahi hai', 'not visible right now') + ' is visible. Its current status is ' + displayValue(item.status, 'Unknown') + '.'
                )
            );

            lines.push(
                choose(
                    'Yeh ' + displayValue(item.creationDate) + ' ko ' + displayValue(item.createdBy)
                        + ' ne banaya tha, account manager ' + displayValue(item.accountManager)
                        + ' hai aur last update ' + displayValue(item.updatedAt) + ' par hua tha.',
                    'It was created on ' + displayValue(item.creationDate) + ' by ' + displayValue(item.createdBy)
                        + '. The account manager is ' + displayValue(item.accountManager)
                        + ' and the last update was on ' + displayValue(item.updatedAt) + '.'
                )
            );

            lines.push(
                choose(
                    'Customer ' + displayValue(item.customer) + ' hai, vessel ' + displayValue(item.vessel)
                        + ' hai aur receiver ' + displayValue(item.consignee) + ' hai.',
                    'The customer is ' + displayValue(item.customer) + ', the vessel is ' + displayValue(item.vessel)
                        + ', and the receiver is ' + displayValue(item.consignee) + '.'
                )
            );

            return lines.join(' ');
        }

        function transportHeadingText(item) {
            return $.trim(String((item && item.transportHeading) || 'Transport details'));
        }

        function transportDetailNoun(item) {
            return transportHeadingText(item).replace(/\s+details$/i, ' detail').toLowerCase();
        }

        function renderTransportLegList(item) {
            var legs = Array.isArray(item.transportLegs) ? item.transportLegs : [];

            if (! legs.length) {
                return '<p>' + escapeHtml(choose(
                    'Abhi is shipment me koi saved ' + transportDetailNoun(item) + ' nahi hai.',
                    'There is no saved ' + transportDetailNoun(item) + ' for this shipment right now.'
                )) + '</p>';
            }

            return '<ul>' + legs.map(function (leg, index) {
                var pieces = [];

                if (hasValue(leg.reference) && hasValue(leg.referenceLabel)) {
                    pieces.push(leg.referenceLabel + ' ' + leg.reference);
                }

                if (hasValue(leg.carrier) && hasValue(leg.carrierLabel)) {
                    pieces.push(leg.carrierLabel + ' ' + leg.carrier);
                }

                if (hasValue(leg.departurePort)) {
                    pieces.push(choose('departure ', 'departure ') + leg.departurePort);
                }

                if (hasValue(leg.departureDate)) {
                    pieces.push(choose('departure date ', 'departure date ') + leg.departureDate);
                }

                if (hasValue(leg.arrivalDate)) {
                    pieces.push(choose('arrival date ', 'arrival date ') + leg.arrivalDate);
                }

                if (hasValue(leg.arrivalTime)) {
                    pieces.push(choose('arrival time ', 'arrival time ') + leg.arrivalTime);
                }

                if (hasValue(leg.note)) {
                    pieces.push(leg.note);
                }

                return '<li><strong>' + escapeHtml(leg.title || ('Leg ' + (index + 1))) + ':</strong> '
                    + escapeHtml(pieces.length
                        ? (pieces.join(', ') + choose(' dikh raha hai.', ' is saved.'))
                        : choose('Abhi is leg ki extra details save nahi hai.', 'No extra details are saved for this leg right now.'))
                    + '</li>';
            }).join('') + '</ul>';
        }

        function shipmentList(items, title, emptyMessage) {
            if (! items.length) {
                return '<strong>' + escapeHtml(title) + '</strong><p>' + escapeHtml(emptyMessage) + '</p>';
            }

            return '<strong>' + escapeHtml(title) + '</strong>' + metricList(items.map(function (item) {
                var meta = [
                    valueOrDash(item.status),
                    valueOrDash(item.service),
                    valueOrDash(item.vessel)
                ].join(' · ');

                if (item.deadlineArrival) {
                    meta += ' · ETA deadline ' + item.deadlineArrival;
                }

                return {
                    label: item.number,
                    value: meta
                };
            }));
        }

        function stockList(items, title, emptyMessage) {
            if (! items.length) {
                return '<strong>' + escapeHtml(title) + '</strong><p>' + escapeHtml(emptyMessage) + '</p>';
            }

            return '<strong>' + escapeHtml(title) + '</strong>' + metricList(items.map(function (item) {
                var meta = [
                    valueOrDash(item.status),
                    valueOrDash(item.priority),
                    valueOrDash(item.customer)
                ].join(' · ');

                if (item.expectedDeliveryDate) {
                    meta += ' · Expected ' + item.expectedDeliveryDate;
                }

                return {
                    label: item.number,
                    value: meta
                };
            }));
        }

        function renderOverview() {
            return {
                kind: 'overview',
                status: choose('Overview ready', 'Overview ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Yeh aapka dashboard summary hai', 'Here is your dashboard summary')) + '</strong>' +
                    '<p>' + escapeHtml(choose(
                        'Yeh last ' + String(assistantData.period) + ' days ka snapshot hai. Main yahan se sirf details aur summary hi dikha sakta hoon.',
                        'This is a snapshot for the last ' + String(assistantData.period) + ' days. I can only show details and summaries here.'
                    )) + '</p>' +
                    metricList([
                        { label: choose('Active stocks', 'Active stocks'), value: formatNumber(assistantData.kpis.activeStocks) },
                        { label: choose('Acceptance pending stocks', 'Acceptance pending stocks'), value: formatNumber(assistantData.kpis.unacceptedStocks) },
                        { label: choose('Pickup pending', 'Pickup pending'), value: formatNumber(assistantData.kpis.pickupQueue) },
                        { label: choose('Urgent stocks', 'Urgent stocks'), value: formatNumber(assistantData.kpis.urgentStocks) },
                        { label: choose('Active shipments', 'Active shipments'), value: formatNumber(assistantData.kpis.activeShipments) },
                        { label: choose('Aaj new shipments', 'New shipments today'), value: formatNumber(assistantData.kpis.newShipmentsToday) },
                        { label: choose('Late arrivals', 'Late arrivals'), value: formatNumber(assistantData.kpis.overdueArrivals) },
                        { label: choose('Pre-alerts due', 'Pre-alerts due'), value: formatNumber(assistantData.kpis.preAlertsDue) },
                        { label: choose('Open issues', 'Open issues'), value: formatNumber(assistantData.kpis.openIrregularities) },
                        { label: choose('Aaj bheje reminders', 'Reminders sent today'), value: formatNumber(assistantData.kpis.remindersToday) }
                    ])
            };
        }

        function renderTodayShipmentCreationSummary() {
            return renderShipmentCreationWindowSummary({
                key: 'today',
                days: 1
            });
        }

        function shipmentCreationWindowLimit() {
            var limit = Number(assistantData.shipmentCreationWindowDays || 0);

            return Number.isFinite(limit) && limit > 0 ? limit : 0;
        }

        function shipmentCreationCountForWindow(windowInfo) {
            var counts = assistantData.shipmentCreationCounts || {};
            var daily = assistantData.shipmentCreationDaily || {};
            var windowDays = Number(windowInfo && windowInfo.days ? windowInfo.days : 0);
            var availableDays = shipmentCreationWindowLimit();
            var values;

            if (windowInfo && windowInfo.key === 'today') {
                return Number((assistantData.kpis || {}).newShipmentsToday || counts.today || 0);
            }

            if (windowInfo && Object.prototype.hasOwnProperty.call(counts, String(windowInfo.key))) {
                return Number(counts[String(windowInfo.key)] || 0);
            }

            if (! Number.isFinite(windowDays) || windowDays < 1 || ! availableDays || windowDays > availableDays) {
                return null;
            }

            values = Object.keys(daily)
                .sort()
                .map(function (dateKey) {
                    return Number(daily[dateKey] || 0);
                });

            return values.slice(-windowDays).reduce(function (total, value) {
                return total + value;
            }, 0);
        }

        function renderShipmentCreationWindowSummary(windowInfo) {
            var windowDays = Number(windowInfo && windowInfo.days ? windowInfo.days : 0);
            var total = shipmentCreationCountForWindow(windowInfo);
            var limit = shipmentCreationWindowLimit();

            if (total == null) {
                return {
                    kind: 'shipment-created-window-unavailable',
                    status: choose('Shipment count limit ready', 'Shipment count limit ready'),
                    html: '' +
                        '<strong>' + escapeHtml(choose('Shipment creation count', 'Shipment creation count')) + '</strong>' +
                        '<p>' + escapeHtml(choose(
                            'Main abhi last ' + formatNumber(limit) + ' days tak ke shipment creation counts dikha sakta hoon.',
                            'I can currently show shipment creation counts for up to the last ' + formatNumber(limit) + ' days.'
                        )) + '</p>'
                };
            }

            if (windowInfo && windowInfo.key === 'today') {
                return {
                    kind: 'shipment-created-today',
                    status: choose('Aaj ke shipments ready', 'Today shipment count ready'),
                    html: '' +
                        '<strong>' + escapeHtml(choose('Aaj create hue shipments', 'New shipments created today')) + '</strong>' +
                        '<p>' + escapeHtml(choose(
                            'Aaj total ' + formatNumber(total) + ' new shipment create hue hain.',
                            'A total of ' + formatNumber(total) + ' new shipments were created today.'
                        )) + '</p>'
                };
            }

            return {
                kind: 'shipment-created-window',
                status: choose('Recent shipment count ready', 'Recent shipment count ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Recent shipment creation count', 'Recent shipment creation count')) + '</strong>' +
                    '<p>' + escapeHtml(choose(
                        'Last ' + formatNumber(windowDays) + ' days me total ' + formatNumber(total) + ' shipment create hue hain.',
                        'A total of ' + formatNumber(total) + ' shipments were created in the last ' + formatNumber(windowDays) + ' days.'
                    )) + '</p>'
            };
        }

        function renderShipmentSummary() {
            var shipmentMix = (assistantData.shipmentStatuses || []).filter(function (item) {
                return Number(item.value || 0) > 0;
            }).slice(0, 5).map(function (item) {
                return item.label + ' ' + formatNumber(item.value);
            }).join(', ');

            return {
                kind: 'shipment-summary',
                status: choose('Shipment summary ready', 'Shipment summary ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Shipment ka short summary', 'Shipment summary')) + '</strong>' +
                    '<p>' + escapeHtml(choose(
                        'Abhi active shipments ' + formatNumber(assistantData.kpis.activeShipments) + ' hain. Late arrivals ' + formatNumber(assistantData.kpis.overdueArrivals) + ' hain. Pre-alerts due ' + formatNumber(assistantData.kpis.preAlertsDue) + ' hain aur open issues ' + formatNumber(assistantData.kpis.openIrregularities) + ' hain.',
                        'There are currently ' + formatNumber(assistantData.kpis.activeShipments) + ' active shipments, ' + formatNumber(assistantData.kpis.overdueArrivals) + ' late arrivals, ' + formatNumber(assistantData.kpis.preAlertsDue) + ' pre-alerts due, and ' + formatNumber(assistantData.kpis.openIrregularities) + ' open issues.'
                    )) + '</p>' +
                    (shipmentMix ? '<p><strong>' + escapeHtml(choose('Status mix:', 'Status mix:')) + '</strong> ' + escapeHtml(shipmentMix) + '</p>' : '') +
                    shipmentList(
                        shipments.slice(0, 5),
                        choose('Recent shipments jo abhi visible hain', 'Recent visible shipments'),
                        choose('Abhi is dashboard snapshot me koi shipment visible nahi hai.', 'No shipment is visible in this dashboard snapshot right now.')
                    )
            };
        }

        function renderStockSummary() {
            var stockMix = (assistantData.stockStatuses || []).filter(function (item) {
                return Number(item.value || 0) > 0;
            }).slice(0, 5).map(function (item) {
                return item.label + ' ' + formatNumber(item.value);
            }).join(', ');

            return {
                kind: 'stock-summary',
                status: choose('Stock summary ready', 'Stock summary ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Stock ka short summary', 'Stock summary')) + '</strong>' +
                    '<p>' + escapeHtml(choose(
                        'Abhi active stocks ' + formatNumber(assistantData.kpis.activeStocks) + ' hain. Acceptance pending stocks ' + formatNumber(assistantData.kpis.unacceptedStocks) + ' hain. Pickup pending ' + formatNumber(assistantData.kpis.pickupQueue) + ' hain aur urgent stocks ' + formatNumber(assistantData.kpis.urgentStocks) + ' hain.',
                        'There are currently ' + formatNumber(assistantData.kpis.activeStocks) + ' active stocks, ' + formatNumber(assistantData.kpis.unacceptedStocks) + ' acceptance-pending stocks, ' + formatNumber(assistantData.kpis.pickupQueue) + ' pickup-pending items, and ' + formatNumber(assistantData.kpis.urgentStocks) + ' urgent stocks.'
                    )) + '</p>' +
                    (stockMix ? '<p><strong>' + escapeHtml(choose('Status mix:', 'Status mix:')) + '</strong> ' + escapeHtml(stockMix) + '</p>' : '') +
                    stockList(
                        stocks.slice(0, 5),
                        choose('Recent stocks jo abhi visible hain', 'Recent visible stocks'),
                        choose('Abhi is dashboard snapshot me koi stock visible nahi hai.', 'No stock is visible in this dashboard snapshot right now.')
                    )
            };
        }

        function renderServices() {
            var services = (assistantData.services || []).map(function (item) {
                return {
                    label: item.label,
                    value: formatNumber(item.value)
                };
            });

            return {
                kind: 'service-summary',
                status: choose('Service summary ready', 'Service summary ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Service-wise shipment mix', 'Shipment mix by service')) + '</strong>' +
                    (services.length
                        ? metricList(services)
                        : '<p>' + escapeHtml(choose('Abhi is snapshot me koi active shipment service visible nahi hai.', 'No active shipment service is visible in this snapshot right now.')) + '</p>')
            };
        }

        function renderOverdueShipments() {
            return {
                kind: 'overdue-arrivals',
                status: choose('Overdue list ready', 'Overdue list ready'),
                html: shipmentList(
                    overdueShipments.slice(0, 6),
                    choose('Late ya overdue arrivals', 'Late or overdue arrivals'),
                    choose('Abhi koi overdue shipment nahi hai.', 'There is no overdue shipment right now.')
                )
            };
        }

        function renderStockFollowUps() {
            return {
                kind: 'stock-follow-ups',
                status: choose('Follow-up list ready', 'Follow-up list ready'),
                html: stockList(
                    stockFollowUps.slice(0, 6),
                    choose('Stocks jin par follow-up chahiye', 'Stocks that need follow-up'),
                    choose('Abhi koi stock follow-up item nahi hai.', 'There is no stock follow-up item right now.')
                )
            };
        }

        function renderCancelledShipmentSummary() {
            var total = Number((assistantData.kpis || {}).cancelledShipments || 0);
            var recentCancelled = shipments.filter(function (item) {
                return normalize(item.status) === 'cancelled' || normalize(item.status) === 'canceled';
            }).slice(0, 4);

            return {
                kind: 'cancelled-shipment-summary',
                status: choose('Cancelled shipment summary ready', 'Cancelled shipment summary ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Cancelled shipment summary', 'Cancelled shipment summary')) + '</strong>' +
                    '<p>' + escapeHtml(choose(
                        'Hamare system me total ' + formatNumber(total) + ' cancelled shipments hain.',
                        'There are ' + formatNumber(total) + ' cancelled shipments in the system.'
                    )) + '</p>' +
                    (recentCancelled.length
                        ? shipmentList(
                            recentCancelled,
                            choose('Recent cancelled shipments jo snapshot me dikh rahe hain', 'Recent visible cancelled shipments'),
                            choose('Snapshot me abhi cancelled shipment visible nahi hai.', 'No cancelled shipment is visible in the snapshot right now.')
                        )
                        : '')
            };
        }

        function renderShipmentDetail(item) {
            var destinationInstruction = item.skipInstructionDestination
                ? choose('Shipping instruction me hide rakha gaya hai', 'It is hidden in the shipping instruction')
                : choose('Shipping instruction me dikhaya jayega', 'It will be shown in the shipping instruction');
            var hubInstruction = item.skipInstructionHub
                ? choose('Departure hub instruction me hide rakha gaya hai', 'It is hidden in the departure hub instruction')
                : choose('Departure hub instruction me dikhaya jayega', 'It will be shown in the departure hub instruction');
            var preAlertVisibility = item.skipPrealert
                ? choose('Pre-alert me hide rakha gaya hai', 'It is hidden in the pre-alert')
                : choose('Pre-alert me dikhaya jayega', 'It will be shown in the pre-alert');
            var flagLine = item.flags && item.flags.length
                ? choose('Flags me ' + item.flags.join(', ') + ' laga hua hai.', 'The flags on this shipment are ' + item.flags.join(', ') + '.')
                : choose('Is shipment par abhi koi flag nahi laga hai.', 'No flag is set on this shipment right now.');
            var linkedStocksText = item.linkedStocks && item.linkedStocks.length
                ? item.linkedStocks.join(', ')
                : choose('abhi koi linked stock number nahi hai', 'no linked stock number is available yet');
            var documentNames = item.documents && item.documents.length
                ? item.documents.map(function (document) {
                    return document.name;
                }).join(', ')
                : '';

            return {
                kind: 'shipment-detail',
                status: choose('Complete shipment summary ready', 'Complete shipment summary ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Shipment ' + item.number + ' ka complete summary', 'Complete summary for shipment ' + item.number)) + '</strong>' +
                    textParagraphs([
                        buildShipmentNarrative(item),
                        flagLine
                    ]) +
                    section(choose('Route aur dates', 'Route and dates'), textParagraphs([
                        choose(
                            'Departure ' + displayValue(item.departure) + ' se hai aur route ' + displayValue(item.departurePort) + ' se ' + displayValue(item.consigneePort) + ' tak set hai.',
                            'The departure is ' + displayValue(item.departure) + ' and the route is set from ' + displayValue(item.departurePort) + ' to ' + displayValue(item.consigneePort) + '.'
                        ),
                        choose(
                            'Service ' + displayValue(item.service) + ' hai aur additional service ' + displayValue(item.additionalService) + ' hai.',
                            'The service is ' + displayValue(item.service) + ' and the additional service is ' + displayValue(item.additionalService) + '.'
                        ),
                        choose(
                            'Preferred shipment date ' + displayValue(item.preferredShipmentDate) + ' hai, deadline arrival ' + displayValue(item.deadlineArrival) + ' hai, vessel ETA ' + displayValue(item.vesselEta) + ' hai, vessel ETD ' + displayValue(item.vesselEtd) + ' hai aur pre-alert reminder ' + displayValue(item.preAlertReminder) + ' par set hai.',
                            'The preferred shipment date is ' + displayValue(item.preferredShipmentDate) + ', the deadline arrival date is ' + displayValue(item.deadlineArrival) + ', the vessel ETA is ' + displayValue(item.vesselEta) + ', the vessel ETD is ' + displayValue(item.vesselEtd) + ', and the pre-alert reminder is set for ' + displayValue(item.preAlertReminder) + '.'
                        ),
                        hasValue(item.customerReference)
                            ? choose('Customer reference ' + item.customerReference + ' hai.', 'The customer reference is ' + item.customerReference + '.')
                            : choose('Customer reference abhi add nahi hai.', 'The customer reference is not added yet.'),
                        item.notApplicableForConsolidation
                            ? choose('Is shipment ko consolidation se alag handle kiya jayega.', 'This shipment will be handled outside consolidation.')
                            : choose('Is shipment ke liye normal consolidation allowed hai.', 'Normal consolidation is allowed for this shipment.')
                    ])) +
                    section(choose('Receiver aur destination', 'Receiver and destination'), textParagraphs([
                        choose(
                            'Consignee ' + displayValue(item.consignee) + ' hai. Contact person ' + displayValue(item.contactPerson) + ' hai, email ' + displayValue(item.consigneeEmail) + ' hai aur location ' + displayValue(item.location) + ' hai.',
                            'The consignee is ' + displayValue(item.consignee) + '. The contact person is ' + displayValue(item.contactPerson) + ', the email is ' + displayValue(item.consigneeEmail) + ', and the location is ' + displayValue(item.location) + '.'
                        ),
                        choose(
                            'Receiver address ' + buildShipmentAddress(item) + ' hai.',
                            'The receiver address is ' + buildShipmentAddress(item) + '.'
                        )
                    ])) +
                    section(choose('Notes aur handling', 'Notes and handling'), textParagraphs([
                        hasValue(item.specialConsiderations)
                            ? choose('Destination note: ' + item.specialConsiderations, 'Destination note: ' + item.specialConsiderations)
                            : choose('Destination ke liye koi special note add nahi hai.', 'No special destination note is added.'),
                        hasValue(item.commentsDepartureHub)
                            ? choose('Departure hub note: ' + item.commentsDepartureHub, 'Departure hub note: ' + item.commentsDepartureHub)
                            : choose('Departure hub ke liye koi note add nahi hai.', 'No departure hub note is added.'),
                        hasValue(item.commentsConsignee)
                            ? choose('Consignee note: ' + item.commentsConsignee, 'Consignee note: ' + item.commentsConsignee)
                            : choose('Consignee ke liye koi note add nahi hai.', 'No consignee note is added.'),
                        destinationInstruction + '. ' + hubInstruction + '. ' + preAlertVisibility + '.',
                        choose(
                            'Project logistics ' + (item.projectLogistics ? 'on hai' : 'on nahi hai') + ' aur port agency ' + (item.portAgency ? 'on hai' : 'on nahi hai') + '.',
                            'Project logistics is ' + (item.projectLogistics ? 'on' : 'off') + ' and port agency is ' + (item.portAgency ? 'on' : 'off') + '.'
                        )
                    ])) +
                    section(choose('Linked stock summary', 'Linked stock summary'), textParagraphs([
                        choose(
                            'Is shipment ke saath ' + describeCount(item.stockCount || 0, 'linked stock', 'linked stocks') + ' ' + countVerb(item.stockCount || 0, 'dikh raha hai', 'dikh rahe hain') + ': ' + linkedStocksText + '.',
                            'This shipment has ' + describeCount(item.stockCount || 0, 'linked stock', 'linked stocks') + ': ' + linkedStocksText + '.'
                        ),
                        choose(
                            'Total ' + describeCount(item.totalPackages || 0, 'package', 'packages') + ', '
                                + (hasValue(item.totalWeight) ? item.totalWeight + ' kg weight' : 'weight abhi add nahi hai') + ', '
                                + (hasValue(item.totalCbm) ? item.totalCbm + ' CBM' : 'CBM abhi add nahi hai') + ' aur '
                                + (hasValue(item.totalValueDisplay || item.totalValue) ? 'value ' + (item.totalValueDisplay || item.totalValue) : 'value abhi add nahi hai') + ' dikh raha hai.',
                            'The totals show ' + describeCount(item.totalPackages || 0, 'package', 'packages') + ', '
                                + (hasValue(item.totalWeight) ? item.totalWeight + ' kg weight' : 'weight is not added yet') + ', '
                                + (hasValue(item.totalCbm) ? item.totalCbm + ' CBM' : 'CBM is not added yet') + ', and '
                                + (hasValue(item.totalValueDisplay || item.totalValue) ? 'value ' + (item.totalValueDisplay || item.totalValue) : 'value is not added yet') + '.'
                        ),
                        choose(
                            'Open issues ' + formatNumber(item.irregularities || 0) + ' hain. Stock repacked ' + formatRepackedNarrative(item.stockRepackedItems, item.stockRepackedWeight) + ' aur service repacked ' + formatRepackedNarrative(item.serviceRepackedItems, item.serviceRepackedWeight) + '.',
                            'There are ' + formatNumber(item.irregularities || 0) + ' open issues. Stock repacked: ' + formatRepackedNarrative(item.stockRepackedItems, item.stockRepackedWeight) + '. Service repacked: ' + formatRepackedNarrative(item.serviceRepackedItems, item.serviceRepackedWeight) + '.'
                        )
                    ])) +
                    section(choose('Linked stock details', 'Linked stock details'), renderShipmentStockItems(item.stockItems || [])) +
                    section(choose('Documents', 'Documents'), textParagraphs([
                        item.documents && item.documents.length
                            ? choose(
                                'Total ' + describeCount(item.documentCount || 0, 'document', 'documents') + ' attached ' + countVerb(item.documentCount || 0, 'hai', 'hain') + ': ' + documentNames + '.',
                                'A total of ' + describeCount(item.documentCount || 0, 'document', 'documents') + ' are attached: ' + documentNames + '.'
                            )
                            : choose('Is shipment ke saath abhi koi document attached nahi hai.', 'No document is attached to this shipment right now.')
                    ]) + renderShipmentDocuments(item.documents || []))
            };
        }

        function renderShipmentTransportDetail(item) {
            var heading = transportHeadingText(item);
            var serviceText = joinMeaningful([item.service, item.additionalService], ' / ');
            var legCount = Number(item.transportLegCount || ((item.transportLegs || []).length));
            var introLines = [
                choose(
                    'Yeh ' + (serviceText || displayValue(item.service, 'shipment service')) + ' shipment hai. Route ' + displayValue(item.departurePort) + ' se ' + displayValue(item.consigneePort) + ' tak dikh raha hai.',
                    'This is a ' + (serviceText || displayValue(item.service, 'shipment service', 'shipment service')) + ' shipment. The route runs from ' + displayValue(item.departurePort) + ' to ' + displayValue(item.consigneePort) + '.'
                ),
                choose(
                    'Current status ' + displayValue(item.status) + ' hai. Customer ' + displayValue(item.customer) + ' hai aur receiver ' + displayValue(item.consignee) + ' hai.',
                    'The current status is ' + displayValue(item.status) + '. The customer is ' + displayValue(item.customer) + ' and the receiver is ' + displayValue(item.consignee) + '.'
                )
            ];

            if (normalize(heading) !== 'flight details') {
                introLines.push(choose(
                    'Is shipment me flight ke bajay ' + heading.toLowerCase() + ' apply hote hain.',
                    'This shipment uses ' + heading.toLowerCase() + ' instead of flight details.'
                ));
            }

            if (legCount > 0) {
                introLines.push(choose(
                    'Is shipment me ' + describeCount(legCount, item.transportUnitSingular || 'transport leg', item.transportUnitPlural || 'transport legs') + ' saved ' + countVerb(legCount, 'hai', 'hain') + '.',
                    'There ' + (legCount === 1 ? 'is ' : 'are ') + describeCount(legCount, item.transportUnitSingular || 'transport leg', item.transportUnitPlural || 'transport legs') + ' saved for this shipment.'
                ));
            } else {
                introLines.push(choose(
                    'Abhi is shipment me koi saved ' + transportDetailNoun(item) + ' nahi hai.',
                    'There is no saved ' + transportDetailNoun(item) + ' for this shipment right now.'
                ));
            }

            introLines.push(choose(
                'Last update ' + displayValue(item.updatedAt) + ' par hua tha.',
                'The last update was on ' + displayValue(item.updatedAt) + '.'
            ));

            return {
                kind: 'shipment-transport-detail',
                status: choose(heading + ' ready', heading + ' ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Shipment ' + item.number + ' ke ' + heading.toLowerCase(), heading + ' for shipment ' + item.number)) + '</strong>' +
                    textParagraphs(introLines) +
                    section(choose('Saved details', 'Saved details'), renderTransportLegList(item))
            };
        }

        function renderRequestedFieldResponse(kind, status, title, blocks) {
            return {
                kind: kind,
                status: status,
                html: '' +
                    '<strong>' + escapeHtml(title) + '</strong>' +
                    blocks.join('')
            };
        }

        function renderUnknownFieldResponse(type, item) {
            var title = choose(
                (type === 'shipment' ? 'Shipment ' : 'Stock ') + item.number + ' ka answer',
                'Answer for ' + type + ' ' + item.number
            );
            var message = type === 'shipment'
                ? choose(
                    'Main is shipment ka exact field samajh nahi paya. Aap status, customer, route, kitna stocks add hai, documents, weight ya flight details puchh sakte hain. Agar poora record chahiye to complete summary likhiye.',
                    'I could not identify the exact shipment field. You can ask for status, customer, route, how many stocks are linked, documents, weight, or flight details. If you want the full record, ask for the complete summary.'
                )
                : choose(
                    'Main is stock ka exact field samajh nahi paya. Aap status, supplier, hub/agent, customs value, packages ya linked shipments puchh sakte hain. Agar poora record chahiye to complete summary likhiye.',
                    'I could not identify the exact stock field. You can ask for status, supplier, hub/agent, customs value, packages, or linked shipments. If you want the full record, ask for the complete summary.'
                );

            return renderRequestedFieldResponse(
                type + '-field-clarify',
                choose('Field clarification ready', 'Field clarification ready'),
                title,
                ['<p>' + escapeHtml(message) + '</p>']
            );
        }

        function looksLikeSensitiveCredentialRequest(text) {
            return containsIntentPhrases(text, ['password', 'pass word', 'passcode', 'credential', 'credentials', 'secret code']);
        }

        function renderSensitiveLookupResponse(type, reason, item) {
            var quickFields = (item && Array.isArray(item.quickFields) ? item.quickFields : []).filter(function (label) {
                return normalize(label).indexOf('password') === -1;
            }).slice(0, 6);
            var fieldList = quickFields.length
                ? quickFields.join(', ')
                : choose(
                    'role, status, email, address ya last update',
                    'role, status, email, address, or the last update'
                );

            return renderRequestedFieldResponse(
                'sensitive-lookup-blocked',
                choose('Sensitive detail blocked', 'Sensitive detail blocked'),
                choose('Sensitive information', 'Sensitive information'),
                ['<p>' + escapeHtml(choose(
                    'Main password ya credential jaisi sensitive login details share nahi kar sakta. Aap ' + fieldList + ' puchh sakte hain.',
                    'I can\'t share passwords or other sensitive login details. You can ask for ' + fieldList + '.'
                )) + '</p>']
            );
        }

        function administrationEntityLabel(type, item) {
            return $.trim(String((item && item.entityLabel) || type || 'record'));
        }

        function administrationFields(item) {
            if (item && Array.isArray(item.fields) && item.fields.length) {
                return item.fields;
            }

            return (item && Array.isArray(item.sections) ? item.sections : []).reduce(function (carry, sectionData) {
                return carry.concat(Array.isArray(sectionData.fields) ? sectionData.fields : []);
            }, []);
        }

        function administrationIdentityValues(item) {
            return []
                .concat(item && Array.isArray(item.identityValues) ? item.identityValues : [])
                .concat(item && item.name ? [item.name] : [])
                .concat(item && item.identifier ? [item.identifier] : [])
                .filter(Boolean);
        }

        function administrationQueryMatchesIdentity(item, query) {
            var compactQuery = normalizeCompact(query);

            return administrationIdentityValues(item).some(function (value) {
                var identity = normalizeCompact(value);
                return identity && (identity === compactQuery || compactQuery.indexOf(identity) !== -1);
            });
        }

        function administrationIntentText(item, query) {
            var intentText = ' ' + normalize(query) + ' ';

            administrationIdentityValues(item).forEach(function (value) {
                var normalizedValue = normalize(value);

                if (! normalizedValue) {
                    return;
                }

                var pattern = new RegExp('\\b' + escapeRegex(normalizedValue).replace(/\s+/g, '\\s+') + '\\b', 'g');
                intentText = intentText.replace(pattern, ' ');
            });

            return intentText.replace(/\s+/g, ' ').trim();
        }

        function administrationIntentSuggestsSpecificField(text) {
            var cleaned = ' ' + normalize(text || '') + ' ';

            [
                'office', 'offices', 'hub', 'hubs', 'agent', 'agents', 'supplier', 'suppliers',
                'customer', 'customers', 'contact', 'contacts', 'vessel', 'vessels', 'user', 'users',
                'portal user', 'portal users', 'login user', 'login users',
                'record', 'records', 'detail', 'details', 'summary', 'summery', 'overview',
                'info', 'information', 'complete', 'full',
                'what', 'who', 'which', 'when', 'where', 'how many', 'count', 'number of',
                'kitna', 'kitne', 'kitni', 'kisne', 'kis', 'kon', 'kaun',
                'show', 'tell', 'give', 'batao', 'dikhao', 'please',
                'ka', 'ke', 'ki', 'ko', 'me', 'mein', 'hai', 'hain', 'tha', 'the', 'kya'
            ].forEach(function (phrase) {
                var pattern = new RegExp('\\b' + escapeRegex(normalize(phrase)).replace(/\s+/g, '\\s+') + '\\b', 'g');
                cleaned = cleaned.replace(pattern, ' ');
            });

            return cleaned.replace(/\s+/g, ' ').trim() !== '';
        }

        function administrationFieldValue(field) {
            return hasValue(field && field.value)
                ? $.trim(String(field.value))
                : choose('abhi add nahi hai', 'not added yet');
        }

        function administrationFieldAliases(field) {
            var aliases = [];

            if (field && field.label) {
                aliases.push(field.label);
            }

            if (field && field.key) {
                aliases.push(String(field.key).replace(/[_-]+/g, ' '));
            }

            aliases = aliases.concat(field && Array.isArray(field.aliases) ? field.aliases : []);

            return aliases.map(function (alias) {
                return normalize(alias);
            }).filter(Boolean).filter(function (alias, index, items) {
                return items.indexOf(alias) === index;
            });
        }

        function administrationFieldMatchScore(text, field) {
            var bestScore = 0;

            administrationFieldAliases(field).forEach(function (alias) {
                if (! containsIntentPhrase(text, alias)) {
                    return;
                }

                var normalizedAlias = normalize(alias);
                var wordCount = normalizedAlias ? normalizedAlias.split(/\s+/).length : 0;
                var score = (wordCount * 40) + normalizedAlias.length;

                if (score > bestScore) {
                    bestScore = score;
                }
            });

            return bestScore;
        }

        function administrationFieldMatches(item, text) {
            var matches = administrationFields(item).map(function (field) {
                return {
                    field: field,
                    score: administrationFieldMatchScore(text, field)
                };
            }).filter(function (entry) {
                return entry.score > 0;
            }).sort(function (left, right) {
                return right.score - left.score;
            });

            if (! matches.length) {
                return [];
            }

            var bestScore = matches[0].score;

            return matches.filter(function (entry) {
                return entry.score >= Math.max(40, bestScore - 18);
            });
        }

        function administrationFieldBlocksFromMatches(matches) {
            return (matches || []).map(function (entry) {
                return '<p><strong>' + escapeHtml(entry.field.label) + ':</strong> ' + escapeHtml(administrationFieldValue(entry.field)) + '</p>';
            });
        }

        function buildAdministrationFieldBlocks(item, text) {
            var directMatches = administrationFieldMatches(item, text);

            if (! directMatches.length) {
                return [];
            }

            var clauses = splitFieldIntentClauses(text);

            if (clauses.length > 1) {
                var clauseMatches = [];
                var seen = {};

                clauses.forEach(function (clause) {
                    administrationFieldMatches(item, clause).forEach(function (entry) {
                        var key = String((entry.field && (entry.field.key || entry.field.label)) || '');

                        if (! key || seen[key]) {
                            return;
                        }

                        seen[key] = true;
                        clauseMatches.push(entry);
                    });
                });

                if (clauseMatches.length > 1) {
                    return administrationFieldBlocksFromMatches(clauseMatches);
                }
            }

            return administrationFieldBlocksFromMatches(directMatches);
        }

        function changeLogEntries(item) {
            return item && Array.isArray(item.logs) ? item.logs : [];
        }

        function latestChangeLogEntry(item) {
            var logs = changeLogEntries(item);

            return logs.length ? logs[0] : null;
        }

        function renderChangeLogEntries(logs) {
            var rows = (logs || []).slice(0, 5).map(function (entry, index) {
                return {
                    label: choose('Change ' + (index + 1), 'Change ' + (index + 1)),
                    value: joinMeaningful([
                        entry.recordName,
                        entry.field,
                        entry.title,
                        entry.userName,
                        entry.date,
                        entry.description
                    ], ' · ') || '—'
                };
            });

            if (! rows.length) {
                return '<p>' + escapeHtml(choose(
                    'Abhi koi matching administration change log nahi mila.',
                    'No matching administration change logs were found.'
                )) + '</p>';
            }

            return metricList(rows);
        }

        function renderChangeLogNoMatchResponse(item) {
            var context = displayValue(
                item && (item.identifier || item.name),
                choose('is search', 'this search'),
                'this search'
            );

            return {
                kind: 'change-log-detail',
                status: choose('Change log check ready', 'Change log check ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose(
                        'Administration change logs ka answer',
                        'Answer for administration change logs'
                    )) + '</strong>' +
                    '<p>' + escapeHtml(choose(
                        context === 'is search'
                            ? 'Is search ke liye koi saved administration change log nahi mila.'
                            : context + ' ke liye koi saved administration change log nahi mila.',
                        context === 'this search'
                            ? 'No saved administration change log was found for this search.'
                            : 'No saved administration change log was found for ' + context + '.'
                    )) + '</p>'
            };
        }

        function renderChangeLogDetail(item) {
            var logs = changeLogEntries(item);
            var totalMatches = Number(item && item.matchedCount || 0);
            var latest = latestChangeLogEntry(item);

            if (! totalMatches) {
                return renderChangeLogNoMatchResponse(item);
            }

            var subject = displayValue(
                item && item.name,
                choose('selected records', 'selected records'),
                'selected records'
            );
            var introLines = [
                choose(
                    'Mujhe ' + describeCount(totalMatches, 'matching log', 'matching logs') + ' mile' + (item && item.windowLabel ? ' ' + item.windowLabel + ' ke andar.' : '.'),
                    'I found ' + describeCount(totalMatches, 'matching log', 'matching logs') + (item && item.windowLabel ? ' within ' + item.windowLabel + '.' : '.')
                ),
                latest
                    ? choose(
                        'Latest matching change ' + displayValue(latest.recordName) + ' me '
                            + displayValue(latest.title, 'change entry', 'change entry')
                            + ' tha, jo ' + displayValue(latest.userName) + ' ne ' + displayValue(latest.date) + ' par kiya.',
                        'The latest matching change was ' + displayValue(latest.title, 'a saved change entry', 'a saved change entry')
                            + ' on ' + displayValue(latest.recordName) + ', made by ' + displayValue(latest.userName) + ' on ' + displayValue(latest.date) + '.'
                    )
                    : '',
                choose(
                    'Main summary ' + subject + ' ke context me dikha raha hoon.',
                    'I am showing the summary in the context of ' + subject + '.'
                )
            ];
            var summarySections = (Array.isArray(item && item.sections) ? item.sections : []).filter(function (sectionData) {
                return normalize(sectionData && sectionData.title) !== 'recent changes';
            });

            return {
                kind: 'change-log-detail',
                status: choose('Administration change log summary ready', 'Administration change log summary ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose(
                        subject + ' ke administration change logs',
                        'Administration change logs for ' + subject
                    )) + '</strong>' +
                    textParagraphs(introLines) +
                    summarySections.map(renderAdministrationSection).join('') +
                    section(choose('Recent changes', 'Recent changes'), renderChangeLogEntries(logs))
            };
        }

        function renderChangeLogFieldResponse(item, text) {
            var logs = changeLogEntries(item);
            var latest = latestChangeLogEntry(item);
            var totalMatches = Number(item && item.matchedCount || 0);
            var blocks = [];
            var seen = {};

            if (! totalMatches) {
                return renderChangeLogNoMatchResponse(item);
            }

            function push(key, html) {
                if (! html || seen[key]) {
                    return;
                }

                seen[key] = true;
                blocks.push(html);
            }

            if (queryHasCountIntent(text) || matchesIntent(text, ['total matching logs', 'total change logs', 'change log count', 'how many change logs', 'kitne change logs'])) {
                push('count', '<p>' + escapeHtml(choose(
                    'Is query ke liye ' + describeCount(totalMatches, 'matching change log', 'matching change logs') + ' mile.',
                    'I found ' + describeCount(totalMatches, 'matching change log', 'matching change logs') + ' for this query.'
                )) + '</p>');
            }

            if (matchesIntent(text, [
                'last changes',
                'latest changes',
                'recent changes',
                'what changes',
                'what were the changes',
                'show changes',
                'changes kya kiye',
                'change kya kiye',
                'kya changes kiye',
                'kya change kiye'
            ], ['change log count', 'how many change logs', 'kitne change logs'])) {
                push('changes-list', '<p>' + escapeHtml(choose(
                    'Latest matching changes ye the.',
                    'These were the latest matching changes.'
                )) + '</p>' + section(choose('Matching changes', 'Matching changes'), renderChangeLogEntries(logs)));
            }

            if (latest && matchesIntent(text, [
                'changed by',
                'who changed',
                'who modified',
                'who updated',
                'last modified by',
                'last edited by',
                'kisne change kiya',
                'kisne modify kiya',
                'kisne update kiya'
            ])) {
                push('changed-by', '<p>' + escapeHtml(hasValue(latest.userName)
                    ? choose(
                        'Latest matching change ' + displayValue(latest.userName) + ' ne kiya tha.'
                            + (hasValue(latest.field) ? ' Changed field ' + displayValue(latest.field) + ' thi.' : '')
                            + (hasValue(latest.recordName) ? ' Record ' + displayValue(latest.recordName) + ' tha.' : ''),
                        displayValue(latest.userName) + ' made the latest matching change.'
                            + (hasValue(latest.field) ? ' The changed field was ' + displayValue(latest.field) + '.' : '')
                            + (hasValue(latest.recordName) ? ' The record was ' + displayValue(latest.recordName) + '.' : '')
                    )
                    : choose(
                        'Latest matching change ka user saved record me available nahi hai.',
                        'The user for the latest matching change is not available in the saved record.'
                    )) + '</p>');
            }

            if (latest && matchesIntent(text, [
                'field',
                'which field changed',
                'last modification field',
                'last changed field',
                'last modified field',
                'kaunsi field',
                'kis field'
            ], ['changed by', 'who changed', 'who modified'])) {
                push('field', '<p>' + escapeHtml(hasValue(latest.field)
                    ? choose(
                        'Latest matching changed field ' + displayValue(latest.field) + ' thi.',
                        'The latest matching changed field was ' + displayValue(latest.field) + '.'
                    )
                    : choose(
                        'Latest matching changed field saved record me available nahi hai.',
                        'The latest matching changed field is not available in the saved record.'
                    )) + '</p>');
            }

            if (latest && matchesIntent(text, [
                'what changed',
                'what was changed',
                'last change',
                'latest change',
                'last modification',
                'latest modification',
                'change description',
                'change detail',
                'change details'
            ], ['last modification field', 'last changed field', 'last modified field', 'changed by', 'who changed', 'who modified'])) {
                push('latest-change', '<p>' + escapeHtml(choose(
                    'Latest matching change ' + displayValue(latest.title, 'change entry', 'change entry')
                        + (hasValue(latest.description) ? ' tha. Details: ' + latest.description + '.' : ' tha.'),
                    'The latest matching change was ' + displayValue(latest.title, 'a saved change entry', 'a saved change entry')
                        + (hasValue(latest.description) ? '. Details: ' + latest.description + '.' : '.')
                )) + '</p>');
            }

            if (latest && matchesIntent(text, [
                'when changed',
                'change date',
                'last change date',
                'latest change date',
                'kab change hua',
                'change kab hua'
            ])) {
                push('date', '<p>' + escapeHtml(choose(
                    'Latest matching change ' + displayValue(latest.date) + ' par hua tha.',
                    'The latest matching change happened on ' + displayValue(latest.date) + '.'
                )) + '</p>');
            }

            if (latest && matchesIntent(text, ['record name', 'which record', 'record'])) {
                push('record', '<p>' + escapeHtml(choose(
                    'Latest matching record ' + displayValue(latest.recordName) + ' hai.',
                    'The latest matching record is ' + displayValue(latest.recordName) + '.'
                )) + '</p>');
            }

            if (latest && matchesIntent(text, ['entity', 'which entity', 'module'])) {
                push('entity', '<p>' + escapeHtml(choose(
                    'Latest matching entity ' + displayValue(latest.entityLabel) + ' hai.',
                    'The latest matching entity is ' + displayValue(latest.entityLabel) + '.'
                )) + '</p>');
            }

            if (! blocks.length) {
                return null;
            }

            return renderRequestedFieldResponse(
                'change-log-field-detail',
                choose('Requested change log detail ready', 'Requested change log detail ready'),
                choose('Administration change logs ka answer', 'Answer for administration change logs'),
                blocks
            );
        }

        function renderAdministrationSection(sectionData) {
            var fields = Array.isArray(sectionData && sectionData.fields) ? sectionData.fields : [];

            if (! fields.length) {
                return '';
            }

            return section(sectionData.title || choose('Details', 'Details'), metricList(fields.map(function (field) {
                return {
                    label: field.label,
                    value: administrationFieldValue(field)
                };
            })));
        }

        function renderAdministrationDetail(type, item) {
            var entityLabel = administrationEntityLabel(type, item);
            var entityName = displayValue(item && item.name, choose('record', 'record'));
            var introLines = [
                choose(
                    'Yeh saved ' + entityLabel.toLowerCase() + ' record ' + entityName + ' ka hai.',
                    'This is the saved ' + entityLabel.toLowerCase() + ' record for ' + entityName + '.'
                ),
                item && item.identifier && item.identifierLabel
                    ? choose(
                        item.identifierLabel + ' ' + item.identifier + ' hai.',
                        'The ' + String(item.identifierLabel).toLowerCase() + ' is ' + item.identifier + '.'
                    )
                    : '',
                item && item.status
                    ? choose('Current status ' + item.status + ' hai.', 'The current status is ' + item.status + '.')
                    : '',
                item && item.updatedAt
                    ? choose('Last update ' + item.updatedAt + ' par hua tha.', 'The last update was on ' + item.updatedAt + '.')
                    : ''
            ];

            return {
                kind: type + '-detail',
                status: choose('Complete ' + entityLabel.toLowerCase() + ' summary ready', 'Complete ' + entityLabel.toLowerCase() + ' summary ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose(entityLabel + ' ' + entityName + ' ka complete summary', 'Complete summary for ' + entityLabel.toLowerCase() + ' ' + entityName)) + '</strong>' +
                    textParagraphs(introLines) +
                    (Array.isArray(item && item.sections) ? item.sections : []).map(renderAdministrationSection).join('')
            };
        }

        function renderAdministrationFieldResponse(type, item, text) {
            var entityLabel = administrationEntityLabel(type, item);
            var blocks = buildAdministrationFieldBlocks(item, text);

            if (! blocks.length) {
                return null;
            }

            return renderRequestedFieldResponse(
                type + '-field-detail',
                choose('Requested ' + entityLabel.toLowerCase() + ' detail ready', 'Requested ' + entityLabel.toLowerCase() + ' detail ready'),
                choose(entityLabel + ' ' + item.name + ' ka answer', 'Answer for ' + entityLabel.toLowerCase() + ' ' + item.name),
                blocks
            );
        }

        function renderAdministrationUnknownFieldResponse(type, item) {
            var entityLabel = administrationEntityLabel(type, item);
            var quickFields = (item && Array.isArray(item.quickFields) ? item.quickFields : []).slice(0, 6);
            var fieldList = quickFields.length
                ? quickFields.join(', ')
                : choose('email, phone number, address ya complete summary', 'email, phone number, address, or the complete summary');

            return renderRequestedFieldResponse(
                type + '-field-clarify',
                choose('Field clarification ready', 'Field clarification ready'),
                choose(entityLabel + ' ' + item.name + ' ka answer', 'Answer for ' + entityLabel.toLowerCase() + ' ' + item.name),
                ['<p>' + escapeHtml(choose(
                    'Main is ' + entityLabel.toLowerCase() + ' ka exact field samajh nahi paya. Aap ' + fieldList + ' ya complete summary puchh sakte hain.',
                    'I could not identify the exact ' + entityLabel.toLowerCase() + ' field. You can ask for ' + fieldList + ' or the complete summary.'
                )) + '</p>']
            );
        }

        function responseForAdministrationQuery(type, item, query) {
            var normalizedQuery = $.trim(String(query || ''));
            var isExactRecordLookup = administrationQueryMatchesIdentity(item, normalizedQuery);
            var fieldIntentText = administrationIntentText(item, normalizedQuery);

            if (! isFullRecordRequest(normalizedQuery)) {
                var fieldResponse = fieldIntentText
                    ? renderAdministrationFieldResponse(type, item, fieldIntentText)
                    : null;

                if (fieldResponse) {
                    return fieldResponse;
                }

                if (looksLikeSensitiveCredentialRequest(fieldIntentText || normalizedQuery)) {
                    return renderSensitiveLookupResponse(type, 'credentials', item);
                }

                if (administrationIntentSuggestsSpecificField(fieldIntentText)) {
                    return renderAdministrationUnknownFieldResponse(type, item);
                }

                if (! isGenericRecordSummaryRequest(normalizedQuery) && ! isExactRecordLookup) {
                    return renderAdministrationUnknownFieldResponse(type, item);
                }
            }

            return renderAdministrationDetail(type, item);
        }

        function responseForChangeLogQuery(item, query) {
            var normalizedQuery = $.trim(String(query || ''));
            var fieldIntentText = administrationIntentText(item, normalizedQuery) || normalizedQuery;

            if (! isFullRecordRequest(normalizedQuery)) {
                var fieldResponse = renderChangeLogFieldResponse(item, fieldIntentText);

                if (fieldResponse) {
                    return fieldResponse;
                }
            }

            return renderChangeLogDetail(item);
        }

        function buildShipmentFieldBlocks(item, text) {
            var blocks = [];
            var seen = {};
            var wantsCount = queryHasCountIntent(text);
            var wantsList = queryHasListIntent(text);
            var consigneeNamePhrases = ['consignee', 'consignee name', 'receiver', 'receiver name', 'consigenee', 'consigenee name'];
            var consigneeAddressPhrases = ['consignee address', 'receiver address', 'consigenee address', 'address'];
            var consigneePortPhrases = ['consignee port', 'consigenee port', 'destination port', 'port code', 'to port'];
            var consigneeNotePhrases = ['consignee note', 'receiver note', 'comments to consignee', 'consigenee note', 'comments to consigenee'];
            var consigneeEmailPhrases = ['email', 'mail id', 'email address'];
            var consigneeContactPhrases = ['contact person', 'contact name', 'attn', 'attention person'];
            var linkedStocks = item.linkedStocks && item.linkedStocks.length
                ? item.linkedStocks.join(', ')
                : 'abhi koi linked stock number nahi hai';
            var documentNames = item.documents && item.documents.length
                ? item.documents.map(function (document) {
                    return document.name;
                }).join(', ')
                : 'abhi koi document attached nahi hai';

            function push(key, html) {
                if (! html || seen[key]) {
                    return;
                }

                seen[key] = true;
                blocks.push(html);
            }

            if (matchesIntent(text, ['status'], ['acceptance status'])) {
                push('status', '<p>' + escapeHtml(choose('Is shipment ka status ' + displayValue(item.status) + ' hai.', 'The status of this shipment is ' + displayValue(item.status) + '.')) + '</p>');
            }

            if (matchesIntent(text, [
                'creation date',
                'created date',
                'create date',
                'when was shipment',
                'when was this shipment created',
                'when created',
                'kab create hua',
                'kab banaya gaya'
            ])) {
                push('creation-date', '<p>' + escapeHtml(choose('Yeh shipment ' + displayValue(item.creationDate) + ' ko create hua tha.', 'This shipment was created on ' + displayValue(item.creationDate) + '.')) + '</p>');
            }

            if (matchesIntent(text, [
                'created by',
                'creator',
                'who created shipment',
                'who created this shipment',
                'who added shipment',
                'who added this shipment',
                'kisne create kiya',
                'kisne banaya'
            ])) {
                push('created-by', '<p>' + escapeHtml(choose('Yeh shipment ' + displayValue(item.createdBy) + ' ne create kiya tha.', 'This shipment was created by ' + displayValue(item.createdBy) + '.')) + '</p>');
            }

            if (matchesIntent(text, [
                'last modification field',
                'last modified field',
                'last updated field',
                'last change field',
                'what field changed',
                'which field changed',
                'what changed last',
                'last modification field kya tha',
                'last modified field kya tha',
                'kis field me last modification hua',
                'kis field me last update hua',
                'kon sa field update hua',
                'kaun sa field update hua',
                'kaunsa field update hua'
            ])) {
                push('last-modified-field', '<p>' + escapeHtml(
                    hasValue(item.lastModifiedField)
                        ? choose(
                            'Is shipment me sabse last modified field ' + displayValue(item.lastModifiedField) + ' tha.',
                            'The most recently modified field on this shipment was ' + displayValue(item.lastModifiedField) + '.'
                        )
                        : hasValue(item.lastModificationLabel)
                            ? choose(
                                'Is shipment ki latest saved change entry ' + displayValue(item.lastModificationLabel) + ' thi.',
                                'The latest saved change entry for this shipment was ' + displayValue(item.lastModificationLabel) + '.'
                            )
                            : choose(
                                'Is shipment ka last modified field saved record me available nahi hai.',
                                'The most recently modified field for this shipment is not available in the saved record.'
                            )
                ) + '</p>');
            }

            if (matchesIntent(text, [
                'updated by',
                'last updated by',
                'modified by',
                'last modified by',
                'who modified',
                'who updated',
                'who made the last change',
                'who made the last modification',
                'who made the latest change',
                'who made the last update',
                'who last modified',
                'kisne update kiya',
                'kisne update kiya tha',
                'kisne modify kiya',
                'kisne modify kiya tha',
                'last change kisne kiya',
                'last change kisne kiya tha',
                'latest change kisne kiya',
                'latest change kisne kiya tha',
                'last modification kisne kiya',
                'last modification kisne kiya tha'
            ])) {
                push('updated-by', '<p>' + escapeHtml(hasValue(item.updatedBy)
                    ? choose(
                        'Is shipment ka last modification ' + displayValue(item.updatedBy) + ' ne kiya tha.',
                        'The last modification on this shipment was made by ' + displayValue(item.updatedBy) + '.'
                    )
                    : choose(
                        'Is shipment ka last modifier saved record me available nahi hai.',
                        'The last modifier for this shipment is not available in the saved record.'
                    )) + '</p>');
            }

            if (matchesIntent(text, [
                'last change',
                'latest change',
                'what happened',
                'what happened last',
                'what happened on the last change',
                'last change kya tha',
                'latest change kya tha',
                'last change me kya hua',
                'latest change me kya hua',
                'last update me kya hua',
                'kya hua',
                'kya hua tha'
            ], ['last modification field', 'last modified field', 'last updated field', 'last change field', 'what field changed', 'which field changed'])) {
                push('last-change-detail', '<p>' + escapeHtml(
                    hasValue(item.lastModificationLabel)
                        ? choose(
                            'Is shipment ki latest saved change entry ' + displayValue(item.lastModificationLabel)
                                + (hasValue(item.lastModificationDescription) ? ' thi. ' + item.lastModificationDescription + '.' : ' thi.'),
                            'The latest saved change entry for this shipment was ' + displayValue(item.lastModificationLabel)
                                + (hasValue(item.lastModificationDescription) ? '. ' + item.lastModificationDescription + '.' : '.')
                        )
                        : hasValue(item.lastModifiedField)
                            ? choose(
                                'Is shipment me latest change ' + displayValue(item.lastModifiedField) + ' field par hua tha.',
                                'The latest change on this shipment was on the ' + displayValue(item.lastModifiedField) + ' field.'
                            )
                            : choose(
                                'Is shipment ki latest change details saved record me available nahi hai.',
                                'The latest change details for this shipment are not available in the saved record.'
                            )
                ) + '</p>');
            }

            if (matchesIntent(text, ['account manager'])) {
                push('account-manager', '<p>' + escapeHtml(choose('Is shipment ka account manager ' + displayValue(item.accountManager) + ' hai.', 'The account manager for this shipment is ' + displayValue(item.accountManager) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['last update', 'updated at', 'update kab hua', 'kab update hua', 'last modified at', 'last modified on', 'last modification kab hua'])) {
                push('updated-at', '<p>' + escapeHtml(choose('Is shipment ka last update ' + displayValue(item.updatedAt) + ' par hua tha.', 'The last update for this shipment was on ' + displayValue(item.updatedAt) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['flag', 'flags'])) {
                push('flags', '<p>'
                    + (item.flags && item.flags.length
                        ? escapeHtml(choose('Is shipment par ye flags lage hain: ' + item.flags.join(', ') + '.', 'The flags on this shipment are: ' + item.flags.join(', ') + '.'))
                        : escapeHtml(choose('Is shipment par abhi koi flag nahi laga hai.', 'No flag is set on this shipment right now.')))
                    + '</p>');
            }

            if (matchesIntent(text, ['departure port', 'origin port', 'from port'])) {
                push('departure-port', '<p>' + escapeHtml(choose('Is shipment ka departure port ' + displayValue(item.departurePort) + ' hai.', 'The departure port for this shipment is ' + displayValue(item.departurePort) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['departure', 'origin'], ['departure port', 'origin port', 'from port', 'departure hub note', 'comments to departure hub'])) {
                push('departure', '<p>' + escapeHtml(choose('Is shipment ka departure ' + displayValue(item.departure) + ' hai.', 'The departure for this shipment is ' + displayValue(item.departure) + '.')) + '</p>');
            }

            if (matchesIntent(text, consigneePortPhrases)) {
                push('destination-port', '<p>' + escapeHtml(choose('Is shipment ka destination port ' + displayValue(item.consigneePort) + ' hai.', 'The destination port for this shipment is ' + displayValue(item.consigneePort) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['route'])) {
                push('route', '<p>' + escapeHtml(choose('Is shipment ka route ' + displayValue(item.departurePort) + ' se ' + displayValue(item.consigneePort) + ' tak hai.', 'The route for this shipment runs from ' + displayValue(item.departurePort) + ' to ' + displayValue(item.consigneePort) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['additional service'])) {
                push('additional-service', '<p>' + escapeHtml(choose('Is shipment ki additional service ' + displayValue(item.additionalService) + ' hai.', 'The additional service for this shipment is ' + displayValue(item.additionalService) + '.')) + '</p>');
            } else if (matchesIntent(text, ['service'], ['service summary', 'additional service'])) {
                push('service', '<p>' + escapeHtml(choose(
                    'Is shipment ka service ' + displayValue(item.service) + ' hai'
                        + (hasValue(item.additionalService) ? ' aur additional service ' + item.additionalService + ' hai.' : '.'),
                    'The service for this shipment is ' + displayValue(item.service)
                        + (hasValue(item.additionalService) ? ' and the additional service is ' + item.additionalService + '.' : '.')
                )) + '</p>');
            }

            if (matchesIntent(text, ['preferred shipment date', 'shipment date', 'preferred date'])) {
                push('preferred-shipment-date', '<p>' + escapeHtml(choose('Is shipment ki preferred shipment date ' + displayValue(item.preferredShipmentDate) + ' hai.', 'The preferred shipment date is ' + displayValue(item.preferredShipmentDate) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['deadline arrival', 'arrival deadline', 'deadline'])) {
                push('deadline-arrival', '<p>' + escapeHtml(choose('Is shipment ki deadline arrival date ' + displayValue(item.deadlineArrival) + ' hai.', 'The deadline arrival date for this shipment is ' + displayValue(item.deadlineArrival) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['vessel eta', 'eta'])) {
                push('eta', '<p>' + escapeHtml(choose('Is shipment ka vessel ETA ' + displayValue(item.vesselEta) + ' hai.', 'The vessel ETA for this shipment is ' + displayValue(item.vesselEta) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['vessel etd', 'etd'])) {
                push('etd', '<p>' + escapeHtml(choose('Is shipment ka vessel ETD ' + displayValue(item.vesselEtd) + ' hai.', 'The vessel ETD for this shipment is ' + displayValue(item.vesselEtd) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['pre alert reminder', 'pre-alert reminder', 'pre alert date'], ['pre alert detail', 'pre alert details', 'pre alert summary'])) {
                push('pre-alert-reminder', '<p>' + escapeHtml(choose('Is shipment ka pre-alert reminder ' + displayValue(item.preAlertReminder) + ' par set hai.', 'The pre-alert reminder for this shipment is set for ' + displayValue(item.preAlertReminder) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['customer reference', 'reference'], ['awb', 'mawb', 'mbl', 'bill of lading'])) {
                push('customer-reference', '<p>' + escapeHtml(choose('Is shipment ka customer reference ' + displayValue(item.customerReference) + ' hai.', 'The customer reference for this shipment is ' + displayValue(item.customerReference) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['consolidation', 'consolidated'])) {
                push('consolidation', '<p>'
                    + escapeHtml(item.notApplicableForConsolidation
                        ? choose('Is shipment ko consolidation se alag handle kiya jayega.', 'This shipment will be handled outside consolidation.')
                        : choose('Is shipment ke liye normal consolidation allowed hai.', 'Normal consolidation is allowed for this shipment.'))
                    + '</p>');
            }

            if (matchesIntent(text, ['customer'], ['customer reference'])) {
                push('customer', '<p>' + escapeHtml(choose('Is shipment ka customer ' + displayValue(item.customer) + ' hai.', 'The customer for this shipment is ' + displayValue(item.customer) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['vessel'], ['vessel eta', 'vessel etd', 'vessel detail', 'vessel details'])) {
                push('vessel', '<p>' + escapeHtml(choose('Is shipment ka vessel ' + displayValue(item.vessel) + ' hai.', 'The vessel for this shipment is ' + displayValue(item.vessel) + '.')) + '</p>');
            }

            if (matchesIntent(text, consigneeAddressPhrases, consigneeEmailPhrases)) {
                push('consignee-address', '<p>' + escapeHtml(choose('Is shipment ka receiver address ' + buildShipmentAddress(item) + ' hai.', 'The receiver address for this shipment is ' + buildShipmentAddress(item) + '.')) + '</p>');
            }

            if (matchesIntent(text, consigneeContactPhrases)) {
                push('contact-person', '<p>' + escapeHtml(choose('Is shipment ka contact person ' + displayValue(item.contactPerson) + ' hai.', 'The contact person for this shipment is ' + displayValue(item.contactPerson) + '.')) + '</p>');
            }

            if (matchesIntent(text, consigneeEmailPhrases)) {
                push('consignee-email', '<p>' + escapeHtml(choose('Is shipment ka receiver email ' + displayValue(item.consigneeEmail) + ' hai.', 'The receiver email for this shipment is ' + displayValue(item.consigneeEmail) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['city'])) {
                push('consignee-city', '<p>' + escapeHtml(choose('Is shipment ki destination city ' + displayValue(item.consigneeCity) + ' hai.', 'The destination city for this shipment is ' + displayValue(item.consigneeCity) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['district'])) {
                push('consignee-district', '<p>' + escapeHtml(choose('Is shipment ka destination district ' + displayValue(item.consigneeDistrict) + ' hai.', 'The destination district for this shipment is ' + displayValue(item.consigneeDistrict) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['zip code', 'zipcode', 'pin code', 'pincode', 'postal code'])) {
                push('consignee-zip', '<p>' + escapeHtml(choose('Is shipment ka zip code ' + displayValue(item.consigneeZip) + ' hai.', 'The zip code for this shipment is ' + displayValue(item.consigneeZip) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['country'])) {
                push('consignee-country', '<p>' + escapeHtml(choose('Is shipment ki destination country ' + displayValue(item.consigneeCountry) + ' hai.', 'The destination country for this shipment is ' + displayValue(item.consigneeCountry) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['location'])) {
                push('location', '<p>' + escapeHtml(choose('Is shipment ki location ' + displayValue(item.location) + ' hai.', 'The location for this shipment is ' + displayValue(item.location) + '.')) + '</p>');
            }

            if (matchesIntent(
                text,
                consigneeNamePhrases,
                consigneeAddressPhrases
                    .concat(consigneePortPhrases)
                    .concat(consigneeContactPhrases)
                    .concat(consigneeEmailPhrases)
                    .concat(consigneeNotePhrases)
            )) {
                push('consignee', '<p>' + escapeHtml(choose('Is shipment ka receiver ' + displayValue(item.consignee) + ' hai.', 'The receiver for this shipment is ' + displayValue(item.consignee) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['special consideration', 'special note', 'destination note'])) {
                push('destination-note', '<p>'
                    + escapeHtml(hasValue(item.specialConsiderations)
                        ? choose('Destination note: ' + item.specialConsiderations, 'Destination note: ' + item.specialConsiderations)
                        : choose('Is shipment ke liye koi destination note add nahi hai.', 'No destination note is added for this shipment.'))
                    + '</p>');
            }

            if (matchesIntent(text, ['departure hub note', 'hub note', 'comments to departure hub'])) {
                push('hub-note', '<p>'
                    + escapeHtml(hasValue(item.commentsDepartureHub)
                        ? choose('Departure hub note: ' + item.commentsDepartureHub, 'Departure hub note: ' + item.commentsDepartureHub)
                        : choose('Is shipment ke liye departure hub note add nahi hai.', 'No departure hub note is added for this shipment.'))
                    + '</p>');
            }

            if (matchesIntent(text, consigneeNotePhrases)) {
                push('consignee-note', '<p>'
                    + escapeHtml(hasValue(item.commentsConsignee)
                        ? choose('Consignee note: ' + item.commentsConsignee, 'Consignee note: ' + item.commentsConsignee)
                        : choose('Is shipment ke liye consignee note add nahi hai.', 'No consignee note is added for this shipment.'))
                    + '</p>');
            }

            if (matchesIntent(text, ['instruction', 'instructions', 'visibility', 'hide', 'show'], ['flight detail', 'flight details'])) {
                push('instruction-visibility', '<p>'
                    + escapeHtml(choose(
                        'Destination instruction: ' + (item.skipInstructionDestination ? 'hide rakha gaya hai' : 'dikhaya jayega') + '. '
                            + 'Departure hub instruction: ' + (item.skipInstructionHub ? 'hide rakha gaya hai' : 'dikhaya jayega') + '. '
                            + 'Pre-alert visibility: ' + (item.skipPrealert ? 'hide rakha gaya hai' : 'dikhaya jayega') + '.',
                        'Destination instruction: ' + (item.skipInstructionDestination ? 'hidden' : 'visible') + '. '
                            + 'Departure hub instruction: ' + (item.skipInstructionHub ? 'hidden' : 'visible') + '. '
                            + 'Pre-alert visibility: ' + (item.skipPrealert ? 'hidden' : 'visible') + '.'
                    ))
                    + '</p>');
            }

            if (matchesIntent(text, ['project logistics'])) {
                push('project-logistics', '<p>' + escapeHtml(choose('Project logistics ' + (item.projectLogistics ? 'on hai.' : 'on nahi hai.'), 'Project logistics is ' + (item.projectLogistics ? 'on.' : 'off.'))) + '</p>');
            }

            if (matchesIntent(text, ['port agency'])) {
                push('port-agency', '<p>' + escapeHtml(choose('Port agency ' + (item.portAgency ? 'on hai.' : 'on nahi hai.'), 'Port agency is ' + (item.portAgency ? 'on.' : 'off.'))) + '</p>');
            }

            if (matchesIntent(text, ['stock details', 'stock detail', 'linked stock details', 'linked stock detail', 'stock item details', 'stock item detail'])) {
                push('stock-details', '<p>' + escapeHtml(choose('Is shipment ke linked stock details ye hain.', 'These are the linked stock details for this shipment.')) + '</p>' + renderShipmentStockItems(item.stockItems || []));
            }

            if (containsIntentPhrases(text, ['stock', 'stocks', 'linked stock', 'linked stocks', 'stock add', 'stocks add']) && wantsCount) {
                push('stock-count', '<p>' + escapeHtml(choose(
                    'Is shipment ke saath ' + describeCount(item.stockCount || 0, 'stock', 'stocks') + ' add ' + countVerb(item.stockCount || 0, 'hai', 'hain') + '.',
                    'This shipment has ' + describeCount(item.stockCount || 0, 'stock', 'stocks') + ' linked.'
                )) + '</p>');
            }

            if (containsIntentPhrases(text, ['stock number', 'stock numbers', 'linked stock', 'linked stocks', 'stock no', 'stock nos', 'which stock', 'kaun se stock', 'kaunse stock', 'stock list']) || (containsIntentPhrases(text, ['stock', 'stocks']) && wantsList)) {
                push('stock-list', '<p>' + escapeHtml(choose('Is shipment ke linked stock numbers ye hain: ' + linkedStocks + '.', 'The linked stock numbers for this shipment are: ' + linkedStocks + '.')) + '</p>');
            }

            if (matchesIntent(text, ['package', 'packages', 'pcs', 'pieces'])) {
                push('packages', '<p>' + escapeHtml(choose(
                    'Is shipment ke saath total ' + describeCount(item.totalPackages || 0, 'package', 'packages') + ' dikh rahe hain.',
                    'The total package count for this shipment is ' + describeCount(item.totalPackages || 0, 'package', 'packages') + '.'
                )) + '</p>');
            }

            if (matchesIntent(text, ['weight'])) {
                push('weight', '<p>' + escapeHtml(choose('Is shipment ka total linked stock weight ' + (hasValue(item.totalWeight) ? item.totalWeight + ' kg' : 'abhi add nahi hai') + ' hai.', 'The total linked stock weight for this shipment is ' + (hasValue(item.totalWeight) ? item.totalWeight + ' kg' : 'not added yet') + '.')) + '</p>');
            }

            if (matchesIntent(text, ['cbm', 'volume'])) {
                push('cbm', '<p>' + escapeHtml(choose('Is shipment ka total CBM ' + displayValue(item.totalCbm) + ' hai.', 'The total CBM for this shipment is ' + displayValue(item.totalCbm) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['customs value', 'value'])) {
                push('value', '<p>' + escapeHtml(choose('Is shipment ke linked stocks ka total value ' + displayValue(item.totalValueDisplay || item.totalValue) + ' hai.', 'The total value of linked stocks for this shipment is ' + displayValue(item.totalValueDisplay || item.totalValue) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['issue', 'issues', 'irregularity', 'irregularities'])) {
                push('issues', '<p>' + escapeHtml(choose('Is shipment me ' + formatNumber(item.irregularities || 0) + ' open issue ' + countVerb(item.irregularities || 0, 'hai', 'hain') + '.', 'There are ' + formatNumber(item.irregularities || 0) + ' open issues in this shipment.')) + '</p>');
            }

            if (matchesIntent(text, ['repacked', 'repack', 'packed as'])) {
                push('repacked', '<p>' + escapeHtml(choose(
                    'Stock repacked ' + formatRepackedNarrative(item.stockRepackedItems, item.stockRepackedWeight) + ' aur service repacked ' + formatRepackedNarrative(item.serviceRepackedItems, item.serviceRepackedWeight) + '.',
                    'Stock repacked: ' + formatRepackedNarrative(item.stockRepackedItems, item.stockRepackedWeight) + '. Service repacked: ' + formatRepackedNarrative(item.serviceRepackedItems, item.serviceRepackedWeight) + '.'
                )) + '</p>');
            }

            if (containsIntentPhrases(text, ['document details', 'document detail', 'attachment details', 'attachment detail'])) {
                push('document-details', '<p>' + escapeHtml(choose('Is shipment ke document details ye hain.', 'These are the document details for this shipment.')) + '</p>' + renderShipmentDocuments(item.documents || []));
            }

            if (containsIntentPhrases(text, ['document', 'documents', 'attachment', 'attachments']) && wantsCount) {
                push('document-count', '<p>' + escapeHtml(choose('Is shipment ke saath ' + describeCount(item.documentCount || 0, 'document', 'documents') + ' attached ' + countVerb(item.documentCount || 0, 'hai', 'hain') + '.', 'This shipment has ' + describeCount(item.documentCount || 0, 'document', 'documents') + ' attached.')) + '</p>');
            }

            if (containsIntentPhrases(text, ['document name', 'document names', 'attachment list', 'attachment names', 'document list']) || (containsIntentPhrases(text, ['document', 'documents', 'attachment', 'attachments']) && wantsList)) {
                push('document-list', '<p>' + escapeHtml(choose('Is shipment ke documents ye hain: ' + documentNames + '.', 'The documents for this shipment are: ' + documentNames + '.')) + '</p>');
            }

            return blocks;
        }

        function renderShipmentFieldResponse(item, text) {
            var blocks = buildShipmentFieldBlocks(item, text);

            if (! blocks.length) {
                return null;
            }

            return renderRequestedFieldResponse(
                'shipment-field-detail',
                choose('Requested shipment detail ready', 'Requested shipment detail ready'),
                choose('Shipment ' + item.number + ' ka answer', 'Answer for shipment ' + item.number),
                blocks
            );
        }

        function renderStockDetail(item) {
            var customsValue = item.customsValue ? [item.customsValue, item.currency || ''].filter(Boolean).join(' ') : '—';

            return {
                kind: 'stock-detail',
                status: choose('Complete stock summary ready', 'Complete stock summary ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Stock ' + item.number + ' ka complete summary', 'Complete summary for stock ' + item.number)) + '</strong>' +
                    textParagraphs([
                        choose(
                            'Is stock ka current status ' + displayValue(item.status, 'Unknown') + ' hai, priority ' + displayValue(item.priority) + ' hai aur acceptance status ' + displayValue(item.acceptance) + ' hai.',
                            'The current status of this stock is ' + displayValue(item.status, 'Unknown') + ', the priority is ' + displayValue(item.priority) + ', and the acceptance status is ' + displayValue(item.acceptance) + '.'
                        ),
                        choose(
                            'Yeh stock vessel ' + displayValue(item.vessel) + ' ke liye dikh raha hai. Customer ' + displayValue(item.customer) + ', supplier ' + displayValue(item.supplier) + ' aur hub/agent ' + displayValue(item.hubAgent) + ' dikh raha hai.',
                            'This stock is for vessel ' + displayValue(item.vessel) + '. The customer is ' + displayValue(item.customer) + ', the supplier is ' + displayValue(item.supplier) + ', and the hub/agent is ' + displayValue(item.hubAgent) + '.'
                        ),
                        hasValue(customsValue)
                            ? choose('Customs value ' + customsValue + ' hai.', 'The customs value is ' + customsValue + '.')
                            : choose('Customs value abhi add nahi hai.', 'The customs value is not added yet.'),
                        choose(
                            'Is stock me ' + describeCount(item.packageCount || 0, 'package', 'packages') + ' ' + countVerb(item.packageCount || 0, 'hai', 'hain') + ' aur expected delivery date ' + displayValue(item.expectedDeliveryDate) + ' hai.',
                            'This stock has ' + describeCount(item.packageCount || 0, 'package', 'packages') + ' and the expected delivery date is ' + displayValue(item.expectedDeliveryDate) + '.'
                        ),
                        item.linkedShipments && item.linkedShipments.length
                            ? choose('Yeh stock in shipments se linked hai: ' + item.linkedShipments.join(', ') + '.', 'This stock is linked to these shipments: ' + item.linkedShipments.join(', ') + '.')
                            : choose('Yeh stock abhi kisi shipment se linked nahi hai.', 'This stock is not linked to any shipment right now.'),
                        choose('Last update ' + displayValue(item.updatedAt) + ' par hua tha.', 'The last update was on ' + displayValue(item.updatedAt) + '.')
                    ])
            };
        }

        function buildStockFieldBlocks(item, text) {
            var blocks = [];
            var seen = {};
            var wantsCount = queryHasCountIntent(text);
            var wantsList = queryHasListIntent(text);
            var linkedShipments = item.linkedShipments && item.linkedShipments.length
                ? item.linkedShipments.join(', ')
                : 'abhi koi linked shipment nahi hai';
            var customsValue = item.customsValue ? [item.customsValue, item.currency || ''].filter(Boolean).join(' ') : '—';

            function push(key, html) {
                if (! html || seen[key]) {
                    return;
                }

                seen[key] = true;
                blocks.push(html);
            }

            if (matchesIntent(text, ['status'], ['acceptance status'])) {
                push('status', '<p>' + escapeHtml(choose('Is stock ka status ' + displayValue(item.status, 'Unknown') + ' hai.', 'The status of this stock is ' + displayValue(item.status, 'Unknown') + '.')) + '</p>');
            }

            if (matchesIntent(text, ['priority'])) {
                push('priority', '<p>' + escapeHtml(choose('Is stock ki priority ' + displayValue(item.priority) + ' hai.', 'The priority of this stock is ' + displayValue(item.priority) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['vessel'])) {
                push('vessel', '<p>' + escapeHtml(choose('Yeh stock vessel ' + displayValue(item.vessel) + ' ke liye hai.', 'This stock is for vessel ' + displayValue(item.vessel) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['customer'], ['linked shipment', 'linked shipments'])) {
                push('customer', '<p>' + escapeHtml(choose('Is stock ka customer ' + displayValue(item.customer) + ' hai.', 'The customer for this stock is ' + displayValue(item.customer) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['supplier'])) {
                push('supplier', '<p>' + escapeHtml(choose('Is stock ka supplier ' + displayValue(item.supplier) + ' hai.', 'The supplier for this stock is ' + displayValue(item.supplier) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['hub agent', 'hub/agent', 'hub', 'agent'])) {
                push('hub-agent', '<p>' + escapeHtml(choose('Is stock ka hub/agent ' + displayValue(item.hubAgent) + ' hai.', 'The hub/agent for this stock is ' + displayValue(item.hubAgent) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['acceptance status', 'acceptance', 'accepted'])) {
                push('acceptance', '<p>' + escapeHtml(choose('Is stock ka acceptance status ' + displayValue(item.acceptance) + ' hai.', 'The acceptance status of this stock is ' + displayValue(item.acceptance) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['currency'], ['customs value', 'value'])) {
                push('currency', '<p>' + escapeHtml(choose('Is stock ki currency ' + displayValue(item.currency) + ' hai.', 'The currency for this stock is ' + displayValue(item.currency) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['customs value', 'value'])) {
                push('customs-value', '<p>' + escapeHtml(choose('Is stock ka customs value ' + displayValue(customsValue) + ' hai.', 'The customs value for this stock is ' + displayValue(customsValue) + '.')) + '</p>');
            }

            if (matchesIntent(text, ['expected delivery', 'expected delivery date', 'delivery date'])) {
                push('expected-delivery', '<p>' + escapeHtml(choose('Is stock ki expected delivery date ' + displayValue(item.expectedDeliveryDate) + ' hai.', 'The expected delivery date for this stock is ' + displayValue(item.expectedDeliveryDate) + '.')) + '</p>');
            }

            if (containsIntentPhrases(text, ['linked shipment details', 'linked shipment detail', 'shipment details', 'shipment detail'])) {
                push('shipment-details', '<p>' + escapeHtml(choose('Is stock ke linked shipments ye hain: ' + linkedShipments + '.', 'These are the linked shipments for this stock: ' + linkedShipments + '.')) + '</p>');
            }

            if (containsIntentPhrases(text, ['shipment', 'shipments', 'linked shipment', 'linked shipments']) && wantsCount) {
                push('shipment-count', '<p>' + escapeHtml(choose(
                    'Yeh stock ' + describeCount((item.linkedShipments || []).length, 'shipment', 'shipments') + ' se linked ' + countVerb((item.linkedShipments || []).length, 'hai', 'hain') + '.',
                    'This stock is linked to ' + describeCount((item.linkedShipments || []).length, 'shipment', 'shipments') + '.'
                )) + '</p>');
            }

            if (containsIntentPhrases(text, ['shipment number', 'shipment numbers', 'linked shipment', 'linked shipments', 'shipment list']) || (containsIntentPhrases(text, ['shipment', 'shipments']) && wantsList)) {
                push('shipment-list', '<p>' + escapeHtml(choose('Is stock ke linked shipment numbers ye hain: ' + linkedShipments + '.', 'The linked shipment numbers for this stock are: ' + linkedShipments + '.')) + '</p>');
            }

            if (matchesIntent(text, ['package', 'packages', 'pcs', 'pieces'])) {
                push('packages', '<p>' + escapeHtml(choose(
                    'Is stock me ' + describeCount(item.packageCount || 0, 'package', 'packages') + ' ' + countVerb(item.packageCount || 0, 'hai', 'hain') + '.',
                    'This stock has ' + describeCount(item.packageCount || 0, 'package', 'packages') + '.'
                )) + '</p>');
            }

            if (matchesIntent(text, ['last update', 'last updated', 'updated at', 'when was stock', 'when was this stock updated', 'update kab hua', 'kab update hua'])) {
                push('updated-at', '<p>' + escapeHtml(choose('Is stock ka last update ' + displayValue(item.updatedAt) + ' par hua tha.', 'The last update for this stock was on ' + displayValue(item.updatedAt) + '.')) + '</p>');
            }

            return blocks;
        }

        function renderStockFieldResponse(item, text) {
            var blocks = buildStockFieldBlocks(item, text);

            if (! blocks.length) {
                return null;
            }

            return renderRequestedFieldResponse(
                'stock-field-detail',
                choose('Requested stock detail ready', 'Requested stock detail ready'),
                choose('Stock ' + item.number + ' ka answer', 'Answer for stock ' + item.number),
                blocks
            );
        }

        function responseForShipmentQuery(item, query) {
            var normalizedQuery = $.trim(String(query || ''));
            var isExactRecordLookup = normalizeCompact(normalizedQuery) === normalizeCompact(item.number);
            var responses = [];

            if (isTransportDetailsRequest(normalizedQuery)) {
                responses.push(renderShipmentTransportDetail(item));
            }

            if (! isFullRecordRequest(normalizedQuery)) {
                var fieldResponse = renderShipmentFieldResponse(item, normalizedQuery);

                if (fieldResponse) {
                    responses.push(fieldResponse);
                }

                var combinedFieldResponse = renderCombinedResponses(
                    responses,
                    'shipment-compound-detail',
                    choose('Requested shipment detail ready', 'Requested shipment detail ready')
                );

                if (combinedFieldResponse) {
                    return combinedFieldResponse;
                }

                if (! isGenericRecordSummaryRequest(normalizedQuery) && ! isExactRecordLookup) {
                    return renderUnknownFieldResponse('shipment', item);
                }
            }

            responses.push(renderShipmentDetail(item));

            return renderCombinedResponses(
                responses,
                'shipment-compound-detail',
                choose('Requested shipment detail ready', 'Requested shipment detail ready')
            ) || renderShipmentDetail(item);
        }

        function responseForStockQuery(item, query) {
            var normalizedQuery = $.trim(String(query || ''));
            var isExactRecordLookup = normalizeCompact(normalizedQuery) === normalizeCompact(item.number);

            if (! isFullRecordRequest(normalizedQuery)) {
                var fieldResponse = renderStockFieldResponse(item, normalizedQuery);

                if (fieldResponse) {
                    return fieldResponse;
                }

                if (! isGenericRecordSummaryRequest(normalizedQuery) && ! isExactRecordLookup) {
                    return renderUnknownFieldResponse('stock', item);
                }
            }

            return renderStockDetail(item);
        }

        function buildRemoteLookupResponse(payload, query) {
            if (! payload) {
                return null;
            }

            if (payload.scopeBlocked) {
                return renderScopeBlockedResponse(payload.type || '');
            }

            if (payload.sensitiveBlocked) {
                return renderSensitiveLookupResponse(payload.type || '', payload.blockedReason || '', payload.item || null);
            }

            if (payload.matched !== true || ! payload.item) {
                return null;
            }

            if (payload.type === 'shipment') {
                if (! scopeAllows('shipments')) {
                    return renderScopeBlockedResponse('shipment');
                }

                rememberLookupItem('shipment', payload.item);
                return responseForShipmentQuery(payload.item, query || '');
            }

            if (payload.type === 'stock') {
                if (! scopeAllows('stocks')) {
                    return renderScopeBlockedResponse('stock');
                }

                rememberLookupItem('stock', payload.item);
                return responseForStockQuery(payload.item, query || '');
            }

            if (payload.type === 'change_log') {
                if (! scopeAllows('administration')) {
                    return renderScopeBlockedResponse('change_log');
                }

                return responseForChangeLogQuery(payload.item, query || '');
            }

            if (['office', 'hub', 'agent', 'supplier', 'customer', 'contact', 'vessel', 'user'].indexOf(payload.type) !== -1) {
                if (! scopeAllows('administration')) {
                    return renderScopeBlockedResponse(payload.type);
                }

                return responseForAdministrationQuery(payload.type, payload.item, query || '');
            }

            return null;
        }

        function renderSearchResults(query) {
            var shipmentMatches = recordMatches(shipments, query, shipmentSearchText).slice(0, 4).map(function (entry) {
                return entry.item;
            });
            var stockMatches = recordMatches(stocks, query, stockSearchText).slice(0, 4).map(function (entry) {
                return entry.item;
            });

            if (shipmentMatches.length === 1 && stockMatches.length === 0) {
                return responseForShipmentQuery(shipmentMatches[0], query);
            }

            if (stockMatches.length === 1 && shipmentMatches.length === 0) {
                return responseForStockQuery(stockMatches[0], query);
            }

            if (! shipmentMatches.length && ! stockMatches.length) {
                return {
                    kind: 'clarify',
                    status: choose('Thoda aur clear likhiye', 'Please be a bit more specific'),
                    html: '' +
                        '<strong>' + escapeHtml(choose(
                            isStockOnlyAssistant() ? 'Main stock details bata sakta hoon' : 'Main dashboard details bata sakta hoon',
                            isStockOnlyAssistant() ? 'I can help with stock details' : 'I can help with dashboard details'
                        )) + '</strong>' +
                        '<p>' + (isStockOnlyAssistant()
                            ? (isEnglishResponse()
                            ? 'You can type a stock number, supplier, hub/agent, <code>stock summary</code>, or <code>follow-up stocks</code>.'
                            : 'Aap stock number, supplier, hub/agent, <code>stock summary</code> ya <code>follow-up stocks</code> likh sakte hain.')
                            : choose(
                                'Aap shipment number, stock number, office, hub, agent, supplier, customer, vessel, user, administration change log, <code>overdue arrivals</code>, <code>stock summary</code> ya <code>overview</code> likh sakte hain.',
                                'You can type a shipment number, stock number, office, hub, agent, supplier, customer, vessel, user, administration change log, <code>overdue arrivals</code>, <code>stock summary</code>, or <code>overview</code>.'
                            )) + '</p>'
                };
            }

            return {
                kind: 'matching-results',
                status: choose('Closest matches ready', 'Closest matches ready'),
                html: '' +
                    '<strong>' + escapeHtml(choose('Mujhe ye closest matches mile', 'I found these closest matches')) + '</strong>' +
                    (shipmentMatches.length ? shipmentList(shipmentMatches, choose('Matching shipments', 'Matching shipments'), choose('Koi shipment match nahi mila.', 'No shipment match was found.')) : '') +
                    (stockMatches.length ? stockList(stockMatches, choose('Matching stocks', 'Matching stocks'), choose('Koi stock match nahi mila.', 'No stock match was found.')) : '')
            };
        }

        function renderHelp() {
            var rows = [];

            if (scopeAllows('overview')) {
                rows.push({
                    label: choose('Overview', 'Overview'),
                    value: choose('Selected period ka overall dashboard summary', 'Overall dashboard summary for the selected period')
                });
            }

            if (scopeAllows('shipments')) {
                rows.push({
                    label: choose('Shipments', 'Shipments'),
                    value: choose('Shipment ka poora summary: status, route, consignee, comments, documents aur linked stocks', 'Complete shipment summary: status, route, consignee, comments, documents, and linked stocks')
                });
                rows.push({
                    label: choose('Field answers', 'Field answers'),
                    value: choose('Status, customer, kitne stocks, documents, weight ya kisi specific field ka seedha jawab', 'Direct answers for status, customer, stock count, documents, weight, or any specific field')
                });
                rows.push({
                    label: choose('Flight details', 'Flight details'),
                    value: choose('Shipment ke saved flight ya transport legs ka alag summary', 'Separate summary of saved flight or transport legs for a shipment')
                });
            }

            if (scopeAllows('stocks')) {
                rows.push({
                    label: choose('Stocks', 'Stocks'),
                    value: choose('Status, supplier, hub/agent, customs value, packages aur linked shipments', 'Status, supplier, hub/agent, customs value, packages, and linked shipments')
                });
            }

            if (scopeAllows('administration')) {
                rows.push({
                    label: choose('Administration', 'Administration'),
                    value: choose('Office, hub, agent, supplier, customer, contact, vessel, user aur administration change logs ke field-wise, history-wise ya full details', 'Field-wise, history-wise, or full details for offices, hubs, agents, suppliers, customers, contacts, vessels, users, and administration change logs')
                });
            }

            if (scopeAllows('overdueShipments')) {
                rows.push({
                    label: choose('Attention items', 'Attention items'),
                    value: choose('Overdue arrivals aur acceptance pending stocks', 'Overdue arrivals and acceptance-pending stocks')
                });
            } else if (scopeAllows('stockFollowUps')) {
                rows.push({
                    label: choose('Stock follow-up', 'Stock follow-up'),
                    value: choose('Acceptance pending ya follow-up wale stocks', 'Stocks waiting for acceptance or follow-up')
                });
            }

            rows.push({
                label: choose('Restriction', 'Restriction'),
                value: isStockOnlyAssistant()
                    ? choose('Aapke role me yahan sirf stock related answers milenge', 'For your role, only stock-related answers are available here')
                    : choose('Yahan se create, update, delete, save ya cancel nahi hoga', 'Create, update, delete, save, or cancel actions are not available here')
            });

            return {
                kind: 'help',
                status: choose('Help ready', 'Help ready'),
                html: '' +
                        '<strong>' + escapeHtml(choose('Main kis cheez me help kar sakta hoon', 'What I can help with')) + '</strong>' +
                    metricList(rows) +
                    '<p><strong>' + escapeHtml(choose('Examples:', 'Examples:')) + '</strong> '
                        + (isStockOnlyAssistant()
                            ? (isEnglishResponse()
                            ? '<code>What is the supplier for CN-72656522 stock</code>, <code>How many packages are in CN-72656522</code>, <code>Show linked shipments for CN-72656522</code>, <code>Show the complete summary for CN-72656522</code>.'
                            : '<code>CN-72656522 stock ka supplier batao</code>, <code>CN-72656522 me kitne packages hain</code>, <code>CN-72656522 ke linked shipments batao</code>, <code>CN-72656522 ka complete summary batao</code>.')
                            : (isEnglishResponse()
                                ? '<code>How many stocks are linked to AZA-41267-0926</code>, <code>What is the status of AZA-41267-0926</code>, <code>Show details for AZA-41267-0926</code>, <code>Who changed the address for MarineCaddie Dubai Office</code>, <code>What is the role for user sunnyazahar@gmail.com</code>, <code>Show full details for DXB hub</code>, <code>What is the IMO for vessel ANGEL</code>.'
                                : '<code>AZA-41267-0926 ka kitna stocks add hai</code>, <code>AZA-41267-0926 ka status batao</code>, <code>AZA-41267-0926 ka details batao</code>, <code>MarineCaddie Dubai Office ka address kisne change kiya</code>, <code>sunnyazahar@gmail.com ka user role batao</code>, <code>DXB hub ka full details batao</code>, <code>ANGEL vessel ka IMO batao</code>.'))
                        + '</p>'
            };
        }

        function buildResponse(input, options) {
            options = options || {};
            var normalized = normalize(input);
            var shipmentCreationWindow = detectShipmentCreationCountWindow(normalized);

            if (! normalized) {
                return renderHelp();
            }

            if (! options.skipCompound) {
                var questionSegments = splitStandaloneQuestionSegments(input);
                var combinedResponse = questionSegments.length > 1
                    ? renderCombinedResponses(
                        questionSegments.map(function (segment) {
                            return buildResponse(segment, { skipCompound: true });
                        }),
                        'compound-response',
                        choose('Combined answer ready', 'Combined answer ready')
                    )
                    : null;

                if (combinedResponse) {
                    return combinedResponse;
                }
            }

            if (isCancelledShipmentSummaryRequest(normalized)) {
                if (! scopeAllows('shipments')) {
                    return renderScopeBlockedResponse('shipment');
                }

                return renderCancelledShipmentSummary();
            }

            if (shipmentCreationWindow) {
                if (! scopeAllows('shipments')) {
                    return renderScopeBlockedResponse('shipment');
                }

                return renderShipmentCreationWindowSummary(shipmentCreationWindow);
            }

            if (isReadOnlyActionRequest(normalized)) {
                return {
                    kind: 'read-only',
                    status: choose('View-only help', 'View-only help'),
                    html: '' +
                        '<strong>' + escapeHtml(choose('Main yahan action complete nahi kar sakta', 'I cannot complete actions here')) + '</strong>' +
                        '<p>' + escapeHtml(choose(
                            isStockOnlyAssistant()
                                ? 'Aap yahan stock details ya stock summary dekh sakte hain, lekin save, create, update ya delete action nahi hoga.'
                                : 'Aap yahan shipment aur stock ki details ya summary dekh sakte hain, lekin save, create, update ya delete action nahi hoga.',
                            isStockOnlyAssistant()
                                ? 'You can view stock details or stock summaries here, but save, create, update, or delete actions are not available.'
                                : 'You can view shipment and stock details or summaries here, but save, create, update, or delete actions are not available.'
                        )) + '</p>'
                };
            }

            var matchedShipment = findShipmentByNumber(input);
            if (matchedShipment) {
                if (! scopeAllows('shipments')) {
                    return renderScopeBlockedResponse('shipment');
                }

                return responseForShipmentQuery(matchedShipment, input);
            }

            var matchedStock = findStockByNumber(input);
            if (matchedStock) {
                if (! scopeAllows('stocks')) {
                    return renderScopeBlockedResponse('stock');
                }

                return responseForStockQuery(matchedStock, input);
            }

            if (containsAny(normalized, ['help', 'what can you', 'kya kar', 'capabilit', 'madad', 'samjha'])) {
                return renderHelp();
            }

            if (isAdministrationLookupRequest(input)) {
                if (! scopeAllows('administration')) {
                    return renderScopeBlockedResponse('administration');
                }

                return renderSearchResults(input);
            }

            if (containsAny(normalized, ['overdue', 'late arrival', 'past deadline', 'late', 'delay', 'der'])) {
                if (! scopeAllows('overdueShipments')) {
                    return renderScopeBlockedResponse('shipment');
                }

                return renderOverdueShipments();
            }

            if (containsAny(normalized, ['follow up', 'follow-up', 'followup', 'unaccepted', 'awaiting acceptance', 'pickup queue', 'pending accept'])) {
                if (! scopeAllows('stockFollowUps')) {
                    return renderScopeBlockedResponse('stock');
                }

                return renderStockFollowUps();
            }

            if (isServiceSummaryRequest(input)) {
                if (! scopeAllows('services')) {
                    return renderScopeBlockedResponse('shipment');
                }

                return renderServices();
            }

            if (isShipmentSummaryRequest(input)) {
                if (! scopeAllows('shipments')) {
                    return renderScopeBlockedResponse('shipment');
                }

                return renderShipmentSummary();
            }

            if (isStockSummaryRequest(input)) {
                if (! scopeAllows('stocks')) {
                    return renderScopeBlockedResponse('stock');
                }

                return renderStockSummary();
            }

            if (containsAny(normalized, ['overview', 'dashboard', 'summary', 'snapshot', 'kpi', 'metrics', 'everything', 'overall', 'sab kuch', 'poora summary'])) {
                if (! scopeAllows('overview')) {
                    return renderScopeBlockedResponse('overview');
                }

                return renderOverview();
            }

            return renderSearchResults(input);
        }

        function showWelcome() {
            if (state.hasWelcomed) {
                return;
            }

            state.hasWelcomed = true;
            syncAssistantChrome(true);
            addMessage(
                'bot',
                '' +
                    '<strong>' + escapeHtml(choose('Main ready hoon.', 'I am ready.')) + '</strong>' +
                    '<p>' + escapeHtml(choose(
                        isStockOnlyAssistant()
                            ? 'Aap simple language me stock ka specific field, supplier, hub/agent, package count, linked shipments ya full stock summary puchh sakte hain.'
                            : 'Aap simple language me shipment, stock, office, hub, agent, supplier, customer, contact, vessel, user ya administration change log ka specific field, full details, overdue arrivals, follow-up stocks ya dashboard summary puchh sakte hain.',
                        isStockOnlyAssistant()
                            ? 'You can ask for a stock field, supplier, hub/agent, package count, linked shipments, or the full stock summary in simple language.'
                            : 'You can ask for a specific shipment, stock, office, hub, agent, supplier, customer, contact, vessel, user, or administration change log field, full details, overdue arrivals, follow-up stocks, or the dashboard summary.'
                    )) + '</p>' +
                    '<p><strong>' + escapeHtml(choose('Last update:', 'Last update:')) + '</strong> ' + escapeHtml(valueOrDash(assistantData.generatedAt)) + '</p>',
                true
            );
        }

        function clearConversation() {
            if (state.isBusy) {
                return;
            }

            $thread.empty();
            $input.val('');
            state.hasWelcomed = false;
            syncAssistantChrome(false);
            showWelcome();

            window.setTimeout(function () {
                $input.trigger('focus');
            }, 30);
        }

        function submitPrompt(value) {
            var text = $.trim(String(value || ''));
            if (! text || state.isBusy) {
                return;
            }

            state.language = detectResponseLanguage(text);
            syncAssistantChrome(true);
            addMessage('user', text);
            var questionSegments = splitStandaloneQuestionSegments(text);

            if (questionSegments.length > 1) {
                var PromiseCtor = window.Promise || Promise;
                var segmentPlans = questionSegments.map(function (segment) {
                    var localResponse = buildResponse(segment, { skipCompound: true });
                    var targetedLookup = extractLookupTerms(segment).length > 0;

                    return {
                        query: segment,
                        localResponse: localResponse,
                        fallbackResponse: targetedLookup ? renderSearchResults(segment) : localResponse,
                        needsRemote: shouldTryRemoteLookup(segment, localResponse)
                    };
                });
                var needsRemoteLookup = segmentPlans.some(function (plan) {
                    return plan.needsRemote;
                });

                if (needsRemoteLookup) {
                    setComposerBusy(true);
                    $launcherStatus.text(checkingRecordsText());

                    PromiseCtor.all(segmentPlans.map(function (plan) {
                        if (! plan.needsRemote) {
                            return PromiseCtor.resolve(plan.localResponse);
                        }

                        return new PromiseCtor(function (resolve) {
                            $.getJSON(lookupUrl, { q: plan.query })
                                .done(function (payload) {
                                    resolve(buildRemoteLookupResponse(payload, plan.query) || plan.fallbackResponse);
                                })
                                .fail(function () {
                                    resolve(plan.fallbackResponse);
                                });
                        });
                    }))
                        .then(function (responses) {
                            var combinedRemoteResponse = renderCombinedResponses(
                                responses,
                                'compound-response',
                                choose('Combined answer ready', 'Combined answer ready')
                            ) || renderHelp();

                            addMessage('bot', combinedRemoteResponse.html, true);
                            $launcherStatus.text(combinedRemoteResponse.status || snapshotReadyText());
                        })
                        .finally(function () {
                            setComposerBusy(false);
                            $input.val('');
                            window.setTimeout(function () {
                                $input.trigger('focus');
                            }, 30);
                        });

                    return;
                }

                var combinedLocalResponse = renderCombinedResponses(
                    segmentPlans.map(function (plan) {
                        return plan.localResponse;
                    }),
                    'compound-response',
                    choose('Combined answer ready', 'Combined answer ready')
                );

                if (combinedLocalResponse) {
                    addMessage('bot', combinedLocalResponse.html, true);
                    $launcherStatus.text(combinedLocalResponse.status || snapshotReadyText());
                    $input.val('');
                    window.setTimeout(function () {
                        $input.trigger('focus');
                    }, 30);

                    return;
                }
            }

            var response = buildResponse(text);
            var targetedLookup = extractLookupTerms(text).length > 0;
            var fallbackResponse = targetedLookup ? renderSearchResults(text) : response;

            if (shouldTryRemoteLookup(text, response)) {
                setComposerBusy(true);
                $launcherStatus.text(checkingRecordsText());

                $.getJSON(lookupUrl, { q: text })
                    .done(function (payload) {
                        var remoteResponse = buildRemoteLookupResponse(payload, text) || fallbackResponse;
                        addMessage('bot', remoteResponse.html, true);
                        $launcherStatus.text(remoteResponse.status || snapshotReadyText());
                    })
                    .fail(function () {
                        addMessage('bot', fallbackResponse.html, true);
                        $launcherStatus.text(fallbackResponse.status || snapshotReadyText());
                    })
                    .always(function () {
                        setComposerBusy(false);
                        $input.val('');
                        window.setTimeout(function () {
                            $input.trigger('focus');
                        }, 30);
                    });

                return;
            }

            addMessage('bot', response.html, true);
            $launcherStatus.text(response.status || snapshotReadyText());
            $input.val('');
            window.setTimeout(function () {
                $input.trigger('focus');
            }, 30);
        }

        function deepClone(value) {
            if (value == null || typeof value !== 'object') {
                return value;
            }

            return JSON.parse(JSON.stringify(value));
        }

        function stripHtml(value) {
            return $('<div></div>').html(String(value || '')).text();
        }

        function normalizeForTest(value) {
            return stripHtml(value)
                .toLowerCase()
                .replace(/\s+/g, ' ')
                .trim();
        }

        if (assistantTestingEnabled) {
            window.__mcAssistantTestApi = {
                buildResponse: buildResponse,
                buildRemoteLookupResponse: buildRemoteLookupResponse,
                detectResponseLanguage: detectResponseLanguage,
                getScope: function () {
                    return deepClone(assistantScope);
                },
                getState: function () {
                    return deepClone(state);
                }
            };
        }

        syncViewportOffset();
        forceClosedLauncherState();
        syncAssistantChrome(false);
        showWelcome();

        $(window).on('resize orientationchange', syncViewportOffset);

        if (window.visualViewport && typeof window.visualViewport.addEventListener === 'function') {
            window.visualViewport.addEventListener('resize', syncViewportOffset);
            window.visualViewport.addEventListener('scroll', syncViewportOffset);
        }

        $launcher.on('click', function () {
            setLauncher(true);
        });

        $close.on('click', function () {
            setLauncher(false);
        });

        $clear.on('click', function () {
            clearConversation();
        });

        $send.on('click', function () {
            submitPrompt($input.val());
        });

        $input.on('focus input', function () {
            var typedText = $.trim($input.val());

            if (typedText) {
                state.language = detectResponseLanguage(typedText);
                syncAssistantChrome(true);
            }

            if (! state.isOpen) {
                setLauncher(true);
            }
        });

        $input.on('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                submitPrompt($input.val());
            }
        });
    });
</script>
