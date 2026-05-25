<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/models/F1ApiModel.php';
require_once BASE_PATH . '/app/models/FantasyTeamModel.php';
require_once BASE_PATH . '/app/models/PriceModel.php';
require_once BASE_PATH . '/app/models/LeagueModel.php';

class FantasyController extends Controller {
    private F1ApiModel       $f1;
    private FantasyTeamModel $ftm;
    private PriceModel       $pm;
    private LeagueModel      $lm;

    public function __construct() {
        $this->f1  = new F1ApiModel();
        $this->ftm = new FantasyTeamModel();
        $this->pm  = new PriceModel();
        $this->lm  = new LeagueModel();
    }

    // Shows the team for a specific league (?league=X)
    public function index(): void {
        $this->requireLogin();
        $userId   = $_SESSION['user_id'];
        $leagueId = (int)($_GET['league'] ?? 0);

        if (!$leagueId) {
            $this->redirect('leagues');
            return;
        }

        $league = $this->lm->getById($leagueId);
        if (!$league || !$this->lm->isMember($leagueId, $userId)) {
            $this->redirect('leagues');
            return;
        }

        $team = $this->ftm->getTeamByUser($userId, $leagueId);
        if (!$team) {
            $this->redirect('fantasy/create?league=' . $leagueId);
            return;
        }

        $selections     = $this->ftm->getTeamSelections($team['id']);
        $allDrivers     = $this->f1->getDrivers();
        $allTeams       = $this->f1->getConstructors();
        $myDrivers      = array_values(array_filter($allDrivers, fn($d) => in_array($d['id'], $selections['drivers'])));
        $myConstructors = array_values(array_filter($allTeams,   fn($t) => in_array($t['id'], $selections['constructors'])));

        // Puntos de la última jornada (carrera + sprint si los hubo)
        $lastRace        = $this->f1->getLastRaceResults();
        $round           = (int)($lastRace['round']  ?? 0);
        $season          = $lastRace['season']        ?? date('Y');
        $lastRaceName    = $lastRace['raceName']      ?? '';

        // Don't show jornada if the race happened before this team was created
        $lastRaceDate = $lastRace['date'] ?? '';
        $teamCreated  = substr($team['created_at'] ?? '', 0, 10);
        if ($lastRaceDate && $teamCreated && $lastRaceDate < $teamCreated) {
            $lastRaceName = '';
        }
        $driverPtsRace   = [];
        $constrPtsRace   = [];
        $driverPtsSprint = [];
        $constrPtsSprint = [];

        foreach ($lastRace['Results'] ?? [] as $r) {
            $did = $r['Driver']['driverId']           ?? '';
            $cid = $r['Constructor']['constructorId'] ?? '';
            $pts = (float)($r['points']               ?? 0);
            if ($did) $driverPtsRace[$did]   = $pts;
            if ($cid) $constrPtsRace[$cid]   = ($constrPtsRace[$cid] ?? 0) + $pts;
        }

        if ($round) {
            $sprintData = $this->f1->getSprintResults($round, $season);
            foreach ($sprintData['SprintResults'] ?? [] as $r) {
                $did = $r['Driver']['driverId']           ?? '';
                $cid = $r['Constructor']['constructorId'] ?? '';
                $pts = (float)($r['points']               ?? 0);
                if ($did) $driverPtsSprint[$did]   = $pts;
                if ($cid) $constrPtsSprint[$cid]   = ($constrPtsSprint[$cid] ?? 0) + $pts;
            }
        }

        $selectionPoints = $this->ftm->getSelectionPoints($team['id']);

        $this->view('fantasy/index', [
            'apiError'       => $this->f1->hasApiError(),
            'title'          => 'Mi Equipo — ' . htmlspecialchars($league['name']),
            'team'           => $team,
            'league'         => $league,
            'myDrivers'      => $myDrivers,
            'myConstructors' => $myConstructors,
            'lastRaceName'   => $lastRaceName,
            'driverPtsRace'  => $driverPtsRace,
            'constrPtsRace'  => $constrPtsRace,
            'driverPtsSprint'=> $driverPtsSprint,
            'constrPtsSprint'=> $constrPtsSprint,
            'driverSelPts'   => $selectionPoints['driver'],
            'constrSelPts'   => $selectionPoints['constructor'],
            'flash'          => $this->getFlash(),
        ]);
    }

