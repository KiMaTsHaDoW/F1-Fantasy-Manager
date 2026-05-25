<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/models/LeagueModel.php';
require_once BASE_PATH . '/app/models/FantasyTeamModel.php';

class LeagueController extends Controller {
    private LeagueModel      $model;
    private FantasyTeamModel $ftm;

    public function __construct() {
        $this->model = new LeagueModel();
        $this->ftm   = new FantasyTeamModel();
    }

    public function index(): void {
        $flash      = $this->getFlash();
        $public     = $this->model->getPublicLeagues();
        $myLeagues  = $this->isLogged() ? $this->model->getMyLeagues($_SESSION['user_id']) : [];
        $myIds      = array_column($myLeagues, 'id');

        $this->view('leagues/index', [
            'title'     => 'Ligas - F1 Fantasy',
            'flash'     => $flash,
            'public'    => $public,
            'myLeagues' => $myLeagues,
            'myIds'     => $myIds,
        ]);
    }

    public function create(): void {
        $this->requireLogin();
        $this->view('leagues/create', [
            'title' => 'Crear Liga - F1 Fantasy',
            'flash' => $this->getFlash(),
        ]);
    }

    public function store(): void {
        $this->requireLogin();

        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isPublic    = isset($_POST['is_public']);

        if (empty($name)) {
            $this->setFlash('error', 'El nombre de la liga es obligatorio.');
            $this->redirect('leagues/create');
            return;
        }

        $id = $this->model->create($_SESSION['user_id'], $name, $description, $isPublic);
        if ($id) {
            $this->setFlash('success', '¡Liga creada! Ahora crea tu equipo para participar.');
            $this->redirect('fantasy/create?league=' . $id);
        } else {
            $this->setFlash('error', 'Error al crear la liga. Inténtalo de nuevo.');
            $this->redirect('leagues/create');
        }
    }

    public function show(string $id): void {
        $league = $this->model->getById((int)$id);
        if (!$league) {
            http_response_code(404);
            $this->view('layouts/404', ['title' => 'Liga no encontrada']);
            return;
        }

        $userId    = $_SESSION['user_id'] ?? null;
        $isMember  = $userId && $this->model->isMember((int)$id, $userId);
        $isCreator = $userId && $league['creator_id'] == $userId;
        $userTeam  = ($userId && $isMember) ? $this->ftm->getTeamByUser($userId, (int)$id) : null;
        $ranking   = $this->model->getRanking((int)$id);

        $this->view('leagues/show', [
            'title'    => $league['name'] . ' - F1 Fantasy',
            'flash'    => $this->getFlash(),
            'league'   => $league,
            'ranking'  => $ranking,
            'isMember' => $isMember,
            'isCreator'=> $isCreator,
            'userTeam' => $userTeam,
        ]);
    }

    public function join(string $id): void {
        $this->requireLogin();

        $league = $this->model->getById((int)$id);
        if (!$league || !$league['is_public']) {
            $this->setFlash('error', 'Liga no encontrada o no es pública.');
            $this->redirect('leagues');
            return;
        }

        if ($this->model->isMember((int)$id, $_SESSION['user_id'])) {
            $this->setFlash('error', 'Ya eres miembro de esta liga.');
            $this->redirect('leagues/show/' . $id);
            return;
        }

        $this->model->join((int)$id, $_SESSION['user_id']);
        $this->setFlash('success', '¡Te has unido a la liga ' . htmlspecialchars($league['name']) . '! Crea tu equipo para participar.');
        $this->redirect('fantasy/create?league=' . $id);
    }

    public function leave(string $id): void {
        $this->requireLogin();

        $league = $this->model->getById((int)$id);
        if (!$league) {
            $this->redirect('leagues');
            return;
        }

        if ($league['creator_id'] == $_SESSION['user_id']) {
            $this->setFlash('error', 'El creador no puede abandonar su propia liga.');
            $this->redirect('leagues/show/' . $id);
            return;
        }

        $this->model->leave((int)$id, $_SESSION['user_id']);
        $this->setFlash('success', 'Has abandonado la liga.');
        $this->redirect('leagues');
    }

    public function kick(): void {
        $this->requireLogin();

        $leagueId = (int)($_POST['league_id'] ?? 0);
        $userId   = (int)($_POST['user_id']   ?? 0);

        $league = $this->model->getById($leagueId);
        if (!$league || $league['creator_id'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'No tienes permiso para expulsar miembros.');
            $this->redirect('leagues');
            return;
        }

        if ($userId === (int)$_SESSION['user_id']) {
            $this->setFlash('error', 'No puedes expulsarte a ti mismo.');
            $this->redirect('leagues/show/' . $leagueId);
            return;
        }

        $this->model->leave($leagueId, $userId);
        $this->ftm->deleteTeamByUser($userId, $leagueId);
        $this->setFlash('success', 'Miembro expulsado de la liga.');
        $this->redirect('leagues/show/' . $leagueId);
    }

    public function joinByCode(): void {
        $this->requireLogin();

        $code   = strtoupper(trim($_POST['invite_code'] ?? ''));
        $league = $this->model->getByInviteCode($code);

        if (!$league) {
            $this->setFlash('error', 'Código de invitación no válido.');
            $this->redirect('leagues');
            return;
        }

        if ($this->model->isMember($league['id'], $_SESSION['user_id'])) {
            $this->setFlash('error', 'Ya eres miembro de esta liga.');
            $this->redirect('leagues/show/' . $league['id']);
            return;
        }

        $this->model->join($league['id'], $_SESSION['user_id']);
        $this->setFlash('success', '¡Te has unido a la liga ' . htmlspecialchars($league['name']) . '! Crea tu equipo para participar.');
        $this->redirect('fantasy/create?league=' . $league['id']);
    }
}
