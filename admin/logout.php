<!DOCTYPE HTML PUBliC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<title>Logout</title>
</head>
<body>
<?php
@session_start();

unset($_SESSION['empid']);
unset($_SESSION['emp_name']);
unset($_SESSION['emp_id']);
unset($_SESSION['activated']);
unset($_SESSION['br_id']);
unset($_SESSION['d_session']);
unset($_SESSION['br_name']);
unset($_SESSION['access_level']);


echo "<script> location.href='index.php'</script>";

?>
</body>
</html>
