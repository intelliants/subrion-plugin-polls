<p class="total-votes">{lang key='total_votes'}</p>
{foreach $options as $key => $item}
	{math equation='key % 4' key=$key assign='color_index'}
	{$item.title}
	<div class="progress progress-{$colors.$color_index}">
		<div class="bar" style="width: {$item.width}%"></div>
		<span class="option-result">{$item.width}% ({$item.votes})</span>
	</div>
{/foreach}