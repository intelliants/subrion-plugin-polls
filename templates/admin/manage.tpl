<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
    {preventCsrf}

    <div class="wrap-list">
        <div class="wrap-group">
            <div class="wrap-group-heading">
                <h4>{lang key='general'}</h4>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-title">{lang key='title'} {lang key='field_required'}</label>
                <div class="col col-lg-4">
                    <input type="text" name="title" value="{$item.title|escape:'html'}" id="input-title">
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="field_date_start">{lang key='date_start'}</label>
                <div class="col col-lg-4">
                    <div class="input-group">
                        <input type="text" class="datepicker js-datepicker-date-only" name="date_start" id="field_date_start" value="{if $item.date_start}{$item.date_start|date_format:'%Y-%m-%d'}{/if}">
                        <span class="input-group-addon js-datepicker-toggle"><i class="i-calendar"></i></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="field_date_expire">{lang key='date_expire'}</label>
                <div class="col col-lg-4">
                    <div class="input-group">
                        <input type="text" class="datepicker js-datepicker-date-only" name="date_expire" id="field_date_expire" value="{if $item.date_expire}{$item.date_expire|date_format:'%Y-%m-%d'}{/if}">
                        <span class="input-group-addon js-datepicker-toggle"><i class="i-calendar"></i></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-options">{lang key='options'} {lang key='field_required'}</label>
                <div class="col col-lg-4 row-options">
                        {if $options}
                            {foreach $options as $option => $title}
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="{lang key='title'}" name="options[{$option}]" maxlength="50" value="{$title}">
                                    <div class="input-group-btn">
                                        <button type="button" class="js-add-option btn btn-primary"><span class="i-plus-alt"></span></button>
                                        <button type="button" class="js-remove-option btn btn-primary"><span class="i-minus-alt"></span></button>
                                    </div>
                                </div>
                            {/foreach}
                        {/if}
                        {if $newoptions}
                            {foreach $newoptions as $option => $title}
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="{lang key='title'}" name="newoptions[]" maxlength="50" value="{$title}">
                                    <div class="input-group-btn">
                                        <button type="button" class="js-add-option btn btn-primary"><span class="i-plus-alt"></span></button>
                                        <button type="button" class="js-remove-option btn btn-primary"><span class="i-minus-alt"></span></button>
                                    </div>
                                </div>
                            {/foreach}
                        {/if}
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="{lang key='title'}" name="newoptions[]" maxlength="50">
                            <div class="input-group-btn">
                                <button type="button" class="js-add-option btn btn-primary"><span class="i-plus-alt"></span></button>
                                <button type="button" class="js-remove-option btn btn-primary"><span class="i-minus-alt"></span></button>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        {capture name='systems' append='fieldset_before'}
            <div class="row">
                <label class="col col-lg-2 control-label" for="input-language">{lang key='language'}</label>
                <div class="col col-lg-4">
                    <select name="lang" id="input-language"{if count($core.languages) == 1} disabled{/if}>
                        {foreach $core.languages as $code => $language}
                            <option value="{$code}"{if $item.lang == $code} selected{/if}>{$language.title}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/capture}

        {include file='fields-system.tpl' datetime=true}

    </div>
</form>
{ia_print_js files="_IA_URL_plugins/polls/js/admin/index"}
{ia_print_css files='_IA_URL_plugins/polls/templates/admin/css/manage'}