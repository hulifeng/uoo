<?php

declare(strict_types=1);

if (!function_exists('randomKey')) {
    /**
     * 随机生成指定长度字符串.
     *
     * @param int $len
     */
    function randomKey($len = 11)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~0123456789#$%^&';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $len; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }
}

function write_php_ini($array, $file)
{
    $res = [];
    foreach ($array as $key => $val) {
        if (is_array($val)) {
            $res[] = "[$key]";
            foreach ($val as $skey => $sval) {
                $res[] = "$skey = $sval";
            }
        } else {
            $res[] = "$key = $val";
        }
    }
    safefilerewrite($file, implode("\r\n", $res));
}

function safefilerewrite($fileName, $dataToSave)
{
    file_put_contents($fileName, $dataToSave);
}

function get_real_ip()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] AS $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    }
    return $ip;
}


// 保存用户表单
function save_customer($isQuestion = false, $mobile, $code, $houseID = '', $country = '', $budget = '', $purposes = '')
{
    $url = config('uoolu')['url']['form'];

    $client = new \GuzzleHttp\Client();

    $data = [
        'grant_type' => 'toutiao',
        'area_code' => 86,
        'mobile' => $mobile,
        'code' => $code,
    ];

    if ($isQuestion) {
        $data['house_id'] = $houseID;
        $data['country'] = $country;
        $data['budget'] = $budget;
        $data['purposes'] = $purposes;
    }

    $response = $client->get($url, [
        'query' => $data
    ]);

    $responseJson = json_decode($response->getBody()->getContents(), true);

    return $responseJson;
}
