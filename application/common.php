<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2017/7/10 0010 17:06
 * Blog：www.myblogs.xyz
 */

// tool function
if (!function_exists('sp')){
    /**
     * 打印函数
     * @param $var
     */
    function sp($var){
        header('Content-Type:text/html; charset=utf-8');
        echo '<pre style="background:#ddd;border-radius:5px;border:1px solid #ccc;padding:10px">';
        if(is_null($var)){
            var_dump($var);
        }elseif(is_bool($var)){
            var_dump($var);
        }else{
            print_r($var);
        }
        echo '</pre>';
    }
}

if (!function_exists('IpToArea')){
    /**
     * 获取ip定位地址
     * @param $_ip
     * @return string
     */
    function IpToArea($_ip) {
        static $IpLocation;
        if(empty($IpLocation)){
            import('location.IpLocation');
            $IpLocation = new IpLocation(); // 实例化类 参数表示IP地址库文件
        }
        $arr = $IpLocation->getlocation($_ip);
        return $arr['country'];
    }
}

if (!function_exists('get_client_ip')){
    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    function get_client_ip($type = 0,$adv=false) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}

if (!function_exists('is_ios')){
    /**
     * 判断是否是ios客户端
     * @return bool
     */
    function is_ios(){
        $agent = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
        if (strpos($agent, 'iphone') || strpos($agent, 'ipad')){
            return true;
        }
        return false;
    }
}

if (!function_exists('is_android')){
    /**
     * 判断是否是android客户端
     * @return bool
     */
    function is_android(){
        $agent = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
        if(strpos($agent, 'android')){
            return true;
        }
        return false;
    }
}

if (!function_exists('is_wechat')){
    /**
     * 判断是否是微信打开
     * @return bool
     */
    function is_wechat(){
        $agent = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
        if(strpos($agent, 'MicroMessenger') === false){
            return false;
        }
        return true;
    }
}

if (!function_exists('isAliClient')) {
    /**
     * 判断是否支付宝内置浏览器访问
     * @return bool
     */
    function isAliClient()
    {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'Alipay') !== false;
    }
}

if (!function_exists('console_log')){
    /**
     * 数据打印
     * @param $data
     */
    function console_log($data){
        echo '<script type="text/javascript">';
        echo 'console.log('. print_r($data) .')';
        echo '</script>';
    }
}

if (!function_exists('screen_url')){
    /**
     * 获取筛选参数
     * @param $url
     * @param $param
     * @return string
     */
    function screen_url($url, $param){
        // 获取GET参数
        if (!empty($_GET)){
            $get_param = array();
            foreach($_GET as $k=>$v){
                if(!is_array($v) && !empty($v)){
                    $get_param[$k] = $v;
                }
            }
            if ($param['keywords']){
                $param['keywords'] = encode($param['keywords']);
            }
            $param = array_merge($get_param,$param);
        }
        return url($url,$param);
    }
}

if (!function_exists('delFileByDir')){
    /**
     * 删除目录结构
     * @param $dir
     */
    function delFileByDir($dir) {
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $full_path = $dir . "/" . $file;
                if(is_dir($full_path)) {
                    delFileByDir($full_path);
                }else{
                    unlink($full_path);
                }
            }
        }
        closedir($dh);
    }
}

if (!function_exists('getAround')){
    /**
     * 根据经纬度和半径计算出范围
     * @param string $lat 纬度
     * @param String $lng 经度
     * @param float $radius 半径(m)
     * @return array
     */
    function getAround($lat,$lng,$radius){
        $PI = 3.14159265;

        $degree = (24901*1609)/360.0;
        $dpmLat = 1/$degree;

        $radiusLat = $dpmLat * $radius;
        $minLat = $lat - $radiusLat;    // 最小经度
        $maxLat = $lat + $radiusLat;    // 最大经度
        $mpdLng = $degree * cos($lat * ($PI/180));
        $dpmLng = 1 / $mpdLng;
        $radiusLng = $dpmLng * $radius;
        $minLng = $lng - $radiusLng;    // 最小纬度
        $maxLng = $lng + $radiusLng;    // 最大纬度

        // 返回范围数组
        $scope = array(
            'minLat'=>$minLat,
            'maxLat'=>$maxLat,
            'minLng'=>$minLng,
            'maxLng'=>$maxLng
        );

        return $scope;
    }
}

