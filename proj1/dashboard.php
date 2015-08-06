<?php
require('main.php');

function get_domain_by_name($name) 
    { global $conn;
        $t = libvirt_domain_lookup_by_name($conn, $name);
        return ($t) ? $t : 0;
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
	echo"dom found\n";
        return $dom;
}
 function domstart($dom) 
    {global $conn;
        $dom=getdomresource($dom);
        if ($dom) {
            $result = libvirt_domain_create($dom);
            $last_error = libvirt_get_last_error();
	echo"$last_error\n";
            return $result;
        }
       
 	$result = libvirt_domain_create_xml($conn, $dom);
        $lasterror = libvirt_get_last_error();
	 return $result;
    }
function domshutdown($domain) 
    {
        $dom = getdomresource($domain);
        if (!$dom)
            return false;

        $t = libvirt_domain_shutdown($dom);
        return ($t) ? $t : lasterror();
    }
function domdestroy($domain) 
    {
        $dom = getdomresource($domain);
        if (!$dom)
            return false;

        $t = libvirt_domain_destroy($dom);
        return ($t) ? $t : lasterror();
    }
function domsuspend($domain) 
    {
        $dom = getdomresource($domain);
        if (!$dom)
            return false;

        $t = libvirt_domain_suspend($dom);
        return ($t) ? $t : lasterror();
    }
function domresume($domain) 
    {
        $dom = getdomresource($domain);
        if (!$dom)
            return false;
	 $t = libvirt_domain_resume($dom);
        return ($t) ? $t : lasterror();
    }

function domain_is_running($domain, $name = false) 
    {$x=libvirt_domain_is_active($domain);
	if(!$x)
	{	return false;
	}
	else 
	{	return true;
		 }
    }
function domain_get_xml($domain, $inactive = false) 
    {global $name;
        $dom = getdomresource($name);
        if (!$dom)
            return false;

        $t = libvirt_domain_get_xml_desc($dom, $inactive ? VIR_DOMAIN_XML_INACTIVE : 0);
        return ($t) ? $t : lasterror();
    }
 function domainstatus($state) 
    {
        switch ($state) {
            case 1:  return 'running';
         
            case 3:   return 'paused';
         
            case 5:  return 'shutoff';
            }
  return 'unknown';
    }

function diskcount($domain) 
    {global $name;
        $d = getdomresource($name);
        $t = diskstats($d);
        $result = sizeof($t);
        unset($t);
  return $result;
    }
 function diskstats($domain, $sort=true) 
    {global $name;
        $dom = getdomresource($name);
 $buses =  getstring($dom, '//domain/devices/disk[@device="disk"]/target/@bus', false);
        $disks =  getstring($dom, '//domain/devices/disk[@device="disk"]/target/@dev', false);
         $result = array();
        for ($i = 0; $i < $disks['num']; $i++) {
            $t = libvirt_domain_get_block_info($dom, $disks[$i]);
            if ($t) {
                $t['bus'] = $buses[$i];
                $result[] = $t;
            }
            else
                lasterror();
        }
  if ($sort) {
        for ($i = 0; $i < sizeof($result); $i++) {
         for ($ii = 0; $ii < sizeof($result); $ii++) {
          if (strcmp($result[$i]['device'], $result[$ii]['device']) < 0) {
                        $t = $result[$i];
                        $result[$i] = $result[$ii];
                        $result[$ii] = $t;
                    } }}}
  unset($buses);
        unset($disks);
   return $result;
    }

function getstring($domain, $xpath, $inactive = false) 
    {global $name;
        $dom = getdomresource($name);
        $flags = 0;
        if ($inactive)
            $flags = VIR_DOMAIN_XML_INACTIVE;

        $t = libvirt_domain_xml_xpath($dom, $xpath, $flags); 
        if (!$t)
            return lasterror();
 return $t;
    }
function getuuid($uuid) 
    { global $conn;
        $dom = libvirt_domain_lookup_by_uuid_string($conn, $uuid);
        if (!$dom)
            return false;
        $t = libvirt_domain_get_name($dom);
        return ($t) ? $t : lasterror();
    }
function domgetid($domain, $name = false) 
    {global $name;
        $dom = getdomresource($name);
        if ((!$dom) || (!domain_is_running($dom, $name)))
            return false;
  $t = libvirt_domain_get_id($dom);
        return ($t) ? $t : setlasterror();
    }
 function diskcapacity($domain, $physical=false, $disk='*', $unit='?') 
    {global $name;
        $dom = getdomresource($name);
        $t = diskstats($dom);
 $result = 0;
        for ($i = 0; $i < sizeof($t); $i++) {
            if (($disk == '*') || ($t[$i]['device'] == $disk))
                if ($physical)
                    $result += $t[$i]['physical'];
                else
                    $result += $t[$i]['capacity'];
        }
        unset($t);
  return format($result, 2, $unit);
    }
function format($value, $decimals, $unit='?') 
    { if ($unit == '?') {
	if ($value > 1099511627776)
                $unit = 'T';
            else
            if ($value > (1 << 30))
                $unit = 'G';
            else
            if ($value > (1 << 20))
                $unit = 'M';
            else
            if ($value > (1 << 10))
                $unit = 'K';
            else
                $unit = 'B';
        }
        $unit = strtoupper($unit);
	 switch ($unit) {
            case 'T': return number_format($value / (float)1099511627776, $decimals, '.', ' ').' TB';
            case 'G': return number_format($value / (float)(1 << 30), $decimals, '.', ' ').' GB';
            case 'M': return number_format($value / (float)(1 << 20), $decimals, '.', ' ').' MB';
            case 'K': return number_format($value / (float)(1 << 10), $decimals, '.', ' ').' kB';
            case 'B': return $value.' B';
        }
        return false;
    }

