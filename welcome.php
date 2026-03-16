<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
</head>
<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $city = htmlspecialchars($_REQUEST['cityName']);
        echo "<h1>Welcome to " . $city . "!</h1>";   
    } else { 
        echo "<h1>Please submit the form first.</h1>";
    }
    ?>
    <br>
    <a href="index.php">Go Back</a>
</body>
</html>