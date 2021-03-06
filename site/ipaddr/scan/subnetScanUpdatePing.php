<?php

/*
 * Update alive status of all hosts in subnet
 ***************************/

/* required functions */
// <eNovance>
require_once('../../../functions/functions.php'); 
require_once('../../../functions/dbfunctions.php');
// </eNovance>

/* verify that user is logged in */
isUserAuthenticated(true);

// <eNovance>
global $db;
$database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);
// </eNovance>

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 	{ die('<div class="alert alert-error">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all existing IP addresses
$addresses = getIpAddressesBySubnetId ($_POST['subnetId']);

// <eNovance>
// Start the queries to update ips with different state
$queryOffline = 'UPDATE ipaddresses SET state = 0 WHERE ip_addr IN (';
$queryOnline = 'UPDATE ipaddresses SET state = 1 WHERE ip_addr IN (';
$online = false;
$offline = false;
// </eNovance>
# loop and check
foreach($addresses as $ip) {
	$m = 0;											//array count
	//if strictly disabled for ping
	if($ip['excludePing']=="1") {
		$ip[$m]['status'] = "excluded from check";
	}
	//ping
	// <eNovance>
	// Switch $count for 1. Complet the state update queries
	else {
		$code = pingHost (transform2long($ip['ip_addr']), 1, false);
	}

	if ( intval($ip['state']) == $code )
	{
		if ($code == 0)
		{
			$queryOnline = $queryOnline.'\''.$ip['ip_addr'].'\''.",";
			$online = true;
		}
		elseif ($code ==1)
		{
			$queryOffline = $queryOffline.'\''.$ip['ip_addr'].'\''.",";
			$offline = true;
		}
	}
	// </eNovance>

	$m++;											//next array item
}
// <eNovance>
if ($offline == true) {$database->executeQuery(substr_replace($queryOffline, ')', -1));}
if ($online == true) {$database->executeQuery(substr_replace($queryOnline, ')', -1));}
// </eNovance>
?>


<h5><?php print _('Scan results');?> (<?php print_r($_POST['pingType']) ?>):</h5>
<!-- <eNovance> -->
<?php echo '<head> <meta http-equiv="refresh" content="0"> </head>'?>
<hr>