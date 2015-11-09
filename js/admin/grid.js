intelli.polls = function()
{	
	return {
		oGrid: null,
		title: _t('polls'),
		url: intelli.config.admin_url + '/polls/',
		removeBtn: true,
		progressBar: false,
		texts:{
			confirm_one: _t('are_you_sure_to_delete_poll'),			
			confirm_many: _t('are_you_sure_to_delete_polls')
		},
		statusesStore: ['active', 'inactive'],
		record:['lang', 'title', 'date', 'expires', 'status', 'remove', 'edit'],
		columns:[
			'checkcolumn',
			{
				header: _t('title'), 
				dataIndex: 'title', 
				sortable: true,
				width: 300,
				editor: new Ext.form.TextField()
			},
			'status',
			{
				header: _t('date'),
				dataIndex: 'date',
				sortable: true,
				width: 120,
                editor: new Ext.form.DateField(
			    {
                    format: 'Y-m-d',
                    xtype: 'datefield',
                    allowBlank: false
                })
			},
			{
				header: _t('expire_date'),
				dataIndex: 'expires',
				sortable: true,
				width: 120,
				editor: new Ext.form.DateField(
				{
					format: 'Y-m-d',
					xtype: 'datefield',
					allowBlank: false
				})
			},
			{
				custom: 'edit',
				redirect: intelli.config.admin_url + '/polls/edit/?id=',
				icon: 'edit-grid-ico.png',
				title: _t('edit')
			}
			,'remove'
		]
	};
}();

Ext.onReady(function()
{
	if (!$('#add_option').html())
	{
		intelli.polls = new intelli.exGrid(intelli.polls);
		intelli.polls.init();
	}
});