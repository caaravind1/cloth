<?php
if (!isset($_GET['user_id']) || !isset($_GET['question'])) {
    die("Invalid request.");
}

$userId = $_GET['user_id'];
$securityQuestion = $_GET['question'];

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $answer = $_POST['answer'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "cloth");

    // Check connection
    if ($conn->connect
