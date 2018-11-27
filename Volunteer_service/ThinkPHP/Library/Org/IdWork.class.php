<?php 
namespace Org;
/**
 * ID 生成策略
 * 毫秒级时间41位+机器ID 10位+毫秒内序列12位。
 * 0           41     51     64
+-----------+------+------+
|time       |pc    |inc   |
+-----------+------+------+
 *  前41bits是以微秒为单位的timestamp。
 *  接着10bits是事先配置好的机器ID。
 *  最后12bits是累加计数器。
 *  macheine id(10bits)标明最多只能有1024台机器同时产生ID，sequence number(12bits)也标明1台机器1ms中最多产生4096个ID，
 *
 *  0---0000000000 0000000000 0000000000 0000000000 0 --- 00000 ---00000 ---000000000000
 *  在上面的字符串中，第一位为未使用（实际上也可作为long的符号位），接下来的41位为毫秒级时间，然后5位datacenter标识位，5位机器ID（并不算标识符，实际是为线程标识），然后12位该毫秒内的当前毫秒内的计数，加起来刚好64位，为一个Long型。
 * auth: Twitter的分布式自增ID算法Snowflake
 */
class IdWork
{
    const debug = 1;
    static $workerId;
    static $twepoch = 1361775855078;
    static $sequence = 0;
    const workerIdBits = 4;
    static $maxWorkerId = 15;
    const sequenceBits = 10;
    static $workerIdShift = 10;
    static $timestampLeftShift = 14;
    static $sequenceMask = 1023;
    private  static $lastTimestamp = -1;

    function __construct($workId = 1){
        if($workId > self::$maxWorkerId || $workId< 0 )
        {
            throw new \Exception("worker Id can't be greater than 15 or less than 0");
        }
        self::$workerId=$workId;
    }

    /**
     * 获得当前13位时间戳
     * @return string
     */
    private function timeGen(){
        $time = explode(' ', microtime());
        $time2= substr($time[0], 2, 3);
        return  $time[1].$time2;
    }
    
    private function tilNextMillis($lastTimestamp) {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }

        return $timestamp;
    }
    
    public function nextAuto(){
        $timestamp=$this->timeGen();
        if(self::$lastTimestamp == $timestamp) {
            self::$sequence = (self::$sequence + 1) & self::$sequenceMask;
            if (self::$sequence == 0) {
                $timestamp = $this->tilNextMillis(self::$lastTimestamp);
            }
        } else {
            self::$sequence  = 0;
        }
        if ($timestamp < self::$lastTimestamp) {
            throw new \Exception("Clock moved backwards.  Refusing to generate id for ".(self::$lastTimestamp-$timestamp)." milliseconds");
        }
        self::$lastTimestamp  = $timestamp;
        $nextId = ((sprintf('%.0f', $timestamp) - sprintf('%.0f', self::$twepoch)  )<< self::$timestampLeftShift ) | ( self::$workerId << self::$workerIdShift ) | self::$sequence;
        return $nextId;
    }

    /**
     * 10位全局自增id
     * @return string
     */
    public function nextId(){
        $today = date('Ymd');
        $start = rand(10000, 20000);
        $model = M();
        $model->execute("INSERT INTO `sequence`(id, modified) VALUES('{$start}', '{$today}') ON DUPLICATE KEY UPDATE id=id+1");
        return $today.$model->getLastInsID();
    }
}
?>