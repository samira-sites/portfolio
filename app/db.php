<?php

require_once __DIR__ . '/config.php';

/* =========================
   DATABASE CONNECTION
========================= */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

/* =========================
   CHECK CONNECTION
========================= */
if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
}