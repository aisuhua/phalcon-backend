<!DOCTYPE html>
<html lang="en">
	<head>
		{%- set url = url(), version = '1.0.0', theme = session.get('language') -%}
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>{{ get_title(false) ~ ' - ' ~ config.site.name }}</title>

		<meta content="{{ config.site.keywords }}" name="keyword">
		<meta content="{{ config.site.description }}" name="description">

        <!-- css here -->
		{{- stylesheet_link("plugins/bootstrap/css/bootstrap.css?v=" ~ version, true) -}}
		{{- stylesheet_link("plugins/metisMenu/metisMenu.css?v=" ~ version, true) -}}
        {{ stylesheet_link("plugins/bootstrap-table/bootstrap-table.css?v=" ~ version, true) }}
        {{ stylesheet_link("plugins/nprogress/nprogress.css?v=" ~ version, true) }}
        {{ stylesheet_link("plugins/pnotify/pnotify.custom.css?v=" ~ version, true) }}
        {{- stylesheet_link("plugins/font-awesome/css/font-awesome.css?v=" ~ version, true) -}}
        {{- stylesheet_link("plugins/colors/colors.css?v=" ~ version, true) -}}

        {{- stylesheet_link("css/sb-admin-2.css?v=" ~ version, true) -}}
        {{- stylesheet_link("css/style.css?v=" ~ version, true) -}}

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="//cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!--js here -->
		{{ javascript_include("plugins/jquery/jquery.js?v=" ~ version) }}
        {{ javascript_include("plugins/bootstrap/js/bootstrap.js?v=" ~ version) }}
        {{ javascript_include("plugins/metisMenu/metisMenu.js?v=" ~ version) }}
        {{ javascript_include("plugins/bootstrap-table/bootstrap-table.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/bootstrap-table/locale/bootstrap-table-zh-CN.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/bootstrap-table/extensions/cookie/bootstrap-table-cookie.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/bootstrap-table/extensions/export/bootstrap-table-export.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/fileSaver/FileSaver.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/tableExport/tableExport.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/jquery-pjax/jquery.pjax.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/nprogress/nprogress.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/pnotify/pnotify.custom.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/bootbox/bootbox.js?v=" ~ version, true) }}
        {{ javascript_include("plugins/jquery-validation/jquery.validate.js?v=" ~ version) }}
        {{ javascript_include("plugins/jquery-validation/localization/messages_zh.js?v=" ~ version) }}

        {{ javascript_include("js/sb-admin-2.js?v=" ~ version) }}
        {{ javascript_include("js/base.js?v=" ~ version) }}
	</head>
	<body>
        <div id="wrapper">
		    {{ content() }}
        </div>
	</body>
</html>
