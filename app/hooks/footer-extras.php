<script>
	$j(() => {
		// only in assignments table
		if(AppGini.currentTableName() != 'assignments') return;

		// if form not editable, no need to proceed
		if(!$j('#insert').length && !$j('#update').length) return;

		// function to reset resource duration to the same as project duration
		const resetResourceDuration = () => {
			const df = AppGini.datetimeFormat(), // example: DD/MM/YYYY
			      pos = {
					d: df.indexOf('DD'),
					m: df.indexOf('MM'),
					y: df.indexOf('YYYY'),
			      },
			      regex = new RegExp(
			      	df.replace('DD', '([0-3]?[0-9])')
			          .replace('MM', '([01]?[0-9])')
			          .replace('YYYY', '(\\d{4})'),
			        'g'
			      ),
			      pdt = $j('#ProjectDuration').text().trim(),
			      matches = [ ...pdt.matchAll(regex) ];

			if(!matches.length) {
				$j('#StartDate-dd, #StartDate-mm, #StartDate, #EndDate-dd, #EndDate-mm, #EndDate').val('');
				return;
			}

			for(p in pos)
				if(pos[p] == 0)
					pos[p] = 1;
				else if(pos[p] > 4)
					pos[p] = 3;
				else
					pos[p] = 2;

			$j('#StartDate-dd').val(parseInt(matches[0][pos.d]));
			$j('#StartDate-mm').val(parseInt(matches[0][pos.m]));
			$j('#StartDate').val(parseInt(matches[0][pos.y]));

			$j('#EndDate-dd').val(parseInt(matches[1][pos.d]));
			$j('#EndDate-mm').val(parseInt(matches[1][pos.m]));
			$j('#EndDate').val(parseInt(matches[1][pos.y]));
		}

		// for new records, set resource duration same as project when a project is selected
		if(!$j('input[name=SelectedID]').val().length) {
			// monitor ProjectDuration for text change
			// and update resource start and end dates accordingly
			let ProjectDuration = '';
			setInterval(() => {
				const pdt = $j('#ProjectDuration').text().trim();
				if(ProjectDuration == pdt) return;

				ProjectDuration = pdt;
				resetResourceDuration();
			}, 50)
		
		// for existing records, add a 'Reset resource duration button' to do the same manually
		} else {
			const btn = $j(`<button type="button" class="btn btn-default btn-lg vspacer-lg">
				<i class="glyphicon glyphicon-refresh"></i> Reset resource duration
				</button>`
			);
			btn.on('click', resetResourceDuration);

			$j('<div class="row"><div class="col-lg-9 col-lg-offset-3" id="reset-resource-duration-wrapper"></div></div>')
				.insertAfter($j('#EndDate').parents('.form-group'));

			btn.appendTo('#reset-resource-duration-wrapper');
		}
	})
</script>