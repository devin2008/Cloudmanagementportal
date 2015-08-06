<?php
  include'main.php';

function get_domain_by_name($name) 
    { global $conn;
        $t = libvirt_domain_lookup_by_name($conn, $name);
        return ($t) ? $t : 0;
    }

    
function getdomresource($nameRes) 
    { global $conn;
        $dom=libvirt_domain_lookup_by_name($conn, $nameRes);
        
	if (!$dom) 
	{return lasterror();
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
            $result = libvirt_domain_create($dom);
            $last_error = libvirt_get_last_error();
            return $result;
        }
       
 	$result = libvirt_domain_create_xml($conn, $dom);
        $lasterror = libvirt_get_last_error();
        return $result;
    }
function get_xpath($n, $xpath, $inactive = false) 
    {global $dname;
        $dom = getdomresource($dname);
        $flags = 0;
        if ($inactive)
            $flags = VIR_DOMAIN_XML_INACTIVE;

        $tmp = libvirt_domain_xml_xpath($dom, $xpath, $flags); 
        if (!$tmp)
            return lasterror();

        return $tmp;
    }

    	
$sub = array_key_exists('sub', $_GET) ? $_GET['sub'] : '';
$ap = array_key_exists('ap', $_GET) ? $_GET['ap'] : '';
$dname = array_key_exists('dname', $_POST) ? $_POST['dname'] : '';
$mem = array_key_exists('mem', $_POST) ? $_POST['mem'] : '';
$nc = array_key_exists('nc', $_POST) ? $_POST['nc'] : '';
?>
<style>

#but {margin-left: 30px;
padding: 10px;
width:250px;
height:45px;
size:28px;}

#t2 {margin-left: 30px;
padding: 10px;
width:250px;
height:45px;}

#t1 {margin-left: 30px;
padding: 10px;
width:250px;
height:45px;
}
</style>
<button class="btn btn-primary" id="but" onclick="javascript:location.href='editvm.php?ap=1&amp;'">Edit CPU</button>

<button class="btn btn-primary" id="but" onclick="javascript:location.href='editvm.php?sub=1&amp;'">Edit Memory</button>
</br>

