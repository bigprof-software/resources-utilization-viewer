<?php
	/*
	* You can add custom links to the navigation menu by appending them here ...
	* The format for each link is:
		$navLinks[] = array(
			'url' => 'path/to/link', 
			'title' => 'Link title', 
			'groups' => array('group1', 'group2'), // groups allowed to see this link, use '*' if you want to show the link to all groups
			'icon' => 'path/to/icon'
		);
	*/

		$navLinks[] = [
			'url' => 'hooks/chart-resources.php', 
			'title' => 'Resources Utilization Chart', 
			'groups' => array('*'), // groups allowed to see this link, use '*' if you want to show the link to all groups
			'icon' => 'images/chart_bar.png'
		];

		$navLinks[] = [
			'url' => 'hooks/chart-projects.php', 
			'title' => 'Projects Chart', 
			'groups' => array('*'), // groups allowed to see this link, use '*' if you want to show the link to all groups
			'icon' => 'images/chart_bar.png'
		];
