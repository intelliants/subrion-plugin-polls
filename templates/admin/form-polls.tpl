<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
    {preventCsrf}

    {capture name='general' append='fieldset_after'}
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
    {/capture}

    {include 'field-type-content-fieldset.tpl' isSystem=true}
</form>


{ia_print_js files="_IA_URL_modules/polls/js/admin/manage"}
