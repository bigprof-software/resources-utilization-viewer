<?php
	define('PREPEND_PATH', '../');
	$appDir = dirname(__DIR__);
	include("$appDir/lib.php");
	
	// config
	$chart = [
		'dayWidth' => .2,
		'projectHeight' => 5,
		'left' => 19,
		'top' => 22,
		'projectSeparator' => .3,
		'colors' => [
			'#FFC57F', '#B9FF7F', '#7FFFC5', '#7FB9FF', '#C47FFF', '#FF7FB9', '#D8984B', '#8BD84B', '#4BD898', '#4B8BD8', '#984BD8', '#D84B8B', '#D8B68C', '#AFD88C', '#8CD8B6', '#8CAFD8', '#B68CD8', '#D88CAF'
		],
	];

	// some initilization
	$project = $projectColor = $proejectIndex = [];

	// chart parameters
	$year = intval($_GET['year']);
	if(!$year) $year = date('Y');

	$startDate = "$year-01-01";
	$endDate = "$year-12-31";
	
	// get projects that are active during specified year
	$eo = ['silentErrors' => true];
	$res = sql("SELECT `Id`, `Name`, `StartDate`, `EndDate` FROM `projects` WHERE NOT `StartDate`>'$endDate' AND NOT `EndDate`<'$startDate'", $eo);
	$i = 0;
	while($row = db_fetch_assoc($res)) {
		$projectIndex[] = $row['Id'];
		$project[$row['Id']] = $row;
		$projectColor[$row['Id']] = $chart['colors'][$i++];
	}

	// invert projectIndex so that we can retrieve the 1-based index of a project given project id
	$projectIndex = array_flip($projectIndex);

	
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
		<a class="btn btn-default btn-lg hspacer-lg hidden-print" href="chart-projects.php?year=<?php echo $prevYear; ?>"><?php echo $prevYear; ?></a>
		<?php echo $year; ?>
		<a class="btn btn-default btn-lg hspacer-lg hidden-print" href="chart-projects.php?year=<?php echo $nextYear; ?>"><?php echo $nextYear; ?></a>

		<button type="button" class="btn btn-default pull-right toggle-today hidden-print" style="margin-top: 3vh;"><i class="glyphicon glyphicon-eye-open"></i> Today</button>
	</h1></div>

	<div class="chart-area">
	<?php

	// Display 'Project' header
	?>
	<div
		class="label-header"
		style="
			position: absolute;
			height: <?php echo ((count($project) + 1) * ($chart['projectHeight'] + $chart['projectSeparator'])); ?>vh;
			top: <?php echo ($chart['top'] + $chart['projectHeight'] + $chart['projectSeparator']); ?>vh;
			font-family: Arial; font-size: 12px; font-weight: bold;
		">
		Project
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
				height: <?php echo ((count($project) + 1) * ($chart['projectHeight'] + $chart['projectSeparator'])); ?>vh;
				border-left: dotted 1px Silver;
				<?php if($m == 12) { ?>border-right: dotted 1px Silver;<?php } ?>
				top: <?php echo ($chart['top'] + $chart['projectHeight'] + $chart['projectSeparator']); ?>vh;
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
					top: <?php echo ($chart['top'] + ($chart['projectHeight'] + $chart['projectSeparator']) * 1.5); ?>vh;
					left: <?php echo ($prevLeft + (date('j') - 1) * $chart['dayWidth']); ?>vw;
					height: <?php echo ((count($project) + 0.5) * ($chart['projectHeight'] + $chart['projectSeparator'])); ?>vh;
					z-index: 2;
				"></div>
			<?php
		}
		
		$prevLeft += $daysPerMonth * $chart['dayWidth'];
	}
	
	
	// Display project names
	foreach($project as $projectId => $prj) {
		$i = $projectIndex[$projectId] + 1;
		?><div
			class="project"
			style="
				position: absolute;
				top: <?php echo ($chart['projectHeight'] * ($i + 1) + $chart['top'] + $i * $chart['projectSeparator']); ?>vh;
				border-bottom: solid 1px Silver;
				width: <?php echo (365 * $chart['dayWidth'] + $chart['left']); ?>vw;
				height: <?php echo intval($chart['projectHeight']); ?>vh;
				font-family: Arial;
				font-size: 12px;
			">
				<a
					href="../projects_view.php?SelectedID=<?php echo $projectId; ?>"
				><?php echo $prj['Name']; ?></a>
		</div>
		<?php
	}

	
	// Display project duration bars
	$yearStartTS = strtotime(date("$year-01-01"));
	$yearEndTS = strtotime(date("$year-12-31 23:59:59"));
	foreach($project as $id => $prj){
		$i = $projectIndex[$id] + 1;
		$chartStartTS = max(strtotime($prj['StartDate']), $yearStartTS);
		$chartEndTS = min(strtotime($prj['EndDate']), $yearEndTS);
		?><div
			style="
				position: absolute;
				width: <?php echo intval(($chartEndTS - $chartStartTS + 86400) / 86400 * $chart['dayWidth']); ?>vw;
				left: <?php echo intval(($chartStartTS - strtotime("$year-01-01")) / 86400 * $chart['dayWidth'] + $chart['left']); ?>vw;
				height: <?php echo intval($chart['projectHeight']); ?>vh;
				background-color: <?php echo $projectColor[$id]; ?> !important;
				print-color-adjust: exact;
				top: <?php 
					echo (
						$chart['top'] + 
						$chart['projectHeight'] * ($i + 1) + 
						$i * $chart['projectSeparator']
					); ?>vh;
				cursor: pointer;
				text-align: center;
				font-size: 10px;
				font-family: Arial;
				color: #004 !important;
				font-weight: bold;
				opacity: 0.5;
				white-space: nowrap;
				overflow: hidden;
			"
			title="<?php echo htmlspecialchars($prj['Name']); ?> from <?php echo date('j/n/Y', strtotime($prj['StartDate'])); ?> to <?php echo date('j/n/Y', strtotime($prj['EndDate'])); ?>"
			onclick="window.location='../projects_view.php?SelectedID=<?php echo $id; ?>';">
				<?php echo $prj['Name']; ?>
		</div><?php
	}

	?></div><?php

	include_once("$appDir/footer.php");
