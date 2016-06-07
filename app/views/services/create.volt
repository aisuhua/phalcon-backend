{{ content() }}

{{ flashSession.output() }}

<div class="page-header">
    <div class="pull-right">
        <a href="/services" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span> 服务列表</a>
    </div>
    <h1>添加服务</h1>
</div>

<form class="form-horizontal" action="/services/create" method="post" data-pjax id="addServiceForm">

    <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}">

    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">服务名称 <span class="red">*</span></label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="name" name="name" placeholder="">
        </div>
    </div>
    <div class="form-group">
        <label for="url" class="col-sm-2 control-label">服务地址 <span class="red">*</span></label>
        <div class="col-sm-5">
            <input type="url" class="form-control" id="url" name="url" placeholder="" >
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="col-sm-2 control-label">服务账号</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="username" name="username" placeholder="Username">
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="col-sm-2 control-label">服务密码</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="password" name="password" placeholder="Password">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </div>
</form>

<script>

    $.validator.setDefaults( {
        submitHandler: function () {
            return true;
        }
    } );

$(function () {

    $('#addServiceForm').validate({
        rules: {
            name: {
                required: true,
                minlength: 2
            },
            url: 'required'
        },
        messages: {

        },
        errorElement: "em",
        errorPlacement: function (error, element) {
            // Add the `help-block` class to the error element
            error.addClass("help-block");

            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.parent("label") );
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).parents(".col-sm-5").addClass("has-error").removeClass("has-success");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).parents(".col-sm-5").addClass("has-success").removeClass("has-error");
        }
    });

});
</script>