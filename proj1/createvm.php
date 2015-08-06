<?php
  include'main.php';

function get_domain_by_name($name) 
    { global $conn;
        $t = libvirt_domain_lookup_by_name($conn, $name);
        return ($t) ? $t : 0;
    }
    $vmname = $_POST['vmname'];
$res =get_domain_by_name($vmname);


if($res)
 {?>	<html>
	<body>
	
<p>Virtual machine already exists please choose another name<p>	
</body></html>
<?php
	exit('');
	}   
else
{

    $ram = ((int)$_POST['ram']);
  $cpucores = $_POST['cpucores'];
	$disksize=$_POST['disk'];
$isopath = $_POST['path'];
$poolchosen=$_POST['pool'];
$volxml="<volume type='file'>
        <name>$vmname</name>
        <allocation></allocation>
        <capacity unit='GB'>$disksize</capacity>
        <target>
          <path>/var/images/$vmname</path>
	<format type='qcow2'/>
          <permissions>
            <owner>107</owner>
            <group>107</group>
            <mode>0744</mode>
          </permissions>
        </target>
	</volume>";
$t=libvirt_list_storagepools($conn);
for ($i = 0; $i < sizeof($t); $i++)
{	$pname[$i]=$t[$i];
	if($pname[$i]=$poolchosen)
	{
	
	$pres[$i]=libvirt_storagepool_lookup_by_name($conn, $pname[$i]);
	$volres=libvirt_storagevolume_create_xml($pres[$i], $volxml);
		
			}
	
}

$macaddress1=exec('MACAddress="$(dd if=/dev/urandom bs=1024 count=1 2>/dev/null|md5sum|sed \'s/^\(..\)\(..\)\(..\)\(..\)\(..\)\(..\).*$/52:\2:\3:\4:\5:\6/\')";echo $MACAddress');

   
    $xml = "<domain type='kvm'>
                <name>".$vmname."</name>
                <memory unit='GiB'>".$ram."</memory>
                <currentMemory unit='GiB'>".$ram."</currentMemory>
                <vcpu>".$cpucores."</vcpu>
                <os>
                    <type arch='x86_64' machine='pc-i440fx-trusty'>hvm</type>
  <boot dev='hd'/>                 
<boot dev='cdrom'/>
                </os>
                <features>
                    <acpi/>
                    <apic/>
                    <pae/>
                </features>
                <clock offset='localtime'/>
                <on_poweroff>destroy</on_poweroff>
                <on_reboot>restart</on_reboot>
                <on_crash>restart</on_crash>
                <devices>
                    <emulator>/usr/bin/kvm-spice</emulator>
                    <disk type='file' device='disk'>
                        <driver name='qemu' type='qcow2'/>
                        <source file='/var/images/".$vmname."'/>
                        <target dev='hda' bus='ide'/>
                    </disk>
                    <disk type='file' device='cdrom'>
                        <source file='".$isopath."'/>
                        <target dev='hdb' bus='ide'/>
                        <readonly/>
                    </disk>
                    <interface type='network'>
                        
                        <mac address='".$macaddress1."'/>
                  <source network='default'/>  
		</interface>
	
                  <serial type='pty'>
      <target port='0'/>
    </serial>
    <console type='pty'>
      <target type='serial' port='0'/>
    </console>
                    <input type='mouse' bus='ps2'/>
<input type='keyboard' bus='ps2'/>
                    <graphics type='vnc' port='-1' autoport='yes' keymap='en-us' listen='0.0.0.0'/>
                </devices>
            </domain>";
             
 function domdef($xml) 
    { global $conn;
        $t = libvirt_domain_define_xml($conn, $xml);
        return ($t) ? $t : lasterror();
    }
function getdomresource($nameRes) 
    { global $conn;
       
        $dom=libvirt_domain_lookup_by_name($conn, $nameRes);
        if (!$dom) 
	{
            	return lasterror();
		}
	else
		{ return $dom;
     	   }

        return $dom;
}
 function domstart($dom) 
    {global $conn;
        $dom=getdomresource($dom);
        if ($dom) {
            $ret = libvirt_domain_create($dom);
            $last_error = libvirt_get_last_error();
            return $ret;
        }
       
 	$ret = libvirt_domain_create_xml($conn, $dom);
        $lasterror = libvirt_get_last_error();
        return $ret;
    }
    $res = domdef($xml);

    $res = get_domain_by_name($vmname);
    $uuid = libvirt_domain_get_uuid_string($res);
   
function domgetuuid($uuid) 
    {global $conn;
        $dom = libvirt_domain_lookup_by_uuid_string($conn, $uuid);
        if (!$dom)
            return false;
        $t = libvirt_domain_get_name($dom);
        return ($t) ? $t : lasterror();
    }

 $domName = domgetuuid($uuid);
    domstart($domName);
}

function get_xpath($domain, $xpath, $inactive = false) 
    {
        $dom = getdomresource($domain);
        $flags = 0;
        if ($inactive)
            $flags = VIR_DOMAIN_XML_INACTIVE;

        $t = libvirt_domain_xml_xpath($dom, $xpath, $flags); 
        if (!$t)
            return lasterror();

        return $t;
    }
 function domain_get_vnc_port($n) 
    {
       $t = get_xpath($n, '//domain/devices/graphics/@port', false);
        $v = (int)$t[0];
        unset($t);
        return $v;
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


$vnc = domain_get_vnc_port($domName);


    if(!empty($res)) 
{	echo"creating machine\n";
	echo"$vnc\n";
	
	
	$result=exec("sudo /home/divyashish/noVNC/utils/launch.sh --vnc 127.0.0.1:$vnc");
	echo"</br>hello prceed to $result";
	if($result)
	{
	header('Location:http://localhost:6080/vnc.html?host=localhost&port=6080');
		}

	//else
	//header('Location:http://localhost/dashboard.php');
//		}
  }

   else exit(lasterror());
?>
