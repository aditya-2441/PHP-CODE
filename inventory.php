<?php
// Start the session to remember data between button clicks without a database
session_start();

// 1. Initialize Default Arrays (Only runs the first time the page loads)
if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [
        "Laptop" => 55000,
        "Mobile" => 20000,
        "Headphones" => 1500,
        "Keyboard" => 1200,
        "Mouse" => 600
    ];
}

// Default states for interactive features
if (!isset($_SESSION['discount'])) {
    $_SESSION['discount'] = 10; // Default 10% off
}

$searchResult = "";
$stringDemoResult = "";

// 2. Handle Form Submissions (Interactivity)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Feature A: Add a New Product
    if (isset($_POST['action']) && $_POST['action'] == "add_product") {
        $newName = htmlspecialchars(trim($_POST['new_name']));
        $newPrice = floatval($_POST['new_price']);
        if (!empty($newName) && $newPrice > 0) {
            $_SESSION['inventory'][$newName] = $newPrice;
        }
    }

    // Feature B: Update Global Discount
    if (isset($_POST['action']) && $_POST['action'] == "update_discount") {
        $_SESSION['discount'] = floatval($_POST['discount_rate']);
    }

    // Feature C: Search for a Product
    if (isset($_POST['action']) && $_POST['action'] == "search") {
        $query = htmlspecialchars(trim($_POST['search_query']));
        $found = false;
        // Search using strpos (case-insensitive via stripos)
        foreach (array_keys($_SESSION['inventory']) as $name) {
            if (stripos($name, $query) !== false) {
                $price = $_SESSION['inventory'][$name];
                $searchResult = "<span style='color:green;'>Found: <strong>$name</strong> at ₹$price</span>";
                $found = true; 
                break;
            }
        }
        if (!$found) {
            $searchResult = "<span style='color:red;'>Product '$query' not found.</span>";
        }
    }

    // Feature D: Live String Manipulator
    if (isset($_POST['action']) && $_POST['action'] == "string_demo") {
        $text = htmlspecialchars($_POST['demo_text']);
        $target = htmlspecialchars($_POST['target_word']);
        $replace = htmlspecialchars($_POST['replace_word']);
        
        $upper = strtoupper($text);
        $lower = strtolower($text);
        $cap = ucfirst($text);
        $len = strlen($text);
        $replaced = str_replace($target, $replace, $text);

        $stringDemoResult = "
            <strong>Uppercase:</strong> $upper <br>
            <strong>Lowercase:</strong> $lower <br>
            <strong>Capitalized:</strong> $cap <br>
            <strong>Length:</strong> $len characters <br>
            <strong>Modified String:</strong> $replaced
        ";
    }
    
    // Feature E: Reset Data
    if (isset($_POST['action']) && $_POST['action'] == "reset") {
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// 3. Prepare Data for Display
$productNames = array_keys($_SESSION['inventory']);
$totalProducts = count($productNames);
$totalInventoryValue = array_sum($_SESSION['inventory']);
$currentDiscount = $_SESSION['discount'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fully Interactive Inventory</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background-color: #f8f9fa; }
        .container { display: flex; gap: 20px; flex-wrap: wrap; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1; min-width: 300px; }
        .full-width { width: 100%; flex: 100%; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        .expensive { background-color: #ffe6e6; font-weight: bold; color: #d9534f; }
        input[type="text"], input[type="number"] { padding: 8px; width: 90%; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 8px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; }
        h2, h3 { color: #333; margin-top: 0; }
    </style>
</head>
<body>

    <h2>Product Inventory Dashboard (Interactive)</h2>

    <div class="container">
        <div class="card">
            <h3>Control Panel</h3>
            
            <form method="POST">
                <input type="hidden" name="action" value="add_product">
                <strong>Add New Product:</strong><br>
                <input type="text" name="new_name" placeholder="Product Name" required>
                <input type="number" name="new_price" placeholder="Price" required>
                <button type="submit">Add Product</button>
            </form>
            <hr>
            
            <form method="POST">
                <input type="hidden" name="action" value="update_discount">
                <strong>Set Global Discount (%):</strong><br>
                <input type="number" name="discount_rate" value="<?php echo $currentDiscount; ?>" required>
                <button type="submit">Apply Discount</button>
            </form>
            <hr>

            <form method="POST">
                <input type="hidden" name="action" value="search">
                <strong>Search Inventory:</strong><br>
                <input type="text" name="search_query" placeholder="Search..." required>
                <button type="submit">Search</button>
            </form>
            <p><?php echo $searchResult; ?></p>
        </div>

        <div class="card" style="flex: 2;">
            <h3>Current Inventory (<?php echo $totalProducts; ?> items)</h3>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Original Price</th>
                    <th>Discounted Price (<?php echo $currentDiscount; ?>% Off)</th>
                </tr>
                <?php
                // Loop through associative array
                foreach ($_SESSION['inventory'] as $name => $price) { 
                    $discountedPrice = $price - ($price * ($currentDiscount / 100)); 
                    $highlightClass = ($price > 20000) ? "class='expensive'" : "";

                    echo "<tr $highlightClass>";
                    echo "<td>" . strtoupper($name) . "</td>";
                    echo "<td>₹" . $price . "</td>"; 
                    echo "<td>₹" . $discountedPrice . "</td>"; 
                    echo "</tr>";
                }
                ?>
            </table>
            <br>
            <p><strong>Total Inventory Value:</strong> ₹<?php echo $totalInventoryValue; ?></p>
            
            <form method="POST" style="margin-top: 20px;">
                <input type="hidden" name="action" value="reset">
                <button type="submit" class="btn-danger">Reset to Default Arrays</button>
            </form>
        </div>

        <div class="card full-width">
            <h3>Live String Manipulation Tester</h3>
            <form method="POST">
                <input type="hidden" name="action" value="string_demo">
                <input type="text" name="demo_text" placeholder="Enter a sentence (e.g., high quality laptop)" required style="width: 40%;">
                <input type="text" name="target_word" placeholder="Word to replace (e.g., high quality)" required style="width: 20%;">
                <input type="text" name="replace_word" placeholder="Replace with (e.g., premium quality)" required style="width: 20%;">
                <button type="submit">Test String Functions</button>
            </form>
            <div style="margin-top: 15px; padding: 10px; background: #e9ecef; border-radius: 5px;">
                <?php echo $stringDemoResult ? $stringDemoResult : "Submit the form to see string functions in action."; ?>
            </div>
        </div>
    </div>

</body>
</html>