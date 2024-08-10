<?php

class Wol_Mgr
{
    function Wol_Open($mac)
    {
        echo "<br />";
        $ip="255.255.255.255";//ip是局域网广播地址
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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mac = $_POST['mac'];
    echo "Wol Mac:" . $mac . "\n";
    $wol_mgr = new Wol_Mgr();
    $wol_mgr->Wol_Open($mac);
    $wol_mgr = null;
    header("Cache-Control: no-cache,must-revalidate");
}
//header("Location: index.html");
?>