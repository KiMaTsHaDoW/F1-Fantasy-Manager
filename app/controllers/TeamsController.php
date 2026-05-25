<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/models/F1ApiModel.php';

class TeamsController extends Controller {
    private F1ApiModel $f1;

    public function __construct() {
        $this->f1 = new F1ApiModel();
    }

    public function index(): void {
        $teams     = $this->f1->getConstructors();
        $standings = $this->f1->getConstructorStandings();

        $pointsMap = [];
        foreach ($standings as $s) {
            $id = $s['Constructor']['constructorId'];
            $pointsMap[$id] = (int)$s['points'];
        }
        foreach ($teams as &$t) {
            $t['points'] = $pointsMap[$t['id']] ?? 0;
        }

        $this->view('teams/index', [
            'title'    => 'Equipos - F1 Fantasy',
            'teams'    => $teams,
            'standings'=> $standings,
        ]);
    }

    public function show(string $id): void {
        $team      = $this->f1->getConstructorById($id);
        $standings = $this->f1->getConstructorStandings();
        $position  = null;
        $points    = 0;

        foreach ($standings as $i => $s) {
            if ($s['Constructor']['constructorId'] === $id) {
                $position = $i + 1;
                $points   = $s['points'];
                break;
            }
        }

        $this->view('teams/show', [
            'title'    => ($team['name'] ?? $id) . ' - F1 Fantasy',
            'team'     => $team,
            'position' => $position,
            'points'   => $points,
            'drivers'  => $this->f1->getDriversByConstructor($id),
        ]);
    }
}
