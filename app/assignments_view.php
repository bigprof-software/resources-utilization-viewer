<?php
// This script and data application were generated by AppGini 22.11
// Download AppGini for free from https://bigprof.com/appgini/download/

	include_once(__DIR__ . '/lib.php');
	@include_once(__DIR__ . '/hooks/assignments.php');
	include_once(__DIR__ . '/assignments_dml.php');

	// mm: can the current member access this page?
	$perm = getTablePermissions('assignments');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied']);
		exit;
	}

	$x = new DataList;
	$x->TableName = 'assignments';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`assignments`.`Id`" => "Id",
		"IF(    CHAR_LENGTH(`projects1`.`Name`), CONCAT_WS('',   `projects1`.`Name`), '') /* Project */" => "ProjectId",
		"IF(    CHAR_LENGTH(if(`projects1`.`StartDate`,date_format(`projects1`.`StartDate`,'%d/%m/%Y'),'')) || CHAR_LENGTH(if(`projects1`.`EndDate`,date_format(`projects1`.`EndDate`,'%d/%m/%Y'),'')), CONCAT_WS('',   if(`projects1`.`StartDate`,date_format(`projects1`.`StartDate`,'%d/%m/%Y'),''), ' <b>to</b> ', if(`projects1`.`EndDate`,date_format(`projects1`.`EndDate`,'%d/%m/%Y'),'')), '') /* Project Duration */" => "ProjectDuration",
		"IF(    CHAR_LENGTH(`resources1`.`Name`), CONCAT_WS('',   `resources1`.`Name`), '') /* Resource */" => "ResourceId",
		"`assignments`.`Commitment`" => "Commitment",
		"if(`assignments`.`StartDate`,date_format(`assignments`.`StartDate`,'%d/%m/%Y'),'')" => "StartDate",
		"if(`assignments`.`EndDate`,date_format(`assignments`.`EndDate`,'%d/%m/%Y'),'')" => "EndDate",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`assignments`.`Id`',
		2 => '`projects1`.`Name`',
		3 => 3,
		4 => '`resources1`.`Name`',
		5 => '`assignments`.`Commitment`',
		6 => '`assignments`.`StartDate`',
		7 => '`assignments`.`EndDate`',
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`assignments`.`Id`" => "Id",
		"IF(    CHAR_LENGTH(`projects1`.`Name`), CONCAT_WS('',   `projects1`.`Name`), '') /* Project */" => "ProjectId",
		"IF(    CHAR_LENGTH(if(`projects1`.`StartDate`,date_format(`projects1`.`StartDate`,'%d/%m/%Y'),'')) || CHAR_LENGTH(if(`projects1`.`EndDate`,date_format(`projects1`.`EndDate`,'%d/%m/%Y'),'')), CONCAT_WS('',   if(`projects1`.`StartDate`,date_format(`projects1`.`StartDate`,'%d/%m/%Y'),''), ' <b>to</b> ', if(`projects1`.`EndDate`,date_format(`projects1`.`EndDate`,'%d/%m/%Y'),'')), '') /* Project Duration */" => "ProjectDuration",
		"IF(    CHAR_LENGTH(`resources1`.`Name`), CONCAT_WS('',   `resources1`.`Name`), '') /* Resource */" => "ResourceId",
		"`assignments`.`Commitment`" => "Commitment",
		"if(`assignments`.`StartDate`,date_format(`assignments`.`StartDate`,'%d/%m/%Y'),'')" => "StartDate",
		"if(`assignments`.`EndDate`,date_format(`assignments`.`EndDate`,'%d/%m/%Y'),'')" => "EndDate",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`assignments`.`Id`" => "ID",
		"IF(    CHAR_LENGTH(`projects1`.`Name`), CONCAT_WS('',   `projects1`.`Name`), '') /* Project */" => "Project",
		"IF(    CHAR_LENGTH(if(`projects1`.`StartDate`,date_format(`projects1`.`StartDate`,'%d/%m/%Y'),'')) || CHAR_LENGTH(if(`projects1`.`EndDate`,date_format(`projects1`.`EndDate`,'%d/%m/%Y'),'')), CONCAT_WS('',   if(`projects1`.`StartDate`,date_format(`projects1`.`StartDate`,'%d/%m/%Y'),''), ' <b>to</b> ', if(`projects1`.`EndDate`,date_format(`projects1`.`EndDate`,'%d/%m/%Y'),'')), '') /* Project Duration */" => "Project Duration",
		"IF(    CHAR_LENGTH(`resources1`.`Name`), CONCAT_WS('',   `resources1`.`Name`), '') /* Resource */" => "Resource",
		"`assignments`.`Commitment`" => "Commitment",
		"`assignments`.`StartDate`" => "Start Date",
		"`assignments`.`EndDate`" => "End Date",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`assignments`.`Id`" => "Id",
		"IF(    CHAR_LENGTH(`projects1`.`Name`), CONCAT_WS('',   `projects1`.`Name`), '') /* Project */" => "ProjectId",
		"IF(    CHAR_LENGTH(if(`projects1`.`StartDate`,date_format(`projects1`.`StartDate`,'%d/%m/%Y'),'')) || CHAR_LENGTH(if(`projects1`.`EndDate`,date_format(`projects1`.`EndDate`,'%d/%m/%Y'),'')), CONCAT_WS('',   if(`projects1`.`StartDate`,date_format(`projects1`.`StartDate`,'%d/%m/%Y'),''), ' <b>to</b> ', if(`projects1`.`EndDate`,date_format(`projects1`.`EndDate`,'%d/%m/%Y'),'')), '') /* Project Duration */" => "ProjectDuration",
		"IF(    CHAR_LENGTH(`resources1`.`Name`), CONCAT_WS('',   `resources1`.`Name`), '') /* Resource */" => "ResourceId",
		"`assignments`.`Commitment`" => "Commitment",
		"if(`assignments`.`StartDate`,date_format(`assignments`.`StartDate`,'%d/%m/%Y'),'')" => "StartDate",
		"if(`assignments`.`EndDate`,date_format(`assignments`.`EndDate`,'%d/%m/%Y'),'')" => "EndDate",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = ['ProjectId' => 'Project', 'ResourceId' => 'Resource', ];

	$x->QueryFrom = "`assignments` LEFT JOIN `projects` as projects1 ON `projects1`.`Id`=`assignments`.`ProjectId` LEFT JOIN `resources` as resources1 ON `resources1`.`Id`=`assignments`.`ResourceId` ";
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
	$x->ScriptFileName = 'assignments_view.php';
	$x->RedirectAfterInsert = 'assignments_view.php?SelectedID=#ID#';
	$x->TableTitle = 'Assignments';
	$x->TableIcon = 'resources/table_icons/client_account_template.png';
	$x->PrimaryKey = '`assignments`.`Id`';
	$x->DefaultSortField = '`assignments`.`EndDate`';
	$x->DefaultSortDirection = 'desc';

	$x->ColWidth = [150, 150, 150, 150, 150, ];
	$x->ColCaption = ['Project', 'Resource', 'Commitment', 'Start Date', 'End Date', ];
	$x->ColFieldName = ['ProjectId', 'ResourceId', 'Commitment', 'StartDate', 'EndDate', ];
	$x->ColNumber  = [2, 4, 5, 6, 7, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/assignments_templateTV.html';
	$x->SelectedTemplate = 'templates/assignments_templateTVS.html';
	$x->TemplateDV = 'templates/assignments_templateDV.html';
	$x->TemplateDVP = 'templates/assignments_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = false;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// hook: assignments_init
	$render = true;
	if(function_exists('assignments_init')) {
		$args = [];
		$render = assignments_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: assignments_header
	$headerCode = '';
	if(function_exists('assignments_header')) {
		$args = [];
		$headerCode = assignments_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once(__DIR__ . '/header.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/header.php');
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: assignments_footer
	$footerCode = '';
	if(function_exists('assignments_footer')) {
		$args = [];
		$footerCode = assignments_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once(__DIR__ . '/footer.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/footer.php');
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
