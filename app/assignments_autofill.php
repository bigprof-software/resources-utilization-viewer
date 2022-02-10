<?php
// This script and data application were generated by AppGini 22.11
// Download AppGini for free from https://bigprof.com/appgini/download/

	include_once(__DIR__ . '/lib.php');

	handle_maintenance();

	header('Content-type: text/javascript; charset=' . datalist_db_encoding);

	$table_perms = getTablePermissions('assignments');
	if(!$table_perms['access']) die('// Access denied!');

	$mfk = Request::val('mfk');
	$id = makeSafe(Request::val('id'));
	$rnd1 = intval(Request::val('rnd1')); if(!$rnd1) $rnd1 = '';

	if(!$mfk) {
		die('// No js code available!');
	}

	switch($mfk) {

		case 'ProjectId':
			if(!$id) {
				?>
				$j('#ProjectDuration<?php echo $rnd1; ?>').html('&nbsp;');
				<?php
				break;
			}
			$res = sql("SELECT `projects`.`Id` as 'Id', `projects`.`Name` as 'Name', if(`projects`.`StartDate`,date_format(`projects`.`StartDate`,'%d/%m/%Y'),'') as 'StartDate', if(`projects`.`EndDate`,date_format(`projects`.`EndDate`,'%d/%m/%Y'),'') as 'EndDate' FROM `projects`  WHERE `projects`.`Id`='{$id}' limit 1", $eo);
			$row = db_fetch_assoc($res);
			?>
			$j('#ProjectDuration<?php echo $rnd1; ?>').html('<?php echo addslashes(str_replace(["\r", "\n"], '', safe_html($row['StartDate'].' <b>to</b> '.$row['EndDate']))); ?>&nbsp;');
			<?php
			break;


	}

?>