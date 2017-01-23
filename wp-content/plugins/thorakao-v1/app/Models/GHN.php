<?php
/**
 * Created by PhpStorm.
 * User: vietanh
 * Date: 01-Aug-16
 * Time: 11:37 PM
 */

namespace TVA\Models;

class GHN
{
    protected $_wpdb;
    protected $_tbl_banner_info;
    protected $api_uri;
    protected $lastResponseInfo;
    protected $httpHeaders;
    protected $fileUpload;


    private static $instance;


    protected function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->api_uri = 'https://testapipds.ghn.vn:9999/external/b2c/';
        $this->api_key = 'dSLjeJstcjwcbcLe';
        $this->api_secret = 'F7BB3C08E9BCA7E8D880B3D9D57FB4DF';
        $this->client_id = '122646';
        $this->password = 'UW2P3R8Np6XyC9wSN';

        $this->lastResponseInfo = '';
        $this->httpHeaders = ["User-Agent: VietAnh"];

        $this->fileUpload = false;

        $this->authenticate = [
            'ClientID'     => $this->client_id,
            'Password'     => $this->password,
            'ApiKey'       => $this->api_key,
            'ApiSecretKey' => $this->api_secret
        ];
    }


    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new GHN();
        }

        return self::$instance;
    }


    public function httpRequestCurl($action, array $data = [], $method = 'POST', $options = [])
    {
        $data = array_merge($data, $this->authenticate);
        // var_dump($data);
        $url = $this->api_uri . "/" . $action;
        $ch = curl_init();
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($this->fileUpload === true) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $this->fileUpload = false;
            }
            else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
            }
        }
        else {
            $url .= '?' . http_build_query($data, '', '&');
            if ($method != 'GET') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        // curl_setopt($ch, CURLOPT_TIMEOUT_MS, $options['timeout']);
        // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $options['connect_timeout']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeaders);
        $response = curl_exec($ch);
        $this->lastResponseInfo = curl_getinfo($ch);
        curl_close($ch);


        // parse headers and body
        $parts = explode("\r\n\r\nHTTP/", $response);
        $parts = (count($parts) > 1 ? 'HTTP/' : '') . array_pop($parts); // deal with HTTP/1.1 100 Continue before other headers
        list($headers, $body) = explode("\r\n\r\n", $parts, 2);

        $body = json_decode($body);

        return $body;
    }


    public function getDistrictProvinceData()
    {
        $rs = $this->httpRequestCurl('GetDistrictProvinceData');

        // global $wpdb;
        // foreach ($rs->Data as $k => $v) {
        //     $wpdb->insert('trk_ghn_location', (array)$v);
        // }

        return $rs;
    }


    public function getServiceList($to_district_code)
    {
        $rs = $this->httpRequestCurl('GetServiceList', [
            "FromDistrictCode" => PICKHUB_DISTRICT,
            "ToDistrictCode"   => $to_district_code
        ]);

        return $rs->Services;
    }


    // Weight (gram)
    // Length (cm)
    // Width (cm)
    // Height (cm)
    public function getServiceInfos($args = [])
    {
        $rs = $this->httpRequestCurl('ServiceInfos', [
            "FromDistrictCode" => PICKHUB_DISTRICT,
            "ToDistrictCode"   => $args['ToDistrictCode'],
            "Weight"           => $args['Weight'],
            "Length"           => $args['Length'],
            "Width"            => $args['Width'],
            "Height"           => $args['Height'],
        ]);

        return $rs;
    }


    public function calculateFee($weight, $to_district_code, $service_id)
    {
        $data = [
            'Items' => [
                [
                    "Weight"           => $weight,
                    "FromDistrictCode" => PICKHUB_DISTRICT,
                    "ToDistrictCode"   => $to_district_code,
                    "ServiceID"        => $service_id
                ]
            ]
        ];
        $rs = $this->httpRequestCurl('CalculateServiceFee', $data);

        return $rs->Items[0]->ServiceFee;
    }


    public function getPickHubs()
    {
        $rs = $this->httpRequestCurl('GetPickHubs');

        return $rs;
    }


    public function createShippingOrder($args = [])
    {
        $default = [
            'PickHubID'        => PICKHUB_ID,
            'FromDistrictCode' => '0210',
            // 'ToDistrictCode'   => '0201',
            'Weight'           => 500,
            'Height'           => 10,
            'Width'            => 10,
            'Length'           => 10,
            'ServiceID'        => 53319,
            // "RecipientName"        => "Nguyễn Dương Hoàng Vũ",
            // "RecipientPhone"       => "0908626483",
            // "DeliveryAddress"      => "214 Bắc Hải",
            // "DeliveryDistrictCode" => "0201"
        ];
        $shipping_args = array_merge($default, $args);

        $rs = $this->httpRequestCurl('CreateShippingOrder', (array)$shipping_args);

        return $rs;
    }


    public function getOrderInfo()
    {
        $rs = $this->httpRequestCurl('GetOrderInfo', [
            'OrderCode' => '1KB6UOFU'
        ]);

        return $rs;
    }


    public function getShippingFee($to_district_code, $service_id)
    {
        $rs = $this->httpRequestCurl('CalculateServiceFee', [
            'Weight'           => 200,
            'Length'           => 10,
            'Width'            => 10,
            'Height'           => 10,
            'FromDistrictCode' => PICKHUB_DISTRICT,
            'ToDistrictCode'   => $to_district_code,
            'ServiceID'        => $service_id
        ]);

        return $rs;
    }

}