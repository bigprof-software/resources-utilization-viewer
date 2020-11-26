<script>
	$j(function(){
		var tn = 'assignments';

		/* data for selected record, or defaults if none is selected */
		var data = {
			ProjectId: { id: '<?php echo $rdata['ProjectId']; ?>', value: '<?php echo $rdata['ProjectId']; ?>', text: '<?php echo $jdata['ProjectId']; ?>' },
			ProjectDuration: '<?php echo $jdata['ProjectDuration']; ?>',
			ResourceId: { id: '<?php echo $rdata['ResourceId']; ?>', value: '<?php echo $rdata['ResourceId']; ?>', text: '<?php echo $jdata['ResourceId']; ?>' }
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for ProjectId */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'ProjectId' && d.id == data.ProjectId.id)
				return { results: [ data.ProjectId ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for ProjectId autofills */
		cache.addCheck(function(u, d){
			if(u != tn + '_autofill.php') return false;

			for(var rnd in d) if(rnd.match(/^rnd/)) break;

			if(d.mfk == 'ProjectId' && d.id == data.ProjectId.id){
				$j('#ProjectDuration' + d[rnd]).html(data.ProjectDuration);
				return true;
			}

			return false;
		});

		/* saved value for ResourceId */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'ResourceId' && d.id == data.ResourceId.id)
				return { results: [ data.ResourceId ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

