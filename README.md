# Phalcon-Backend

使用 Phalcon 搭建的一个后台框架，它使用了 Bootstrap 及其优秀的插件，如 bootstrap-table/bootbox/metisMenu/pnotify 等等。同时为了实现全站异步，使用 pajx + nprogress，使页面更加美观一点。
该

## 安装

下载本仓库源码 [Phalcon-Backend](https://github.com/aisuhua/phalcon-backend)

新建数据库

    CREATE DATABASE phalcon_backend

创建数据表

    CREATE TABLE `services` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) DEFAULT NULL,
      `url` varchar(255) DEFAULT NULL,
      `username` varchar(255) DEFAULT NULL,
      `password` varchar(255) DEFAULT NULL,
      `auth_token` varchar(255) DEFAULT NULL,
      `update_time` int(11) DEFAULT NULL,
      `create_time` int(11) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

修改 `app/config/config.php` 中的数据库配置信息即可。

配置好虚拟机后，就可以直接浏览器中查看效果。

