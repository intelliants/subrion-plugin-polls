<p class="total-votes">{$total_text}</p>

{foreach $options as $option}
	{math equation='index % 4' index=$option@index assign='colorIndex'}
	{math equation='votes * 100 / total' votes=$option.votes total=$total assign='percent'}

	<span class="title">{$option.title}</span>
	<div class="progress">
		<p class="option-result">{$percent|round:1}% ({$option.votes})</p>
		<div class="progress-bar progress-bar-{$colors.$colorIndex}" style="width: {$percent|round:1}%"></div>
	</div>
{/foreach}