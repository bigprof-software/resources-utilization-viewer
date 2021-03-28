<?php
// This script and data application were generated by AppGini 5.95
// Download AppGini for free from https://bigprof.com/appgini/download/

	$currDir = dirname(__FILE__);
	include_once("{$currDir}/lib.php");
	@include_once("{$currDir}/hooks/resources.php");
	include_once("{$currDir}/resources_dml.php");

	// mm: can the current member access this page?
	$perm = getTablePermissions('resources');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout(function() { window.location = "index.php?signOut=1"; }, 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = 'resources';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`resources`.`Id`" => "Id",
		"`resources`.`Name`" => "Name",
		"concat('<i class=\"glyphicon glyphicon-', if(`resources`.`Available`, 'check', 'unchecked'), '\"></i>')" => "Available",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`resources`.`Id`',
		2 => 2,
		3 => 3,
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`resources`.`Id`" => "Id",
		"`resources`.`Name`" => "Name",
		"`resources`.`Available`" => "Available",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`resources`.`Id`" => "ID",
		"`resources`.`Name`" => "Name",
		"`resources`.`Available`" => "Available",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`resources`.`Id`" => "Id",
		"`resources`.`Name`" => "Name",
		"concat('<i class=\"glyphicon glyphicon-', if(`resources`.`Available`, 'check', 'unchecked'), '\"></i>')" => "Available",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = [];

	$x->QueryFrom = "`resources` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm['view'] == 0 ? 1 : 0);
	$x->AllowDelete = $perm['delete'];
	$x->AllowMassDelete = true;
	$x->AllowInsert = $perm['insert'];
	$x->AllowUpdate = $perm['edit'];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 1;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowPrintingDV = 1;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation['quick search'];
	$x->ScriptFileName = 'resources_view.php';
	$x->RedirectAfterInsert = 'resources_view.php?SelectedID=#ID#';
	$x->TableTitle = 'Resources';
	$x->TableIcon = 'resources/table_icons/account_balances.png';
	$x->PrimaryKey = '`resources`.`Id`';
	$x->DefaultSortField = '2';
	$x->DefaultSortDirection = 'asc';

	$x->ColWidth = [150, 150, ];
	$x->ColCaption = ['Name', 'Available', ];
	$x->ColFieldName = ['Name', 'Available', ];
	$x->ColNumber  = [2, 3, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/resources_templateTV.html';
	$x->SelectedTemplate = 'templates/resources_templateTVS.html';
	$x->TemplateDV = 'templates/resources_templateDV.html';
	$x->TemplateDVP = 'templates/resources_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = false;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, ['user', 'group'])) { $DisplayRecords = 'all'; }
	if($perm['view'] == 1 || ($perm['view'] > 1 && $DisplayRecords == 'user' && !$_REQUEST['NoFilter_x'])) { // view owner only
		$x->QueryFrom .= ', `membership_userrecords`';
		$x->QueryWhere = "WHERE `resources`.`Id`=`membership_userrecords`.`pkValue` AND `membership_userrecords`.`tableName`='resources' AND LCASE(`membership_userrecords`.`memberID`)='" . getLoggedMemberID() . "'";
	} elseif($perm['view'] == 2 || ($perm['view'] > 2 && $DisplayRecords == 'group' && !$_REQUEST['NoFilter_x'])) { // view group only
		$x->QueryFrom .= ', `membership_userrecords`';
		$x->QueryWhere = "WHERE `resources`.`Id`=`membership_userrecords`.`pkValue` AND `membership_userrecords`.`tableName`='resources' AND `membership_userrecords`.`groupID`='" . getLoggedGroupID() . "'";
	} elseif($perm['view'] == 3) { // view all
		// no further action
	} elseif($perm['view'] == 0) { // view none
		$x->QueryFields = ['Not enough permissions' => 'NEP'];
		$x->QueryFrom = '`resources`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: resources_init
	$render = true;
	if(function_exists('resources_init')) {
		$args = [];
		$render = resources_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: resources_header
	$headerCode = '';
	if(function_exists('resources_header')) {
		$args = [];
		$headerCode = resources_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once("{$currDir}/header.php"); 
	} else {
		ob_start();
		include_once("{$currDir}/header.php");
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: resources_footer
	$footerCode = '';
	if(function_exists('resources_footer')) {
		$args = [];
		$footerCode = resources_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once("{$currDir}/footer.php"); 
	} else {
		ob_start();
		include_once("{$currDir}/footer.php");
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
