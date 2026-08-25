<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Stock updated</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f6fa; color:#1e293b; font-family:Arial, Helvetica, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        Stock {{ $stockNumber }} was updated for vessel {{ $vesselName }}.
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f3f6fa;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:620px;">
                    <tr>
                        <td align="center" style="padding:0 0 24px;">
                            @php
                                $logoSrc = '';
                                if (! empty($logoPath) && is_file($logoPath) && isset($message)) {
                                    $logoSrc = $message->embed($logoPath);
                                } elseif (! empty($logoPath) && is_file($logoPath)) {
                                    $logoSrc = rtrim((string) config('app.url'), '/') . '/files/assets/images/marinecaddie-logo.png';
                                }
                            @endphp
                            @if ($logoSrc !== '')
                                <img src="{{ $logoSrc }}" alt="MarineCaddie" width="200" height="auto" style="display:block; margin:0 auto; width:200px; max-width:200px; height:auto; border:0;">
                            @else
                                <div style="color:#0f2d55; font-size:22px; font-weight:700;">MarineCaddie</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="overflow:hidden; border:1px solid #e2e8f0; border-radius:18px; background-color:#ffffff; box-shadow:0 10px 30px rgba(15, 23, 42, 0.08);">
                            <div style="height:6px; background:linear-gradient(90deg, #ef626c 0%, #39a7e0 100%);"></div>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding:40px 36px;">
                                        <p style="margin:0 0 8px; color:#64748b; font-size:13px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase;">
                                            Stock update notice
                                        </p>
                                        <h1 style="margin:0 0 12px; color:#0f2d55; font-size:26px; line-height:1.25;">
                                            Stock {{ $stockNumber }} was updated
                                        </h1>
                                        <p style="margin:0 0 22px; color:#475569; font-size:14px; line-height:1.55;">
                                            Hello {{ $accountManagerName }},
                                            <br>
                                            The following changes were saved for vessel <strong style="color:#0f2d55;">{{ $vesselName }}</strong>.
                                            @if (! empty($shipmentNumbersLabel))
                                                <br>
                                                Linked shipment: <strong style="color:#0f2d55;">{{ $shipmentNumbersLabel }}</strong>
                                            @endif
                                            <br>
                                            Changed by: <strong style="color:#0f2d55;">{{ $changedByName }}</strong>
                                        </p>

                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                                            <tr>
                                                <td style="background:#f8fafc; padding:12px 16px; color:#0f2d55; font-size:12px; font-weight:700; border-bottom:1px solid #e2e8f0;">
                                                    Change
                                                </td>
                                                <td style="background:#f8fafc; padding:12px 16px; color:#0f2d55; font-size:12px; font-weight:700; border-bottom:1px solid #e2e8f0;">
                                                    Details
                                                </td>
                                            </tr>
                                            @foreach ($changes as $change)
                                                <tr>
                                                    <td style="padding:12px 16px; border-bottom:1px solid #f1f5f9; color:#0f2d55; font-size:13px; font-weight:700; vertical-align:top; width:38%;">
                                                        {{ $change['title'] }}
                                                    </td>
                                                    <td style="padding:12px 16px; border-bottom:1px solid #f1f5f9; color:#475569; font-size:13px; line-height:1.45; vertical-align:top;">
                                                        {{ $change['description'] ?: '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>

                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-top:28px;">
                                            <tr>
                                                <td style="border-radius:8px; background:#008080;">
                                                    <a href="{{ $stockUrl }}" style="display:inline-block; padding:12px 20px; color:#ffffff; font-size:14px; font-weight:700; text-decoration:none;">
                                                        Open stock
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <p style="margin:28px 0 0; color:#94a3b8; font-size:12px; line-height:1.5;">
                                            This is an automated MarineCaddie notification for the vessel account manager.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
