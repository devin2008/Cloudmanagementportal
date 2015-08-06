<?php

include'main.php';

?>
<html>
	<head>
		<script type="text/javascript">
		    function submitForm(action)
		    {
		        document.getElementById('form1').action = action;
		        document.getElementById('form1').submit();
		    }
		</script>
	</head>
<body>
	<div class="container">
		<form class="pageForm" id="form1" name="form1" method="post" onsubmit="" onreset="" action="main.php">
			<div class="e2"> <input type="hidden" name="listvm" value="1"></div>
			<div class="e2">
				<div class="col-md-5">
					<label><h4>Enter the virtual machine name to delete</h4></label>
				</div>
				<div class="col-md-5">
					<input type="textarea" class="form-control" name="vmname">
				</div>
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-default" onclick="submitForm('delvm.php')">Delete Machine</button>
			</div>
			<div class="col-md-12"></div>
			<div class="e1">
				<div class="col-md-5">
					<label><h4>List of available virtual Machines</h4></label>
				</div>
				<div class="col-md-4">
					<button type="button" class="btn btn-default" name="listvm" onclick="submitForm('domlist.php')" >View machines</button>
				</div>
			</div>
		</form>
	</div>
</body>
<html>


