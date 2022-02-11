<?php
	define('PREPEND_PATH', '../');
	$appDir = dirname(__DIR__);
	include("$appDir/lib.php");
	
	// config
	// TODO: these should be set in js or css rather than server-side to accomodate screen size
	$chart = [
		'dayWidth' => .2,
		'resourceHeight' => 5,
		'left' => 19,
		'top' => 22,
		'resourceSeparator' => .3,
		'colors' => [
			'#FFC57F', '#B9FF7F', '#7FFFC5', '#7FB9FF', '#C47FFF', '#FF7FB9', '#D8984B', '#8BD84B', '#4BD898', '#4B8BD8', '#984BD8', '#D84B8B', '#D8B68C', '#AFD88C', '#8CD8B6', '#8CAFD8', '#B68CD8', '#D88CAF'
		],
	];

	$t1 = microtime(true);
	
	// some initilization
	$resourceProject = $project = $projectColor = $resource = $resourceIndex = $unavailableResource = $assignment = [];

	// chart parameters
	$year = intval($_GET['year']);
	if(!$year) $year = date('Y');

	$startDate = "$year-01-01";
	$endDate = "$year-12-31";
	
	// get projects
	$eo = ['silentErrors' => true];
	$res = sql("SELECT `Id`, `Name` FROM `projects`", $eo);
	$i = 0;
	while($row = db_fetch_row($res)) {
		$project[$row[0]] = $row[1];
		$projectColor[$row[0]] = $chart['colors'][$i++];
	}
	
	// get resources
	$res = sql("SELECT `Id`, `Name`, `Available` FROM `resources`", $eo);
	while($row = db_fetch_row($res)) {
		$resourceIndex[] = $row[0];
		$resource[$row[0]] = $row[1];
		if(!$row[2]) $unavailableResource[$row[0]] = $row[1];
	}

	// invert resourceIndex so that we can retrieve the 1-based index of a resource given resource id
	$resourceIndex = array_flip($resourceIndex);
	
	// get assignments for open projects for selected year
	$assignment = [];
	$res = sql("SELECT * FROM `assignments` WHERE `StartDate` <= '$endDate' AND `EndDate` >= '$startDate'", $eo);
	while($row = db_fetch_assoc($res)) {
		$ResourceId = $row['ResourceId'];
		$ProjectId = $row['ProjectId'];

		$assignment[] = [
			'Id' => $row['Id'],
			'ProjectId' => $ProjectId,
			'ResourceId' => $ResourceId,
			'StartTS' => strtotime($row['StartDate']),
			'EndTS' => strtotime($row['EndDate']),
			'Commitment' => $row['Commitment'],
		];
		
		if(!isset($resourceProject[$ResourceId]))
			$resourceProject[$ResourceId][$ProjectId] = 0;
		else
			$resourceProject[$ResourceId][$ProjectId] = count($resourceProject[$ResourceId]);
	}
	
	$t2 = microtime(true);
	
	
	
	/*******************************************************
		View code begins below
		TODO: the code below is in need of serious refactoring
	*******************************************************/
	include_once("$appDir/header.php");
	
	// Years navigator
	$prevYear = $year - 1;
	$nextYear = $year + 1;
	?>
	
	<script>
		$j(function() {
			// reload every 60 seconds
			setTimeout(() => { location.reload(); }, 60000);

			// hide today button if not current year
			let showToday = false;
			try {
				showToday = parseInt(location.search.match(/year=(\d+)/)[1]) == (new Date).getFullYear();
			} catch(e) {
				showToday = true; // if no year in url, this means we're displaying current year
			}

			$j('.toggle-today')
				.toggleClass('hidden', !showToday)
				.on('click', function() {
					let btn = $j(this), activate = btn.hasClass('btn-default');

					btn
						.toggleClass('btn-info', activate)
						.toggleClass('btn-default', !activate)

					$j('.today-line').toggleClass('hidden', !activate);
				})
		})
	</script>

	<div class="page-header" style="position: absolute; left: 10vw; top: 3vh; width: 80vw;"><h1 class="text-center">
		<a class="btn btn-default btn-lg hspacer-lg" href="chart-resources.php?year=<?php echo $prevYear; ?>"><?php echo $prevYear; ?></a>
		<?php echo $year; ?>
		<a class="btn btn-default btn-lg hspacer-lg" href="chart-resources.php?year=<?php echo $nextYear; ?>"><?php echo $nextYear; ?></a>

		<button type="button" class="btn btn-default pull-right toggle-today hidden-print" style="margin-top: 3vh;"><i class="glyphicon glyphicon-eye-open"></i> Today</button>
	</h1></div>

	<div class="chart-area">
	<?php

	// Display 'Resource' header
	?>
	<div
		class="label-header"
		style="
			position: absolute;
			height: <?php echo ((count($resource) + 1) * ($chart['resourceHeight'] + $chart['resourceSeparator'])); ?>vh;
			top: <?php echo ($chart['top'] + $chart['resourceHeight'] + $chart['resourceSeparator']); ?>vh;
			font-family: Arial; font-size: 12px; font-weight: bold;
		">
		Resource
	</div>

	<?php

	// Display month grid lines
	$prevLeft = $chart['left'] ?? 0;
	$thisMonth = date('n');
	$thisYear = date('Y');
	for($m = 1; $m <= 12; $m++) {
		$daysPerMonth = date('t', strtotime("$year-$m-01"));
		?>
		<div
			class="month-label"
			style="
				position: absolute;
				left: <?php echo $prevLeft; ?>vw;
				height: <?php echo ((count($resource) + 1) * ($chart['resourceHeight'] + $chart['resourceSeparator'])); ?>vh;
				border-left: dotted 1px Silver;
				<?php if($m == 12) { ?>border-right: dotted 1px Silver;<?php } ?>
				top: <?php echo ($chart['top'] + $chart['resourceHeight'] + $chart['resourceSeparator']); ?>vh;
				text-align: center;
				font-family: Arial; font-size: 10px; font-weight: bold;
				width: <?php echo ($daysPerMonth * $chart['dayWidth']); ?>vw;
			">
			<?php echo date('M Y', strtotime("$year-$m-01")); ?>
		</div>
		<?php
		
		// today line
		if($year == $thisYear && $m == $thisMonth) {
			?>
			<div
				class="today-line hidden"
				title="Today, <?php echo date('j/n/Y'); ?>"
				style="
					border-left: solid 2px DarkRed;
					position: absolute;
					top: <?php echo ($chart['top'] + ($chart['resourceHeight'] + $chart['resourceSeparator']) * 1.5); ?>vh;
					left: <?php echo ($prevLeft + (date('j') - 1) * $chart['dayWidth']); ?>vw;
					height: <?php echo ((count($resource) + 0.5) * ($chart['resourceHeight'] + $chart['resourceSeparator'])); ?>vh;
					z-index: 2;
				"></div>
			<?php
		}
		
		$prevLeft += $daysPerMonth * $chart['dayWidth'];
	}
	
	
	// Display resource names
	foreach($resource as $ResourceId => $ResourceName) {
		$i = $resourceIndex[$ResourceId] + 1;
		$available = !array_key_exists($ResourceId, $unavailableResource);
		?><div
			class="resource"
			style="
				position: absolute;
				top: <?php echo ($chart['resourceHeight'] * ($i + 1) + $chart['top'] + $i * $chart['resourceSeparator']); ?>vh;
				border-bottom: solid 1px Silver;
				width: <?php echo (365 * $chart['dayWidth'] + $chart['left']); ?>vw;
				height: <?php echo intval($chart['resourceHeight']); ?>vh;
				font-family: Arial;
				font-size: 12px;
			">
				<a
					href="../resources_view.php?SelectedID=<?php echo $ResourceId; ?>"
					style="
						text-decoration: <?php echo ($available ? 'none' : 'line-through'); ?>;
						color: <?php echo ($available ? 'DarkBlue' : 'Silver'); ?>;
					"><?php echo $ResourceName; ?></a>
		</div>
		<?php
	}

	
	// Display project assignment bars
	$yearStartTS = strtotime(date("$year-01-01"));
	$yearEndTS = strtotime(date("$year-12-31 23:59:59"));
	foreach($assignment as $assDetails){
		$i = $resourceIndex[$assDetails['ResourceId']] + 1;
		$chartStartTS = max($assDetails['StartTS'], $yearStartTS);
		$chartEndTS = min($assDetails['EndTS'], $yearEndTS);
		?><div
			style="
				position: absolute;
				width: <?php echo intval(($chartEndTS - $chartStartTS + 86400) / 86400 * $chart['dayWidth']); ?>vw;
				left: <?php echo intval(($chartStartTS - strtotime("$year-01-01")) / 86400 * $chart['dayWidth'] + $chart['left']); ?>vw;
				height: <?php echo intval($chart['resourceHeight'] * $assDetails['Commitment']); ?>vh;
				background-color: <?php echo $projectColor[$assDetails['ProjectId']]; ?>;
				top: <?php 
					echo (
						$chart['top'] + 
						$chart['resourceHeight'] * ($i + 1) + 
						$i * $chart['resourceSeparator'] + 
						($assDetails['Commitment'] < 1 ?
							($resourceProject[$assDetails['ResourceId']][$assDetails['ProjectId']] > 1 ?
								(1 - $assDetails['Commitment']) * $chart['resourceHeight']
							:
								0
							)
						:
							0
						)
					); ?>vh;
				cursor: pointer;
				text-align: center;
				font-size: 10px;
				font-family: Arial;
				color: #004 !important;
				font-weight: bold;
				opacity: 0.5;
				filter:alpha(opacity=50);
				white-space:nowrap;
				overflow:hidden;
			"
			title="<?php echo htmlspecialchars($project[$assDetails['ProjectId']].': '.$resource[$assDetails['ResourceId']]); ?>. <?php echo ($assDetails['Commitment'] * 100); ?>% commitment from <?php echo date('j/n/Y', $assDetails['StartTS']); ?> to <?php echo date('j/n/Y', $assDetails['EndTS']); ?>"
			onclick="window.location='../assignments_view.php?SelectedID=<?php echo $assDetails['Id']; ?>';">
				<?php echo $project[$assDetails['ProjectId']]; ?>
		</div><?php
	}

	?></div><?php

	include_once("$appDir/footer.php");
