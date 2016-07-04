<?php
/**
 * Created by PhpStorm.
 * User: yuanping3
 * Date: 16/6/15
 * Time: 上午10:54
 * Detail: 
 */

$ROOT_PATH = dirname(__FILE__) . '/../';
include_once $ROOT_PATH . '/tools/traceFormat.php';

$GLOBALS['THRIFT_ROOT_'] = '/Users/fanyuanping/Workbench/git_repository/zipkin_php_client/include/zipkin'; //$ini_array["includepath"].'/zipkin';
require_once $GLOBALS['THRIFT_ROOT_'].'/Thrift.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TPhpStream.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/THttpClient.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TFramedTransport.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TMemoryBuffer.php';
//error_reporting(E_NONE);

//包含helloworld接口文件
require_once $GLOBALS['THRIFT_ROOT_'].'/packages/zipkinCollector/ZipkinCollector.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/packages/zipkinCore/zipkinCore_types.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/phpClient/testLogEntry.php';
include_once $GLOBALS['THRIFT_ROOT_'].'/phpClient/mq.php';

main();



function main()
{
    $logFile = './input.binary';

    $binaryData = getConfigDataFromLog($logFile);


    $socket = new TSocket('10.227.88.174', 9410);               //连接zipkin collector
    $transport = new TFramedTransport($socket, 6024, 6024);     //按照chunk传输数据的通道
    $protocol = new TBinaryProtocol($transport);                //thrift 二进制协议
    $client = new ZipkinCollectorClient($protocol);


    foreach ($binaryData as $spanData) {
        echo "-----------------------------";

        $transport->open();                                         //开数据结构
        $lentry = new LogEntry();
        $lentry->category = "zipkin";
        var_dump($spanData);

        $lentry->message = $spanData;
        $lognetry_array = array($lentry);
        $client->Log($lognetry_array);
        $transport->close();
    }
}


/**
 * 从本地文件获取trace+span的config数据
 * @param $file
 */
function getConfigDataFromLog($file = null)
{
    $result = array();

    $file = trim($file);
    if (empty($file) || !is_readable($file))
    {
        echo "log file can't be null or unreadable!\n";
        exit;
    }

    $fd = fopen($file, 'r');
    while(!feof($fd)){
        $unitData = array();
        $logStr = '';
        $handled = 0;

        $logStr = trim(fgets($fd));
        if (empty($logStr))
            continue;

        $result[] = $logStr;

//        $tmp = explode('{', $logStr);
//        $tmp = $tmp[1];
//        $tmp = explode('}', $tmp);
//        $tmp = $tmp[0];
////    $spanConfig1 =  array(
////        'trace_id' => 11114,
////        'id' => 11114,
////        'name' => 'apple',
////        'parent_id' => 0,
////        'annotation' => array(
////            array(
////                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'],
////                'port' => 80,
////                'timestamp' => (microtime(true) * 1000000),
////            ),
////        ),
////    );
//        $oriData = explode('|', $tmp);
//        $unitData['span_name'] = $oriData[3];//domain
//        $unitData['config'] = array();
//        $singleConfig['trace_id'] = intval($oriData[0]);
//        $singleConfig['id'] = intval($oriData[1]);
//        $singleConfig['name'] = trim($oriData[6]);//test
//        $singleConfig['parent_id'] = intval($oriData[2]);
//        $singleConfig['annotation'] = array(
//            array(
//                'type' => $oriData[5],
//                'port' => intval($oriData[7]),
//                'timestamp' => floatval($oriData[4]),
//            )
//        );
//        $unitData['config'] = $singleConfig;
//
////        foreach ($configData as $key => $compareData)
////        {
////            if (($compareData['span_name'] == $unitData['span_name'])
////                    && ($compareData['config']['trace_id'] == $unitData['config']['trace_id'])
////                    && ($compareData['config']['id'] == $unitData['config']['id'])
////                    && ($compareData['config']['parent_id'] == $unitData['config']['parent_id'])
////                    && ($compareData['config']['parent_id'] != 0)
////            )
////            {
////                $configData[$key]['config']['annotation'][] = $unitData['config']['annotation'][0];
////                $handled = 1;
////                break;
////            }
////        }
////
////        if(!$handled)
////            $configData[] = $unitData;
//
//        $configData[] = $unitData;
//
////array(8) {
////            [0]=>
////    string(15) "844180087144417"
////            [1]=>
////    string(15) "844180087144417"
////            [2]=>
////    string(1) "0"
////            [3]=>
////    string(21) "mall.auto.sina.com.cn"
////            [4]=>
////    string(19) "1.4653844182191E+15"
////            [5]=>
////    string(2) "ss"
////            [6]=>
////    string(5) "apple"
////            [7]=>
////    string(2) "80"
////  }
//
    }
//
//    $result[0] = $configData[0];
//    $result[0]['config']['annotation'][] = $configData[3]['config']['annotation'][0];
////    $result[0]['config']['annotation'][] = $configData[4]['config']['annotation'][0];
//    $result[1] = $configData[1];
//    $result[1]['config']['annotation'][] = $configData[2]['config']['annotation'][0];
////    $result[1]['config']['annotation'][] = $configData[3]['config']['annotation'][0];
////    $result[2] = $configData[0];
////    $result[2]['config']['annotation'][] = $configData[6]['config']['annotation'][0];
//
////    $result[3] = $configData[6];

    fclose($fd);
    return $result;
}

