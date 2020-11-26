<?php
	$rdata = array_map('to_utf8', array_map('nl2br', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('nl2br', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function() {
		var tn = 'assignments';

		/* data for selected record, or defaults if none is selected */
		var data = {
			ProjectId: <?php echo json_encode(array('id' => $rdata['ProjectId'], 'value' => $rdata['ProjectId'], 'text' => $jdata['ProjectId'])); ?>,
			ProjectDuration: <?php echo json_encode($jdata['ProjectDuration']); ?>,
			ResourceId: <?php echo json_encode(array('id' => $rdata['ResourceId'], 'value' => $rdata['ResourceId'], 'text' => $jdata['ResourceId'])); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for ProjectId */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'ProjectId' && d.id == data.ProjectId.id)
				return { results: [ data.ProjectId ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for ProjectId autofills */
		cache.addCheck(function(u, d) {
			if(u != tn + '_autofill.php') return false;

			for(var rnd in d) if(rnd.match(/^rnd/)) break;

			if(d.mfk == 'ProjectId' && d.id == data.ProjectId.id) {
				$j('#ProjectDuration' + d[rnd]).html(data.ProjectDuration);
				return true;
			}

			return false;
		});

		/* saved value for ResourceId */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'ResourceId' && d.id == data.ResourceId.id)
				return { results: [ data.ResourceId ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

