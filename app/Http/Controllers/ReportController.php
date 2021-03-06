<?php
namespace App\Http\Controllers;

use App\Services\ExportService;
use App\Entities\ExportEntity;

class ReportController extends ParserController
{
    protected $repo;

    public function __construct(ExportService $repo)
    {
        $this->repo = $repo;
    }

    public function index($id = 1)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(10 * 60);

	$path = public_path()."/events/".$id .".json";
	if(!file_exists($path)) {
		return [
			"code" 		=> 1,
			"description" 	=>"File Not Found!"
		];
	}

        $event_ids = json_decode(file_get_contents($path), true);

        $header = [
            "Наименование",
            "Дата",
            "Место",
            "Проданный",
            "Пригласительный",
            "Сумма"
        ];

        $body = [];

	$url = "https://cabinet.cultureticket.uz/api/CultureTicket/Sessions/";

	foreach($event_ids as $id) {
		$res      = $this->getResponse($url . $id);
        $sessions = json_decode($res->getBody()->getContents(), true);
		foreach($sessions["result"] as $session) {
            $counter = $this->calc($session["sessionId"]);
			$body[] = [
				$session["eventName"],
				date("d.m.Y", strtotime($session["beginDate"])),
				$session["palaceName"],
				$counter["sold"],
				$counter["free"],
				$counter["sum"],
			];
		}
	}

	$export = new ExportEntity($header, $body);
        return $this->repo->exec($export);
    }

    protected function calc($sessionId)
    {
        $url = "https://cabinet.cultureticket.uz/api/CultureTicket/SessionTickets/";
        $url1 = "https://cabinet.cultureticket.uz/api/CultureTicket/Tarifs/";

        $res = $this->getResponse($url . $sessionId);
        $res1 = $this->getResponse($url1 . $sessionId);

        $tickets = json_decode($res->getBody()->getContents(), true);
        $tarifs = json_decode($res1->getBody()->getContents(), true);
//        dd($tarifs);

        $counter = [
            'sold' => 0,
            'free' => 0,
            'sum'  => 0,
        ];

        foreach($tickets["result"] as $ticket) {
            if($ticket["ticketStatusId"] == 3 || $ticket["ticketStatusId"] == 7) {
                if($ticket["tarifName"] == "Пригласительное место") {
                    $counter["free"]++;
                } else {
                    $counter["sold"]++;
                    foreach ($tarifs['result'] as $tarif) {
                        if ($ticket["tarifId"] == $tarif["id"]){
                            $counter["sum"] += $tarif["price"];
                        }
                    }
                }
            }
        }

        return $counter;
    }

}
