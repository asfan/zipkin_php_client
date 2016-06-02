zipkin php client
===================================

概述
-----------------------------------
pull from [malakaw/zipkin_php_scribe](https://github.com/malakaw/zipkin_php_scribe)
支持scribe 为collector接入的zipkin数据导入  

zipkin下载地址：
[https://github.com/twitter/zipkin/releases](https://github.com/twitter/zipkin/releases)<br />或者[https://github.com/twitter/zipkin](https://github.com/twitter/zipkin)<br />这里的zipkin php scribe主要功能是在php页面中埋点，然后收集发送给collector;
这里的分两个功能模块，一个是埋点的，然后发送给MQ(system V IPC,可以参考[http://www.ibm.com/developerworks/cn/linux/l-ipc/](http://www.ibm.com/developerworks/cn/linux/l-ipc/)<br />)，另一个是从MQ收集信息发送给collector.


环境
-----------------------------------
php需要安装sysvsem ；
php.ini需要开启相关扩展： shmop.so sysvmsg.so

配置
-----------------------------------
###埋点
(include/zipkin/phpClient/Trace.php)
修改$GLOBALS['THRIFT_ROOT_'] ，设置为include的绝对目录:
ex:  

        $GLOBALS['THRIFT_ROOT_'] = '/usr/local/web/apache/htdocs/include';   


###收集MQ,发送给collector,也需要修改**上述**环境变量
(include/zipkin/phpClient/mq2collector.php)
修改$socket = new TSocket('x.x.x.x', 9410);    collector的ip和端口。

具体实例
-----------------------------------

###埋点，可以参考文件test_zipkin/demo.php
		ZKTrace::clientSend("phpspansubeub49");
		ZKTrace::clientReceive();
		
查看MQ ，在linux shell下:ipcs<br/>
![github](https://raw.github.com/malakaw/zipkin_php_scribe/master/img/ipcs.png "ipcs")
<br/>
主要是查看Message Queues

###收集MQ,发送给collector
		/usr/local/php/bin/php /usr/local/web/apache/htdocs/include/zipkin/phpClient/mq2collector.php







