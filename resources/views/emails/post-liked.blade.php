<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Post Was Liked!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Reset styles for email clients */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }

        /* Responsive styles */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                min-width: 0 !important;
            }
        }
    </style>
</head>
<body style="margin: 0 !important; padding: 0 !important; background-color: #f4f4f4;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 10px;">
                <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 30px 20px 30px; background-color: #4F46E5; color: #ffffff; border-top-left-radius: 4px; border-top-right-radius: 4px;">
                            <h1 style="font-family: Arial, sans-serif; font-size: 24px; font-weight: bold; margin: 0; color: #ffffff;">{{ config('app.name') }}</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td align="left" style="padding: 40px 30px 20px 30px; font-family: Arial, sans-serif; font-size: 16px; line-height: 22px; color: #333333;">
                            <h2 style="font-size: 20px; font-weight: bold; margin: 0 0 15px 0;">Hello {{ $userName }}!</h2>
                            <p style="margin: 0 0 15px 0;">
                                <strong>{{ $likerName }}</strong> liked your post: <em>{{ $postTitle }}</em>
                            </p>
                            <p style="margin: 0 0 15px 0;">
                                <strong>Post Content:</strong><br>
                                {{ \Illuminate\Support\Str::limit($postContent, 150) }}
                            </p>
                        </td>
                    </tr>

                    <!-- Button -->
                    <tr>
                        <td align="center" style="padding: 20px 30px 40px 30px;">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="border-radius: 4px;" bgcolor="#4F46E5">
                                        <a href="{{ $postUrl }}" target="_blank" style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; border-radius: 4px; padding: 15px 30px; display: inline-block;">
                                            View Your Post
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="left" style="padding: 0 30px 30px 30px; font-family: Arial, sans-serif; font-size: 12px; line-height: 18px; color: #666666;">
                            <p style="margin: 0 0 10px 0;">
                                You received this email because you posted on {{ config('app.name') }}.
                            </p>
                            <p style="margin: 0;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                                This is an automated message, please do not reply directly to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
