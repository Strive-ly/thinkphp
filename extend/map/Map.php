<?php

/**
 * @author xialei <xialeistudio@gmail.com>
 */
class Map
{
    private static $_instance;

    const REQ_GET = 1;
    const REQ_POST = 2;

    const x_PI  = 52.35987755982988;
    const PI  = 3.1415926535897932384626;
    const a = 6378245.0;
    const ee = 0.00669342162296594323;

    /**
     * 单例模式
     * @return map
     */
    public static function instance()
    {
        if (!self::$_instance instanceof self)
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * 执行CURL请求
     * @author: xialei<xialeistudio@gmail.com>
     * @param $url
     * @param array $params
     * @param bool $encode
     * @param int $method
     * @return mixed
     */
    private function async($url, $params = array(), $encode = true, $method = self::REQ_GET)
    {
        $ch = curl_init();
        if ($method == self::REQ_GET)
        {
            $url = $url . '?' . http_build_query($params);
            $url = $encode ? $url : urldecode($url);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        else
        {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_REFERER, '百度地图referer');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; en-us) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp;
    }

    /**
     * ip定位
     * @param string $ip
     * @return array
     * @throws Exception
     */
    public function locationByIP($ip)
    {
        //检查是否合法IP
        if (!filter_var($ip, FILTER_VALIDATE_IP))
        {
            throw new Exception('ip地址不合法');
        }
        $params = array(
            'ak' => 'LxblDomfbe3E8U2AWCBKfF2ixwCltKra',
            'ip' => $ip,
            'coor' => 'bd09ll'//百度地图GPS坐标
        );
        $api = 'http://api.map.baidu.com/location/ip';
        $resp = $this->async($api, $params);
        $data = json_decode($resp, true);
        //有错误
        if ($data['status'] != 0)
        {
            throw new Exception($data['message']);
        }
        //返回地址信息
        return array(
            'address' => $data['content']['address'],
            'province' => $data['content']['address_detail']['province'],
            'city' => $data['content']['address_detail']['city'],
            'district' => $data['content']['address_detail']['district'],
            'street' => $data['content']['address_detail']['street'],
            'street_number' => $data['content']['address_detail']['street_number'],
            'city_code' => $data['content']['address_detail']['city_code'],
            'lng' => $data['content']['point']['x'],
            'lat' => $data['content']['point']['y']
        );
    }


    /**
     * GPS定位
     * @param $lng
     * @param $lat
     * @return array
     * @throws Exception
     */
    public function locationByGPS($lng, $lat)
    {
        $params = array(
            'coordtype' => 'wgs84ll',
            'location' => $lat . ',' . $lng,
            'ak' => 'LxblDomfbe3E8U2AWCBKfF2ixwCltKra',
            'output' => 'json',
            'pois' => 0
        );
        $resp = $this->async('http://api.map.baidu.com/geocoder/v2/', $params, false);
        $data = json_decode($resp, true);
        if ($data['status'] != 0)
        {
            throw new Exception($data['message']);
        }
        return array(
            'address' => $data['result']['formatted_address'],
            'province' => $data['result']['addressComponent']['province'],
            'city' => $data['result']['addressComponent']['city'],
            'street' => $data['result']['addressComponent']['street'],
            'street_number' => $data['result']['addressComponent']['street_number'],
            'city_code'=>$data['result']['cityCode'],
            'lng'=>$data['result']['location']['lng'],
            'lat'=>$data['result']['location']['lat']
        );
    }

    /**
     * WGS84转GCj02(北斗转高德)
     * @param $lng
     * @param $lat
     * @return array
     */
    public function wgs84togcj02($lng,  $lat) {
        if ($this->out_of_china($lng, $lat)) {
            return array($lng, $lat);
        } else {
            $dlat = $this->transformlat($lng - 105.0, $lat - 35.0);
            $dlng = $this->transformlng($lng - 105.0, $lat - 35.0);
            $radlat = $lat / 180.0 * self::PI;
            $magic = sin($radlat);
            $magic = 1 - self::ee * $magic * $magic;
            $sqrtmagic = sqrt($magic);
            $dlat = ($dlat * 180.0) / ((self::a * (1 - self::ee)) / ($magic * $sqrtmagic) * self::PI);
            $dlng = ($dlng * 180.0) / (self::a / $sqrtmagic * cos($radlat) * self::PI);
            $mglat = $lat + $dlat;
            $mglng = $lng + $dlng;
            return array(
                'lng'=>$mglng,
                'lat'=>$mglat
            );
        }
    }
    /**
     * GCJ02 转换为 WGS84 (高德转北斗)
     * @param lng
     * @param lat
     * @return array(lng, lat);
     */
    public function gcj02towgs84($lng, $lat) {
        if ($this->out_of_china($lng, $lat)) {
            return array($lng, $lat);
        } else {
            $dlat = $this->transformlat($lng - 105.0, $lat - 35.0);
            $dlng = $this->transformlng($lng - 105.0, $lat - 35.0);
            $radlat = $lat / 180.0 * self::PI;
            $magic = sin($radlat);
            $magic = 1 - self::ee * $magic * $magic;
            $sqrtmagic = sqrt($magic);
            $dlat = ($dlat * 180.0) / ((self::a * (1 - self::ee)) / ($magic * $sqrtmagic) * self::PI);
            $dlng = ($dlng * 180.0) / (self::a / $sqrtmagic * cos($radlat) * self::PI);
            $mglat = $lat + $dlat;
            $mglng = $lng + $dlng;
            return array(
                'lng'=>$lng * 2 - $mglng,
                'lat'=>$lat * 2 - $mglat
            );
        }
    }

    /**
　　* 百度坐标系 (BD-09) 与 火星坐标系 (GCJ-02)的转换
　　* 即 百度 转 谷歌、高德
　　* @param bd_lon
　　* @param bd_lat
　　* @returns
　　*/
    public function bd09togcj02 ($bd_lon, $bd_lat) {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $bd_lon - 0.0065;
        $y = $bd_lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $gg_lng = $z * cos(theta);
        $gg_lat = $z * sin(theta);
        return array(
            'lng'=>$gg_lng,
            'lat'=>$gg_lat
        );
    }

    /**
     * GCJ-02 转换为 BD-09  （火星坐标系 转百度即谷歌、高德 转 百度）
     * @param $lng
     * @param $lat
     * @return array
     */
    public function gcj02tobd09($lng, $lat) {
        $z = sqrt($lng * $lng + $lat * $lat) + 0.00002 * Math.sin($lat * x_PI);
        $theta = Math.atan2($lat, $lng) + 0.000003 * Math.cos($lng * x_PI);
        $bd_lng = $z * cos($theta) + 0.0065;
        $bd_lat = z * sin($theta) + 0.006;
        return array(
            'lng'=>$bd_lng,
            'lat'=>$bd_lat
        );
    }


    private function transformlat($lng, $lat) {
        $ret = -100.0 + 2.0 * $lng + 3.0 * $lat + 0.2 * $lat * $lat + 0.1 * $lng * $lat + 0.2 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * self::PI) + 20.0 * sin(2.0 * $lng * self::PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lat * self::PI) + 40.0 * sin($lat / 3.0 * self::PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($lat / 12.0 * self::PI) + 320 * sin($lat * self::PI / 30.0)) * 2.0 / 3.0;
        return $ret;
    }
    private function transformlng($lng, $lat) {
        $ret = 300.0 + $lng + 2.0 * $lat + 0.1 * $lng * $lng + 0.1 * $lng * $lat + 0.1 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * self::PI) + 20.0 * sin(2.0 * $lng * self::PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lng * self::PI) + 40.0 * sin($lng / 3.0 * self::PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($lng / 12.0 * self::PI) + 300.0 * sin($lng / 30.0 * self::PI)) * 2.0 / 3.0;
        return $ret;
    }


    private function rad($param)
    {
        return  $param * self::PI / 180.0;
    }
    /**
     * 判断是否在国内，不在国内则不做偏移
     * @param $lng
     * @param $lat
     * @return bool
     */
    private function out_of_china($lng, $lat) {
        return ($lng < 72.004 || $lng > 137.8347) || (($lat < 0.8293 || $lat > 55.8271) || false);
    }

}