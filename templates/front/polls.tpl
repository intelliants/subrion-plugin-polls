{if isset($single_poll) && !empty($single_poll)}
	<div class="info">
		<i class="icon-calendar"></i> {$single_poll.date|date_format:$core.config.date_format} - {$single_poll.expires|date_format:$core.config.date_format}
	</div>

	<div class="polls">
	
		{if $single_poll.alreadyVoted}
			{if $core.config.polls_google_chart}
				<script type="text/javascript" src="https://www.google.com/jsapi"></script>
				<script type="text/javascript">
					google.load("visualization", "1", {
						packages: ['corechart']
					});

					google.setOnLoadCallback(drawChart);
					function drawChart()
					{
						var data = new google.visualization.DataTable();

						data.addColumn('string', 'Votes');
						data.addColumn('number', 'Num Votes');
						data.addRows({$single_poll.options|count});

						{foreach from=$single_poll.options item=option key=key}
							data.setValue({$key}, 0, '{$option.title|escape}');
							data.setValue({$key}, 1, {$option.votes});
						{/foreach}

						var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
						chart.draw(data, {
							width: '100%',
							height: 300,
							title: '{$single_poll.title|escape}'
						});
					}
			    </script>

				<div id="chart_div"></div>
			{else}
				{$single_poll.results}
				{ia_print_css files='_IA_URL_plugins/polls/templates/front/css/block'}
			{/if}
			<hr>
			<div class="info c"><a href="{$smarty.const.IA_URL}polls/">{lang key='show_polls'}</a></div>
		{else}
			<ul class="poll_{$single_poll.id} unstyled">
			{foreach from=$single_poll.options item=option}
				<li>
					<label for="poll_option_{$single_poll.id}_{$option.id}_all" class="radio">
						<input class="poll_option" type="radio" id="poll_option_{$single_poll.id}_{$option.id}_all" value="0" name="poll_option_{$single_poll.id}_all">
						{$option.title}
					</label>
				</li>
			{/foreach}
			</ul>
		{/if}
	</div>
{else}
	{if $all_polls}
		{foreach from=$all_polls item=onepoll}
			<p><a href="{$smarty.const.IA_URL}polls/{$onepoll.id}-{$onepoll.allias}.html">{$onepoll.title}</a> ({$onepoll.date|date_format:$core.config.date_format} - {$onepoll.expires|date_format:$core.config.date_format})</p>
		{/foreach}
		
		{navigation aTotal=$total aTemplate=$aTemplate aItemsPerPage=$core.config.polls_count_page aNumPageItems=5}
	{else}
		<div class="alert alert-info">{lang key='no_polls'}</div>
	{/if}
{/if}