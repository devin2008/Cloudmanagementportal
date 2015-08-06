<?php

require('main.php');
function get_domain_by_name($name) 
    { global $conn;
        $tmp = libvirt_domain_lookup_by_name($conn, $name);
        return ($tmp) ? $tmp : 0;
    }
function domnamebyuuid($uuid) 
    { global $conn;
        $dom = libvirt_domain_lookup_by_uuid_string($conn, $uuid);
        if (!$dom)
            return false;
        $tmp = libvirt_domain_get_name($dom);
        return ($tmp) ? $tmp : lasterror();
    }
function getdomresource($nameRes) 
    {global $conn;
      $dom=libvirt_domain_lookup_by_name($conn, $nameRes);
        if (!$dom) 
	{
            return lasterror();
	}
	else{ return $dom;
       	 }
	
        return $dom;
}


function poolactive($pool, $name = false) 
    {$x=libvirt_storagepool_is_active($pool);
	if(!$x)
	{	return false;
	}
	else 
	{	return true;
		 }
    }
function getpoolresource($n) 
    {global $conn;
      $pres=libvirt_storagepool_lookup_by_name($conn, $n);
        if (!$pres) 
	{
            return lasterror();
	}
	else{ return $pres;
       	 }
	
        return $pres;
}
function pooldestroy($pool) 
    {
        $p = getpoolresource($pool);
        if (!$p)
            return false;
	 $t = libvirt_storagepool_destroy($p);
        return ($t) ? $t : lasterror();
    }
function voldel($volres) 
    {	global $conn,$pres;
	
	$sp=libvirt_storagepool_get_name($pres);
     	$volname=libvirt_storagevolume_get_name($volres);	
	$t=exec("sudo rm -f /var/images/$volname");	
	
	pooldestroy($sp);
	poolstart($sp);
	
    }
function poolundefine($pool) 
    { $p = getpoolresource($pool);
        if (!$p)
            return false;
	 $t = libvirt_storagepool_undefine($p);
        return ($t) ? $t : lasterror();
    }

function poolstart($pool) 
    {
        $p = getpoolresource($pool);
        if (!$p)
            return false;
	 $t = libvirt_storagepool_create($p);
        return ($t) ? $t : lasterror();
    }

function poolnameuuid($puuid) 
    { global $conn;
        $p = libvirt_storagepool_lookup_by_uuid_string($conn, $puuid);
        if (!$p)
            return false;
        $t = libvirt_storagepool_get_name($p);
        return ($t) ? $t : lasterror();
    }

function getvolres($pres, $l)
{$res=libvirt_storagevolume_lookup_by_name($pres, $l);
	return $res;}

function domaindiskadd($domain, $img, $dev='ide', $type='file', $driver='qcow2') 
    { $dom = getdomresource($domain);
	$t = libvirt_domain_disk_add($domain, $img, $dev, $type, $driver);
	
            return ($t) ? $t : lasterror();
    }

function domaindiskremove($domain, $dev='ide') 
    {
        $dom = getdomresource($domain);

        $t = libvirt_domain_disk_remove($dom, $dev);
        return ($t) ? $t :lasterror();
    }

$action = array_key_exists('action', $_GET) ? $_GET['action'] : '';
$sub = array_key_exists('sub', $_GET) ? $_GET['sub'] : '';
$ap = array_key_exists('ap', $_GET) ? $_GET['ap'] : '';
$openvol = array_key_exists('openvol', $_GET) ? $_GET['openvol'] : '';
$spn = array_key_exists('spn', $_POST) ? $_POST['spn'] : '';
$cap = array_key_exists('cap', $_POST) ? $_POST['cap'] : '';
$nv = array_key_exists('nv', $_GET) ? $_GET['nv'] : '';
$nvname = array_key_exists('nvname', $_POST) ? $_POST['nvname'] : '';
$vcap = array_key_exists('vcap', $_POST) ? $_POST['vcap'] : '';
$dn = array_key_exists('dn', $_GET) ? $_GET['dn'] : '';
$dname = array_key_exists('dname', $_POST) ? $_POST['dname'] : '';
$b=$openvol;

?>
 <style>
