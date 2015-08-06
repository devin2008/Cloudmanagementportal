<?php
?>
 <style>

h2{margin-left: 25px;
padding: 10px;
}

h3{margin-left: 25px;
padding: 10px;
}
#but {margin-left: 30px;
padding: 10px;
}

table { font-family:Arial, Helvetica, sans-serif;color:#666;font-size:170%;text-shadow: 1px 1px 0px #fff;background:#eaebec;margin:2%;border:#ccc 1px solid;border-radius:6px;box-shadow: 0 4px 3px #d1d1d1;}
table th {padding:21px 25px 22px 25px;border-top:1px solid #fafafa;border-bottom:1px solid #e0e0e0;background: #ededed;background: }
table td {padding:25px 25px 22px 25px;border-top:1px solid #fafafa;border-bottom:1px solid #e0e0e0;background: #ededed;}</style> 

<?php
include'main.php';

function get_domain_by_name($name) 
    { global $conn;
        $tmp = libvirt_domain_lookup_by_name($conn, $name);
        return ($tmp) ? $tmp : 0;
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
function domgetvncport($dom) 
    {
        $tmp = get_xpath($dom, '//domain/devices/graphics/@port', false);
        $var = (int)$tmp[0];
        unset($tmp);

        return $var;
    }
function get_xpath($n, $xpath, $inactive = false) 
    {global $name;
        $dom = getdomresource($name);
        $flags = 0;
        if ($inactive)
            $flags = VIR_DOMAIN_XML_INACTIVE;

        $tmp = libvirt_domain_xml_xpath($dom, $xpath, $flags); 
        if (!$tmp)
            return lasterror();

        return $tmp;
    }
function domgetarch($dom) 
    {
        $d = getdomresource($dom);
        $tmp = get_xpath($d, '//domain/os/type/@arch', false);
        $var = $tmp[0];
        unset($tmp);
        return $var;
    }
function diskstats($domain, $sort=true) 
    {global $name;
        $dom = getdomresource($name);
        $buses =  get_xpath($dom, '//domain/devices/disk[@device="disk"]/target/@bus', false);
        $disks =  get_xpath($dom, '//domain/devices/disk[@device="disk"]/target/@dev', false);
        $ret = array();
        for ($i = 0; $i < $disks['num']; $i++) {
            $tmp = libvirt_domain_get_block_info($dom, $disks[$i]);
            if ($tmp) {
                $tmp['bus'] = $buses[$i];
                $ret[] = $tmp;
            }
            else
          lasterror();
        }
        if ($sort) {
          for ($i = 0; $i < sizeof($ret); $i++) {
          for ($ii = 0; $ii < sizeof($ret); $ii++) {
            if (strcmp($ret[$i]['device'], $ret[$ii]['device']) < 0) {
                        $tmp = $ret[$i];
                        $ret[$i] = $ret[$ii];
                        $ret[$ii] = $tmp;
                    }} }}
        unset($buses);
        unset($disks);
        return $ret;
    }
function diskcapacity($domain, $physical=false, $disk='*', $unit='?') 
    {global $name;
        $dom = getdomresource($name);
        $tmp = diskstats($dom);

        $ret = 0;
        for ($i = 0; $i < sizeof($tmp); $i++) {
            if (($disk == '*') || ($tmp[$i]['device'] == $disk))
                if ($physical)
                    $ret += $tmp[$i]['physical'];
                else
                    $ret += $tmp[$i]['capacity'];
        }
        unset($tmp);
        return format($ret, 2, $unit);
    }
function format($value, $decimals, $unit='?') 
    {
        if ($unit == '?') {  
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
function domnamebyuuid($uuid) 
    { global $conn;
        $dom = libvirt_domain_lookup_by_uuid_string($conn, $uuid);
        if (!$dom)
            return false;
        $tmp = libvirt_domain_get_name($dom);
        return ($tmp) ? $tmp : lasterror();
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

   	$action = array_key_exists('action', $_GET) ? $_GET['action'] : '';
   	$subaction = array_key_exists('subaction', $_GET) ? $_GET['subaction'] : '';
	$name = domnamebyuuid($_GET['uuid']);
	$res = getdomresource($name);
        $l =libvirt_domain_get_info($res);
	$maxmem= $l['maxMem']/1024;
	$memused= $l['memory'] ;
        $cpu = $l['nrVirtCpu'];
        $s = domainstatus($l['state']);
        $id = libvirt_domain_get_id($res);
        $arch = domgetarch($name);
       	$vnc = domgetvncport($name);

            if (!$id)
                $id = 'N/A';
            
            echo "<h2 'id=h'>$name - VM Information</h2>";
           $type=get_xpath($res, '//domain/@type', false);
	$typ = $type[0];
        unset($type);
		 
		echo "<b id='but'>VM type: </b>$typ<br />";
           $emul=get_xpath($res, '//domain/devices/emulator', false);
	$emu = $emul[0];
        unset($emul);
	 	echo "<b id='but'>Domain emulator: </b>$emu<br />";
            echo "<b id='but'>Memory allocated: </b>$memused<br />";
            echo "<b id='but'>Number of vCPUs: </b>$cpu<br />";
            echo "<b id='but'>Current State: </b>$s<br />";
            echo "<b id='but'>Srchitecture: </b>$arch<br />";
            echo "<b id='but'>VM ID: </b>$id<br />";
           
            echo '<br />';
echo "<h3 'id=h'>Disk devices</h3>";
            $tmp = diskstats($name);
  
            if (!empty($tmp)) {
                echo "<table  align=\"center\" > <tr text-align=\"center\"> <th text-align=\"center\">Disk storage</th >
                                <th>Storage driver type</th>
                                <th>Domain device</th>
                                <th>Disk capacity</th>
                    <th>Disk allocation</th>
                    <th>Physical disk size</th>
                    <th text-align=\"center\">    Actions    </th>
                      </tr>";

                for ($i = 0; $i < sizeof($tmp); $i++) {
                    $capacity = format($tmp[$i]['capacity'], 2);
                    $allocation = format($tmp[$i]['allocation'], 2);
                    $physical = format($tmp[$i]['physical'], 2);
                    $dev = (array_key_exists('file', $tmp[$i])) ? $tmp[$i]['file'] : $tmp[$i]['partition'];

                    echo "<tr><td>".basename($dev)."</td><td>{$tmp[$i]['type']}</td> <td>{$tmp[$i]['device']}</td><td>$capacity</td><td>$allocation</td> <td>$physical</td></tr>";
                        }
                echo "</table>";
            }
            else
                echo "Domain doesn't have any disk devices";

?>
