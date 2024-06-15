<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_POST['ip'];
    $mac = $_POST['mac'];
    echo "IP:" . $ip . "        Mac:" . $mac . "\n";
    $addr_byte = explode(':', $mac);
    $hw_addr = '';
    for ($a = 0; $a < 6; $a++) $hw_addr .= chr(hexdec($addr_byte[$a]));
    $msg = chr(255) . chr(255) . chr(255) . chr(255) . chr(255) . chr(255);
    for ($a = 1; $a <= 16; $a++) $msg .= $hw_addr;
    //使用UDP socket广播消息
    $s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    if ($s === false) {
        echo "create socket fail!\n";
        echo "error code：'" . socket_last_error($s) . "' - " . socket_strerror(socket_last_error($s));
        return FALSE;
    } else {
        //设置socket的选项，第三个参数设置为SO_REUSEADDR时，在关机短时间内有效
        $opt_ret = socket_set_option($s, SOL_SOCKET, SO_BROADCAST, 1);
        if ($opt_ret < 0) {
            echo "set socket option fail :" . strerror($opt_ret) . "\n";
            return FALSE;
        }
        $result = socket_sendto($s, $msg, strlen($msg), 0, $ip, 666);
        if ($result === false) {
            echo "send fail :" . socket_strerror(socket_last_error()) . "\n";
        } else {
            echo "send success.";
        }
        socket_close($s);
    }

}
//header("Location: index.html");
?>