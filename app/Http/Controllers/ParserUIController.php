<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ParserUIController extends Controller {

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

    public function auth1(Request $request) {
        $login = $request->login;
        $password = $request->password;

        $client = new Client();
        $url = 'https://cabinet.cultureticket.uz/api/CultureTicket/Token';


        try {
            $res = $client->request('POST', $url, [
                'json' => [
                    "login" => $login,
                    "password" => $password
                ]
            ]);
            $json = json_decode($res->getBody()->getContents(), true);

            $data = [
                'data' => $json
            ];

            if($res->getStatusCode() === 200) {
                return view('agent', $data);
            }
        } catch(\Exception $e) {
            if ($e->getCode() === 400) {
                $request->session()->flash('error', 'Не найден пользователь');
                return redirect()->route('index');
            }
            if ($e->getCode() !== 200) {
                $request->session()->flash('error', 'Internel server error');
                return redirect()->route('index');
            }
        }
    }

    public function checkSale(Request $request) {

        dd($request);
        $url ='https://cabinet.cultureticket.uz/api/CultureTicket/SessionTickets/' . $request->sessionId;
        $res = $this->getResponse($url);
        $seats = json_decode($res->getBody()->getContents(), true);

        $data = [];
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

        dd($all);
        return $all;
    }
}
