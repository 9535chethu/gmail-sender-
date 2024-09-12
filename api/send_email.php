<?php
// File: C:\wamp64\www\gmail-sender\api\send_email.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/vendor/autoload.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

function getPostData() {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    return $data;
}

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'nishanthnishu049@gmail.com';
    $mail->Password   = 'xyog laoi vicr zouu';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('nishanthnishu049@gmail.com', 'Nishanth');
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->send();
}

function generateOTP() {
    return strval(random_int(100000, 999999));
}

session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('This endpoint is for POST requests only.');
    }

    $data = getPostData();

    if (isset($data['action']) && $data['action'] === 'send_otp') {
        if (!isset($data['email'])) {
            throw new Exception('Email is required for OTP');
        }
        $otp = generateOTP();
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $data['email'];
        sendMail($data['email'], 'Your OTP for Gmail Sender', "Your OTP is: $otp");
        echo json_encode(['message' => 'OTP sent successfully']);
    } elseif (isset($data['action']) && $data['action'] === 'verify_otp') {
        if (!isset($data['otp']) || !isset($_SESSION['otp']) || !isset($_SESSION['email'])) {
            throw new Exception('Invalid OTP verification request');
        }
        if ($data['otp'] === $_SESSION['otp']) {
            echo json_encode(['message' => 'OTP verified successfully']);
        } else {
            throw new Exception('Invalid OTP');
        }
    } elseif (isset($data['to']) && isset($data['subject']) && isset($data['message'])) {
        if (!isset($_SESSION['email']) || $data['to'] !== $_SESSION['email']) {
            throw new Exception('Unauthorized email send attempt');
        }
        sendMail($data['to'], $data['subject'], $data['message']);
        echo json_encode(['message' => 'Email sent successfully']);
    } else {
        throw new Exception('Invalid request');
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => "Operation failed. Error: " . $e->getMessage(),
        'debug_info' => [
            'error_message' => $e->getMessage(),
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'parsed_data' => $data ?? 'No data parsed',
        ]
    ]);
}
?>