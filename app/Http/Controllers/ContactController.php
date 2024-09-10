<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Models\Setting;
use Exception;


class ContactController extends Controller
{
    //

    public function sendContactForm(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'email' => 'required|email',
            ]);

            $details = [
                'return' => 'Test',
                'title' => 'Mail from Laravel',
                'body' => 'This is a test email.'
            ];

            $arr = [
                "email_driver",
                "email_hostName",
                "email_encryption",
                "email_port",
                "email_userName",
                "email_password",
                "email_senderName",
                "email_senderEmail",
            ];

            // Fetch configurations in a single query
            $configurations = Setting::whereIn('key', $arr)->pluck('value', 'key');

            // Set email configuration dynamically
            config([
                'mail.mailers.smtp.host' => $configurations["email_hostName"] ?? 'default_host',
                'mail.mailers.smtp.port' => $configurations["email_port"] ?? 587,
                'mail.mailers.smtp.encryption' => $configurations["email_encryption"] ?? 'tls',
                'mail.mailers.smtp.username' => $configurations["email_userName"] ?? 'default_username',
                'mail.mailers.smtp.password' => $configurations["email_password"] ?? 'default_password',
                'mail.from.address' => $configurations["email_senderEmail"] ?? 'default_sender@example.com',
                'mail.from.name' => $configurations["email_senderName"] ?? 'Default Sender',
            ]);

            // Send email
            Mail::to($validatedData['email'])->send(new ContactFormMail($details));

            return response()->json([
                'status' => 'success',
                'message' => 'Email sent successfully.',
                'configurations' => $configurations
            ], 200);
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while sending the email.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
