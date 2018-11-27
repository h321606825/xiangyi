<?php
// +----------------------------------------------------------------------
// | 常用公共函数类库
// +----------------------------------------------------------------------
// | 请不要修改或删除原有内容.
// +----------------------------------------------------------------------
/**
 * 调试输出
 * @param unknown $data
 */
function print_data($data, $var_dump = false)
{
    header("Content-type: text/html; charset=utf-8");
    echo "<pre>";
    if ($var_dump) {
        var_dump($data);
    } else {
        print_r($data);
    }
    exit();
}

/**
 * 将数组变成键值对数组
 *
 * @param array $data            
 * @param mixed $key            
 * @return array
 */
function array_kv(array $data, $key = 'id', $all = false, $all_field = 'id')
{
    if (! isset($data[0])) {
        return $data;
    }
    
    $array = array();
    if (count($data[0]) > 2) {
        foreach ($data as $_k => $_v) {
            if ($all) {
                if (count($_v) > 3) {
                    $new_value = $_v;
                    unset($new_value[$all_field]);
                    unset($new_value[$key]);
                    $array[''.$_v[$key]][''.$_v[$all_field]] = $new_value;
                } else {
                    $new_value = $_v;
                    unset($new_value[$all_field]);
                    unset($new_value[$key]);
                    $array[''.$_v[$key]][''.$_v[$all_field]] = end($new_value);
                }
            } else {
                $array[''.$_v[$key]] = $_v;
                unset($array[$_v[$key]][$key]);
            }
        }
    } else {
        foreach ($data as $_k => $_v) {
            $array[''.$_v[$key]] = end($_v);
        }
    }
    
    return $array;
}

/**
 * 将列表按照字段分组
 *
 * @param array $data            
 * @param unknown $key            
 * @return Ambigous <multitype:, unknown>
 */
function array_group(array $data, $key)
{
    $list = array();
    foreach ($data as $index => $item) {
        $_temp = $item;
        unset($_temp[$key]);
        $list[$item[$key]][] = $_temp;
    }
    
    return $list;
}

/**
 * CURL网络请求
 * 
 * @param unknown $url            
 * @param string $data            
 * @param string $contentType            
 * @return mixed
 */
function http_request($url, $data = null, $contentType = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL 报错时使用
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // SSL 报错时使用
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (! empty($contentType)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:' . $contentType
        ));
    }
    if (! empty($data)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * 数组排序
 */
function sort_list(&$list, $pid = 0, $index = 0){
    if (empty($list)) {
        return $list;
    }
    $data = array();
    
    $level = array('一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
    foreach ($list as $key => $value) {
        if ($value['pid'] == $pid) {
            unset($list[$key]);
            if ($pid > 0) {
                $split_str = '├─';
                for ($i = $index - 1; $i > 0; $i --) {
                    $split_str .= '──';
                }
                $value['split'] = $split_str;
                $value['level'] = $level[$index];
            }else{
                $value['split'] = '';
                $value['level'] = $level[0];
            }
            $data[] = $value;
            $children = sort_list($list, $value['id'], $index + 1);
            if(!empty($children)){
                $data = array_merge($data , $children);
            }
        }
    }
    
    // 把没有父节点的数据追加到返回结果中，避免数据丢失
    if($pid == 0 ){
        if(count($list) > 0){
            $data = array_merge($data, $list);
        }
        
        $list = $data;
        return $list;
    }
    return $data;
}

function shorturl($input) {
    $base32 = array (
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
        'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', '0', '1', '2', '3', '4', '5'
    );
     
    $hex = md5($input);
    $hexLen = strlen($hex);
    $subHexLen = $hexLen / 8;
    $output = array();
     
    for ($i = 0; $i < $subHexLen; $i++) {
        $subHex = substr ($hex, $i * 8, 8);
        $int = 0x3FFFFFFF & (1 * ('0x'.$subHex));
        $out = '';
         
        for ($j = 0; $j < 6; $j++) {
            $val = 0x0000001F & $int;
            $out .= $base32[$val];
            $int = $int >> 5;
        }
         
        $output[] = $out;
    }
     
    return $output;
}

/**
 * 数据XML编码
 * @param  object $xml  XML对象
 * @param  mixed  $data 数据
 * @param  string $item 数字索引时的节点名称
 * @return string
 */
function data_xml($xml, $data, $item = 'item'){
    foreach ($data as $key => $value) {
        /* 指定默认的数字key */
        is_numeric($key) && $key = $item;
    
        /* 添加子元素 */
        if(is_array($value) || is_object($value)){
            $child = $xml->addChild($key);
        } else {
            if(is_numeric($value)){
                $child = $xml->addChild($key, $value);
            } else {
                $child = $xml->addChild($key);
                $node  = dom_import_simplexml($child);
                $cdata = $node->ownerDocument->createCDATASection($value);
                $node->appendChild($cdata);
            }
        }
    }
}

/**
 * 冒泡排序
 */
function maopao($array, $sortField = 'sort', $childField = ''){
    $count = count($array);
    if ($count <= 0) {
        return $array;
    }
    for ($i = 0; $i < $count; $i ++) {
        if ($childField != '' && !empty($array[$i][$childField])) {
            $array[$i][$childField] = maopao($array[$i][$childField]);
        }
    
        for ($k = $count - 1; $k > $i; $k --) {
            if ($array[$k][$sortField] > $array[$k - 1][$sortField]) {
                $tmp = $array[$k];
                $array[$k] = $array[$k - 1];
                $array[$k - 1] = $tmp;
            }
        }
    }
    return $array;
}

function get_child($all_list, $pid = 0){
    $list = array();
    foreach($all_list as $index=>$item){
        if($item['pid'] == $pid){
            unset($all_list[$index]);

            $children = get_child($all_list, $item['id']);
            $item['has_child'] = count($children) > 0 ? 1 : 0;
            $item['children'] = $children;
            $list[] = $item;
        }
    }

    return $list;
}

/**
 * 根据appid获取微信配置文件
 * @param unknown $appid
 */
function get_wx_config($appid = ''){
    $wxList = C('WXLIST');
    if($appid == ''){
        $appid = $_SERVER['HTTP_HOST'];
    }
    if(strpos($appid, '.') > 1){ // 根据host匹配
        $host = $appid;
        foreach ($wxList as $key=>$item){
            if(is_array($item['host'])){
                if(in_array($host, $item['host'])){
                    if(IS_APP){
                        if($item['type'] == 'open'){
                            $appid = $key;
                            break;
                        }
                    }else{
                        $appid = $key;
                        break;
                    }
                }
            }else if($item['host'] == $host){
                if(IS_APP){
                    if($item['type'] == 'open'){
                        $appid = $key;
                        break;
                    }
                 }else{
                    $appid = $key;
                    break;
                }
            }
        }
    }
    
    if(isset($wxList[$appid])){
        return require COMMON_PATH.'Conf/'.$appid.'/config.php';
    }
    //E('微信配置文件不存在');
}

/**
 * 获取规格名称
 * @param unknown $skuJson
 */
function get_spec_name($skuJson){
    if(empty($skuJson) || $skuJson == '[]'){
        return '';
    }else if(!is_array($skuJson)){
        $skuJson = json_decode($skuJson, true);
    }
    
    $sku_name = '';
    foreach($skuJson as $sku){
        $sku_name .= $sku['v'].' ';
    }
    return rtrim($sku_name);
}
?>