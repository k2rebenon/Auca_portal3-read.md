<?php
// Database connection + session bootstrap. Edit credentials for your server.
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "auca_portal";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
