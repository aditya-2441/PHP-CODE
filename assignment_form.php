<?php include 'header.php'; ?>

<?php
$old = [
    'student_name' => '',
    'roll_number' => '',
    'email' => '',
    'assignment_title' => '',
    'subject' => []
];
if (!empty($_SESSION['form_old'])) {
    $old = array_merge($old, $_SESSION['form_old']);
    unset($_SESSION['form_old']);
}
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);
?>

<div class="card">
    <h1>Online Assignment Submission System</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-msg">
            <strong>Please fix the following errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form id="assignmentForm" action="submit_assignment.php" method="POST" enctype="multipart/form-data" novalidate>
        <div>
            <label for="student_name">Student Name:</label>
            <input type="text" id="student_name" name="student_name" value="<?php echo esc($old['student_name']); ?>" required>
        </div>
        <div>
            <label for="roll_number">Roll Number:</label>
            <input type="text" id="roll_number" name="roll_number" required>
        </div>
        <div>
            <label for="email">Email ID:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="subject">Subject(s):</label>
            <select id="subject" name="subject[]" multiple size="5" required>
                <option value="Mathematics">Mathematics</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Physics">Physics</option>
                <option value="Chemistry">Chemistry</option>
                <option value="English">English</option>
            </select>
            <p><small>Use Ctrl/Cmd + click to select multiple subjects.</small></p>
        </div>
        <div>
            <label for="assignment_title">Assignment Title:</label>
            <input type="text" id="assignment_title" name="assignment_title" required>
        </div>
        <div>
            <label for="assignment_file">Assignment File:</label>
            <input type="file" id="assignment_file" name="assignment_file" accept=".pdf,.doc,.docx,.zip" required>
        </div>
        <div>
            <button type="submit">Submit Assignment</button>
        </div>
    </form>
</div>

<div class="card">
    <h2>Form Help</h2>
    <ul>
        <li>Select one or more subjects.</li>
        <li>Allowed file types: .pdf, .doc, .docx, .zip</li>
        <li>Maximum file size: 10MB (browser-enforced client-side check).</li>
    </ul>
</div>

<script>
    document.getElementById('assignmentForm').addEventListener('submit', function(event) {
        const fileInput = document.getElementById('assignment_file');
        if (!fileInput.value) {
            alert('Please choose an assignment file to upload.');
            event.preventDefault();
            return;
        }

        const allowed = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'];
        const file = fileInput.files[0];
        if (file && !allowed.includes(file.type)) {
            alert('File type not allowed. Use .pdf, .doc, .docx, or .zip.');
            event.preventDefault();
            return;
        }

        const maxSize = 10 * 1024 * 1024;
        if (file && file.size > maxSize) {
            alert('File is larger than 10MB. Please upload a smaller file.');
            event.preventDefault();
        }
    });
</script>

<?php include 'footer.php'; ?>