#but {margin-left: 30px;
padding: 10px; font-weight:bold;
width:150px;
color:white;
background-color:RoyalBlue;}
h2{margin-left: 25px;
padding: 5px;}
table { padding:50px;
font-family:Arial, Helvetica, sans-serif;
color:#666;
font-size:170%;
text-shadow: 1px 1px 0px #fff;
background:#eaebec;
margin:1%;
border:#ccc 1px solid;border-radius:6px;box-shadow: 0 4px 3px #d1d1d1;}

table th {padding:21px 25px 22px 25px;border-top:1px solid #fafafa;border-bottom:1px solid #e0e0e0;background: #ededed;background: }

table td {padding:25px 25px 22px 25px;border-top:1px solid #fafafa;border-bottom:1px solid #e0e0e0;background: #ededed;}</style> 
<h2>Storage Pools</h2></br>

<div class="col-md-2">
<button  class="btn btn-default" id="but"  onclick="javascript:location.href='imagemanage.php?ap=1&amp;'">Add Storage Pool</button>
</div>


<?php

if ($ap)
    {	
	if ($ap == $_GET['ap'])
	{
	if($spn&&$cap)
	   {	
		$newspnname=$_POST['spn'];
		$cap=$_POST['cap'];
		$poolxml="<pool type='dir'><name>$newspnname</name><capacity unit='GB'>$cap</capacity><allocation unit='GB'>$cap*0.5</allocation><available unit='GB'>$cap*0.5</available><target> <path>/var/images</path> <permissions><mode>0777</mode>  </permissions></target></pool>";

		$result=libvirt_storagepool_define_xml($conn, $poolxml, VIR_STORAGE_POOL_BUILD_NEW)? 'Pool has been successfully created' :'Cannot create pool : '.lasterror();
		$newpool = poolstart($spn) ? "Storage Pool Active" : 'Error : '.lasterror();
	echo "<pre>$result</pre>";	}
	else {
		$result = '<br/><br/>  <form method="POST">
			<br/><div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>Pool name:</label>
				</div>
			<div class="col-md-5">
					<input type="text" name="spn" class="form-control" />
				</div>
			<br /><br /> 

		<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>Size:(GB)</label>
				</div>

		<div class="col-md-5">
					  <select name="cap" class="form-control">
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="15">15</option>
						</select>
				</div>
                   <br /><br /> 	<br/><br/>
			
				<div class="col-md-2" style="margin-left: 70%;" id="submit-button">		
					<input type="submit" class="btn btn-primary" value="Create Pool"/>
				</div>			
 			</form>';
	echo "<pre>$result</pre>";}
		 }
	}
$t=libvirt_list_storagepools($conn);

if(!empty($t))
{	echo"<table ><tr><th>Pool</th><th>State</th><th>Total Volumes</th><th>Capacity</th><th>Allocation</th><th>Actions</th></tr>";
	 $result = false;
	if ($action)
 	{	$spname=poolnameuuid($_GET['puuid']);
	if ($action == 'poolstart') {$b=1;
                    $result = poolstart($spname) ? "<br>Storage Pool Active" : 'Error : '.lasterror();}
                else if ($action == 'pooldestroy') {$b=0;
                    $result =pooldestroy($spname) ? "</br>Storage Pool inactive" : 'Error : '.lasterror(); }
		else if ($action == 'poolundefine') {
                    $result = poolundefine($spname) ? " Pool Deleted" : 'Error : '.lasterror();}
	} 
 	for ($i = 0; $i < sizeof($t); $i++)
      {	$pname=$t[$i];$nvol="";$state="";
	$pres=libvirt_storagepool_lookup_by_name($conn, $pname);
	$sp=libvirt_storagepool_get_name($pres);
	if(libvirt_storagepool_is_active($pres))	
	{$nvol=libvirt_storagepool_get_volume_count($pres);}
	$puuid=	libvirt_storagepool_get_uuid_string($pres);
	$info=libvirt_storagepool_get_info($pres);
	$pl=libvirt_storagepool_is_active($pres);
	if(!$pl)
	{	$state="Inactive";
	}
	else 
	{	$state="Active";
		 }
	
	echo"<tr><td>$pname</td><td>$state</td><td>$nvol</td><td>".intval($info[capacity]/(1024*1024*1024))." GB</td><td>".intval($info[allocation]/(1024*1024*1024));
	
	echo" GB</td>";}
	echo "<td>";
  
	if (poolactive($pres, $pname))
		{ echo "<button onclick=\"javascript:location.href='imagemanage.php?action=pooldestroy&amp;puuid=$puuid'\">Deactivate</button>";
			}
	else if (!poolactive($pres, $pname))
              {      echo "<button  onclick=\"javascript:location.href='imagemanage.php?action=poolstart&amp;puuid=$puuid'\">Start</button> | <button onclick=\"javascript:location.href='imagemanage.php?action=poolundefine&amp;puuid=$puuid'\">Delete</button>";
            	}
         echo "</td></tr>";




	 ?>

        </table>

<h2>Volumes</h2>
<div class="col-md-2"></br>
<button  class="btn btn-default" id="but" onclick="javascript:location.href='imagemanage.php?nv=1&amp;'">Add Volume</button>
</div>
 

	<?php

//new volume

if ($nv)
    {	if ($nv == $_GET['nv'])
	{if($nvname&&$vcap)
	   {	
		$nvname=$_POST['nvname'];
		$vcap=$_POST['vcap'];

	$volxml="<volume type='file'>
        <name>$nvname</name>
        <allocation></allocation>
        <capacity unit='GB'>$vcap</capacity>
        <target>
          <path>/var/images/$nvname</path>
	<format type='qcow2'/>
          <permissions>
            <owner>107</owner>
            <group>107</group>
            <mode>0744</mode>
          </permissions>
        </target>
	</volume>";
	$result=libvirt_storagevolume_create_xml($pres, $volxml)? 'Volume has been succefully created' :'Cannot create vol : '.lasterror();
	}
	else 
	$result = '<br /><br />  <form method="POST">
			<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>Volume name:</label>
				</div>
			<div class="col-md-5">
					<input type="text" name="nvname" class="form-control" />
				</div>
			<br /><br /> 

		<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>Size:(GB)</label>
				</div>

		<div class="col-md-5">
					  <select name="vcap" class="form-control">
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="15">15</option>
						</select>
				</div>
                   <br /><br /> 	<br/><br/>
			
				<div class="col-md-2" style="margin-left: 70%;" id="submit-button">		
					<input type="submit" class="btn btn-primary" value="Create Volume"/>
				</div>			
 			</form>';
		      }
	}

		
//Addition of volume to domain
if ($dn)
    {	
	if ($dn == $_GET['dn'])
	
		$dname=$_POST['dname'];
	{if($nvname&&$dname)
	   {	
		$vres="";$volinfo="";$volname="";$cap="";$alloc="";$type="";
		$x=libvirt_storagepool_list_volumes($pres);
		  for($i = 0; $i < sizeof($x); $i++)
			
			{	$vres=getvolres($pres, $x[$i]);
				$volname=libvirt_storagevolume_get_name($vres);	
				if($volname==$nvname)
			      {	
				echo"$dname";
				$res = get_domain_by_name($dname);				
				$img=libvirt_storagevolume_get_path($vres);				
				$result=domaindiskadd($res, $img, $dev='ide', $type='file', $driver='qcow2')?'Volume has been successfully added to domain' :'Cannot add vol : '.lasterror();
				 }
						}
				}
	else 
	$result = '<br /><br />  <form method="POST">
			<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>Domain name:</label>
				</div>
			<div class="col-md-5">
					<input type="text" name="dname" class="form-control" />
				</div>
			<br /><br /> 

		<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>Volume Name: </label>
				</div>
			
			<div class="col-md-5">
					<input type="text" name="nvname" class="form-control" />
				</div>
              <br /><br /> 	<br/><br/>
			
				<div class="col-md-2" style="margin-left: 70%;" id="submit-button">		
					<input type="submit" class="btn btn-primary" value="ADD"/>
				</div>			
 			</form>';
		      }
	}


$q=libvirt_storagepool_is_active($pres);

//showing volumes 
if((!empty($q))&&(libvirt_storagepool_is_active($pres)))
		{ 	echo"<table><tr><th>Volume</th><th>Type</th><th>Capacity</th><th>Allocation</th><th>Action</th></tr>";
			$rt = false;
			if ($sub)
			 { $volname=$_GET['volname'];
		        	$r=libvirt_storagevolume_lookup_by_name($pres, $volname);
				
				if ($sub == 'addtodom')
				 {echo"$r in add";}
        	         	  else if ($sub == 'delvol')
				  	$rt =voldel($r)? "Volume deleted" : 'Error : '.lasterror(); 
					
			  } 
		$x=libvirt_storagepool_list_volumes($pres);
		$vres="";$volinfo="";$volname="";$cap="";$alloc="";$type="";
	
	for($i = 0; $i < sizeof($x); $i++)
	{	$vres=getvolres($pres, $x[$i]);
		$volname=libvirt_storagevolume_get_name($vres);	
		$volinfo=libvirt_storagevolume_get_info($vres);
		
	}		
		 for($i = 0; $i < sizeof($x); $i++)
		{	$vres=getvolres($pres, $x[$i]);
			
			$volname=libvirt_storagevolume_get_name($vres);	
			$volinfo=libvirt_storagevolume_get_info($vres);
							
			if ($volinfo[type]=2){$type=".qcow2";}
			echo"<tr><td>$volname</td><td>$type</td><td>".intval($volinfo[capacity]/(1024*1024*1024))." GB</td><td>".intval($volinfo[allocation]/(1024*1024*1024));
			echo" GB</td>";
	
	
			echo "<td><button onclick=\"javascript:location.href='imagemanage.php?dn=1;openvol=1&amp;volname=$volname'\">Add To VM</button> | 
<button onclick=\"javascript:location.href='imagemanage.php?sub=delvol&amp;volname=$volname'\">Delete</button> ";
		
        	     echo "</td></tr>";
        	 }
        	    ?>
        	</table>
	<?php
	}else echo "<pre>Sorry no volumes found</pre>";
	
echo"$result";		
}	
else exit("<pre>Sorry no storage pools</pre>");
?>
