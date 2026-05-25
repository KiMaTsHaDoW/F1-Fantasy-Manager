<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/models/F1ApiModel.php';

class HomeController extends Controller {
    private F1ApiModel $f1;

    public function __construct() {
        $this->f1 = new F1ApiModel();
    }

    public function index(): void {
        $standings = $this->f1->getDriverStandings();
        $constructorStandings = $this->f1->getConstructorStandings();
        $nextRace = $this->f1->getNextRace();
        $lastRace = $this->f1->getLastRaceResults();

        $this->view('home/index', [
            'title'                => 'Inicio - F1 Fantasy',
            'standings'            => array_slice($standings, 0, 5),
            'constructorStandings' => array_slice($constructorStandings, 0, 5),
            'nextRace'             => $nextRace,
            'lastRace'             => $lastRace,
            'apiError'             => $this->f1->hasApiError(),
            'flash'                => $this->getFlash(),
        ]);
    }
}
