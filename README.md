# sitemap
PHP 网站地图 Sitemap工具类

Sitemap 可方便网站管理员通知搜索引擎他们网站上有哪些可供抓取的网页。

## 安装
~~~php
composer require luo/sitemap
~~~

## 用法示例

~~~php
<?php
use luo\sitemap\Sitemap;
$Sitemap = new Sitemap();
$Sitemap->setTitle("html title");
$Sitemap->setPath("文件存放路径");

$Sitemap->addItem('https://github.com/Romxiao/0','GIT0');
$Sitemap->addItem('https://github.com/Romxiao/1','GIT1');


#根据需求生成对应后缀的文件 目前只支持txt html xml
$Sitemap->generated('html');
$Sitemap->generated('xml');
~~~
