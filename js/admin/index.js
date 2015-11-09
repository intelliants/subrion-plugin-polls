Ext.onReady(function()
{
	if(Ext.get('expire_date'))
	{		
		new Ext.form.DateField(
		{
			allowBlank: false,
			format: 'Y-m-d',
			applyTo: 'expire_date'
		});
	}
	
	if(Ext.get('start_date'))
	{		
		new Ext.form.DateField(
		{
			allowBlank: false,
			format: 'Y-m-d',
			applyTo: 'start_date'
		});
	}
});

function option_add()
{
	if($('#add_option'))
	{
		$('#add_option').clone(true).css('display', 'block').attr('id', '').insertBefore('#add_option');
	}
}
function option_remove(item)
{
	$(item).parent().remove();
}