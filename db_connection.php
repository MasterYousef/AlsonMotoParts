<?php

// Create connection
$conn = new mysqli("localhost","yousef","1212@#@#$", "alson");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>