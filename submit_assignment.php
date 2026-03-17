<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: assignment_form.php');
    exit;
}

$name = trim($_POST['student_name'] ?? '');
$roll = trim($_POST['roll_number'] ?? '');
$email = trim($_POST['email'] ?? '');
$assignmentTitle = trim($_POST['assignment_title'] ?? '');
$subjects = $_POST['subject'] ?? [];

if (!is_array($subjects)) {
    $subjects = [$subjects];
}

$errors = [];

if ($name === '' || $roll === '' || $email === '' || $assignmentTitle === '' || empty($subjects)) {
    $errors[] = 'All fields are required.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
}

if (!isset($_FILES['assignment_file']) || $_FILES['assignment_file']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Assignment file upload failed.';
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_old'] = [
        'student_name' => $name,
        'roll_number' => $roll,
        'email' => $email,
        'assignment_title' => $assignmentTitle,
        'subject' => $subjects,
    ];
    header('Location: assignment_form.php');
    exit;
}

$uploadDir = __DIR__ . '/Assignments';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
    die('Unable to create upload directory.');
}

$fileInfo = pathinfo($_FILES['assignment_file']['name']);
$fileBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileInfo['filename']);
$fileExt = isset($fileInfo['extension']) ? strtolower($fileInfo['extension']) : '';
if ($fileExt === '') {
    $errors[] = 'Uploaded file must have an extension.';
}

$safeRoll = preg_replace('/[^a-zA-Z0-9_-]/', '_', $roll);
$targetFilename = $safeRoll . '_' . $fileBase;
if ($fileExt !== '') {
    $targetFilename .= '.' . $fileExt;
}
$targetPath = $uploadDir . '/' . $targetFilename;

if (!move_uploaded_file($_FILES['assignment_file']['tmp_name'], $targetPath)) {
    die('Unable to move uploaded file.');
}

$subjectText = implode(', ', array_map('trim', $subjects));
$date = date('j M Y');
$time = date('h:i A');

$recordFile = __DIR__ . '/submission_record.txt';
$recordLine = sprintf(
    "%s|%s|%s|%s|%s|%s|%s|%s\n",
    $date,
    $time,
    $name,
    $roll,
    $email,
    $subjectText,
    $assignmentTitle,
    $targetFilename
);

if ($fp = fopen($recordFile, 'a')) {
    fwrite($fp, $recordLine);
    fclose($fp);
}

$subjectEmail = 'Assignment Submission Confirmation';
$message = "Dear $name,\n\nYour assignment has been submitted successfully.\n\n" .
    "Assignment Title: $assignmentTitle\n" .
    "Subject(s): $subjectText\n" .
    "Submission Date: $date\n" .
    "Submission Time: $time\n\n" .
    "Thank you.\n";
$headers = 'From: no-reply@college.edu';
$mailSuccess = mail($email, $subjectEmail, $message, $headers);

$queryParams = http_build_query([
    'name' => $name,
    'title' => $assignmentTitle,
    'date' => $date,
    'time' => $time,
    'success' => $mailSuccess ? '1' : '0'
]);

header('Location: success.php?' . $queryParams);
exit;
