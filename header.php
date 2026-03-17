<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function esc($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="container">
        <div class="card">
            <nav class="site-nav">
                <a class="button-link" href="index.php">Home</a>
                <a class="button-link" href="assignment_form.php">Student Form</a>
                <a class="button-link" href="teacher_dashboard.php">Teacher Dashboard</a>
                <a class="button-link" href="archive_assignments.php">Archive Cleanup</a>
            </nav>
        </div>
