<?php
	/*
	 * You can add custom links in the home page by appending them here ...
	 * The format for each link is:
		$homeLinks[] = array(
			'url' => 'path/to/link', 
			'title' => 'Link title', 
			'description' => 'Link text',
			'groups' => array('group1', 'group2'), // groups allowed to see this link, use '*' if you want to show the link to all groups
			'grid_column_classes' => '', // optional CSS classes to apply to link block. See: http://getbootstrap.com/css/#grid
			'panel_classes' => '', // optional CSS classes to apply to panel. See: http://getbootstrap.com/components/#panels
			'link_classes' => '', // optional CSS classes to apply to link. See: http://getbootstrap.com/css/#buttons
			'icon' => 'path/to/icon' // optional icon to use with the link
		);
	 */

		$homeLinks[] = array(
			'url' => 'chart.php', 
			'title' => 'Resources Utilization Chart', 
			'description' => 'Visualize the utilization of resources accross projects.',
			'groups' => array('*'), // groups allowed to see this link, use '*' if you want to show the link to all groups
			'grid_column_classes' => 'col-xs-12 col-sm-6 col-md-4 col-lg-3', // optional CSS classes to apply to link block. See: http://getbootstrap.com/css/#grid
			'panel_classes' => 'panel-info', // optional CSS classes to apply to panel. See: http://getbootstrap.com/components/#panels
			'link_classes' => 'btn-info', // optional CSS classes to apply to link. See: http://getbootstrap.com/css/#buttons
			'icon' => 'images/chart_bar.png' // optional icon to use with the link
		);
