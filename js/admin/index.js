Ext.onReady(function () {
    var grid = new IntelliGrid(
        {
            columns: [
                'selection',
                {name: 'title', title: _t('title'), width: 2, editor: 'text'},
                {name: 'date_start', title: _t('date_start'), width: 120, editor: 'date'},
                {name: 'date_expire', title: _t('date_expire'), width: 120, editor: 'date'},
                'status',
                {name: 'title', title: _t('poll_result'), icon: 'image', click: function (record) {
                        var id = record.get('id');
                        var poll_title = record.get('title');
                        $.get(intelli.config.admin_url + '/polls/results.json', {id: id}, function (response) {
                            if (response) {
                                var html = '';
                                html += '<h4 class="text-center">' + poll_title + '</h4>';
                                $.each(response.result, function (k, item) {
                                    html += '<p class="text-center"><b>' + item.title + '</b> - ' + item.votes + '</p>';
                                });
                                $.fancybox.open(html);
                            }
                        });
                    }
                },

                'update',
                'delete'
            ],
            sorters: [{property: 'title', direction: 'ASC'}]
        }, false);

    grid.toolbar = Ext.create('Ext.Toolbar', {
        items: [
            {
                emptyText: _t('title'),
                name: 'title',
                listeners: intelli.gridHelper.listener.specialKey,
                width: 275,
                xtype: 'textfield'
            }, {
                displayField: 'title',
                editable: false,
                emptyText: _t('status'),
                id: 'fltStatus',
                name: 'status',
                store: grid.stores.statuses,
                typeAhead: true,
                valueField: 'value',
                xtype: 'combo'
            }, {
                handler: function () {
                    intelli.gridHelper.search(grid);
                },
                id: 'fltBtn',
                text: '<i class="i-search"></i> ' + _t('search')
            }, {
                handler: function () {
                    intelli.gridHelper.search(grid, true);
                },
                text: '<i class="i-close"></i> ' + _t('reset')
            }]
    });
    grid.init();

    var searchStatus = intelli.urlVal('status');
    if (searchStatus) {
        Ext.getCmp('fltStatus').setValue(searchStatus);
        intelli.gridHelper.search(grid);
    }
});
