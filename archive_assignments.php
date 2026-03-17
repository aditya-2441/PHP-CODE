<?php
function rrmdir($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            rrmdir($path);
        } else {
            unlink($path);
        }
    }

    return rmdir($dir);
}

$assignmentsDir = __DIR__ . '/Assignments';
if (!is_dir($assignmentsDir)) {
    echo 'No Assignments directory exists.';
    exit;
}

$deleted = 0;
$threshold = strtotime('-30 days');
$children = array_diff(scandir($assignmentsDir), ['.', '..']);

foreach ($children as $child) {
    $path = $assignmentsDir . '/' . $child;
    if (is_dir($path)) {
        $mtime = filemtime($path);
        if ($mtime !== false && $mtime < $threshold) {
            if (rrmdir($path)) {
                $deleted++;
            }
        }
    }
}

echo "Archive cleanup completed. Old folders deleted: $deleted.";