<?php
if ($ap)
    {	if ($ap == $_GET['ap'])
	{if($dname&&$nc)
	   {	
		$result = false;
		$nc=$_POST['nc'];
		$b=false;
		$dname=$_POST['dname'];
		
		$res =get_domain_by_name($dname);
		
		$oldxml=libvirt_domain_get_xml_desc($res,$b ? VIR_DOMAIN_XML_INACTIVE : 0);
		$mem=get_xpath($res, '//domain/memory', false);print_r($mem);
		$id=get_xpath($res, '//domain/@id', false);
		$ncor=get_xpath($res, '//domain/vcpu', false);
		$uuid=get_xpath($res, '//domain/uuid', false);
		$mac=get_xpath($res, '//domain/devices/interface/mac/@address', false);
		$vnet=get_xpath($res, '//domain/devices/interface/target/@dev', false);
		$cnsole=get_xpath($res, '//domain/devices/serial/source/@path', false);	print_r($cnsole);
		$vnc=get_xpath($res, '//domain/devices/graphics/@port', false);
		$label=get_xpath($res, '//domain/seclabel/label', false);
		$ilabel=get_xpath($res, '//domain/seclabel/imagelabel', false);	
		$newxml="<domain type='kvm' id='$id'>
  <name>$dname</name>
  <uuid>".$uuid[0]."</uuid>
  <memory unit='KiB'>".$mem[0]."</memory>
  <currentMemory unit='KiB'>".$mem[0]."</currentMemory>
  <vcpu placement='static'>".$nc."</vcpu>
  <resource>
    <partition>/machine</partition>
  </resource>
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
      <source file='/var/images/$dname'/>
      <target dev='hda' bus='ide'/>
      <alias name='ide0-0-0'/>
      <address type='drive' controller='0' bus='0' target='0' unit='0'/>
    </disk>
    <disk type='file' device='cdrom'>
      <driver name='qemu' type='raw'/>
      <source file='/home/divyashish/Desktop/pup-431.iso'/>
      <target dev='hdb' bus='ide'/>
      <readonly/>
      <alias name='ide0-0-1'/>
      <address type='drive' controller='0' bus='0' target='0' unit='1'/>
    </disk>
    <controller type='usb' index='0'>
      <alias name='usb0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x01' function='0x2'/>
    </controller>
    <controller type='pci' index='0' model='pci-root'>
      <alias name='pci.0'/>
    </controller>
    <controller type='ide' index='0'>
      <alias name='ide0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x01' function='0x1'/>
    </controller>
    <interface type='network'>
      <mac address='".$mac[0]."'/>
      <source network='default'/>
      <target dev='$vnet'/>
      <model type='rtl8139'/>
      <alias name='net0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x03' function='0x0'/>
    </interface>
    <serial type='pty'>
      <source path='".$cnsole[0]."'/>
      <target port='0'/>
      <alias name='serial0'/>
    </serial>
    <console type='pty' tty='".$cnsole[0]."'>
      <source path='".$cnsole[0]."'/>
      <target type='serial' port='0'/>
      <alias name='serial0'/>
    </console>
    <input type='mouse' bus='ps2'/>
    <input type='keyboard' bus='ps2'/>
    <graphics type='vnc' port='".$vnc[0]."' autoport='yes' listen='0.0.0.0' keymap='en-us'>
      <listen type='address' address='0.0.0.0'/>
    </graphics>
    <video>
      <model type='cirrus' vram='9216' heads='1'/>
      <alias name='video0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x02' function='0x0'/>
    </video>
    <memballoon model='virtio'>
      <alias name='balloon0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x04' function='0x0'/>
    </memballoon>
  </devices>
  <seclabel type='dynamic' model='apparmor' relabel='yes'>
    <label>".$label[0]."</label>
    <imagelabel>$ilabel</imagelabel>
  </seclabel>
</domain>";
				
       
	$result=libvirt_domain_define_xml($conn, $newxml)? "VM CPU configuration changed please reboot to take effect" : 'Error : '.lasterror();
	echo "<pre>$result</pre>";}
	else {
	$result = '<br /><br />  <form method="POST">
			<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>VM to edit</label>
				</div>
			<div class="col-md-5">
					<input type="text" name="dname" class="form-control" />
				</div>
			<br /><br /> 

		<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>Select No of virtual CPU cores</label>
				</div>

		<div class="col-md-5">
					  <select name="nc" class="form-control">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						</select>
				</div>
                   <br /><br /> 	<br/><br/>
			
				<div class="col-md-2" style="margin-left: 70%;" id="submit-button">		
					<input type="submit" class="btn btn-primary" value="Done"/>
				</div>			
 </form>';
		     echo $result;} }
	}