if (!function_exists('squarePoint')){
    /**
     * 根据经纬度和半径计算出正方形范围
     * @param string $lng 经度
     * @param String $lat 纬度
     * @param float $radius 默认是500米（0.5Km）
     * @return array
     */
    function squarePoint($lng, $lat, $radius = 0.5)
    {
        $dlng =  2 * asin(sin($radius / (2 * 6371)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $radius/6371;
        $dlat = rad2deg($dlat);
        return array(
            'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
            'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
            'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
            'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
        );
    }
}

if (!function_exists('getDistance')){
    /**
     * 计算两点地理坐标之间的距离
     * @param string $lng_start 起点经度
     * @param string $lat_start 起点纬度
     * @param string $lng_end 终点经度
     * @param string $lat_end 终点纬度
     * @param int $unit 单位 1:米 2:公里
     * @param int $decimal 精度 保留小数位数
     * @return float
     */
    function getDistance($lng_start, $lat_start, $lng_end, $lat_end, $unit=2, $decimal=2){

        $EARTH_RADIUS = 6370.996; // 地球半径系数
        $PI = 3.1415926;

        $radLat1 = $lat_start * $PI / 180.0;
        $radLat2 = $lat_end * $PI / 180.0;

        $radLng1 = $lng_start * $PI / 180.0;
        $radLng2 = $lng_end * $PI /180.0;

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
        $distance = $distance * $EARTH_RADIUS * 1000;

        if($unit==2){
            $distance = $distance / 1000;
        }
        return round($distance, $decimal);
    }
}

if (!function_exists('is_point_in_polygon')){
    /**
     * 判断一个坐标是否在一个多边形内（由多个坐标围成的）
     * 基本思想是利用射线法，计算射线与多边形各边的交点，如果是偶数，则点在多边形外，否则
     * 在多边形内。还会考虑一些特殊情况，如点在多边形顶点上，点在多边形边上等特殊情况。
     * @param array $point 指定点坐标
     * @param array $pts 多边形坐标 顺时针方向
     * @return bool
     */
    function is_point_in_polygon($point, $pts) {
        $N = count($pts);
        $boundOrVertex = true; //如果点位于多边形的顶点或边上，也算做点在多边形内，直接返回true
        $intersectCount = 0;//cross points count of x
        $precision = 2e-10; //浮点类型计算时候与0比较时候的容差
        $p1 = 0;//neighbour bound vertices
        $p2 = 0;
        $p = $point; //测试点

        $p1 = $pts[0];//left vertex
        for ($i = 1; $i <= $N; ++$i) {//check all rays
            // dump($p1);
            if ($p['lng'] == $p1['lng'] && $p['lat'] == $p1['lat']) {
                return $boundOrVertex;//p is an vertex
            }

            $p2 = $pts[$i % $N];//right vertex
            if ($p['lat'] < min($p1['lat'], $p2['lat']) || $p['lat'] > max($p1['lat'], $p2['lat'])) {//ray is outside of our interests
                $p1 = $p2;
                continue;//next ray left point
            }

            if ($p['lat'] > min($p1['lat'], $p2['lat']) && $p['lat'] < max($p1['lat'], $p2['lat'])) {//ray is crossing over by the algorithm (common part of)
                if($p['lng'] <= max($p1['lng'], $p2['lng'])){//x is before of ray
                    if ($p1['lat'] == $p2['lat'] && $p['lng'] >= min($p1['lng'], $p2['lng'])) {//overlies on a horizontal ray
                        return $boundOrVertex;
                    }

                    if ($p1['lng'] == $p2['lng']) {//ray is vertical
                        if ($p1['lng'] == $p['lng']) {//overlies on a vertical ray
                            return $boundOrVertex;
                        } else {//before ray
                            ++$intersectCount;
                        }
                    } else {//cross point on the left side
                        $xinters = ($p['lat'] - $p1['lat']) * ($p2['lng'] - $p1['lng']) / ($p2['lat'] - $p1['lat']) + $p1['lng'];//cross point of lng
                        if (abs($p['lng'] - $xinters) < $precision) {//overlies on a ray
                            return $boundOrVertex;
                        }

                        if ($p['lng'] < $xinters) {//before ray
                            ++$intersectCount;
                        }
                    }
                }
            } else {//special case when ray is crossing through the vertex
                if ($p['lat'] == $p2['lat'] && $p['lng'] <= $p2['lng']) {//p crossing over p2
                    $p3 = $pts[($i+1) % $N]; //next vertex
                    if ($p['lat'] >= min($p1['lat'], $p3['lat']) && $p['lat'] <= max($p1['lat'], $p3['lat'])) { //p.lat lies between p1.lat & p3.lat
                        ++$intersectCount;
                    } else {
                        $intersectCount += 2;
                    }
                }
            }
            $p1 = $p2;//next ray left point
        }

        if ($intersectCount % 2 == 0) {//偶数在多边形外
            return false;
        } else { //奇数在多边形内
            return true;
        }
    }
}

if (!function_exists('bd_encrypt')){
    /**
     * 高德坐标转百度
     * @param $gg_lon
     * @param $gg_lat
     * @return mixed
     */
    function bd_encrypt($gg_lon,$gg_lat)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $gg_lon;
        $y = $gg_lat;
        $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
        $data['lng'] = $z * cos($theta) + 0.0065;
        $data['lat'] = $z * sin($theta) + 0.006;
        return $data;
    }
}

if (!function_exists('bd_decrypt')){
    /**
     * 百度坐标转高德
     * @param $bd_lon
     * @param $bd_lat
     * @return mixed
     */
    function bd_decrypt($bd_lon,$bd_lat)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $bd_lon - 0.0065;
        $y = $bd_lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $data['lng'] = $z * cos($theta);
        $data['lat'] = $z * sin($theta);
        return $data;
    }
}

