
<?php

 $uri="qemu:///system";

 function lasterror()
    { $lasterror = libvirt_get_last_error();
        return false;
 }
$credentials=array(VIR_CRED_AUTHNAME=>"divyashish",VIR_CRED_PASSPHRASE=>"qwerty123");
$conn=libvirt_connect($uri,false,$credentials);
if ($conn==false)
	{ echo ("Libvirt last error: ".lasterror()."\n\n\n");
		exit;
	}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<title>My Cloud</title>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.3.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		
		<style>
			.header {
				background-color: #222222;
				color: #D2D2D1;
				padding: 40px;
			}
			h1 {
				text-align: left;
				padding: 0 !important;
				margin: 0 !important;
			}
			li-a {
				color: #00478F;
			}
		</style>
	</head>
	<body>
		<div class="header"><h1>My Cloud Manager</h1></div>
		<nav class="navbar navbar-inverse" id="navigation">
			<div class="container-fluid">
		  
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar" aria-expanded="false">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		    </div>

		  
		    <div class="collapse navbar-collapse" id="bs-navbar">
		      <ul class="nav navbar-nav">
		        <li><a href="home.php">Home <span class="sr-only">(current)</span></a></li>
		        <li><a href="dashboard.php">Dashboard</a></li>
		        <li><a href="createvmhome.php">Create VM</a></li>
			<li><a href="editvm.php">Edit VM</a></li>		        
			<li><a href="delvmhome.php">Delete VM</a></li>
		        <li><a href="imagemanage.php">Images</a></li>
			
		      </ul>
		      <ul class="nav navbar-nav navbar-right">
        		
        	  </ul>
		    </div>
		  </div>
		</nav>
	</body>
</html>

