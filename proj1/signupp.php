<?php
	include('loginpres.php');
	include('db.php');


$con=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("Failed to connect to MySQL: " . mysql_error());
$db=mysql_select_db(DB_NAME,$con) or die("Failed to connect to MySQL: " . mysql_error());

function newuser()
{
	$fullname = $_POST['name'];
	$userName = $_POST['user'];
	$email = $_POST['email'];
	$password =  $_POST['pass'];
	$query = "insert into signup(fullname,userName,email,pass) values ('$fullname','$userName','$email','$password')";
	$data = mysql_query ($query) or die(mysql_error());
echo $data;
	if($data)
	{
	echo "YOUR REGISTRATION IS COMPLETED...";
	}
	else{echo"error";}
}


function signup()
{
if(!empty($_POST['user']))   
{
	$u = mysql_query("SELECT userName FROM signup WHERE userName = '$_POST[user]' ") or die(mysql_error());
		
	if($_POST['user']!=$u){

		newuser();
   header("Refresh: 2; url=http://localhost/proj1/login.php");
	}
	else
	{
		echo "SORRY...YOU ARE ALREADY REGISTERED USER...";
		header("Refresh: 2; url=http://localhost/proj1/signup.php");
	}
}

}
	signup();

?>