$t =libvirt_domain_get_counts($conn);
$action = array_key_exists('action', $_GET) ? $_GET['action'] : '';

?>
  <style>
#box{color:grey;margin-left: 20px;
padding: 10px;
width:800px;
border: 2px red;}
#but {margin-left: 30px;
padding: 10px;
width:250px;
height:45px;size:30px}

table {font-family:Arial, Helvetica, sans-serif;color:#666;font-size:200%;text-shadow: 1px 1px 0px #fff;background:#eaebec;margin:2%;border:#ccc 1px solid;border-radius:6px;box-shadow: 0 1px 2px #d1d1d1;}
table th {padding:21px 25px 22px 25px;border-top:1px solid #fafafa;border-bottom:1px solid #e0e0e0;background: #ededed;background: }
table td {padding:25px 25px 22px 25px;border-top:1px solid #fafafa;border-bottom:1px solid #e0e0e0;background: #ededed;}</style> 

            <button class="btn btn-primary" id="but" onclick="javascript:window.location.href='createvmhome.php'"><i class="icon-plus icon-white"></i>Add a Virtual Machine</button>
        </p>
   
    
        <?php
            $no_ofdoms = libvirt_list_domains($conn);
            $domkeys = array_keys($no_ofdoms);
            $active = $t['active'];
        ?>
         
		<table >
            <tr>
                <th>VM Name</th>
                <th>CPU</th>
                <th>RAM</th>
                <th>HDD(s)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php
            $result = false;
	
            if ($action) {
                $domName = getuuid($_GET['uuid']);
	        if ($action == 'domainstart') {
                    $result = domstart($domName) ? "Domain started successfully" : 'Error : '.lasterror();}
                else if ($action == 'domainstop') {
                    $result = domshutdown($domName) ? "Domain stopped successfully" : 'Error : '.lasterror();}
                else if ($action == 'domaindestroy') {
                    $result = domdestroy($domName) ? "Domain forcefully shutdown" : 'Error : '.lasterror(); }
		else if ($action == 'domainsuspend') {
                    $result = domsuspend($domName) ? "Domain Suspended" : 'Error : '.lasterror();}
		else if ($action == 'domainresume') {
                    $result = domresume($domName) ? "Domain resumed" : 'Error : '.lasterror();
               } }
		
            for ($i = 0; $i < sizeof($no_ofdoms); $i++) {
                $name = $no_ofdoms[$i];
                $res = get_domain_by_name($name);
                $uuid = libvirt_domain_get_uuid_string($res);
                $dom = libvirt_domain_is_active($res);
		$l=libvirt_domain_get_info($res);                
		$maxmem= $l['maxMem']/1024;
		$memused= $l['memory'] ;
                $cpu = $l['nrVirtCpu'];
                $state = domainstatus($l['state']);
                $id = domgetid($res);
               if (($diskcnt = diskcount($res)) > 0) {
                    $disks = $diskcnt.' / '.diskcapacity($res);
                    $diskdesc = 'Current physical size: '.diskcapacity($res, true);
                }
                else {
                    $disks = '-';
                    $diskdesc = '';
                }
    
                unset($t);
                if (!$id)
                $id = '-';
                unset($dom);
		 echo "<tr><td><a href=\"domdetails.php?uuid=$uuid\">$name</a></td><td>$cpu</td><td>$maxmem</td><td title='$diskdesc'>$disks</td> <td>$state</td> ";
		echo "<td>";

            if (domain_is_running($res, $name)){
                    
                    echo "<button class=\"btn btn-default\" style=\"background-color: orange\" onclick=\"javascript:location.href='dashboard.php?action=domainstop&amp;uuid=$uuid'\">Shutdown</button> | ";
                    echo "<button class=\"btn btn-default\" style=\"background-color: red\" onclick=\"javascript:location.href='dashboard.php?action=domaindestroy&amp;uuid=$uuid'\">Force shutdown</button> | ";
		echo "<button class=\"btn btn-default\" style=\"background-color: yellow\" onclick=\"javascript:location.href='dashboard.php?action=domainsuspend&amp;uuid=$uuid'\">Suspend</button> | ";
		 echo "<button class=\"btn btn-default\" style=\"background-color: pink\" onclick=\"javascript:location.href='dashboard.php?action=domainresume&amp;uuid=$uuid'\">Resume</button>";
                }else
                    echo "<button class=\"btn btn-default\" style=\"background-color:lightgreen\" onclick=\"javascript:location.href='dashboard.php?action=domainstart&amp;uuid=$uuid'\">Start</button>";

 	        if (!domain_is_running($res, $name))
              {      echo " | <button class=\"btn btn-default\" style=\"background-color:\" onclick=\"javascript:location.href='delvm.php?vmname=$name'\">Delete</button>";
            		}
             
   echo "</td></tr>";
            }
            ?>
        </table>
<?php  $t =libvirt_domain_get_counts($conn);
echo "<div id=\"box\">Current : {$t['total']} machine(s) present, {$t['active']} active , {$t['inactive']} inactive</div>";?>


        <?php if ($result) echo "<br /><pre>$result</pre>";?>
   
</div>

<?php ?>
