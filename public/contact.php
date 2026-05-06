<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/db.php';

/* =========================
   SESSION SAFETY
========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   ONLY POST ALLOWED
========================= */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit;
}

/* =========================
   HONEYPOT (ANTI-SPAM)
========================= */
if (!empty($_POST['website'] ?? '')) {
    exit("❌ Spam detected");
}

/* =========================
   RATE LIMIT
========================= */
if (!isset($_SESSION['last_submit'])) {
    $_SESSION['last_submit'] = 0;
}

if (time() - $_SESSION['last_submit'] < 10) {
    exit("❌ Please wait before sending again");
}

$_SESSION['last_submit'] = time();

/* =========================
   GET DATA
========================= */
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    exit("❌ All fields are required");
}

/* =========================
   SAVE TO DATABASE
========================= */
$stmt = $conn->prepare("INSERT INTO form (name, email, message) VALUES (?, ?, ?)");

if (!$stmt) {
    die("❌ Prepare failed: " . $conn->error);
}

$stmt->bind_param("sss", $name, $email, $message);

/* =========================
   EXECUTE
========================= */
if ($stmt->execute()) {

    /* =========================
       TELEGRAM SAFE SEND
    ========================= */
    $botToken = $_ENV['TELEGRAM_BOT_TOKEN'] ?? null;
    $chatID   = $_ENV['TELEGRAM_CHAT_ID'] ?? null;

    if ($botToken && $chatID) {
        $text = "📩 New Contact Form Message\n\n"
              . "👤 Name: $name\n"
              . "📧 Email: $email\n"
              . "💬 Message: $message";

        file_get_contents(
            "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatID&text=" . urlencode($text)
        );
    }

    echo "✅ Message sent successfully";

} else {
    echo "❌ Database error: " . $stmt->error;
}

$stmt->close();
$conn->close();