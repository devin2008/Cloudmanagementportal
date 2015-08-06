<?php

include'delvmhome.php';

function get_domain_by_name($name) 
    { global $conn;
        $tmp = libvirt_domain_lookup_by_name($conn, $name);
        return ($tmp) ? $tmp : 0;
    }
function get_domain_object($nameRes) 
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

function domain_is_running($domain, $name = false) 
    {
	$x=libvirt_domain_is_active($domain);
	if(!$x)
	{	echo"not running\n";
		return false;
	}
	else 
	{	echo"running\n";return true;
		 } }

$x=$_POST['listvm'];

if (!empty($_POST['listvm']))
	{	 $p=libvirt_domain_get_counts($conn);
	print_r($p);
		echo"{$p['active']}\n\n";
		$x=libvirt_list_domains($conn);
		$j=count($x);	
	if($j==0)
	{	
		echo "Sorry no virtual machines to show";
		exit;
	  }
	?>

 <style>
table { font-family:Arial, Helvetica, sans-serif;color:#666;font-size:170%;text-shadow: 1px 1px 0px #fff;background:#eaebec;margin:2%;border:#ccc 1px solid;border-radius:6px;box-shadow: 0 4px 3px #d1d1d1;}
table th {padding:21px 25px 22px 25px;border-top:1px solid #fafafa;border-bottom:1px solid #e0e0e0;background: #ededed;background: }
table td {padding:25px 25px 22px 25px;border-top:1px solid #fafafa;border-bottom:1px solid #e0e0e0;background: #ededed;}</style> 
<?php
	echo "<table >";
	echo "<tr'><th>Vmname</th><th>Status</th></tr>";		
		for($i=0;$i<$j;$i++)		
		{	$res = get_domain_by_name($x[$i]);
			$uuid = libvirt_domain_get_uuid_string($res);
			$status=domain_is_running($res,$x[$i]);
    			if($status==1)
			{ 
				echo "<tr><td>"; 
 				echo "<a href=\"domdetails.php?uuid=$uuid\">$x[$i]</a>";//$uuid
		
				 echo "</td><td>Running</td></tr>";
			}
	else
			{ echo "<tr ><td >"."$x[$i]"."</td><td>Off</td></tr>";
			}	
		}
echo"</table>";
}	
else
	{	echo"Please enter the name to delete a domain";
		}
?>
