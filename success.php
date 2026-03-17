<?php include 'header.php'; ?>

<div class="card">
    <h1>Assignment submitted successfully</h1>

    <?php
            $name = htmlspecialchars($_GET['name'] ?? '');
    $title = htmlspecialchars($_GET['title'] ?? '');
    $date = htmlspecialchars($_GET['date'] ?? '');
    $time = htmlspecialchars($_GET['time'] ?? '');
    $mail = $_GET['success'] ?? '0';
    ?>

    <p>Thank you, <strong><?php echo $name ?: 'student'; ?></strong>.</p>
    <p>Assignment: <strong><?php echo $title ?: '-'; ?></strong></p>
    <p>Submitted on: <strong><?php echo $date ?: '-'; ?></strong> at <strong><?php echo $time ?: '-'; ?></strong></p>

    <?php if ($mail === '1'): ?>
        <p>A confirmation email has been sent to your email address.</p>
    <?php else: ?>
        <p>Unable to send email from this PHP environment. Please check your PHP configuration (sendmail settings).</p>
    <?php endif; ?>

    <p><a class="button-link" href="assignment_form.php">Submit another assignment</a></p>
    <p><a class="button-link" href="teacher_dashboard.php">Go to Teacher Dashboard</a></p>
    <p><a class="button-link" href="index.php">Back to Home</a></p>
</div>

<?php include 'footer.php'; ?>