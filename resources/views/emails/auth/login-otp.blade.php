<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>MarineCaddie verification code</title>
    <style>
        @media only screen and (max-width: 620px) {
            .email-shell {
                padding: 16px 10px !important;
            }

            .email-card {
                border-radius: 14px !important;
            }

            .email-content {
                padding: 34px 22px !important;
            }

            .brand {
                font-size: 30px !important;
            }

            .heading {
                font-size: 25px !important;
            }

            .otp-code {
                font-size: 30px !important;
                letter-spacing: 8px !important;
            }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f3f6fa; color:#1e293b; font-family:Arial, Helvetica, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        Use code {{ $otp }} to verify your MarineCaddie login.
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f3f6fa;">
        <tr>
            <td class="email-shell" align="center" style="padding:40px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;">
                    <tr>
                        <td align="center" style="padding:0 0 24px;">
                            {!! \App\Support\LogoHelper::imgTag('200px', 'display:block; margin:0 auto;') !!}
                        </td>
                    </tr>
                    <tr>
                        <td class="email-card" style="overflow:hidden; border:1px solid #e2e8f0; border-radius:18px; background-color:#ffffff; box-shadow:0 10px 30px rgba(15, 23, 42, 0.08);">
                            <div style="height:6px; background:linear-gradient(90deg, #ef626c 0%, #39a7e0 100%);"></div>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td class="email-content" align="center" style="padding:48px 46px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td align="center" style="width:74px; height:74px; border-radius:18px; background-color:#eef7fc; color:#073b6f; font-size:18px; font-weight:700; letter-spacing:1px;">
                                                    OTP
                                                </td>
                                            </tr>
                                        </table>

                                        <h1 class="heading" style="margin:28px 0 12px; color:#0f2d55; font-size:30px; line-height:1.25;">
                                            Verify your identity
                                        </h1>
                                        <p style="margin:0; color:#64748b; font-size:16px; line-height:1.65;">
                                            Enter this verification code to complete your secure MarineCaddie login.
                                        </p>

                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:30px 0 24px;">
                                            <tr>
                                                <td align="center" style="padding:22px 12px; border:1px solid #bae6fd; border-radius:12px; background-color:#f0f9ff;">
                                                    <div class="otp-code" style="color:#075985; font-family:'Courier New', monospace; font-size:38px; font-weight:700; letter-spacing:11px; line-height:1;">
                                                        {{ $otp }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>

                                        <p style="margin:0; color:#475569; font-size:14px; line-height:1.6;">
                                            This code expires in <strong>{{ $expiresInMinutes }} minutes</strong>.
                                            For your security, do not share it with anyone.
                                        </p>

                                        <div style="margin-top:30px; padding-top:24px; border-top:1px solid #e2e8f0; color:#94a3b8; font-size:12px; line-height:1.6;">
                                            If you did not attempt to sign in, you can safely ignore this email.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:22px 16px 0; color:#94a3b8; font-size:11px; line-height:1.6;">
                            &copy; {{ date('Y') }} MarineCaddie. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