if (!function_exists('byte_format')){
    /**
     * 字节格式化 把字节数格式为 B K M G T 描述的大小
     * @param $size
     * @param int $dec
     * @return string
     */
    function byte_format($size, $dec = 2) {
        $a = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $pos = 0;
        while ($size >= 1024) {
            $size /= 1024;
            $pos++;
        }
        return round($size, $dec) . " " . $a[$pos];
    }
}

if (!function_exists('time_cycle')){
    /**
     * 时间戳转换时间
     * @param $time
     * @return bool|string
     */
    function time_cycle($time) {
        if(is_numeric($time)) {
            $value = array(
                'years' => 0,
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0
            );
            if($time >= 31556926){
                $value['years'] = floor($time/31556926);
                $time = ($time%31556926);
            }
            if($time >= 86400){
                $value['days'] = floor($time/86400);
                $time = ($time%86400);
            }
            if($time >= 3600){
                $value['hours'] = floor($time/3600);
                $time = ($time%3600);
            }
            if($time >= 60){
                $value['minutes'] = floor($time/60);
                $time = ($time%60);
            }
            $value['seconds'] = floor($time);
            $t = empty($value['years'])? '' : $value['years'] . '年';
            $t .= empty($value['days'])? '' : $value['days'] . '天';
            $t .= empty($value['hours'])? '' : $value['hours'] . '时';
            //$t .= empty($value['minutes'])? '' : $value['minutes'] . '分';
            $t .= $value['minutes'] . '分';
            //$t .= empty($value['seconds'])? '' : $value['seconds'] . '秒';
            Return $t;
        }else{
            return (bool) FALSE;
        }
    }
}

if (!function_exists('getEditorImg')){
    /**
     * 提取编辑器图片
     * @param $content
     * @param string $order
     * @return string
     */
    function getEditorImg($content, $order = 'all'){
        $pattern="/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$content,$match);
        if(isset($match[1])&&!empty($match[1])){
            if($order === 'all'){
                return $match[1];
            }
            if(is_numeric($order) && isset($match[1][$order])){
                return $match[1][$order];
            }
        }
        return '';
    }
}

