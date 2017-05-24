<?php
/**
 * Created by PhpStorm.
 * User: hao
 * Date: 17-3-6
 * Time: 下午9:45
 */
error_reporting(0);

$mysqli;

function linkMysql()
{
//创建对象并打开连接，最后一个参数是选择的数据库名称
    global $mysqli;

    $mysqli = new mysqli('localhost','root','123456','IM');
//检查连接是否成功
    if (mysqli_connect_errno())
    {
        die('Unable to connect!'). mysqli_connect_error();
        return 0;
    }
    else
    {
        return 1;
    }
}

function saveId($username,$id)
{
    global $mysqli;

    if (getid($username) == false)
    {

        $sql = "INSERT INTO user(username,userid) VALUES ('$username','$id')";

        if ($mysqli->query($sql) == TRUE)
        {
            echo "user entry saved successfully.";
        }
        else
        {
            echo "INSERT attempt failed";
        }
    }
    else
    {
       $sql1 ="UPDATE user SET userid = '$id' WHERE username = '$username'";

        if ($mysqli->query($sql1) == TRUE)
        {
            echo "user entry saved successfully.";
        }
        else
        {
            echo "INSERT attempt failed";
        }
    }


}

function getid($username)
{
    global $mysqli;

    $sql = "select userid from user where username='$username'";

    if ($result = $mysqli->query($sql))
    {
        return $result->fetch_array()[0];
    }
    else
    {
        return false;
    }
}

function saveMessage($message,$myid,$otherid)
{
    global $mysqli;

    $date =  date('Y-m-d H:i:s',time());

    $sql = "INSERT INTO message(msg,date,my,other) VALUES ('$message','$date',$myid,$otherid)";

    if ($mysqli->query($sql) == TRUE)
    {
        echo "user entry saved successfully.";
    }
    else
    {
        echo "INSERT attempt failed";
    }
}




////创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("127.0.0.1", 8081);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request)
{

    echo $request->get[username];

    saveId($request->get[username],$request->fd);

//    var_dump($request->fd, $request->get, $request->server);
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame)
{
    $message = $frame->data;
//    echo  $message;
    $id = explode('~',$message)[0];
    $msg = explode('~',$message)[1];
    echo $id;
    if ($id == "1")
    {
        $ws->push(getid('2'), $msg);
    }
    else
    {
        $ws->push(getid('1'), $msg);
    }


});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd)
{
    echo "client-{$fd} is closed\n";
});

linkMysql();
$ws->start();



//swoole定时器
//swoole_timer_tick(2000, function ($timer_id) {
//    echo "tick-2000ms\n";
//});

//swoole延时器
//swoole_timer_after(2000,function()
//{
//    echo '\n1'.getmygid();
//});


//$serv = new swoole_server("127.0.0.1", 9501);
//
////设置异步任务的工作进程数量
//$serv->set(array('task_worker_num' => 4));
//
//$serv->on('receive', function($serv, $fd, $from_id, $data) {
//    //投递异步任务
//
//    echo 'sdasdasdasd';
//
//    $task_id = $serv->task($data);
//    echo "Dispath AsyncTask: id=$task_id\n";
//});
//
////处理异步任务
//$serv->on('task', function ($serv, $task_id, $from_id, $data) {
//    echo "New AsyncTask[id=$task_id]".PHP_EOL;
//    //返回任务执行的结果
//    $serv->finish("$data -> OK");
//});
//
////处理异步任务的结果
//$serv->on('finish', function ($serv, $task_id, $data) {
//    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
//});
//
//$serv->start();
//
//echo 'main'.getmygid();