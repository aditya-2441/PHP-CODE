<html>
<head>
<title>Simple Calculator Program in PHP - Tutorials Class</title> 
</head>
<body>
<h1>PHP - Simple Calculator Program</h1>
<form action="" method="post">
<input type="number" name="first_num">
<input type="number" name="second_num">
<input type="submit" name="operator" value="Add" />
<input type="submit" name="operator" value="Subtract" />
<input type="submit" name="operator" value="Multiply" />
<input type="submit" name="operator" value="Divide" />
</form>

<?php
// FIX 2: Added isset() so PHP waits for the user to click a button
if (isset($_POST['operator'])) { 
    $first_num = $_POST['first_num'];
    $second_num = $_POST['second_num'];
    $operator = $_POST['operator'];
    $result = '';

    if (is_numeric($first_num) && is_numeric($second_num))
    {
        switch ($operator)
        {
            case "Add":
                $result = $first_num + $second_num;
                break;
            case "Subtract":
                $result = $first_num - $second_num;
                break;
            case "Multiply":
                $result = $first_num * $second_num;
                break;
            case "Divide":
                // FIX 3: Added a quick check to prevent crashing if dividing by zero
                if ($second_num == 0) {
                    $result = "Cannot divide by zero";
                } else {
                    $result = $first_num / $second_num;
                }
        }
    }
    echo "Result is $result";
} // Closes the isset() check
?>

</body>
</html>