<?php
include 'config.php';

session_start();

/* 🛡️ Set form load time ONLY when page is opened */
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $_SESSION['form_time'] = time();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 🛡️ ANTI-SPAM 1: Honeypot (hidden field)
    $website = $_POST['website'] ?? '';
    if (!empty($website)) {
        exit; // silent block (bot caught)
    }

    // 🛡️ ANTI-SPAM 2: Time check (must not be too fast)
    if (!isset($_SESSION['form_time']) || (time() - $_SESSION['form_time'] < 3)) {
        exit; // too fast submission
    }

    // 🛡️ ANTI-SPAM 3: Rate limiting
    if (!isset($_SESSION['last_submit'])) {
        $_SESSION['last_submit'] = 0;
    }

    if (time() - $_SESSION['last_submit'] < 10) {
        exit; // prevent spam clicking
    }

    $_SESSION['last_submit'] = time();

    // 📩 Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    // 💾 Save to database
    $stmt = $conn->prepare("INSERT INTO form (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);


    if ($stmt->execute()) {
    $botToken = "8788596221:AAGWCxx9tCGnvJTaPMTOuTD1exkFGED1ekw";
$chatID = "8637189183";

$text = "📩 New Contact Form Message\n\n"
      . "👤 Name: $name\n"
      . "📧 Email: $email\n"
      . "💬 Message: $message";

$url = "https://api.telegram.org/bot$botToken/sendMessage";

$data = [
    'chat_id' => $chatID,
    'text' => $text
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

curl_close($ch);
        echo "✅ Message sent successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>