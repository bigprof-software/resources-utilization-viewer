<?php
// This script and data application were generated by AppGini 22.11
// Download AppGini for free from https://bigprof.com/appgini/download/

	include_once(__DIR__ . '/lib.php');
	@include_once(__DIR__ . '/hooks/projects.php');
	include_once(__DIR__ . '/projects_dml.php');

	// mm: can the current member access this page?
	$perm = getTablePermissions('projects');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied']);
		exit;
	}

	$x = new DataList;
	$x->TableName = 'projects';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`projects`.`Id`" => "Id",
		"`projects`.`Name`" => "Name",
		"if(`projects`.`StartDate`,date_format(`projects`.`StartDate`,'%d/%m/%Y'),'')" => "StartDate",
		"if(`projects`.`EndDate`,date_format(`projects`.`EndDate`,'%d/%m/%Y'),'')" => "EndDate",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`projects`.`Id`',
		2 => 2,
		3 => '`projects`.`StartDate`',
		4 => '`projects`.`EndDate`',
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`projects`.`Id`" => "Id",
		"`projects`.`Name`" => "Name",
		"if(`projects`.`StartDate`,date_format(`projects`.`StartDate`,'%d/%m/%Y'),'')" => "StartDate",
		"if(`projects`.`EndDate`,date_format(`projects`.`EndDate`,'%d/%m/%Y'),'')" => "EndDate",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`projects`.`Id`" => "ID",
		"`projects`.`Name`" => "Name",
		"`projects`.`StartDate`" => "Start Date",
		"`projects`.`EndDate`" => "End Date",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`projects`.`Id`" => "Id",
		"`projects`.`Name`" => "Name",
		"if(`projects`.`StartDate`,date_format(`projects`.`StartDate`,'%d/%m/%Y'),'')" => "StartDate",
		"if(`projects`.`EndDate`,date_format(`projects`.`EndDate`,'%d/%m/%Y'),'')" => "EndDate",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = [];

	$x->QueryFrom = "`projects` ";
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
	$x->ScriptFileName = 'projects_view.php';
	$x->RedirectAfterInsert = 'projects_view.php?SelectedID=#ID#';
	$x->TableTitle = 'Projects';
	$x->TableIcon = 'resources/table_icons/application_from_storage.png';
	$x->PrimaryKey = '`projects`.`Id`';
	$x->DefaultSortField = '`projects`.`StartDate`';
	$x->DefaultSortDirection = 'asc';

	$x->ColWidth = [150, 150, 150, ];
	$x->ColCaption = ['Name', 'Start Date', 'End Date', ];
	$x->ColFieldName = ['Name', 'StartDate', 'EndDate', ];
	$x->ColNumber  = [2, 3, 4, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/projects_templateTV.html';
	$x->SelectedTemplate = 'templates/projects_templateTVS.html';
	$x->TemplateDV = 'templates/projects_templateDV.html';
	$x->TemplateDVP = 'templates/projects_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = false;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// hook: projects_init
	$render = true;
	if(function_exists('projects_init')) {
		$args = [];
		$render = projects_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: projects_header
	$headerCode = '';
	if(function_exists('projects_header')) {
		$args = [];
		$headerCode = projects_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once(__DIR__ . '/header.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/header.php');
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: projects_footer
	$footerCode = '';
	if(function_exists('projects_footer')) {
		$args = [];
		$footerCode = projects_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once(__DIR__ . '/footer.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/footer.php');
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
