<?php
include_once '../include/zipkin/phpClient/Trace.php'; 


print_r(1);
    ZKTrace::getInstance()->clientSend("motor1");
//	ZKTrace::clientSend("phpspansub49");
//		ZKTrace::clientSend("phpspansubeub49");
//		ZKTrace::clientReceive();
//	ZKTrace::clientReceive();
print_r(2);

    ZKTrace::getInstance()->clientSend("motor2");
print_r(3);

    ZKTrace::getInstance()->clientReceive();
print_r(4);

    ZKTrace::getInstance()->clientReceive();
    
//for($i=1;$i<=10;$i++){
//echo Demo::add()."<br/>";

//SpanBuilder::clientSend("1");
//SpanBuilder::clientReceive();

