{{ content() }}

{{ flashSession.output() }}

<div class="page-header">
    <div class="pull-right">
        <a href="/services/create" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> 添加服务</a>
    </div>
    <h1>服务列表</h1>
</div>


<div id="toolbar">
    <div class="form-inline">
        <div class="form-group has-feedback">
            <span>服务名称：</span>
            <input name="name" class="form-control" type="text" value="" placeholder="请输入查询信息">
            <span class="glyphicon glyphicon-search form-control-feedback text-muted"></span>
        </div>
    </div>
</div>

<table id="table"
       data-url="/services?data=1"
       data-side-pagination="server"
       data-pagination="true"
       data-page-size="10"
       data-show-refresh="true"
       data-show-columns="true"
       data-toolbar="#toolbar"
       data-query-params="queryParams"
       data-striped="false"
       data-cookie="true"
       data-cookie-id-table="services"
       data-show-export="true"
       data-unique-id="id"
       data-classes="table table-hover">
</table>

<script>

    var $table = $('#table');
    var $toolbar = $('#toolbar');
    var timeoutId;

    function initTable() {

        $table.bootstrapTable({
            columns: [
                {
                    field: 'name',
                    title: '服务名称',
                },
                {
                    field: 'url',
                    title: '服务地址'
                },
                {
                    field: 'status',
                    title: '服务状态',
                    formatter: statusFormatter
                },
                {
                    field: 'operate',
                    title: '管理',
                    formatter: operateFormatter,
                    events: operateEvents
                }
            ],
        });
    }

    function queryParams(params)
    {
        params.page = (params.limit + params.offset) / params.limit;
        params.pagesize = params.limit;

        delete params.limit;
        delete params.offset;

        $('#toolbar').find('input[name]').each(function() {
            params[$(this).attr('name')] = $(this).val();
        });

        return params;
    }

    function operateFormatter(value, row, index) {
        return [
//            '<a href="javascript:;" class="log btn btn-default btn-xs" title="服务日志">',
//            '<span class="glyphicon glyphicon-cog"></span> 服务日志',
//            '</a> ',
            '<a href="/services/edit/'+row.id+'" class="edit btn btn-default btn-xs" title="修改服务">',
            '<span class="glyphicon glyphicon-pencil"></span> 修改',
            '</a> ',
            '<a href="/services/delete/'+row.id+'" class="delete btn btn-default btn-xs" title="删除">',
            '<span class="glyphicon glyphicon-trash"></span> 删除',
            '</a> '
        ].join('');
    }

    operateEvents = {
        'click .delete': function(event, value, row, index) {

            var url = $(this).attr('href');

            bootbox.confirm("真的要删除该服务吗？", function(result) {
                if(result) {
                    $.pjax({
                        url: url,
                        container: '#pjax-container'
                    });

                    //$table.bootstrapTable('refresh');
                }
            });

            event.preventDefault();
        }
    };

    function statusFormatter(value, row, index)
    {
        if(row.status == true) {
            return '<span class="text-success">正常 '+row.runtime+'秒</span>'
        } else {
            return '<span class="text-danger"><i class="glyphicon glyphicon-remove"></i> 不可用</span>'
        }
    }

    function rowStyle(row, index) {
        if(row.status == false) {
            return {
                classes: 'danger'
            };
        }

        return {};
    }

    $(function() {

        initTable();

        $toolbar.find('input[name]').on('keyup', function() {

            clearTimeout(timeoutId);

            timeoutId = setTimeout(function () {
                $table.bootstrapTable('refresh');
            }, 500);
        });

    });
</script>