    public function create(): void {
        $this->requireLogin();
        $userId   = $_SESSION['user_id'];
        $leagueId = (int)($_GET['league'] ?? 0);

        if (!$leagueId) { $this->redirect('leagues'); return; }

        $league = $this->lm->getById($leagueId);
        if (!$league || !$this->lm->isMember($leagueId, $userId)) {
            $this->redirect('leagues');
            return;
        }

        if ($this->ftm->getTeamByUser($userId, $leagueId)) {
            $this->redirect('fantasy?league=' . $leagueId);
            return;
        }

        $this->view('fantasy/create', [
            'title'      => 'Crear Equipo — ' . htmlspecialchars($league['name']),
            'league'     => $league,
            'drivers'    => $this->f1->getDrivers(),
            'teams'      => $this->f1->getConstructors(),
            'budget'     => BUDGET,
            'maxDrivers' => MAX_DRIVERS,
            'maxTeams'   => MAX_TEAMS,
        ]);
    }

    public function store(): void {
        $this->requireLogin();
        $userId   = $_SESSION['user_id'];
        $leagueId = (int)($_POST['league_id'] ?? 0);

        if (!$leagueId) { $this->redirect('leagues'); return; }

        $league = $this->lm->getById($leagueId);
        if (!$league || !$this->lm->isMember($leagueId, $userId)) {
            $this->redirect('leagues');
            return;
        }

        if ($this->ftm->getTeamByUser($userId, $leagueId)) {
            $this->redirect('fantasy?league=' . $leagueId);
            return;
        }

        $teamName     = trim($_POST['team_name'] ?? '');
        $driverIds    = $_POST['drivers'] ?? [];
        $constructors = $_POST['constructors'] ?? [];

        if (empty($teamName)) {
            $this->setFlash('error', 'El nombre del equipo es obligatorio.');
            $this->redirect('fantasy/create?league=' . $leagueId);
            return;
        }
        if (count($driverIds) !== MAX_DRIVERS) {
            $this->setFlash('error', 'Debes seleccionar exactamente ' . MAX_DRIVERS . ' pilotos.');
            $this->redirect('fantasy/create?league=' . $leagueId);
            return;
        }
        if (count($constructors) !== MAX_TEAMS) {
            $this->setFlash('error', 'Debes seleccionar exactamente ' . MAX_TEAMS . ' equipos.');
            $this->redirect('fantasy/create?league=' . $leagueId);
            return;
        }

        $driverMap = array_column($this->f1->getDrivers(),       null, 'id');
        $teamMap   = array_column($this->f1->getConstructors(),  null, 'id');

        $total = 0.0;
        foreach ($driverIds    as $did) $total += $driverMap[$did]['price'] ?? 0;
        foreach ($constructors as $cid) $total += $teamMap[$cid]['price']   ?? 0;

        if ($total > BUDGET) {
            $this->setFlash('error', "Presupuesto excedido: {$total}M€ / Límite: " . BUDGET . 'M€');
            $this->redirect('fantasy/create?league=' . $leagueId);
            return;
        }

        $teamId = $this->ftm->createTeam($userId, $leagueId, $teamName);
        if (!$teamId) {
            $this->setFlash('error', 'Error al crear el equipo.');
            $this->redirect('fantasy/create?league=' . $leagueId);
            return;
        }

        foreach ($driverIds    as $did) $this->ftm->addSelection($teamId, 'driver',      $did, $driverMap[$did]['price'] ?? 0);
        foreach ($constructors as $cid) $this->ftm->addSelection($teamId, 'constructor', $cid, $teamMap[$cid]['price']   ?? 0);
        $this->ftm->updateTeam($teamId, $total);

        foreach ($driverIds    as $did) $this->pm->recordTransaction($did, 'driver',      'buy', $userId);
        foreach ($constructors as $cid) $this->pm->recordTransaction($cid, 'constructor', 'buy', $userId);

        $this->setFlash('success', '¡Equipo "' . htmlspecialchars($teamName) . '" creado!');
        $this->redirect('leagues/show/' . $leagueId);
    }

