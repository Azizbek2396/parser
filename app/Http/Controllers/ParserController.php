<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ParserController extends Controller
{
    public function  index()
    {
        $client = new Client();

        $res = $client->request('GET', 'http://ip-api.com/json');

//        $json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
//        dd(json_decode($json));
//        dd(json_decode($json, true)['a']);

        return [
            'code' => $res->getStatusCode(),
            'body' => json_decode($res->getBody()->getContents(), true),
        ];
    }

    public function auth()
    {
        $client = new Client();
        $url = 'http://185.211.129.21:6003/api/CultureTicket/Token';
        $res = $client->request('POST', $url, [
                'json' => [
                        "login" => 'aAgent',
                        "password" => '123456'
                ]
        ]);

        $json = json_decode($res->getBody()->getContents(), true);
        $path = dirname(__DIR__, 3) . '/public/data/auth';

        if (isset($json['result']['accessToken'])){
            file_put_contents($path, $json['result']['accessToken']);
        }
        dd($json);
    }

    public function getToken()
    {
        $token = file_get_contents(dirname(__DIR__, 3) . '/public/data/auth');
//        dd($token);
        return $token;
    }

    public function seats($hallId = "319")
    {
        $url ='http://185.211.129.21:6003/api/CultureTicket/PalaceHallSeats/' . $hallId;
        $client = new Client();
        $res = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => "Bearer " . $this->getToken(),
                ]
        ]);

        $seats = json_decode($res->getBody()->getContents(), true);

        return $seats['result'];
    }

    public function countedSeats($hallId = "319")
    {
        $url ='http://185.211.129.21:6003/api/CultureTicket/PalaceHallSeats/' . $hallId;
        $client = new Client();
        $res = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => "Bearer " . $this->getToken(),
                ]
        ]);

        $seats = json_decode($res->getBody()->getContents(), true);

        $total = count($seats['result']);
        $count = 0;
        $arr = [];
        $rowNumber = $seats['result'][0]['rowNumber'];
        $sectorName = $seats['result'][0]['sectorName'];

        foreach ($seats['result'] as $seat)
        {
            if ($rowNumber != $seat['rowNumber'] || $sectorName != $seat['sectorName']) {
                array_push($arr, ['sectorName' => $sectorName, 'rowNumber' => $rowNumber, 'countSeats' => $count]);
                $count = 0;
                $rowNumber = $seat['rowNumber'];
                $sectorName = $seat['sectorName'];
            }
            $count++;
        }
        array_push($arr, ['sectorName' => $sectorName, 'rowNumber' => $rowNumber, 'countSeats' => $count]);
        array_push($arr, ['total' => $total]);
        return $arr;
    }
}
