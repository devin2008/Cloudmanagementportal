<?php
include'main.php';

function get_domain_by_name($name) 
    { global $conn;
        $tmp = libvirt_domain_lookup_by_name($conn, $name);
        return ($tmp) ? $tmp : 0;
    }

$t=libvirt_list_storagepools($conn);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<style>
			#submit-button {
				margin-left: 650px;
				padding: 20px;
			}
		</style>
	</head>
	<body>
		<left>
			<form action="createvm.php" method="POST">
				<div class="col-md-4">
					<label>Name of the virtual machine</label>
				</div>
				<div class="col-md-5">
					<input type="text" class="form-control" name="vmname"/>
				</div>
				<br /><br /> 
				<div class="col-md-4">
					<label>Select Pool</label>
				</div>
				<div class="col-md-5">
					<select name="pool" class="form-control">
						<?php

						for ($i = 0; $i < sizeof($t); $i++)
						      {	
								$pname[$i]=$t[$i];
							echo "<option value=".$pname[$i].">".$pname[$i]."</option>";
						}
						?>	
					</select>
				</div>
				<br/><br/>
				<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>Choose RAM (Value Selected is in GB)</label>
				</div>
				<div class="col-md-5">
					<select name="ram" class="form-control">
						<option value="2">1</option>
						<option value="3">2</option>
						<option value="4">3</option>
					</select>
				</div>
				<br/><br/>
				<div class="col-md-12"></div>
				<div class="col-md-4">
					<label>Operating System</label>
				</div>
				<div class="col-md-5">
					<select name="os" class="form-control">
						<option value="Ubuntu">Ubuntu</option>
						<option value="Linux Mint">Linux Mint</option>
						<option value="Windows 7">Windows 7</option>
						<option value="Windows 8">Windows 8</option>
						<option value="Windows 8.1">Windows 8.1</option>
					</select>
				</div>	

  				<div class="c4">	
				</div>
				<br /><br />
				<div class="col-md-12"></div>
	  			<div class="c5 col-md-4">
					<label>Choose Disk Size (Value Selected is in GB)</label>
				</div>
				<div class="col-md-5">
					<select name="disk" class="form-control">
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
							<option value="8">8</option><option value="10">10</option><option value="6">15</option>
					</select>
				</div>
				<br/><br/>
				<div class="col-md-12"></div>
				<div class="c6 col-md-4">
					<label>Iso Image Path</label>
				</div>
				<div class="col-md-5">
					<input type="text" class="form-control" name="path" />
				</div>
				<br/><br/>
				<div class="col-md-12"></div>
				<div class="c7 col-md-4">
				<label>CPU CORES:</label></div>
				<div class="col-md-5">
					<select name="cpucores"  class="form-control">
						<option value="1">2</option>
						<option value="2">3</option>
						<option value="3">4</option>
						
					</select>
				</div>
				
				<br /><br />
				<div class="col-md-12"></div>
				<div class="col-md-2" id="submit-button">		
					<input type="submit" class="btn btn-primary" value="Create"/>
				</div>        
			</form>
		</left>
	</body>
</html>
