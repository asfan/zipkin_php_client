zipkin php client
===================================

����
-----------------------------------
pull from [malakaw/zipkin_php_scribe](https://github.com/malakaw/zipkin_php_scribe)
֧��scribe Ϊcollector�����zipkin���ݵ���  

zipkin���ص�ַ��
[https://github.com/twitter/zipkin/releases](https://github.com/twitter/zipkin/releases)<br />����[https://github.com/twitter/zipkin](https://github.com/twitter/zipkin)<br />�����zipkin php scribe��Ҫ��������phpҳ������㣬Ȼ���ռ����͸�collector;
����ķ���������ģ�飬һ�������ģ�Ȼ���͸�MQ(system V IPC,���Բο�[http://www.ibm.com/developerworks/cn/linux/l-ipc/](http://www.ibm.com/developerworks/cn/linux/l-ipc/)<br />)����һ���Ǵ�MQ�ռ���Ϣ���͸�collector.


����
-----------------------------------
php��Ҫ��װsysvsem ��
php.ini��Ҫ���������չ�� shmop.so sysvmsg.so

����
-----------------------------------
###���
(include/zipkin/phpClient/Trace.php)
�޸�$GLOBALS['THRIFT_ROOT_'] ������Ϊinclude�ľ���Ŀ¼:
ex:  

        $GLOBALS['THRIFT_ROOT_'] = '/usr/local/web/apache/htdocs/include';   


###�ռ�MQ,���͸�collector,Ҳ��Ҫ�޸�**����**��������
(include/zipkin/phpClient/mq2collector.php)
�޸�$socket = new TSocket('x.x.x.x', 9410);    collector��ip�Ͷ˿ڡ�

����ʵ��
-----------------------------------

###��㣬���Բο��ļ�test_zipkin/demo.php
		ZKTrace::clientSend("phpspansubeub49");
		ZKTrace::clientReceive();
		
�鿴MQ ����linux shell��:ipcs<br/>
![github](https://raw.github.com/malakaw/zipkin_php_scribe/master/img/ipcs.png "ipcs")
<br/>
��Ҫ�ǲ鿴Message Queues

###�ռ�MQ,���͸�collector
		/usr/local/php/bin/php /usr/local/web/apache/htdocs/include/zipkin/phpClient/mq2collector.php







