<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Object_;

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
        $url = 'https://cabinet.cultureticket.uz/api/CultureTicket/Token';

        $res = $client->request('POST', $url, [
                'json' => [
                        "login" => 'umar@iticket.uz',
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

    public function getResponse($url) {
        $client = new Client();
        $res = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => "Bearer " . $this->getToken(),
            ]
        ]);

        return $res;
    }

    public function seats($hallId = "319")
    {
        $url ='https://cabinet.cultureticket.uz/api/CultureTicket/PalaceHallSeats/' . $hallId;

        $res = $this->getResponse($url);

        $seats = json_decode($res->getBody()->getContents(), true);

        return $seats['result'];
    }

    public function countedSeats($hallId = "319")
    {
        $url ='https://cabinet.cultureticket.uz/api/CultureTicket/PalaceHallSeats/' . $hallId;

        $res = $this->getResponse($url);

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

    public function checkBuy($sessionId = "1554") {
        $url ='https://cabinet.cultureticket.uz/api/CultureTicket/SessionTickets/' . $sessionId;
        $res = $this->getResponse($url);
        $seats = json_decode($res->getBody()->getContents(), true);

        $data = [];
        $tickets = [];
        $TotalSeatsCount = 0;

        foreach ($seats['result'] as $seat) {
            array_push($data, $seat);
            $TotalSeatsCount++;
        }

        $object = new \stdClass();
        $src = new \stdClass();
        $object->data = $data;
        $object->Total = $TotalSeatsCount;
        $src->src = $object;

        $statusCount = 0;
        $statusNames = [];
        $ticketStatusName = $seats['result'][0]['ticketStatusName'];
        array_push($statusNames, $ticketStatusName);

        foreach ($seats['result'] as $seat) {
            if ($seat['ticketStatusName'] !== $ticketStatusName && !in_array($seat['ticketStatusName'], $statusNames)) {
                array_push($statusNames, $seat['ticketStatusName']);
                $ticketStatusName = $seat['ticketStatusName'];
            }
        }

        $statusData = new \stdClass();
        $sortedSeats = [];
        foreach ($statusNames as $statusName) {
            foreach ($seats['result'] as $seat) {
                if($seat['ticketStatusName'] === $statusName) {
                    array_push($sortedSeats, $seat);
                    $statusCount++;
                }
            }
            $statusData->$statusName = new \stdClass();
            $statusData->$statusName->tickets = $sortedSeats;
            $statusData->$statusName->totalCount = $statusCount;
            $sortedSeats = [];
            $statusCount = 0;
        }


        $all = new \stdClass();
        $all->src = $src;
        $all->calculate = $statusData;

        return $all;
    }
}
