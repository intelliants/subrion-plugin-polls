{if isset($poll) && $poll}
	<div class="info">
		<span class="fa fa-calendar"></span> {$poll.date_start|date_format:$core.config.date_format} - {$poll.date_expire|date_format:$core.config.date_format}
	</div>

	<div class="polls">
		{if $poll.alreadyVoted}
			{if $core.config.polls_google_chart}
				<script src="https://www.google.com/jsapi"></script>
				<script>
					google.load("visualization", "1", {
						packages: ['corechart']
					});

					google.setOnLoadCallback(drawChart);
					function drawChart()
					{
						var data = new google.visualization.DataTable();

						data.addColumn('string', 'Votes');
						data.addColumn('number', 'Num Votes');
						data.addRows({$poll.options|count});

						{foreach $poll.options as $key => $option}
							data.setValue({$key}, 0, '{$option.title|escape}');
							data.setValue({$key}, 1, {$option.votes});
						{/foreach}

						var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
						chart.draw(data, {
							width: '100%',
							height: 300,
							title: '{$poll.title|escape}'
						});
					}
				</script>
				<div id="chart_div"></div>
			{else}
				{$poll.results}
				{ia_print_css files='_IA_URL_plugins/polls/templates/front/css/block'}
			{/if}
			<hr>
			<div class="info c"><a href="{$smarty.const.IA_URL}polls/">{lang key='show_polls'}</a></div>
		{else}
			<ul class="js-poll-options poll-{$poll.id}">
			{foreach $poll.options as $option}
				<li>
					<label for="poll-option-{$poll.id}-{$option.id}">
						<input type="radio" id="poll-option-{$poll.id}-{$option.id}" value="0" data-poll-id="{$poll.id}" data-option-id="{$option.id}">
						{$option.title}
					</label>
				</li>
			{/foreach}
			</ul>
		{/if}
	</div>
{else}
	{if $polls}
		{foreach $polls as $poll}
			<p><a href="{$smarty.const.IA_URL}poll/{$poll.id}">{$poll.title}</a> ({$poll.date_start|date_format:$core.config.date_format} - {$poll.date_expire|date_format:$core.config.date_format})</p>
		{/foreach}

		{navigation aTotal=$pagination.total aTemplate=$pagination.template aItemsPerPage=$core.config.polls_count_page aNumPageItems=5}
	{else}
		<div class="alert alert-info">{lang key='no_polls'}</div>
	{/if}
{/if}