    public function ranking(): void {
        $ranking = $this->ftm->getRanking();
        $this->view('fantasy/ranking', [
            'title'   => 'Clasificación Global',
            'ranking' => $ranking,
        ]);
    }

    public function myTeam(): void {
        $leagueId = (int)($_GET['league'] ?? 0);
        $this->redirect($leagueId ? 'fantasy?league=' . $leagueId : 'leagues');
    }

    public function edit(): void {
        $this->requireLogin();
        $userId   = $_SESSION['user_id'];
        $leagueId = (int)($_GET['league'] ?? 0);

        if (!$leagueId) { $this->redirect('leagues'); return; }

        $league = $this->lm->getById($leagueId);
        if (!$league || !$this->lm->isMember($leagueId, $userId)) {
            $this->redirect('leagues');
            return;
        }

        $team = $this->ftm->getTeamByUser($userId, $leagueId);
        if (!$team) { $this->redirect('fantasy/create?league=' . $leagueId); return; }

        $selections = $this->ftm->getTeamSelections($team['id']);

        $this->view('fantasy/edit', [
            'title'      => 'Editar Equipo — ' . htmlspecialchars($league['name']),
            'league'     => $league,
            'team'       => $team,
            'drivers'    => $this->f1->getDrivers(),
            'teams'      => $this->f1->getConstructors(),
            'budget'     => BUDGET,
            'maxDrivers' => MAX_DRIVERS,
            'maxTeams'   => MAX_TEAMS,
            'selDrivers' => $selections['drivers'],
            'selTeams'   => $selections['constructors'],
        ]);
    }

    public function update(): void {
        $this->requireLogin();
        $userId   = $_SESSION['user_id'];
        $leagueId = (int)($_POST['league_id'] ?? 0);

        if (!$leagueId) { $this->redirect('leagues'); return; }

        $team = $this->ftm->getTeamByUser($userId, $leagueId);
        if (!$team) { $this->redirect('fantasy/create?league=' . $leagueId); return; }

        $driverIds    = $_POST['drivers'] ?? [];
        $constructors = $_POST['constructors'] ?? [];

        if (count($driverIds) !== MAX_DRIVERS || count($constructors) !== MAX_TEAMS) {
            $this->setFlash('error', 'Selección incorrecta.');
            $this->redirect('fantasy?league=' . $leagueId);
            return;
        }

        $driverMap = array_column($this->f1->getDrivers(),      null, 'id');
        $teamMap   = array_column($this->f1->getConstructors(), null, 'id');

        $total = 0.0;
        foreach ($driverIds    as $did) $total += $driverMap[$did]['price'] ?? 0;
        foreach ($constructors as $cid) $total += $teamMap[$cid]['price']   ?? 0;

        if ($total > BUDGET) {
            $this->setFlash('error', "Presupuesto excedido: {$total}M€ / Límite: " . BUDGET . 'M€');
            $this->redirect('fantasy?league=' . $leagueId);
            return;
        }

        $oldSelections = $this->ftm->getTeamSelections($team['id']);
        $this->ftm->clearSelections($team['id']);
        foreach ($driverIds    as $did) $this->ftm->addSelection($team['id'], 'driver',      $did, $driverMap[$did]['price'] ?? 0);
        foreach ($constructors as $cid) $this->ftm->addSelection($team['id'], 'constructor', $cid, $teamMap[$cid]['price']   ?? 0);
        $this->ftm->updateTeam($team['id'], $total);

        foreach ($driverIds as $did) {
            if (!in_array($did, $oldSelections['drivers']))    $this->pm->recordTransaction($did, 'driver', 'buy',  $userId);
        }
        foreach ($oldSelections['drivers'] as $did) {
            if (!in_array($did, $driverIds))                   $this->pm->recordTransaction($did, 'driver', 'sell', $userId);
        }
        foreach ($constructors as $cid) {
            if (!in_array($cid, $oldSelections['constructors'])) $this->pm->recordTransaction($cid, 'constructor', 'buy',  $userId);
        }
        foreach ($oldSelections['constructors'] as $cid) {
            if (!in_array($cid, $constructors))                  $this->pm->recordTransaction($cid, 'constructor', 'sell', $userId);
        }

        $this->setFlash('success', 'Equipo actualizado correctamente.');
        $this->redirect('leagues/show/' . $leagueId);
    }
}
