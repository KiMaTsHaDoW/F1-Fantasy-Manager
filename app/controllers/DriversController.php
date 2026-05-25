<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/models/F1ApiModel.php';

class DriversController extends Controller {
    private F1ApiModel $f1;

    public function __construct() {
        $this->f1 = new F1ApiModel();
    }

    public function index(): void {
        $drivers   = $this->f1->getDrivers();
        $standings = $this->f1->getDriverStandings();

        // Fusionar puntos reales con la lista de pilotos
        $pointsMap = [];
        foreach ($standings as $s) {
            $id = $s['Driver']['driverId'];
            $pointsMap[$id] = (int)$s['points'];
        }
        foreach ($drivers as &$d) {
            $d['points'] = $pointsMap[$d['id']] ?? 0;
        }

        $this->view('drivers/index', [
            'title'    => 'Pilotos - F1 Fantasy',
            'drivers'  => $drivers,
            'standings'=> $standings,
        ]);
    }

    public function show(string $id): void {
        $driver   = $this->f1->getDriverById($id);
        $standings = $this->f1->getDriverStandings();
        $position = null;
        $points   = 0;

        foreach ($standings as $i => $s) {
            if ($s['Driver']['driverId'] === $id) {
                $position = $i + 1;
                $points   = $s['points'];
                break;
            }
        }

        $this->view('drivers/show', [
            'title'    => ($driver['givenName'] ?? '') . ' ' . ($driver['familyName'] ?? '') . ' - F1 Fantasy',
            'driver'   => $driver,
            'position' => $position,
            'points'   => $points,
        ]);
    }
}