if (!function_exists('u')){
    /**
     * 后台模版带权限的URL校验
     * @param string $url URL表达式，格式：'[分组/模块/操作#锚点@域名]?参数1=值1&参数2=值2...'
     * @param string $vars 参数
     * @param string $title 标题
     * @param string $mini 是否异步加载
     * @param string $class A标签样式
     * @param string $width 新窗口width
     * @param string $height 新窗口height
     * @return string
     */
    function u($url = '', $vars = '', $title = '', $mini = '', $class = '', $width = '', $height = '') {
        static $admin;
        if(empty($admin)){
            $admin = session('admin_data');
        }
        if ($admin['role_id'] != 1) {
            $menu_id = model('Menu')->menuField(['menu_action'=>$url], 'menu_id');
            if (!model('RoleMaps')->roleExists(['role_id'=>$admin['role_id'], 'menu_id'=>$menu_id])){
                return '';
            }else{
                $url = url($url, $vars);
            }
        } else {
            $url = url($url, $vars);
        }

        $href = 'href="javascript:;"';
        //权限判断 暂时忽略，后面补充
        $c = $m = $u = $h = $w = '';
        if (!empty($class)) {
            $c = ' class="layui-btn ' . $class . '" ';
        }
        if (!empty($mini)) {
            $m = ' lay-event="' . $mini . '" ';
        }
        if (!empty($url)){
            if (empty($mini)){
                $u = ' lay-href="' . $url . '" ';
            }else{
                $u = ' data-url="' . $url . '" ';
            }
        }
        if (!empty($width)) {
            $w = ' w="' . $width . '" ';
        }
        if (!empty($width)) {
            $h = ' h="' . $height . '" ';
        }
        return '<a ' . $href . $c . $m . $u . $w . $h . ' >' . L($title) . '</a>';
    }
}

if (!function_exists('build_query')){
    /**
     * 重写实现 http_build_query 提交实现(同名key)key=val1&key=val2
     * @param array $formData 数据数组
     * @param string $numericPrefix 数字索引时附加的Key前缀
     * @param string $argSeparator 参数分隔符(默认为&)
     * @param string $prefixKey Key 数组参数，实现同名方式调用接口
     * @return string
     */
    function build_query($formData, $numericPrefix = '', $argSeparator = '&', $prefixKey = '') {
        $str = '';
        foreach ($formData as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= urlencode($key) . '=' . urlencode($val);
                } else {
                    $str .= urlencode($prefixKey) . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey == '') {
                    $prefixKey .= $key;
                }
                if (isset($val[0]) && is_array($val[0])) {
                    $arr = array();
                    $arr[$key] = $val[0];
                    $str .= $argSeparator . http_build_query($arr);
                } else {
                    $str .= $argSeparator . $this->build_query($val, $numericPrefix, $argSeparator, $prefixKey);
                }
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }
}

if (!function_exists('order_code')){
    /**
     * 生成24位唯一订单号码
     */
    function order_code(){
        // 生成24位唯一订单号码，格式：YYYY-MMDD-HHII-SS-NNNN,NNNN-CC，其中：YYYY=年份，MM=月份，DD=日期，HH=24格式小时，II=分，SS=秒，NNNNNNNN=随机数，CC=检查码
        // 订单号码主体（s）
        $order_id_main = date('YmdHis') . rand(10000000,99999999);
        //订单号码主体长度
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for($i=0; $i<$order_id_len; $i++){
            $order_id_sum += (int)(substr($order_id_main,$i,1));
        }
        // 唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
        $order_code = 'YH' . $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);
        return $order_code;
    }
}

// is_* function
if (!function_exists('is_phone')){
    /**
     * 判断输入的字符串是否是一个合法的电话号码（仅限中国大陆）
     * @param string $string
     * @return boolean
     */
    function is_phone($string) {
        if (preg_match('/^[0,4]\d{2,3}-\d{7,8}$/', $string))
            return true;
        return false;
    }

}

if (!function_exists('is_email')){
    /**
     * 判断一个字符串是否是一个合法邮箱
     * @param string $string
     * @return boolean
     */
    function is_email($string) {
        return (boolean) preg_match('/^[a-z0-9.\-_]{2,64}@[a-z0-9]{2,32}(\.[a-z0-9]{2,5})+$/i', $string);
    }
}

