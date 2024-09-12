<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to log messages
function logMessage($message) {
    echo $message . "<br>";
    error_log($message);
}

// Start output buffering to capture all output
ob_start();

try {
    logMessage("Initializing PHPMailer...");
    $mail = new PHPMailer(true);

    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->Debugoutput = function($str, $level) {
        logMessage("Debug ($level): $str");
    };
    
    logMessage("Configuring SMTP settings...");
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'nishanthnishu049@gmail.com';           // SMTP username
    $mail->Password   = 'xyog laoi vicr zouu';                  // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
    $mail->Port       = 587;                                    // TCP port to connect to

    logMessage("Attempting SMTP connection...");
    if ($mail->smtpConnect()) {
        logMessage("SMTP connection and authentication successful!");
        
        // Attempt to send a test email
        logMessage("Attempting to send a test email...");
        $mail->setFrom('nishanthnishu049@gmail.com', 'Nishanth');
        $mail->addAddress('nishanthnishu049@gmail.com', 'Nishanth');
        $mail->Subject = 'SMTP Test';
        $mail->Body    = 'This is a test email to verify SMTP settings.';
        
        if ($mail->send()) {
            logMessage("Test email sent successfully!");
        } else {
            throw new Exception("Failed to send test email: " . $mail->ErrorInfo);
        }
        
        $mail->smtpClose();
    } else {
        throw new Exception("SMTP connection failed.");
    }
} catch (Exception $e) {
    logMessage("An error occurred: " . $e->getMessage());
    
    // Additional error information
    logMessage("Detailed error information:");
    logMessage("Error code: " . $mail->ErrorInfo);
    logMessage("PHP version: " . phpversion());
    logMessage("OpenSSL version: " . OPENSSL_VERSION_TEXT);
    logMessage("Loaded PHP extensions: " . implode(', ', get_loaded_extensions()));
    
    // Check if SMTP debugging is available
    if (method_exists($mail, 'getSMTPInstance')) {
        $smtp = $mail->getSMTPInstance();
        if ($smtp !== null) {
            logMessage("Last SMTP response: " . $smtp->getLastResponse());
        }
    }
} finally {
    // Capture and log all output
    $debug_output = ob_get_clean();
    logMessage("Full debug output:");
    logMessage($debug_output);
}

// Additional system checks
logMessage("Checking SMTP connectivity...");
$ports = [587, 465];
foreach ($ports as $port) {
    $connection = @fsockopen('smtp.gmail.com', $port, $errno, $errstr, 5);
    if (is_resource($connection)) {
        logMessage("Successfully connected to smtp.gmail.com on port $port");
        fclose($connection);
    } else {
        logMessage("Failed to connect to smtp.gmail.com on port $port: $errstr ($errno)");
    }
}

// Check TLS support
logMessage("Checking TLS support...");
if (function_exists('openssl_get_cert_locations')) {
    logMessage("OpenSSL cert locations: " . print_r(openssl_get_cert_locations(), true));
} else {
    logMessage("OpenSSL function openssl_get_cert_locations not available");
}

// Final message
logMessage("Script execution completed. Please check the logs for detailed information.");
?>