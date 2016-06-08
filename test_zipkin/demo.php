<?php
include_once '../include/zipkin/phpClient/Trace.php';


$spanConfig = array(
    'trace_id' => 123456789,
    'id' => 987654321,
    'name' => 'apple',
    'parent_id' => 0,
//    'annotations' => AnnotationBuilder::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['CLIENT_SEND'], 'motor_test_1')
);
$span1 = new Span($spanConfig);

ZKTrace::getInstance()->clientSend("motor1");
//ZKTrace::getInstance()->clientSend("motor2");
//ZKTrace::getInstance()->clientReceive();
ZKTrace::getInstance()->clientReceive();