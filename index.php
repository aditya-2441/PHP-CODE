<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite City Form</title>
</head>
<body>

    <h2>Which is your favorite city?</h2>
    
    <form action="welcome.php" method="POST">
        <label for="city">Enter your favorite city:</label><br><br>
        <input type="text" id="city" name="cityName" required><br><br>
        <input type="submit" value="Submit">
    </form>

</body>
</html>