if (!function_exists('is_date')){
    /**
     * 判断一个字符串是否是一个合法时间
     * @param string $string
     * @return boolean
     */
    function is_date($string) {
        if (preg_match('/^\d{4}-[0-9][0-9]-[0-9][0-9]$/', $string)) {
            $date_info = explode('-', $string);
            return checkdate(ltrim($date_info[1], '0'), ltrim($date_info[2], '0'), $date_info[0]);
        }
        if (preg_match('/^\d{8}$/', $string)) {
            return checkdate(ltrim(substr($string, 4, 2), '0'), ltrim(substr($string, 6, 2), '0'), substr($string, 0, 4));
        }
        return false;
    }
}

if (!function_exists('is_mobile')){
    /**
     * 判断输入的字符串是否是一个合法的手机号(仅限中国大陆)
     * @param string $string
     * @return boolean
     */
    function is_mobile($string) {
        if(preg_match('/^[1]+[0,1,2,3,4,5,6,7,8,9]+\d{9}$/', $string))
            return true;
        return false;
    }
}

if (!function_exists('is_QQ')){
    /**
     * 判断输入的字符串是否是一个合法的QQ
     * @param string $string
     * @return boolean
     */
    function is_QQ($string) {
        if (ctype_digit($string)) {
            $len = strlen($string);
            if ($len < 5 || $len > 13)
                return false;
            return true;
        }
        return is_email($string);
    }
}

if (!function_exists('is_email')){
    /**
     * 判断一个字符串是否是一个合法邮箱
     * @param string $string
     * @return boolean
     */
    function is_email($string) {
        return (boolean) preg_match('/^[a-z0-9.\-_]{2,64}@[a-z0-9]{2,32}(\.[a-z0-9]{2,5})+$/i', $string);
    }
}

if (!function_exists('is_images')){
    /**
     *  判断是否是图片文件
     * @param string $fileName
     * @return boolean
     */
    function is_images($fileName) {
        $ext = explode('.', $fileName);
        $ext_seg_num = count($ext);
        if ($ext_seg_num <= 1)
            return false;

        $ext = strtolower($ext[$ext_seg_num - 1]);
        return in_array($ext, array('jpeg', 'jpg', 'png', 'gif'));
    }
}


if (!function_exists('is_url')){
    /**
     * 判断是否是网络连接
     * @param $url
     * @return bool
     */
    function is_url($url) {
        $pattern_1 = "/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)$)(:(\d+))?\/?/i";
        $pattern_2 = "/^(www)((\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)$)(:(\d+))?\/?/i";
        if(preg_match($pattern_1, $url) || preg_match($pattern_2, $url)){
            return true;
        } else{
            return false;
        }
    }
}

if (!function_exists('is_base64')){
    /**
     * 判断是否是base64
     * @param $str
     * @return bool
     */
    function is_base64($str){
        if($str == base64_encode(base64_decode($str))){
            return true;
        }else{
            return false;
        }
    }
}

if (!function_exists('is_encode')){
    /**
     * 判断是否加密
     * @param $str
     * @return bool
     */
    function is_encode($str){
        if($str == encode(decode($str))){
            return true;
        }else{
            return false;
        }
    }
}

if (!function_exists('is_utf8')){
    /**
     * 检查字符串是否是UTF8编码
     * @param string $string 字符串
     * @return Boolean
     */
    function is_utf8($string) {
        return preg_match('%^(?:
		 [\x09\x0A\x0D\x20-\x7E]			# ASCII
	   | [\xC2-\xDF][\x80-\xBF]			 # non-overlong 2-byte
	   |  \xE0[\xA0-\xBF][\x80-\xBF]		# excluding overlongs
	   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	   |  \xED[\x80-\x9F][\x80-\xBF]		# excluding surrogates
	   |  \xF0[\x90-\xBF][\x80-\xBF]{2}	 # planes 1-3
	   | [\xF1-\xF3][\x80-\xBF]{3}		  # planes 4-15
	   |  \xF4[\x80-\x8F][\x80-\xBF]{2}	 # plane 16
	)*$%xs', $string);
    }
}

