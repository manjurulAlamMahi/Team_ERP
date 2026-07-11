<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#0d6efd;padding:20px 28px;">
                            <span style="color:#ffffff;font-size:18px;font-weight:bold;">Daily Reminder</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 12px;font-size:15px;color:#212529;">Hi {{ $reminder->user->name }},</p>
                            <p style="margin:0 0 16px;font-size:15px;color:#212529;">
                                This is a reminder that the following task is due in <strong>{{ $label }}</strong>,
                                on <strong>{{ $reminder->dueAt()->format('d F Y, h:i A') }}</strong>:
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-left:4px solid #0d6efd;border-radius:4px;">
                                <tr>
                                    <td style="padding:14px 16px;font-size:15px;color:#212529;">
                                        {{ $reminder->details }}
                                    </td>
                                </tr>
                            </table>
                            @if ($reminder->isAssigned())
                                <p style="margin:16px 0 0;font-size:13px;color:#6c757d;">
                                    Assigned by {{ $reminder->creator->name ?? 'your Leader' }}.
                                    @unless ($reminder->isCompletableBy($reminder->user))
                                        Only a Leader/Co Leader can mark this as completed.
                                    @endunless
                                </p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px;background:#f8f9fa;">
                            <p style="margin:0;font-size:12px;color:#adb5bd;">{{ config('app.name') }} - Daily Reminder</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
