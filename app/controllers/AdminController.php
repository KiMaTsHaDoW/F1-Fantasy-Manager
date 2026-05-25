<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/models/F1ApiModel.php';
require_once BASE_PATH . '/app/models/PriceModel.php';
require_once BASE_PATH . '/app/models/FantasyTeamModel.php';

class AdminController extends Controller {
    private F1ApiModel       $f1;
    private PriceModel       $pm;
    private FantasyTeamModel $ftm;

    public function __construct() {
        $this->f1  = new F1ApiModel();
        $this->pm  = new PriceModel();
        $this->ftm = new FantasyTeamModel();
    }

    public function recalculate(): void {
        $this->requireAdmin();

        $lastRace  = $this->f1->getLastRaceResults();
        $round     = (int)($lastRace['round'] ?? 0);
        $season    = $lastRace['season'] ?? date('Y');
        $raceName  = $lastRace['raceName'] ?? '';
        $alreadyDone = $round && $this->ftm->isRoundScored($season, $round, 'recalculate');

        $this->view('admin/recalculate', [
            'title'       => 'Recalcular Precios',
            'result'      => null,
            'round'       => $round,
            'season'      => $season,
            'raceName'    => $raceName,
            'alreadyDone' => $alreadyDone,
            'error'       => null,
        ]);
    }

    public function doRecalculate(): void {
        $this->requireAdmin();

        $lastRace = $this->f1->getLastRaceResults();
        $round    = (int)($lastRace['round'] ?? 0);
        $season   = $lastRace['season'] ?? date('Y');
        $raceName = $lastRace['raceName'] ?? 'Desconocida';

        if (!$round || empty($lastRace['Results'])) {
            $this->view('admin/recalculate', [
                'title' => 'Recalcular Precios', 'result' => null,
                'round' => null, 'season' => null, 'raceName' => null,
                'alreadyDone' => false,
                'error' => 'No hay resultados de carrera disponibles en la API.',
            ]);
            return;
        }

        if ($this->ftm->isRoundScored($season, $round, 'recalculate')) {
            $this->view('admin/recalculate', [
                'title' => 'Recalcular Precios', 'result' => null,
                'round' => $round, 'season' => $season, 'raceName' => $raceName,
                'alreadyDone' => true,
                'error' => "Los precios de la ronda $round ya fueron recalculados.",
            ]);
            return;
        }

        $driverPoints      = [];
        $constructorPoints = [];
        foreach ($lastRace['Results'] as $r) {
            $driverId = $r['Driver']['driverId']           ?? '';
            $constId  = $r['Constructor']['constructorId'] ?? '';
            $pts      = (float)($r['points'] ?? 0);
            if ($driverId) $driverPoints[$driverId] = $pts;
            if ($constId)  $constructorPoints[$constId] = ($constructorPoints[$constId] ?? 0) + $pts;
        }

        $result = $this->pm->recalculate($driverPoints, $constructorPoints, $round);
        $this->ftm->markRoundScored($season, $round, 'recalculate');

        $this->view('admin/recalculate', [
            'title'       => 'Recalcular Precios',
            'result'      => $result,
            'round'       => $round,
            'season'      => $season,
            'raceName'    => $raceName,
            'alreadyDone' => true,
            'error'       => null,
        ]);
    }

}