// str_* function
if (!function_exists('str_encrypt')){
    /**
     * 字符串加密
     * @param $str
     * @param int $start
     * @param int $length
     * @param string $encrypt
     * @param string $encoding
     * @return mixed
     */
    function str_encrypt($str, $start = 0, $length = 1, $encrypt = '*', $encoding = 'utf8'){
        $str_len = mb_strlen($str, $encoding);
        if($str_len > $length){
            for($i = 0; $i < $str_len; $i++){
                if ($i > $start && $i < $length){
                    $str[$i] = $encrypt;
                }
            }
        }
        return $str;
    }
}

if (!function_exists('substr_cut')){
    /**
     * 用户姓名隐藏
     * @param $user_name
     * @return string
     */
    function substr_cut($user_name){
        $str_len     = mb_strlen($user_name, 'utf-8');
        $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
        if (mb_strlen($user_name, 'utf-8') < 2){
            return $user_name;
        }
        return $str_len == 2 ? str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) . $lastStr : str_repeat("*", $str_len - 2) . $lastStr;
    }
}

if (!function_exists('form_time')){
    /**
     * 获取发布时时间
     * @param $time
     * @return string
     */
    function form_time($time) {
        $t = time() - $time;
        $mon = (int) ($t / (86400 * 30));
        if ($mon >= 1) {
            return '一个月前';
        }
        $day = (int) ($t / 86400);
        if ($day >= 1) {
            return $day . '天前';
        }
        $h = (int) ($t / 3600);
        if ($h >= 1) {
            return $h . '小时前';
        }
        $min = (int) ($t / 60);
        if ($min >= 1) {
            return $min . '分钟前';
        }
        return '刚刚';
    }
}

if (!function_exists('duration_time')){
    function duration_time($begin_time, $end_time, $tags = ':') {
        if($begin_time > $end_time){
            $begin_time = $end_time;
            $end_time = $begin_time;
        }
        //计算天数
        $time_diff = $end_time - $begin_time;
        $days = intval($time_diff / 86400);
        //计算小时数
        $remain = $time_diff % 86400;
        $hours = intval($remain / 3600);
        $hours = $hours < 10 ? '0' . $hours : $hours;
        //计算分钟数
        $remain = $remain % 3600;
        $minute = intval($remain/60);
        $minute = $minute < 10 ? '0' . $minute : $minute;
        //计算秒数
        $second = $remain % 60;
        $second = $second < 10 ? '0' . $second : $second;
        //$res = array("days" => $days,"hours" => $hours,"minute" => $minute,"second" => $second);
        $res = '';
        if ($days > 0){
            if ($tags == ':'){
                $res .= "{$days}:";
            }else{
                $res .= "{$days}天";
            }
        }
        if ($tags == ':'){
            $res .= "{$hours}:{$minute}:{$second}";
        }else{
            $res .= "{$hours}时{$minute}分";
        }
        return $res;
    }
}

if (!function_exists('getMonthNum')){
    /**
     * 获取两个时间段相差的月份数量
     * @param $date1
     * @param $date2
     * @param string $tags
     * @return float|int
     * @example:
     * $date1 = "2003-08-11";
     * $date2 = "2008-11-06";
     * $monthNum = getMonthNum( $date1 , $date2 );
     * echo $monthNum;
     */
    function getMonthNum($date1, $date2, $tags='-' ){
        $date1 = explode($tags, $date1);
        $date2 = explode($tags, $date2);
        return abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
    }
}

if (!function_exists('text_line')){
    /**
     * 评论换行
     * @param $content
     * @param int $line
     * @return string
     */
    function text_line($content,$line = 30){
        $length = mb_strlen($_POST[$content],'utf8');
        $comment = "";
        for($i = 0;$i < $length/$line;$i++){
            $comment .= mb_substr($_POST[$content], $i * $line, $line,'utf8') . "\n";
        }
        return htmlspecialchars($comment);
    }
}