///**
// * 从本地文件获取trace+span的config数据
// * @param $file
// */
//function getConfigDataFromLog($file = null)
//{
//    $configData = array();
//
//    $file = trim($file);
//    if (empty($file) || !is_readable($file))
//    {
//        echo "log file can't be null or unreadable!\n";
//        exit;
//    }
//
//    $fd = fopen($file, 'r');
//    while(!feof($fd)){
//        $unitData = array();
//        $logStr = '';
//        $handled = 0;
//
//        $logStr =  trim(fgets($fd));
//        if (empty($logStr))
//            continue;
//
//        $tmp = explode('{', $logStr);
//        $tmp = $tmp[1];
//        $tmp = explode('}', $tmp);
//        $tmp = $tmp[0];
////    $spanConfig1 =  array(
////        'trace_id' => 11114,
////        'id' => 11114,
////        'name' => 'apple',
////        'parent_id' => 0,
////        'annotation' => array(
////            array(
////                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'],
////                'port' => 80,
////                'timestamp' => (microtime(true) * 1000000),
////            ),
////        ),
////    );
//        $oriData = explode('|', $tmp);
//        $unitData['span_name'] = $oriData[3];
//        $unitData['config'] = array();
//        $singleConfig['trace_id'] = intval($oriData[0]);
//        $singleConfig['id'] = intval($oriData[1]);
//        $singleConfig['name'] = trim($oriData[6]);
//        $singleConfig['parent_id'] = intval($oriData[2]);
//        $singleConfig['annotation'] = array(
//            array(
//                'type' => $oriData[5],
//                'port' => intval($oriData[7]),
//                'timestamp' => floatval($oriData[4]),
//            )
//        );
//        $unitData['config'] = $singleConfig;
//
//        foreach ($configData as $key => $compareData)
//        {
//            if (($compareData['span_name'] == $unitData['span_name'])
//                && ($compareData['config']['trace_id'] == $unitData['config']['trace_id'])
//                && ($compareData['config']['id'] == $unitData['config']['id'])
//                && ($compareData['config']['parent_id'] == $unitData['config']['parent_id'])
//                && ($compareData['config']['parent_id'] != 0)
//            )
//            {
//                $configData[$key]['config']['annotation'][] = $unitData['config']['annotation'][0];
//                $handled = 1;
//                break;
//            }
//
//        }
//
//        if(!$handled)
//            $configData[] = $unitData;
//
//
//
//
////array(8) {
////            [0]=>
////    string(15) "844180087144417"
////            [1]=>
////    string(15) "844180087144417"
////            [2]=>
////    string(1) "0"
////            [3]=>
////    string(21) "mall.auto.sina.com.cn"
////            [4]=>
////    string(19) "1.4653844182191E+15"
////            [5]=>
////    string(2) "ss"
////            [6]=>
////    string(5) "apple"
////            [7]=>
////    string(2) "80"
////  }
//
//    }
//
//    fclose($fd);
//    return $result;
//}







