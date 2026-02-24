<?php
$num1=29;
$num2=95;
$num3=672;
if($num1>$num2 && $num1>$num3)
{
    echo "$num1, is largest among $num1,$num2 & $num3";
}
else
{
if($num2>$num1 && $num2>$num3)
{
    echo "$num2 is largest among $num1,$num2 & $num3";
}
else
    echo "$num3, is largest among $num1,$num2 & $num3";
}
?>