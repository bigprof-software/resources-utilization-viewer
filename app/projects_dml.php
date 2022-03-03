<?php

// Data functions (insert, update, delete, form) for table projects

// This script and data application were generated by AppGini 22.12
// Download AppGini for free from https://bigprof.com/appgini/download/

function projects_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('projects');
	if(!$arrPerm['insert']) return false;

	$data = [
		'Name' => Request::val('Name', ''),
		'StartDate' => Request::dateComponents('StartDate', ''),
		'EndDate' => Request::dateComponents('EndDate', ''),
	];


	// hook: projects_before_insert
	if(function_exists('projects_before_insert')) {
		$args = [];
		if(!projects_before_insert($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('projects', backtick_keys_once($data), $error);
	if($error)
		die("{$error}<br><a href=\"#\" onclick=\"history.go(-1);\">{$Translation['< back']}</a>");

	$recID = db_insert_id(db_link());

	update_calc_fields('projects', $recID, calculated_fields()['projects']);

	// hook: projects_after_insert
	if(function_exists('projects_after_insert')) {
		$res = sql("SELECT * FROM `projects` WHERE `Id`='" . makeSafe($recID, false) . "' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args=[];
		if(!projects_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	set_record_owner('projects', $recID, getLoggedMemberID());

	// if this record is a copy of another record, copy children if applicable
	if(strlen(Request::val('SelectedID'))) projects_copy_children($recID, Request::val('SelectedID'));

	return $recID;
}

function projects_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function projects_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('projects', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: projects_before_delete
	if(function_exists('projects_before_delete')) {
		$args = [];
		if(!projects_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	// child table: assignments
	$res = sql("SELECT `Id` FROM `projects` WHERE `Id`='{$selected_id}'", $eo);
	$Id = db_fetch_row($res);
	$rires = sql("SELECT COUNT(1) FROM `assignments` WHERE `ProjectId`='" . makeSafe($Id[0]) . "'", $eo);
	$rirow = db_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'assignments', $RetMsg);
		return $RetMsg;
	} elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation['confirm delete'];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'assignments', $RetMsg);
		$RetMsg = str_replace('<Delete>', '<input type="button" class="btn btn-danger" value="' . html_attr($Translation['yes']) . '" onClick="window.location = \'projects_view.php?SelectedID=' . urlencode($selected_id) . '&delete_x=1&confirmed=1&csrf_token=' . urlencode(csrf_token(false, true)) . '\';">', $RetMsg);
		$RetMsg = str_replace('<Cancel>', '<input type="button" class="btn btn-success" value="' . html_attr($Translation[ 'no']) . '" onClick="window.location = \'projects_view.php?SelectedID=' . urlencode($selected_id) . '\';">', $RetMsg);
		return $RetMsg;
	}

	sql("DELETE FROM `projects` WHERE `Id`='{$selected_id}'", $eo);

	// hook: projects_after_delete
	if(function_exists('projects_after_delete')) {
		$args = [];
		projects_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='projects' AND `pkValue`='{$selected_id}'", $eo);
}

function projects_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('projects', $selected_id, 'edit')) return false;

	$data = [
		'Name' => Request::val('Name', ''),
		'StartDate' => Request::dateComponents('StartDate', ''),
		'EndDate' => Request::dateComponents('EndDate', ''),
	];

	// get existing values
	$old_data = getRecord('projects', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: projects_before_update
	if(function_exists('projects_before_update')) {
		$args = ['old_data' => $old_data];
		if(!projects_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'projects', 
		backtick_keys_once($set), 
		['`Id`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="projects_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	$eo = ['silentErrors' => true];

	update_calc_fields('projects', $data['selectedID'], calculated_fields()['projects']);

	// hook: projects_after_update
	if(function_exists('projects_after_update')) {
		$res = sql("SELECT * FROM `projects` WHERE `Id`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['Id'];
		$args = ['old_data' => $old_data];
		if(!projects_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update ownership data
	sql("UPDATE `membership_userrecords` SET `dateUpdated`='" . time() . "' WHERE `tableName`='projects' AND `pkValue`='" . makeSafe($selected_id) . "'", $eo);
}

function projects_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $separateDV = 0, $TemplateDV = '', $TemplateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;
	$eo = ['silentErrors' => true];
	$noUploads = null;
	$row = $urow = $jsReadOnly = $jsEditable = $lookups = null;

	// mm: get table permissions
	$arrPerm = getTablePermissions('projects');
	if(!$arrPerm['insert'] && $selected_id == '')
		// no insert permission and no record selected
		// so show access denied error unless TVDV
		return $separateDV ? $Translation['tableAccessDenied'] : '';
	$AllowInsert = ($arrPerm['insert'] ? true : false);
	// print preview?
	$dvprint = false;
	if(strlen($selected_id) && Request::val('dvprint_x') != '') {
		$dvprint = true;
	}


	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: StartDate
	$combo_StartDate = new DateCombo;
	$combo_StartDate->DateFormat = "dmy";
	$combo_StartDate->MinYear = defined('projects.StartDate.MinYear') ? constant('projects.StartDate.MinYear') : 1900;
	$combo_StartDate->MaxYear = defined('projects.StartDate.MaxYear') ? constant('projects.StartDate.MaxYear') : 2100;
	$combo_StartDate->DefaultDate = parseMySQLDate('', '');
	$combo_StartDate->MonthNames = $Translation['month names'];
	$combo_StartDate->NamePrefix = 'StartDate';
	// combobox: EndDate
	$combo_EndDate = new DateCombo;
	$combo_EndDate->DateFormat = "dmy";
	$combo_EndDate->MinYear = defined('projects.EndDate.MinYear') ? constant('projects.EndDate.MinYear') : 1900;
	$combo_EndDate->MaxYear = defined('projects.EndDate.MaxYear') ? constant('projects.EndDate.MaxYear') : 2100;
	$combo_EndDate->DefaultDate = parseMySQLDate('', '');
	$combo_EndDate->MonthNames = $Translation['month names'];
	$combo_EndDate->NamePrefix = 'EndDate';

	if($selected_id) {
		// mm: check member permissions
		if(!$arrPerm['view']) return $Translation['tableAccessDenied'];

		// mm: who is the owner?
		$ownerGroupID = sqlValue("SELECT `groupID` FROM `membership_userrecords` WHERE `tableName`='projects' AND `pkValue`='" . makeSafe($selected_id) . "'");
		$ownerMemberID = sqlValue("SELECT LCASE(`memberID`) FROM `membership_userrecords` WHERE `tableName`='projects' AND `pkValue`='" . makeSafe($selected_id) . "'");

		if($arrPerm['view'] == 1 && getLoggedMemberID() != $ownerMemberID) return $Translation['tableAccessDenied'];
		if($arrPerm['view'] == 2 && getLoggedGroupID() != $ownerGroupID) return $Translation['tableAccessDenied'];

		// can edit?
		$AllowUpdate = 0;
		if(($arrPerm['edit'] == 1 && $ownerMemberID == getLoggedMemberID()) || ($arrPerm['edit'] == 2 && $ownerGroupID == getLoggedGroupID()) || $arrPerm['edit'] == 3) {
			$AllowUpdate = 1;
		}

		$res = sql("SELECT * FROM `projects` WHERE `Id`='" . makeSafe($selected_id) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'projects_view.php', false);
		}
		$combo_StartDate->DefaultDate = $row['StartDate'];
		$combo_EndDate->DefaultDate = $row['EndDate'];
		$urow = $row; /* unsanitized data */
		$row = array_map('safe_html', $row);
	} else {
		$filterField = Request::val('FilterField');
		$filterOperator = Request::val('FilterOperator');
		$filterValue = Request::val('FilterValue');
	}

	ob_start();
	?>

	<script>
		// initial lookup values

		jQuery(function() {
			setTimeout(function() {
			}, 50); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_clean());


	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/projects_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/projects_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Detail View', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', (Request::val('Embedded') ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert) {
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return projects_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return projects_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if(Request::val('Embedded')) {
		$backAction = 'AppGini.closeParentModal(); return false;';
	} else {
		$backAction = '$j(\'form\').eq(0).attr(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id) {
		if(!Request::val('Embedded')) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$j(\'form\').eq(0).prop(\'novalidate\', true); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate) {
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return projects_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		} else {
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		}
		if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3) { // allow delete?
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" onclick="return confirm(\'' . $Translation['are you sure?'] . '\');" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		} else {
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', ($separateDV ? '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>' : ''), $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate && !$AllowInsert) || (!$selected_id && !$AllowInsert)) {
		$jsReadOnly = '';
		$jsReadOnly .= "\tjQuery('#Name').replaceWith('<div class=\"form-control-static\" id=\"Name\">' + (jQuery('#Name').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#StartDate').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#StartDateDay, #StartDateMonth, #StartDateYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#EndDate').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#EndDateDay, #EndDateMonth, #EndDateYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	} elseif($AllowInsert) {
		$jsEditable = "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(StartDate)%%>', ($selected_id && !$arrPerm[3] ? '<div class="form-control-static">' . $combo_StartDate->GetHTML(true) . '</div>' : $combo_StartDate->GetHTML()), $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(StartDate)%%>', $combo_StartDate->GetHTML(true), $templateCode);
	$templateCode = str_replace('<%%COMBO(EndDate)%%>', ($selected_id && !$arrPerm[3] ? '<div class="form-control-static">' . $combo_EndDate->GetHTML(true) . '</div>' : $combo_EndDate->GetHTML()), $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(EndDate)%%>', $combo_EndDate->GetHTML(true), $templateCode);

	/* lookup fields array: 'lookup field name' => ['parent table name', 'lookup field caption'] */
	$lookup_fields = [];
	foreach($lookup_fields as $luf => $ptfc) {
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if($pt_perm['view'] || $pt_perm['edit']) {
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] /* && !Request::val('Embedded')*/) {
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-default add_new_parent" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus text-success"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(Id)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Name)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(StartDate)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(EndDate)%%>', '', $templateCode);

	// process values
	if($selected_id) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Id)%%>', safe_html($urow['Id']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Id)%%>', html_attr($row['Id']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Id)%%>', urlencode($urow['Id']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Name)%%>', safe_html($urow['Name']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Name)%%>', html_attr($row['Name']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Name)%%>', urlencode($urow['Name']), $templateCode);
		$templateCode = str_replace('<%%VALUE(StartDate)%%>', app_datetime($row['StartDate']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(StartDate)%%>', urlencode(app_datetime($urow['StartDate'])), $templateCode);
		$templateCode = str_replace('<%%VALUE(EndDate)%%>', app_datetime($row['EndDate']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(EndDate)%%>', urlencode(app_datetime($urow['EndDate'])), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(Id)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Id)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Name)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Name)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(StartDate)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(StartDate)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(EndDate)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(EndDate)%%>', urlencode(''), $templateCode);
	}

	// process translations
	$templateCode = parseTemplate($templateCode);

	// clear scrap
	$templateCode = str_replace('<%%', '<!-- ', $templateCode);
	$templateCode = str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if(Request::val('dvprint_x') == '') {
		$templateCode .= "\n\n<script>\$j(function() {\n";
		$arrTables = getTableList();
		foreach($arrTables as $name => $caption) {
			$templateCode .= "\t\$j('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\t\$j('#xs_{$name}_link').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id) {
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode .= '<script>';
	$templateCode .= '$j(function() {';


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields
	$filterField = Request::val('FilterField');
	$filterOperator = Request::val('FilterOperator');
	$filterValue = Request::val('FilterValue');

	// don't include blank images in lightbox gallery
	$templateCode = preg_replace('/blank.gif" data-lightbox=".*?"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	/* default field values */
	$rdata = $jdata = get_defaults('projects');
	if($selected_id) {
		$jdata = get_joined_record('projects', $selected_id);
		if($jdata === false) $jdata = get_defaults('projects');
		$rdata = $row;
	}
	$templateCode .= loadView('projects-ajax-cache', ['rdata' => $rdata, 'jdata' => $jdata]);

	// hook: projects_dv
	if(function_exists('projects_dv')) {
		$args=[];
		projects_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}