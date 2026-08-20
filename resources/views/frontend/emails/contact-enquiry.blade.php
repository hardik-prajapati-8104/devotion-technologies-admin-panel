{{-- resources/views/emails/contact-enquiry.blade.php --}}
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color:#333; line-height:1.6;">
    <h2 style="color:#ff7a00;">New Contact Enquiry Received</h2>

    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="padding:8px 0; width:120px;"><strong>Name</strong></td>
            <td style="padding:8px 0;">{{ $enquiry->name }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;"><strong>Email</strong></td>
            <td style="padding:8px 0;">{{ $enquiry->email }}</td>
        </tr>
        @if($enquiry->phone)
        <tr>
            <td style="padding:8px 0;"><strong>Phone</strong></td>
            <td style="padding:8px 0;">{{ $enquiry->phone }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding:8px 0;"><strong>Service</strong></td>
            <td style="padding:8px 0;">{{ $enquiry->subject }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0; vertical-align:top;"><strong>Message</strong></td>
            <td style="padding:8px 0;">{{ $enquiry->message }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;"><strong>Submitted</strong></td>
            <td style="padding:8px 0;">{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
        </tr>
    </table>
</body>
</html>