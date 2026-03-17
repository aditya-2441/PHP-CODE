<?php include 'header.php'; ?>

<div class="card">
    <h1>Teacher Assignment List</h1>

    <?php
    $assignmentsDir = __DIR__ . '/Assignments';
    if (!is_dir($assignmentsDir)) {
        echo '<p>No assignments have been uploaded yet.</p>';
    } else {
        $files = array_values(array_filter(scandir($assignmentsDir), function ($f) {
            return !in_array($f, ['.', '..']) && is_file(__DIR__ . '/Assignments/' . $f);
        }));

        if (empty($files)) {
            echo '<p>No assignment files available.</p>';
        } else {
            echo '<table border="1" cellpadding="5" cellspacing="0">';
            echo '<tr><th>#</th><th>File</th><th>Action</th></tr>';
            foreach ($files as $idx => $file) {
                $safe = urlencode($file);
                echo '<tr>';
                echo '<td>' . ($idx + 1) . '</td>';
                echo '<td>' . htmlspecialchars($file) . '</td>';
                echo '<td><a href="download_assignment.php?file=' . $safe . '">Download</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    $recordFile = __DIR__ . '/submission_record.txt';
    if (is_file($recordFile)) {
        echo '<h2>Submission Records</h2>';
        echo '<pre>' . htmlspecialchars(file_get_contents($recordFile)) . '</pre>';
    }
    ?>

            <p><a class="button-link" href="index.php">Back to Home</a></p>
</div>

<?php include 'footer.php'; ?>