if ($sub)
    {	if ($sub == $_GET['sub'])
	{if($dname&&$mem)
	   {	$result = false;
		$b=false;
		$dname=$_POST['dname'];
		$mem=$_POST['mem'];
		$res =get_domain_by_name($dname);
		$oldxml=libvirt_domain_get_xml_desc($res,$b ? VIR_DOMAIN_XML_INACTIVE : 0);	
		$id=get_xpath($res, '//domain/@id', false);
		$nc=get_xpath($res, '//domain/vcpu', false);
		$uuid=get_xpath($res, '//domain/uuid', false);
		$mac=get_xpath($res, '//domain/devices/interface/mac/@address', false);
		$vnet=get_xpath($res, '//domain/devices/interface/target/@dev', false);
		$cnsole=get_xpath($res, '//domain/devices/serial/source/@path', false);
		$vnc=get_xpath($res, '//domain/devices/graphics/@port', false);
		$label=get_xpath($res, '//domain/seclabel/label', false);
		$ilabel=get_xpath($res, '//domain/seclabel/imagelabel', false);		
		
		
	$newxml="<domain type='kvm' id='$id'>
  <name>$dname</name>
  <uuid>".$uuid[0]."</uuid>
  <memory unit='GiB'>".$mem."</memory>
  <currentMemory unit='GiB'>".$mem."</currentMemory>
  <vcpu placement='static'>".$nc[0]."</vcpu>
  <resource>
    <partition>/machine</partition>
  </resource>
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
      <source file='/var/images/$dname'/>
      <target dev='hda' bus='ide'/>
      <alias name='ide0-0-0'/>
      <address type='drive' controller='0' bus='0' target='0' unit='0'/>
    </disk>
    <disk type='file' device='cdrom'>
      <driver name='qemu' type='raw'/>
      <source file='/home/divyashish/Desktop/pup-431.iso'/>
      <target dev='hdb' bus='ide'/>
      <readonly/>
      <alias name='ide0-0-1'/>
      <address type='drive' controller='0' bus='0' target='0' unit='1'/>
    </disk>
    <controller type='usb' index='0'>
      <alias name='usb0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x01' function='0x2'/>
    </controller>
    <controller type='pci' index='0' model='pci-root'>
      <alias name='pci.0'/>
    </controller>
    <controller type='ide' index='0'>
      <alias name='ide0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x01' function='0x1'/>
    </controller>
    <interface type='network'>
      <mac address='".$mac[0]."'/>
      <source network='default'/>
      <target dev='$vnet'/>
      <model type='rtl8139'/>
      <alias name='net0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x03' function='0x0'/>
    </interface>
    <serial type='pty'>
      <source path='".$cnsole[0]."'/>
      <target port='0'/>
      <alias name='serial0'/>
    </serial>
    <console type='pty' tty='".$cnsole[0]."'>
      <source path='".$cnsole[0]."'/>
      <target type='serial' port='0'/>
      <alias name='serial0'/>
    </console>
    <input type='mouse' bus='ps2'/>
    <input type='keyboard' bus='ps2'/>
    <graphics type='vnc' port='".$vnc[0]."' autoport='yes' listen='0.0.0.0' keymap='en-us'>
      <listen type='address' address='0.0.0.0'/>
    </graphics>
    <video>
      <model type='cirrus' vram='9216' heads='1'/>
      <alias name='video0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x02' function='0x0'/>
    </video>
    <memballoon model='virtio'>
      <alias name='balloon0'/>
      <address type='pci' domain='0x0000' bus='0x00' slot='0x04' function='0x0'/>
    </memballoon>
  </devices>
  <seclabel type='dynamic' model='apparmor' relabel='yes'>
    <label>".$label[0]."</label>
    <imagelabel>$ilabel</imagelabel>
  </seclabel>
</domain>";
				
       
	$result=libvirt_domain_define_xml($conn, $newxml)? 'VM memory edited please reboot to take effect' :'Cannot edit VM : '.lasterror();echo "<pre>$result</pre>";
	}
	else {
	$result = '<br /><br />  <form method="POST">
			<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>VM to edit</label>
				</div>
			<div class="col-md-5">
					<input type="text" name="dname" class="form-control" />
				</div>
			<br /><br /> 

		<div class="col-md-12"></div>
 				<div class="c3 col-md-4">
					<label>Choose memory (in GB):</label>
				</div>

		<div class="col-md-5">
					  <select name="mem" class="form-control">
						<option value="1">1 GB</option>
						<option value="2">2 GB</option>
						<option value="3">3 GB</option>
						<option value="4">4 GB</option>
					</select>
				</div>
                   <br /><br />  <br /><br />	
			
				<div class="col-md-6" style="margin-left: 70%;" id="submit-button">		
					<input type="submit" class="btn btn-primary" value="Done"/>
				</div>			
 </form>';
		 echo"$result";}     }
	}




?>
