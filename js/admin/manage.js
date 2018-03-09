
$('.js-add-option').click(function()
{
    var $this = $(this);
    var thisParent = $this.closest('.input-group');
    var clone = thisParent.clone(true);
    $('input', clone).val('');
    $('input', clone).attr('name', 'newoptions[]');
    thisParent.after(clone);
});

$('.js-remove-option').click(function()
{
    if (1 < $(this).closest('.row-options').children().length)
    {
        $(this).closest('.input-group').remove();
    }
});
if ($().datepicker)
{
    $('.js-datepicker-date-only').datepicker(
        {
            format: 'yyyy-mm-dd',
            language: intelli.config.lang
        });
}
