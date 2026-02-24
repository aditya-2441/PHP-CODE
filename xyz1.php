<html>
<body>
<form method="POST">
Enter a number:
<input type="number" name="NUMBER">
<input type="submit" value="Submit">
</form>
<?php
if($_POST)
{
$number = $_POST['NUMBER'];
if(($number % 2) == 0)
{
echo "$number is an Even number";
}
else
{
echo "$number is Odd number";
}
}
?>
</body>
</html>