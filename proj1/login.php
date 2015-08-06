<?php
	include('loginpres.php');
?>
<html>
	<title>Log In</title>
	<head>
		<style>
			.box {
				border: 1px solid rgba(0, 0, 0, 0.5);
				border-radius: 5px;
				padding: 20px;
			}
			.container {
				padding: 20px;
			}
			#form-user-login {
				padding-top: 20px;
			}
			#signin-btn {
				margin-left: 200px;
			}
		</style>
	</head>

	<body>
<form action="logindetail.php" method="POST">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<!--This is where you put the code for displaying the image like you wanted-->
				</div>
				<div class="col-md-6 box">
					<form id="form-user-login" class="pageForm" method="post" action="signup.php" role="form">
						<div class="col-md-3"
							<label><h4>Username</h4></label>
						</div>
						<div class="col-md-6">
							<input type="text" class="form-control" placeholder="Username" name="user"></input>
						</div>
						<div class="col-md-12"></div>
						<div class="col-md-3">
							<label><h4>Password</h4></label>
						</div>
						<div class="col-md-6">
							<input type="password" class="form-control" name="pass"></input>
						</div>
						<div class="col-md-12"></div>
						<div class="col-md-2" id="signin-btn">
							<button class="btn btn-primary">Sign In</button>
						</div>
						<div class="col-md-2" id="signup-btn">
							<a class="btn btn-success" href="signup.php">Sign Up</a>
						</div>
					</form>
				</div>
			</div>
		</div>
</form>	
</body>
</html>