if (!function_exists('jsonReturn')){
    /**
     * @param int $status 返回状态
     * @param string $data 返回成功数据
     * @param string $message 返回信息
     */
    function jsonReturn($status=0, $data='', $message=''){
        header('Content-Type:application/json; charset=utf-8');
        $jsonData = array(
            'status'   => $status,
            'data'     => $data,
            'message'  => $message,
        );
        exit(json_encode($jsonData));
    }
}

if (!function_exists('length')){
    /**
     * 截取字符串
     * @param $str
     * @param $n
     * @return string
     */
    function length($str, $n){
        if(is_numeric($n) || $n > 0){
            $str = str_replace('&nbsp;', ' ', $str);
            $str = strip_tags($str);
            $str = mb_substr($str, 0, $n, 'utf-8');
        }
        return($str);
    }
}

if (!function_exists('str_cut')){
    /**
     * 判断字符串长度并截取字符串
     * @param $str
     * @param int $length
     * @param string $joint
     * @param string $encoding
     * @return string
     */
    function str_cut($str, $length = 20, $joint = '', $encoding = 'utf8'){
        if(mb_strlen($str, $encoding) > $length){
            $str = mb_substr($str, 0, $length, $encoding) . $joint;
        }
        return $str;
    }
}

if (!function_exists('sub_str')){
    /**
     * 截取字符串alias
     * @param $str
     * @param $start
     * @param null $length
     * @param string $join
     * @param string $encoding
     * @return string
     */
    function sub_str($str, $start, $length = null, $join = '', $encoding = 'utf8'){
        $str = mb_substr($str, $start, $length, $encoding) . $join;
        return $str;
    }
}

if (!function_exists('str_len')){
    /**
     * 判断字符串长度
     * @param $str
     * @param $length
     * @param string $coding
     * @return bool
     */
    function str_len($str, $length, $coding = 'utf8'){
        if(mb_strlen($str,$coding) > $length){
            return true;
        }
        return false;
    }
}

if (!function_exists('mb_str_cut')){
    /**
     * 字符串截取，支持中文和其他编码
     * @param string $str 需要转换的字符串
     * @param int $start 开始位置
     * @param int $length 截取长度
     * @param string $charset 截取长度
     * @param bool $suffix 截断显示字符
     * @return string
     */
    function mb_str_cut($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
        if (function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
            if (false === $slice) {
                $slice = '';
            }
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice . '...' : $slice;
    }
}

if (!function_exists('explode_list')){
    /**
     * 文本换行
     * @param string $delimiter
     * @param $string
     * @param int $element
     * @return bool
     */
    function explode_list($delimiter = '', $string, $element = 0){
        if (empty($string)) return false;
        if (is_array($string)){
            $string = implode($delimiter,$string);
        }
        $string_array = explode($delimiter,$string);
        $string_element = $string_array[$element];
        return $string_element;
    }
}

if (!function_exists('hide_tel')){
    /**
     * 隐藏手机号
     * @param $phone
     * @return mixed
     */
    function hide_tel($phone){
        $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i',$phone); //固定电话
        if($IsWhat == 1){
            return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2',$phone);
        }else{
            return  preg_replace('/(1[3578]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
        }
    }
}

if (!function_exists('encode')){
    /**
     * 简单对称加密算法之加密
     * @param String $string 需要加密的字串
     * @param String $keys 加密EKY
     * @author Anyon Zou <zoujingli@qq.com>
     * @date 2013-08-13 19:30
     * @update 2014-10-10 10:10
     * @return String
     */
    function encode($string = '', $keys = 'cxphp') {
        $strArr = str_split(base64_encode($string));
        $strCount = count($strArr);
        foreach (str_split($keys) as $key => $value)
            $key < $strCount && $strArr[$key].=$value;
        return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
    }
}

if (!function_exists('decode')){
    /**
     * 简单对称加密算法之解密
     * @param String $string 需要解密的字串
     * @param String $keys 解密KEY
     * @author Anyon Zou <zoujingli@qq.com>
     * @date 2013-08-13 19:30
     * @update 2014-10-10 10:10
     * @return String
     */
    function decode($string = '', $keys = 'cxphp') {
        $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
        $strCount = count($strArr);
        foreach (str_split($keys) as $key => $value)
            $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
        return base64_decode(join('', $strArr));
    }
}

// array_* function
if (!function_exists('order_sort')){
    /**
     * 数组排序
     * @param $order
     * @return string
     */
    function order_sort($order){
        $data = '';
        foreach($order as $key=>$val){
            foreach($val as $k=>$v){
                if($key > 0){
                    $data .= ',' . "$k $v";
                }else{
                    $data .= "$k $v";
                }
            }
        }
        return $data;
    }
}

if (!function_exists('array_unique_two')){
    /**
     * 二维数组去重复
     * @param $array
     * @return array
     */
    function array_unique_two($array){
        if (empty($array)) return array();
        $new_array = array();
        foreach ($array as $key=>$val){
            $new_array[] = implode(',',$val);
        }
        $new_array = array_unique($new_array);
        foreach ($new_array as $key=>$value){
            $new_array[$key] = explode(',',$value);
        }
        return $new_array;
    }
}

if (!function_exists('array_removal')){
    /**
     * 二维数组转一维数组
     * @param $array
     * @return array
     */
    function array_removal($array){
        $new_array = array();
        foreach ($array as $key=>$val){
            foreach ($val as $value){
                $new_array[] = $value;
            }
        }
        return $new_array;
    }
}

if (!function_exists('object_array')){
    /**
     * 对象转换为数组
     * @param $array
     * @return array
     */
    function object_array($array){
        if(is_object($array)) {
            $array = (array)$array;
        }
        if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = object_array($value);
            }
        }
        return $array;
    }
}

