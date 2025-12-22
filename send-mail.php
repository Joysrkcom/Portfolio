<?php
// Load PHPMailer at the top
$phpmailer_src = __DIR__ . '/PHPMailer/src/PHPMailer.php';
if (file_exists($phpmailer_src)) {
    require __DIR__ . '/PHPMailer/src/Exception.php';
    require __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/PHPMailer/src/SMTP.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// ============================================
// SMTP CONFIGURATION - HOSTINGER
// ============================================
$smtp_config = [
    'host'     => 'smtp.hostinger.com',
    'port'     => 465,
    'username' => 'contact@joysrk.com',
    'password' => '6D+ltP0q7r',
    'secure'   => 'ssl',
];

// Admin emails
$admin_emails = [
    'contact@joysrk.com',
    'noinmia130@gmail.com'
];

$your_name = "Joy Sarkar";
$your_website = "joysrk.com";

// Get form data
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
$subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : '';
$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

// Validate
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

$date = date('F j, Y');
$time = date('g:i A');
$year = date('Y');
$firstName = explode(' ', $name)[0];
$initial = strtoupper(substr($name, 0, 1));

// ============================================
// ADMIN EMAIL TEMPLATE
// ============================================
$admin_subject = "New Contact: " . $subject;
$admin_body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;background-color:#f4f4f7;">
    <table width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f4f7;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellspacing="0" cellpadding="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);padding:40px;text-align:center;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <div style="width:70px;height:70px;background:rgba(255,255,255,0.2);border-radius:50%;margin:0 auto 16px;">
                                            <table width="70" height="70" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td align="center" valign="middle" style="color:#ffffff;font-size:28px;font-weight:bold;">JS</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:700;">New Message Received</h1>
                                        <p style="color:rgba(255,255,255,0.85);margin:10px 0 0;font-size:14px;">' . $date . ' at ' . $time . '</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Sender Card -->
                    <tr>
                        <td style="padding:30px 40px 20px;">
                            <table width="100%" cellspacing="0" cellpadding="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding:20px;">
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="50" valign="middle">
                                                    <div style="width:50px;height:50px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:10px;">
                                                        <table width="50" height="50" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td align="center" valign="middle" style="color:#ffffff;font-size:20px;font-weight:bold;">' . $initial . '</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                                <td style="padding-left:15px;vertical-align:middle;">
                                                    <p style="margin:0 0 4px;color:#1e293b;font-size:17px;font-weight:600;">' . $name . '</p>
                                                    <a href="mailto:' . $email . '" style="color:#4f46e5;text-decoration:none;font-size:14px;">' . $email . '</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Subject & Message -->
                    <tr>
                        <td style="padding:10px 40px 30px;">
                            <p style="margin:0 0 8px;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Subject</p>
                            <h2 style="margin:0 0 24px;color:#1e293b;font-size:18px;font-weight:600;">' . $subject . '</h2>
                            
                            <p style="margin:0 0 10px;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Message</p>
                            <div style="background:#f8fafc;border-left:4px solid #4f46e5;padding:20px;border-radius:0 10px 10px 0;">
                                <p style="margin:0;color:#475569;font-size:15px;line-height:1.7;">' . nl2br($message) . '</p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Reply Button -->
                    <tr>
                        <td style="padding:0 40px 40px;text-align:center;">
                            <a href="mailto:' . $email . '?subject=Re: ' . rawurlencode($subject) . '" style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#ffffff;text-decoration:none;padding:14px 40px;border-radius:10px;font-weight:600;font-size:14px;">Reply to ' . $firstName . '</a>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc;padding:20px 40px;border-top:1px solid #e2e8f0;text-align:center;">
                            <p style="margin:0;color:#94a3b8;font-size:12px;">Message from portfolio contact form</p>
                            <p style="margin:6px 0 0;color:#64748b;font-size:11px;font-weight:600;">' . $your_website . '</p>
                        </td>
                    </tr>
                </table>
                
                <p style="margin:20px 0 0;color:#94a3b8;font-size:11px;text-align:center;">&copy; ' . $year . ' ' . $your_name . '</p>
            </td>
        </tr>
    </table>
</body>
</html>';

// ============================================
// AUTO-REPLY EMAIL TEMPLATE
// ============================================
$reply_subject = "Thanks for reaching out, " . $firstName . "!";
$reply_body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;background-color:#f4f4f7;">
    <table width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f4f7;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellspacing="0" cellpadding="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);padding:50px 40px;text-align:center;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <div style="width:80px;height:80px;background:rgba(255,255,255,0.2);border-radius:50%;margin:0 auto 20px;">
                                            <table width="80" height="80" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td align="center" valign="middle" style="color:#ffffff;font-size:32px;font-weight:bold;">JS</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <h1 style="color:#ffffff;margin:0;font-size:28px;font-weight:700;">Thank You!</h1>
                                        <p style="color:rgba(255,255,255,0.9);margin:10px 0 0;font-size:15px;">Your message has been received</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <h2 style="margin:0 0 16px;color:#1e293b;font-size:20px;font-weight:600;">Hi ' . $firstName . ',</h2>
                            <p style="margin:0 0 24px;color:#475569;font-size:15px;line-height:1.7;">Thank you for getting in touch! I appreciate you taking the time to reach out. I have received your message and will get back to you within <strong style="color:#4f46e5;">24-48 hours</strong>.</p>
                            
                            <!-- Message Summary -->
                            <table width="100%" cellspacing="0" cellpadding="0" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin:24px 0;">
                                <tr>
                                    <td style="padding:20px;">
                                        <p style="margin:0 0 14px;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Your Message Summary</p>
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding:10px 0;border-bottom:1px solid #e2e8f0;">
                                                    <span style="color:#64748b;font-size:13px;">Subject:</span>
                                                    <span style="color:#1e293b;font-size:13px;font-weight:500;float:right;">' . $subject . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:14px 0 0;">
                                                    <span style="color:#64748b;font-size:13px;display:block;margin-bottom:8px;">Message:</span>
                                                    <div style="background:#ffffff;padding:14px;border-radius:8px;border:1px solid #e2e8f0;">
                                                        <p style="margin:0;color:#475569;font-size:13px;line-height:1.6;">' . nl2br($message) . '</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin:24px 0;color:#475569;font-size:15px;line-height:1.7;">Feel free to connect with me on social media!</p>
                            
                            <!-- Social Links - Clean Text Style -->
                            <table cellspacing="0" cellpadding="0" style="margin:20px 0;">
                                <tr>
                                    <td style="padding-right:8px;">
                                        <a href="https://web.facebook.com/neel.trapezium" style="display:inline-block;padding:10px 16px;background:#1877f2;border-radius:8px;text-decoration:none;color:#ffffff;font-size:13px;font-weight:600;">Facebook</a>
                                    </td>
                                    <td style="padding-right:8px;">
                                        <a href="https://github.com/Joysrkcom" style="display:inline-block;padding:10px 16px;background:#24292e;border-radius:8px;text-decoration:none;color:#ffffff;font-size:13px;font-weight:600;">GitHub</a>
                                    </td>
                                    <td style="padding-right:8px;">
                                        <a href="https://www.linkedin.com/in/joy-sarker-a90237390/" style="display:inline-block;padding:10px 16px;background:#0077b5;border-radius:8px;text-decoration:none;color:#ffffff;font-size:13px;font-weight:600;">LinkedIn</a>
                                    </td>
                                    <td>
                                        <a href="https://joysrk.com" style="display:inline-block;padding:10px 16px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:8px;text-decoration:none;color:#ffffff;font-size:13px;font-weight:600;">Website</a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Signature -->
                            <table width="100%" cellspacing="0" cellpadding="0" style="border-top:1px solid #e2e8f0;padding-top:24px;margin-top:30px;">
                                <tr>
                                    <td width="50" valign="top">
                                        <div style="width:50px;height:50px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:50%;">
                                            <table width="50" height="50" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td align="center" valign="middle" style="color:#ffffff;font-size:18px;font-weight:bold;">JS</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                    <td style="padding-left:14px;vertical-align:middle;">
                                        <p style="margin:0 0 2px;color:#1e293b;font-size:16px;font-weight:700;">' . $your_name . '</p>
                                        <p style="margin:0;color:#4f46e5;font-size:12px;font-weight:500;">Full-Stack Developer & IT Specialist</p>
                                        <p style="margin:4px 0 0;color:#64748b;font-size:11px;">contact@joysrk.com</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc;padding:20px 40px;border-top:1px solid #e2e8f0;text-align:center;">
                            <p style="margin:0 0 6px;color:#64748b;font-size:12px;">This is an automated response from <a href="https://' . $your_website . '" style="color:#4f46e5;text-decoration:none;font-weight:500;">' . $your_website . '</a></p>
                            <p style="margin:0;color:#94a3b8;font-size:11px;">Please do not reply directly to this email</p>
                        </td>
                    </tr>
                </table>
                
                <p style="margin:20px 0 0;color:#94a3b8;font-size:11px;text-align:center;">&copy; ' . $year . ' ' . $your_name . '. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>';

// ============================================
// SEND EMAILS
// ============================================
if (!file_exists($phpmailer_src)) {
    echo json_encode(['success' => false, 'error' => 'PHPMailer not found']);
    exit;
}

try {
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host       = $smtp_config['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtp_config['username'];
    $mail->Password   = $smtp_config['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $smtp_config['port'];
    $mail->CharSet    = 'UTF-8';
    
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    
    // Send to admins
    $mail->setFrom($smtp_config['username'], $your_name . ' Portfolio');
    $mail->addReplyTo($email, $name);
    
    foreach ($admin_emails as $admin_email) {
        $mail->addAddress($admin_email);
    }
    
    $mail->isHTML(true);
    $mail->Subject = $admin_subject;
    $mail->Body    = $admin_body;
    $mail->send();
    
    // Send auto-reply
    $mail->clearAddresses();
    $mail->addAddress($email, $name);
    $mail->Subject = $reply_subject;
    $mail->Body    = $reply_body;
    $mail->send();
    
    echo json_encode(['success' => true, 'message' => 'Email sent successfully!']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to send email: ' . $mail->ErrorInfo]);
}
?>
