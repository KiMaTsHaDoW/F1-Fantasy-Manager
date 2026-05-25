<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/models/F1ApiModel.php';

class RacesController extends Controller {
    private F1ApiModel $f1;

    public function __construct() {
        $this->f1 = new F1ApiModel();
    }

    public function index(): void {
        $races    = $this->f1->getRaces();
        $today    = date('Y-m-d');

        $this->view('races/index', [
            'title'  => 'Calendario - F1 Fantasy',
            'races'  => $races,
            'today'  => $today,
        ]);
    }

    public function show(string $round): void {
        $type  = ($_GET['type'] ?? 'race') === 'sprint' ? 'sprint' : 'race';
        $races = $this->f1->getRaces();
        $race  = null;
        foreach ($races as $r) {
            if ($r['round'] === $round) { $race = $r; break; }
        }

        if ($type === 'sprint') {
            $sprintData = $this->f1->getSprintResults((int)$round);
            $results    = $sprintData['SprintResults'] ?? [];
        } else {
            $results = $this->f1->getRaceResults($round);
        }

        $this->view('races/show', [
            'title'   => ($race['raceName'] ?? "Carrera $round") . ($type === 'sprint' ? ' — Sprint' : '') . ' - F1 Fantasy',
            'race'    => $race,
            'results' => $results,
            'round'   => $round,
            'type'    => $type,
        ]);
    }
}