if (!function_exists('array_remove')){
    function array_remove($array, $offset){
        array_splice($array, $offset, 1);
    }
}

if (!function_exists('array_remove_two')){
    function array_remove_two($array, $offset){
        $new_array = array();
        foreach ($array as $key=>$val){
            $new_array[] = json_encode($val);
        }
        unset($new_array[$offset]);
        foreach ($new_array as $key=>$val){
            $new_array[$key] = json_decode($val,true);
        }
        return $new_array;
    }
}

// 数据类
if (!function_exists('L')) {
    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array  $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function L($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name) {
            return $name;
        }
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return \think\facade\Lang::get($name, $vars, $lang);
    }
}

if (!function_exists('getMessage')){
    /**
     * 提示信息
     * @param $type
     * @return string
     */
    function getMessage($type)
    {
        if (empty($type)) return '';
        $join = '';
        if (!empty($type)){
            if (strpos($type, '.')){
                list($type, $join) = explode('.', $type);
            }
        }
        $lang = 'cn';
        if (!empty($type)){
            if (strpos($type, ',')){
                list($type, $lang) = explode(',', $type);
            }
        }
        if ($lang == 'cn'){
            $message = include_once APP_PATH . 'common/lang/zh-cn.php';
        }else{
            $message = include_once APP_PATH . 'common/lang/en.php';
        }
        $str = !isset($message[$type]) ? $type : $message[$type];
        return $str . $join;
    }
}

if (!function_exists('M')){
    /**
     * @param string $table 操作表
     * @param string $pk 操作表主键 false 没有主键
     * @return \app\common\model\Common
     */
    function M($table, $pk = '')
    {
        $auth_model = new \app\common\model\Auth($table, $pk);
        return $auth_model;
    }
}

if (!function_exists('import')){
    function import($value){
        $file_path = str_replace('.', '/', $value);
        include_once getcwd() . '/../extend/' . $file_path . '.php';
    }
}

if (!function_exists('posUrl')){
    function posUrl($url)
    {
        $pos = strpos($url, '_');
        if ($pos != false){
            $url_one_array = explode('_', $url);
            $url_str = '';
            foreach ($url_one_array as $key=>$val){
                $url_str .= ucfirst($val);
            }
            return $url_str;
        }
        return $url;
    }
}

/*if (!function_exists('')){

}

if (!function_exists('')){

}

if (!function_exists('')){

}

if (!function_exists('')){

}

if (!function_exists('')){

}

if (!function_exists('')){

}

if (!function_exists('')){

}*/

/*if (!function_exists('')){

}*/