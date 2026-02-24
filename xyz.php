<html>
<body>
<title></title>
</head>
<body>
<form method= 'POST' >
<h2>Please input your name :</h2>
<input type="text" name="name">
<input type="submit" value="Submit Name"></form>
<?php
//Retrieve name from query string and store to a local variable
if(isset($_POST['name'])){
    $name = $_POST ['name'];
echo "<h3> Hello $name </h3>";}
?>
</body>
</html>