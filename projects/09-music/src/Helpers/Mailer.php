<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Mailer — thin wrapper around PHPMailer.
 *
 * PHP CONCEPT: PHPMailer vs PHP's built-in mail()
 * ─────────────────────────────────────────────────
 * PHP has a built-in mail() function, but it relies on the server having
 * sendmail configured — almost never works on local dev, often goes to spam.
 *
 * PHPMailer speaks SMTP directly (like Nodemailer in Node.js).
 * You give it an SMTP host + credentials and it authenticates and delivers.
 *
 * Node.js (Nodemailer):                 PHP (PHPMailer):
 * ──────────────────────                ─────────────────
 * createTransport({ host, auth })  →    $m->isSMTP(); $m->Host; $m->Username
 * transporter.sendMail({ to, subj })→   $m->addAddress(); $m->Subject; $m->send()
 *
 * SMTP flow:
 *   1. Connect to smtp.gmail.com:587
 *   2. Upgrade to TLS (STARTTLS)
 *   3. Authenticate with username + App Password
 *   4. Send the message
 *   5. Disconnect
 */
class Mailer
{
    /**
     * Send an email.
     *
     * @param string $toAddress  Recipient email
     * @param string $toName     Recipient display name
     * @param string $subject    Email subject line
     * @param string $htmlBody   HTML body content
     * @param string $textBody   Plain-text fallback (for old email clients)
     * @throws Exception         If sending fails (message in $e->getMessage())
     */
    public static function send(
        string $toAddress,
        string $toName,
        string $subject,
        string $htmlBody,
        string $textBody = ''
    ): void {
        // PHPMailer(true) — the `true` argument turns on exceptions.
        // Without it, errors are only accessible via $mail->ErrorInfo.
        $mail = new PHPMailer(true);

        // ── SMTP configuration ─────────────────────────────────────────────
        $mail->isSMTP();                              // use SMTP (not sendmail)
        $mail->Host       = $_ENV['MAIL_HOST'];       // smtp.gmail.com
        $mail->SMTPAuth   = true;                     // require login
        $mail->Username   = $_ENV['MAIL_USERNAME'];   // Gmail address
        $mail->Password   = $_ENV['MAIL_PASSWORD'];   // App Password (not your Gmail password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS on port 587
        $mail->Port       = (int) $_ENV['MAIL_PORT']; // 587

        // ── Sender ────────────────────────────────────────────────────────
        $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);

        // ── Recipient ─────────────────────────────────────────────────────
        $mail->addAddress($toAddress, $toName);

        // ── Content ───────────────────────────────────────────────────────
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody ?: strip_tags($htmlBody); // plain-text fallback

        $mail->send();
        // If send() fails it throws PHPMailer\PHPMailer\Exception.
        // Catch it in the calling code (contact.php) to show a user-friendly error.
    }

    /**
     * Build the notification email sent to Kutumba when someone fills the contact form.
     */
    public static function contactNotification(string $name, string $email, string $message): void
    {
        $to      = $_ENV['MAIL_TO'] ?? $_ENV['MAIL_FROM_ADDRESS'];
        $subject = 'New contact inquiry from ' . $name;

        $safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
        $safeName    = htmlspecialchars($name,    ENT_QUOTES, 'UTF-8');
        $safeEmail   = htmlspecialchars($email,   ENT_QUOTES, 'UTF-8');

        $html = "
            <h2 style='color:#f97316'>New Contact Inquiry — Kutumba</h2>
            <p><strong>From:</strong> {$safeName} &lt;{$safeEmail}&gt;</p>
            <p><strong>Message:</strong></p>
            <blockquote style='border-left:4px solid #f97316;padding-left:16px;color:#444'>
                {$safeMessage}
            </blockquote>
            <hr>
            <p style='font-size:.8rem;color:#999'>Sent via kutumba.com.np contact form</p>
        ";

        static::send($to, 'Kutumba', $subject, $html, "From: {$name} <{$email}>\n\n{$message}");
    }
}
