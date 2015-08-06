<?php

  include'delvmhome.php';

 function get_domain_by_name($name) 
    { global $conn;
        $tmp = libvirt_domain_lookup_by_name($conn, $name);
	if($tmp==0)
	{echo"Sorry no machine ".$name." found";
	exit;}
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
	echo"dom found\n";
        return $dom;
}
  
//check wheter domain is running
   function domain_is_running($domain, $name = false) 
    {
	$x=libvirt_domain_is_active($domain);
	if(!$x)
	{	return false;
	}
	else 
	{	return true;
		 }
    }

function domain_undefine($domain) 
    { $dom = get_domain_object($domain);
        if (!$dom)
            return false;
        $tmp = libvirt_domain_undefine($dom);
        return ($tmp) ? $tmp : lasterror();
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

function poolstart($pool) 
    {
        $p = getpoolresource($pool);
        if (!$p)
            return false;
	 $t = libvirt_storagepool_create($p);
        return ($t) ? $t : lasterror();
    }
function pooldestroy($pool) 
    {
        $p = getpoolresource($pool);
        if (!$p)
            return false;
	 $t = libvirt_storagepool_destroy($p);
        return ($t) ? $t : lasterror();
    }
function getvolres($pres, $l)
{$res=libvirt_storagevolume_lookup_by_name($pres, $l);
	return $res;}

 $name =$_POST['vmname'];
    $res = get_domain_by_name($name);



if($res)
 {   $x=libvirt_list_storagepools($conn);
	$pname="";$pres="";$vres="";$volname="";
	for ($i = 0; $i < sizeof($x); $i++)
      {	$pname=$x[$i];
	
	$pres=libvirt_storagepool_lookup_by_name($conn, $pname);
		
	$vres=getvolres($pres, $name);
	$volname=libvirt_storagevolume_get_name($vres);	
}
	 $msg = '';
    if(!domain_is_running($res, $name))
    {
        if(!domain_undefine($name))
	   {$msg .= $name.' Delete failed';
       		echo "<pre>$msg</pre>.\n"; }  

	else
	{       exec("sudo rm -f /var/images/$volname");
		$sp=libvirt_storagepool_get_name($pres);
		pooldestroy($sp);
		poolstart($sp);
            	$msg .= $name.' Delete successfull';
        	echo "<pre>$msg</pre>.\n";
		}
  	}
	else{
        $msg.= $name.' Vm is still running can\'t Delete!';
    echo "<pre>$msg</pre>\n";
}
}
 

else
{?>	<html>
	<body>
	<a href="javascript:history.back(-1);">
<p><p>	
</body></html>
<?php
	exit('<pre>Virtual Machine doesent exists...</pre>');
	} 


?>
