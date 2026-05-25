<?php
class AutoScore {
    private const CHECK_INTERVAL = 1800;
    private string $lockFile;

    public function __construct() {
        $this->lockFile = BASE_PATH . '/cache/autoscore_last_check.txt';
    }

    public function run(): void {
        if (!$this->shouldCheck()) return;

        file_put_contents($this->lockFile, time());

        try {
            $f1  = new F1ApiModel();
            $ftm = new FantasyTeamModel();

            $lastRace = $f1->getLastRaceResults();
            $round    = (int)($lastRace['round']  ?? 0);
            $season   = $lastRace['season']        ?? date('Y');

            if (!$round || empty($lastRace['Results'])) return;

            // Atomic: claim scoring rights before fetching — prevents double-scoring
            if ($ftm->claimRoundScore($season, $round, 'race')) {
                $this->scoreResults($lastRace['Results'], $ftm);
            }

            $sprintData = $f1->getSprintResults($round, $season);
            if (!empty($sprintData['SprintResults']) && $ftm->claimRoundScore($season, $round, 'sprint')) {
                $this->scoreResults($sprintData['SprintResults'], $ftm);
            }

            $ftm->cleanupOrphanedTeams();
        } catch (Throwable $e) {
            // Silent — never break the app
        }
    }

    private function shouldCheck(): bool {
        if (!file_exists($this->lockFile)) return true;
        return (time() - (int)file_get_contents($this->lockFile)) >= self::CHECK_INTERVAL;
    }

    private function scoreResults(array $results, FantasyTeamModel $ftm): void {
        $driverPoints      = [];
        $constructorPoints = [];
        foreach ($results as $r) {
            $did = $r['Driver']['driverId']           ?? '';
            $cid = $r['Constructor']['constructorId'] ?? '';
            $pts = (float)($r['points']               ?? 0);
            if ($did) $driverPoints[$did]      = $pts;
            if ($cid) $constructorPoints[$cid] = ($constructorPoints[$cid] ?? 0) + $pts;
        }
        $ftm->scoreRound($driverPoints, $constructorPoints);
    }
}
