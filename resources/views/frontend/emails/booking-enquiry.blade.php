<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Booking Enquiry</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f5; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="background-color:rgba(255,255,255,.08); padding:20px 32px;">
                            <h1 style="margin:0; color:#ffffff; font-size:20px;">New Booking Enquiry</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 20px; color:#333333; font-size:15px; line-height:1.5;">
                                A new booking request just came in through the website.
                            </p>

                            <table role="presentation" width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; font-size:14px; color:#333333;">
                                <tr style="background-color:#f8f9fa;">
                                    <td style="width:140px; font-weight:bold; border:1px solid #eeeeee;">Name</td>
                                    <td style="border:1px solid #eeeeee;">{{ $enquiry->full_name }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold; border:1px solid #eeeeee;">Phone</td>
                                    <td style="border:1px solid #eeeeee;">{{ $enquiry->phone }}</td>
                                </tr>
                                <tr style="background-color:#f8f9fa;">
                                    <td style="font-weight:bold; border:1px solid #eeeeee;">Email</td>
                                    <td style="border:1px solid #eeeeee;">{{ $enquiry->email }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold; border:1px solid #eeeeee;">Service</td>
                                    <td style="border:1px solid #eeeeee;">{{ $enquiry->serviceCategory->name ?? 'N/A' }}</td>
                                </tr>
                                <tr style="background-color:#f8f9fa;">
                                    <td style="font-weight:bold; border:1px solid #eeeeee;">Address</td>
                                    <td style="border:1px solid #eeeeee;">{{ $enquiry->address }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold; border:1px solid #eeeeee;">Submitted</td>
                                    <td style="border:1px solid #eeeeee;">{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>

                            @if ($enquiry->description)
                                <p style="margin:20px 0 4px; font-weight:bold; color:#333333; font-size:14px;">Notes from customer:</p>
                                <p style="margin:0; color:#555555; font-size:14px; line-height:1.5; white-space:pre-line;">{{ $enquiry->description }}</p>
                            @endif

                            <p style="margin:28px 0 0; color:#888888; font-size:12px;">
                                Reply-to on this email is already set to the customer's address ({{ $enquiry->email }}), so you can hit "Reply" directly.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8f9fa; padding:16px 32px; text-align:center; font-size:12px; color:#999999;">
                            {{ config('app.name') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>