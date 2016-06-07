{{ content() }}

{{ flashSession.output() }}

<div class="page-header">
    <div class="pull-right">
        <a href="/services" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span> 服务列表</a>
    </div>
    <h1>修改服务</h1>
</div>

<form class="form-horizontal" action="/services/edit/{{ service.id }}" method="post" data-pjax>
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">服务名称 <span>*</span></label>
        <div class="col-sm-4">
            <input type="name" class="form-control" id="name" name="name" placeholder="" value="{{ service.name }}">
        </div>
    </div>
    <div class="form-group">
        <label for="url" class="col-sm-2 control-label">服务地址 <span>*</span></label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="url" name="url" placeholder="" value="{{ service.url }}">
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="col-sm-2 control-label">服务账号</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="username" name="username" placeholder="WebSite" value="{{ service.username }}">
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="col-sm-2 control-label">服务密码</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="password" name="password" placeholder="7daysinn" value="{{ service.password }}">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </div>
</form>