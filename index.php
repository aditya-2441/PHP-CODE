<?php
session_start();

// 1. Student Data Processing: Define Constants
define("MAX_MARKS", 500);
define("PASS_MARKS", 40);

// 5. Function Implementation: Call by Value
function calculatePercentage($totalMarks) {
    return ($totalMarks / MAX_MARKS) * 100;
}

// 5. Function Implementation: Call by Reference
function updateAttendance(&$attendancePercent, $classesConducted, $classesAttended) {
    if ($classesConducted > 0) {
        $attendancePercent = ($classesAttended / $classesConducted) * 100;
    } else {
        $attendancePercent = 0;
    }
}

// Handle form submissions to add students, reset, or download CSV
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- NEW: CSV Export Logic ---
    if (isset($_POST['download_csv']) && !empty($_SESSION['students'])) {
        error_reporting(0);
        // Set headers to force download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="student_performance_records.csv"');
        
        // Open the output stream
        $output = fopen('php://output', 'w');
        
        // Output the column headings

        fputcsv($output, array('Name', 'Roll No', 'Total Marks', 'Percentage', 'Grade', 'Remarks', 'Attendance %', 'Eligibility', 'Status'), ",", '"', "");
        
        // Process and output each student's data
        foreach ($_SESSION['students'] as $student) {
            $marks = $student['marks'];
            $totalMarks = 0;
            $subjectFailed = false;

            for ($k = 0; $k < count($marks); $k++) {
                $totalMarks += $marks[$k]; 
                if ($marks[$k] < PASS_MARKS) { 
                    $subjectFailed = true;
                }
            }

            $percentage = calculatePercentage($totalMarks);
            $attendancePercent = 0;
            updateAttendance($attendancePercent, $student['conducted'], $student['attended']);

            $grade = "";
            if ($subjectFailed || $percentage < 40) { $grade = "Fail"; } 
            elseif ($percentage >= 90) { $grade = "A+"; } 
            elseif ($percentage >= 75) { $grade = "A"; } 
            elseif ($percentage >= 60) { $grade = "B"; } 
            elseif ($percentage >= 50) { $grade = "C"; } 
            else { $grade = "Fail"; }

            $remarks = "";
            switch ($grade) {
                case "A+": $remarks = "Excellent"; break; 
                case "A": $remarks = "Very Good"; break; 
                case "B": $remarks = "Good"; break; 
                case "C": $remarks = "Average"; break; 
                case "Fail": $remarks = "Needs Improvement"; break; 
                default: $remarks = "Unknown";
            }

            $attendanceEligibility = ($attendancePercent >= 75) ? "Eligible" : "Not Eligible"; 
            $finalStatus = ($grade == "Fail" || $attendanceEligibility == "Not Eligible") ? "Fail" : "Pass";

            // Write row to CSV
            fputcsv($output, array(
                $student['name'],
                $student['roll'],
                $totalMarks,
                number_format($percentage, 2) . '%',
                $grade,
                $remarks,
                number_format($attendancePercent, 2) . '%',
                $attendanceEligibility,
                $finalStatus
            ), ",", '"', "");
        }
        fclose($output);
        exit(); // Stop script execution so HTML doesn't get appended to the CSV
    }
    // --- End CSV Logic ---

    // Add Student Logic
    elseif (isset($_POST['add_student'])) {
        $student = [
            'name' => htmlspecialchars($_POST['name']),
            'roll' => htmlspecialchars($_POST['roll']),
            'marks' => [
                (int)$_POST['sub1'], (int)$_POST['sub2'], (int)$_POST['sub3'], (int)$_POST['sub4'], (int)$_POST['sub5']
            ],
            'conducted' => (int)$_POST['conducted'],
            'attended' => (int)$_POST['attended']
        ];
        
        if (!isset($_SESSION['students'])) {
            $_SESSION['students'] = [];
        }
        $_SESSION['students'][] = $student;
    } 
    // Clear Data Logic
    elseif (isset($_POST['clear_data'])) {
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance Evaluation System</title>
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --danger-color: #ef4444;
            --danger-hover: #dc2828;
            --success-color: #10b981;
            --success-hover: #059669;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
        }

        body { 
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; 
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0; 
            padding: 30px 15px; 
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container { width: 100%; max-width: 1000px; }
        h2, h3 { color: var(--text-main); margin-top: 0; }
        .header-title { text-align: center; margin-bottom: 30px; color: var(--primary-color); }

        .card {
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 25px;
            margin-bottom: 30px;
        }

        /* Form Grid System */
        .form-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group { display: flex; flex-direction: column; }
        .form-group label {
            font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 5px;
        }

        input { 
            padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 6px; 
            font-size: 1rem; transition: all 0.2s;
        }

        input:focus {
            outline: none; border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        /* Buttons */
        .button-group { display: flex; gap: 15px; margin-top: 10px; }
        button {
            padding: 12px; border: none; border-radius: 6px; font-size: 1rem;
            font-weight: 600; cursor: pointer; transition: background-color 0.2s, transform 0.1s;
        }
        .btn-flex { flex: 1; }
        button:active { transform: translateY(1px); }

        .btn-primary { background-color: var(--primary-color); color: white; }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-danger { background-color: var(--danger-color); color: white; }
        .btn-danger:hover { background-color: var(--danger-hover); }
        .btn-success { background-color: var(--success-color); color: white; padding: 8px 16px; font-size: 0.9rem;}
        .btn-success:hover { background-color: var(--success-hover); }

        /* Table Header Flex */
        .table-header-row {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;
        }
        .table-header-row h3 { margin-bottom: 0; }

        /* Table Styles */
        .table-responsive { overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; min-width: 800px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { 
            background-color: #f8fafc; font-weight: 600; color: var(--text-muted);
            text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;
        }
        tr:hover { background-color: #f8fafc; }

        /* Status Badges */
        .badge {
            padding: 4px 8px; border-radius: 999px; font-size: 0.85rem;
            font-weight: bold; display: inline-block; text-align: center;
        }
        .pass { background-color: #dcfce7; color: #166534; }
        .fail { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="header-title">Student Performance Evaluation System</h2>
        
        <div class="card">
            <form method="post" action="">
                <h3>Enter Student Details</h3>
                
                <div class="form-section">
                    <div class="form-group">
                        <label>Student Name</label>
                        <input type="text" name="name" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" name="roll" placeholder="e.g. 101" required>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-group"><label>Subject 1</label><input type="number" name="sub1" min="0" max="100" required></div>
                    <div class="form-group"><label>Subject 2</label><input type="number" name="sub2" min="0" max="100" required></div>
                    <div class="form-group"><label>Subject 3</label><input type="number" name="sub3" min="0" max="100" required></div>
                    <div class="form-group"><label>Subject 4</label><input type="number" name="sub4" min="0" max="100" required></div>
                    <div class="form-group"><label>Subject 5</label><input type="number" name="sub5" min="0" max="100" required></div>
                </div>

                <div class="form-section">
                    <div class="form-group">
                        <label>Total Classes Conducted</label>
                        <input type="number" name="conducted" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Classes Attended</label>
                        <input type="number" name="attended" min="0" required>
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="add_student" class="btn-primary btn-flex">Add Student Record</button>
                    <button type="submit" name="clear_data" class="btn-danger btn-flex" formnovalidate>Clear All Records</button>
                </div>
            </form>
        </div>

        <?php
        if (!empty($_SESSION['students'])) {
            echo "<div class='card'>";
            
            // Flex container for the title and the export button
            echo "<div class='table-header-row'>";
            echo "<h3>Performance Records</h3>";
            echo "<form method='post' action=''>";
            echo "<button type='submit' name='download_csv' class='btn-success'>Download CSV</button>";
            echo "</form>";
            echo "</div>";

            echo "<div class='table-responsive'>";
            echo "<table>";
            echo "<tr><th>Name</th><th>Roll No</th><th>Total Marks</th><th>Percentage</th><th>Grade</th><th>Remarks</th><th>Attendance %</th><th>Eligibility</th><th>Status</th></tr>";

            $students = $_SESSION['students'];
            $studentCount = count($students);
            $processedResults = [];
            $j = 0;
            
            // 4. Looping Implementation: do-while loop
            if ($studentCount > 0) {
                do { 
                    $currentStudent = $students[$j];
                    $marks = $currentStudent['marks'];
                    $totalMarks = 0;
                    $subjectFailed = false;

                    // 4. Looping Implementation: for loop
                    for ($k = 0; $k < count($marks); $k++) {
                        $totalMarks += $marks[$k]; 
                        if ($marks[$k] < PASS_MARKS) { 
                            $subjectFailed = true;
                        }
                    }

                    $percentage = calculatePercentage($totalMarks);
                    $attendancePercent = 0;
                    updateAttendance($attendancePercent, $currentStudent['conducted'], $currentStudent['attended']);

                    // 3. Decision-Making Logic: if-elseif ladder
                    $grade = "";
                    if ($subjectFailed || $percentage < 40) { 
                        $grade = "Fail";
                    } elseif ($percentage >= 90) { 
                        $grade = "A+";
                    } elseif ($percentage >= 75) { 
                        $grade = "A";
                    } elseif ($percentage >= 60) { 
                        $grade = "B";
                    } elseif ($percentage >= 50) { 
                        $grade = "C";
                    } else {
                        $grade = "Fail";
                    }

                    // 3. Decision-Making Logic: switch statement
                    $remarks = "";
                    switch ($grade) {
                        case "A+": $remarks = "Excellent"; break; 
                        case "A": $remarks = "Very Good"; break; 
                        case "B": $remarks = "Good"; break; 
                        case "C": $remarks = "Average"; break; 
                        case "Fail": $remarks = "Needs Improvement"; break; 
                        default: $remarks = "Unknown";
                    }

                    $attendanceEligibility = ($attendancePercent >= 75) ? "Eligible" : "Not Eligible"; 
                    $finalStatus = ($grade == "Fail" || $attendanceEligibility == "Not Eligible") ? "Fail" : "Pass";

                    $processedResults[] = [
                        'name' => $currentStudent['name'],
                        'roll' => $currentStudent['roll'],
                        'total' => $totalMarks,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'remarks' => $remarks,
                        'att_percent' => $attendancePercent,
                        'eligibility' => $attendanceEligibility,
                        'status' => $finalStatus
                    ];
                    $j++;
                } while ($j < $studentCount);
            }

            // 4. Looping Implementation: while loop
            $m = 0;
            while ($m < count($processedResults)) {
                $res = $processedResults[$m];
                $statusClass = ($res['status'] == 'Pass') ? 'pass' : 'fail'; 
                
                echo "<tr>";
                echo "<td>{$res['name']}</td>";
                echo "<td>{$res['roll']}</td>";
                echo "<td>{$res['total']} / " . MAX_MARKS . "</td>";
                echo "<td>" . number_format($res['percentage'], 2) . "%</td>";
                echo "<td><strong>{$res['grade']}</strong></td>";
                echo "<td>{$res['remarks']}</td>";
                echo "<td>" . number_format($res['att_percent'], 2) . "%</td>";
                echo "<td>{$res['eligibility']}</td>";
                echo "<td><span class='badge {$statusClass}'>{$res['status']}</span></td>"; 
                echo "</tr>";
                
                $m++;
            }
            echo "</table>";
            echo "</div>"; // End table-responsive
            echo "</div>"; // End card
        }
        ?>
    </div>
</body>
</html>