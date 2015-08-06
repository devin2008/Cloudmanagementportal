<?php
	include('loginpres.php');
?>

	<head>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<style>
			.box {
				border: 1px solid rgba(0, 0, 0, 0.5);
				border-radius: 5px;
				padding: 20px;
				margin-right: 200px;
			}
			.container {
				padding: 20px;
			}
			#submit-btn {
				margin-left: 700px;
				padding: 10px;
				position: relative;
			}
		</style>
	</head>
		<div class="container">
			<h2>Register</h2>
			<div class="container box">
				<form id="form-user-signup" class="pageForm" method="post" action="signupp.php" role="form">
					<div class="col-md-2"
						<label><h4>Name</h4></label>
					</div>
					<div class="col-md-8">
						<input type="text" class="form-control" placeholder="Full Name" name="name"></input>
					</div>
					<div class="col-md-12"></div>
					<div class="col-md-2">
						<label><h4>Email</h4></label>
					</div>
					<div class="col-md-8">
						<input type="text" class="form-control" placeholder="email@example.com" name="email"></input>
					</div>
					<div class="col-md-12"></div>
					<div class="col-md-2">
						<label><h4>Username</h4></label>
					</div>
					<div class="col-md-8">
						<input type="text" class="form-control" placeholder="Username" name="user"></input>
					</div>
					<div class="col-md-12"></div>
					<div class="col-md-2">
						<label><h4>Password</h4></label>
					</div>
					<div class="col-md-8">
						<input type="password" class="form-control" name="pass"></input>
					</div>
					<div class="col-md-12"></div>
					<div class="col-md-2">
						<label><h4>Confirm Password</h4></label>
					</div>
					<div class="col-md-8">
						<input type="password" class="form-control" name="cpass"></input>
					</div>
					<div class="col-md-2" id="submit-btn">
						<button class="btn btn-primary">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</body>

