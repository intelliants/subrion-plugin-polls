{if isset($block_polls) && $block_polls}
	<div class="items">
		{foreach $block_polls as $poll}
			<div class="polls item">
				<p class="poll-title"><a href="{$smarty.const.IA_URL}poll/{$poll.id}">{$poll.title}</a></p>
				{if $poll.alreadyVoted}
					{$poll.results}
				{else}
					<ul class="js-poll-options poll-{$poll.id} nav">
						{foreach $poll.options as $option}
							<li>
								<label for="poll-option-{$poll.id}-{$option.id}" class="poll-option">
									<input type="radio" id="poll-option-{$poll.id}-{$option.id}" value="0" data-poll-id="{$poll.id}" data-option-id="{$option.id}">
									{$option.title}
								</label>
							</li>
						{/foreach}
					</ul>
				{/if}
			</div>
		{/foreach}
	</div>
	<div class="m-t-md">
		<a href="{$smarty.const.IA_URL}polls/">{lang key='show_polls'}</a>
	</div>

	{ia_print_js files='_IA_URL_modules/polls/js/frontend/index'}

{/if}