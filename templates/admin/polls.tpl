{if iaCore::ACTION_ADD == $pageAction || iaCore::ACTION_EDIT == $pageAction}
	{ia_print_js files="_IA_URL_plugins/polls/js/polls"}
	{include file="box-header.tpl" title=$gTitle}
	<form action="" method="post" id="page_form">
	{preventCsrf}
	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="200"><strong>{lang key="language"}:</strong></td>
		<td>
			<select class="common" name="language"{if count($core.languages) == 1} disabled="disabled" {/if}>
			{foreach $core.languages as $code => $language}
				<option value="{$code}"{if $form.language == $code} selected="selected"{/if}>{$language.title}</option>
			{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td><strong>{lang key="title"}:</strong></td>
		<td><input type="text" name="title" size="24" class="common" value="{$form.title}"></td>
	</tr>
	<tr>
		<td><strong>{lang key="start_date"}:</strong></td>
		<td><input type="text" name="start_date" id="start_date" size="24" class="common" value="{$form.start_date}"></td>
	</tr>
	<tr>
		<td><strong>{lang key="expire_date"}:</strong></td>
		<td><input type="text" name="expire_date" id="expire_date" size="24" class="common" value="{$form.expire_date}"></td>
	</tr>
	<tr>
		<td><strong>{lang key="poll_options"}:</strong></td>
		<td>
			{if iaCore::ACTION_EDIT == $pageAction}
			<div style="padding:5px;font-size:14px;font-weight: bold">{lang key="have_vote"}</div>
			{foreach from=$form.options item="option" key="opt_id" name="poll_options"}
				<div style="padding:3px;">
					<strong>{lang key="title"}:</strong>
					<input type="text" class="common" name="options[{$opt_id}]" value="{$option}">
					<a href="javascript:;" onclick="option_remove(this)">{lang key="remove"}</a>
				</div>
			{/foreach}
			<div style="padding:5px;font-size:14px;font-weight: bold">{lang key="new_options"}</div>
			{/if}
			{foreach from=$form.newoptions item="option" name="poll_options"}
				<div style="padding:3px;">
					<strong>{lang key="title"}:</strong>
					<input type="text" class="common" name="newoptions[]" value="{$option}">
					<a href="javascript:;" onclick="option_remove(this)">{lang key="remove"}</a>
				</div>
			{/foreach}
			<div style="display:none;padding:3px;" id="add_option">
				<strong>{lang key="title"}:</strong>
				<input type="text" class="common" name="newoptions[]">
				<a href="javascript:;" onclick="option_remove(this)">{lang key="remove"}</a>
			</div>
			<a href="javascript:;" onclick="option_add('add_option')">{lang key="add_option"}</a>
		</td>
	</tr>
	<tr>
		<td><strong>{lang key="status"}</strong></td>
		<td>
			<select name="status">
				<option value="active" {if $form.status == 'active'}selected="selected"{/if}>{lang key='active'}</option>
				<option value="inactive" {if $form.status == 'inactive'}selected="selected"{/if}>{lang key='inactive'}</option>
			</select>
		</td>
	</tr>
	<tr class="all">
		<td colspan="2">
			<input type="submit" name="send" value="{lang key='save'}" class="common">
			{if iaCore::ACTION_ADD == $pageAction}
		    	{include file="goto.tpl"}
		    {/if}
		</td>
	</tr>
	</table>
	</form>
	{include file="box-footer.tpl"}

	{ia_print_js files="_IA_URL_plugins/polls/js/admin/index"}
{/if}