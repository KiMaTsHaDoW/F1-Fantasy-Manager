<?php
class F1ApiModel {
    private string     $jolpicaBase = 'https://api.jolpi.ca/ergast/f1';
    private string     $openf1Base  = 'https://api.openf1.org/v1';
    private int        $cacheTtl    = 3600;
    private string     $cacheDir;
    private PriceModel $pm;
    private bool       $apiError    = false;

    public function hasApiError(): bool { return $this->apiError; }

    public function __construct() {
        $this->cacheDir = BASE_PATH . '/cache/';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
        $this->pm = new PriceModel();
    }

    private function fetchJson(string $url): ?array {
        $cacheFile = $this->cacheDir . md5($url) . '.json';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $this->cacheTtl) {
            return json_decode(file_get_contents($cacheFile), true);
        }
        $ctx = stream_context_create(['http' => ['timeout' => 10, 'user_agent' => 'F1Fantasy/1.0']]);
        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) {
            $this->apiError = true;
            return file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : null;
        }
        file_put_contents($cacheFile, $response);
        return json_decode($response, true);
    }

    private function fetchJolpica(string $endpoint): ?array {
        return $this->fetchJson($this->jolpicaBase . $endpoint . '.json');
    }

    // Devuelve mapa [code => openf1_driver] para enriquecer datos
    private function getOpenF1DriverMap(): array {
        $data = $this->fetchJson($this->openf1Base . '/drivers?session_key=latest');
        if (!$data) return [];
        $map = [];
        foreach ($data as $d) {
            $code = $d['name_acronym'] ?? '';
            if ($code && !isset($map[$code])) {
                $map[$code] = $d;
            }
        }
        return $map;
    }

    public function getCurrentSeason(): string { return date('Y'); }

    public function getDrivers(?string $season = null): array {
        $season = $season ?? $this->getCurrentSeason();
        $data   = $this->fetchJolpica("/$season/drivers");
        if (!$data) return $this->getDefaultDrivers();

        // Build points map + constructor map from standings
        $standingsData = $this->fetchJolpica("/$season/driverStandings");
        $ptsMap         = [];
        $driverTeamMap  = [];
        foreach ($standingsData['MRData']['StandingsTable']['StandingsLists'][0]['DriverStandings'] ?? [] as $s) {
            $did = $s['Driver']['driverId'];
            $ptsMap[$did]        = (float)$s['points'];
            $driverTeamMap[$did] = $s['Constructors'][0]['constructorId'] ?? '';
        }
        $maxPts = max(array_merge([1], array_values($ptsMap)));

        // Build constructor color map from OpenF1
        $of1Data = $this->fetchJson($this->openf1Base . '/drivers?session_key=latest') ?? [];
        $teamColorMap = [];   // constructorId-like key → hex color
        $of1 = [];
        foreach ($of1Data as $entry) {
            $acronym = $entry['name_acronym'] ?? '';
            if ($acronym && !isset($of1[$acronym])) $of1[$acronym] = $entry;
            $team = strtolower(str_replace([' ', '-'], '_', $entry['team_name'] ?? ''));
            if ($team && isset($entry['team_colour'])) $teamColorMap[$team] = '#' . $entry['team_colour'];
        }

        $drivers = [];
        foreach ($data['MRData']['DriverTable']['Drivers'] ?? [] as $d) {
            $code  = $d['code'] ?? strtoupper(substr($d['familyName'], 0, 3));
            $extra = $of1[$code] ?? null;
            $id    = $d['driverId'] ?? '';
            $pts   = $ptsMap[$id] ?? 0;
            $seed  = round(max(4.0, min(35.0, 4.0 + ($pts / $maxPts) * 31.0)), 1);

            // Resolve color: OpenF1 direct match → team color map → grey
            if ($extra) {
                $color = '#' . $extra['team_colour'];
                $team  = $extra['team_name'] ?? '';
            } else {
                $cid   = $driverTeamMap[$id] ?? '';
                $ckey  = strtolower(str_replace([' ', '-'], '_', $cid));
                $color = $teamColorMap[$ckey] ?? '#cccccc';
                // Try partial match if exact key not found
                if ($color === '#cccccc' && $cid) {
                    foreach ($teamColorMap as $k => $v) {
                        if (str_contains($k, $ckey) || str_contains($ckey, $k)) {
                            $color = $v;
                            break;
                        }
                    }
                }
                $team = $cid;
            }

            $drivers[] = [
                'id'          => $id,
                'code'        => $code,
                'number'      => $d['permanentNumber'] ?? '?',
                'forename'    => $d['givenName'] ?? '',
                'surname'     => $d['familyName'] ?? '',
                'nationality' => $d['nationality'] ?? '',
                'dob'         => $d['dateOfBirth'] ?? '',
                'url'         => $d['url'] ?? '',
                'headshot'    => $extra['headshot_url'] ?? '',
                'team'        => $team,
                'team_color'  => $color,
                'price'       => $this->resolvePrice($id, 'driver', $seed),
                'points'      => $pts,
            ];
        }
        return $drivers;
    }

    public function getDriverById(string $driverId): ?array {
        foreach ($this->getDrivers() as $d) {
            if ($d['id'] === $driverId) return $d;
        }
        return null;
    }

    public function getConstructors(?string $season = null): array {
        $season = $season ?? $this->getCurrentSeason();
        $data   = $this->fetchJolpica("/$season/constructors");
        if (!$data) return $this->getDefaultTeams();

        // Colores de equipo desde OpenF1
        $of1Colors = [];
        $of1Data   = $this->fetchJson($this->openf1Base . '/drivers?session_key=latest') ?? [];
        foreach ($of1Data as $d) {
            $team = $d['team_name'] ?? '';
            if ($team && !isset($of1Colors[$team])) {
                $of1Colors[$team] = '#' . $d['team_colour'];
            }
        }

        // Build constructor points map for price seeding
        $cStandingsData = $this->fetchJolpica("/$season/constructorStandings");
        $cPtsMap = [];
        foreach ($cStandingsData['MRData']['StandingsTable']['StandingsLists'][0]['ConstructorStandings'] ?? [] as $s) {
            $cPtsMap[$s['Constructor']['constructorId']] = (float)$s['points'];
        }
        $cMaxPts = max(array_merge([1], array_values($cPtsMap)));

        $teams = [];
        foreach ($data['MRData']['ConstructorTable']['Constructors'] ?? [] as $c) {
            $color = '#cccccc';
            foreach ($of1Colors as $teamName => $hex) {
                if (stripos($teamName, $c['name']) !== false || stripos($c['name'], $teamName) !== false) {
                    $color = $hex;
                    break;
                }
            }
            $cid  = $c['constructorId'];
            $pts  = $cPtsMap[$cid] ?? 0;
            $seed = round(max(4.0, min(35.0, 4.0 + ($pts / $cMaxPts) * 31.0)), 1);
            $teams[] = [
                'id'          => $cid,
                'name'        => $c['name'],
                'nationality' => $c['nationality'],
                'url'         => $c['url'] ?? '',
                'color'       => $color,
                'price'       => $this->resolvePrice($cid, 'constructor', $seed),
                'points'      => $pts,
            ];
        }
        return $teams;
    }

    public function getConstructorById(string $constructorId): ?array {
        foreach ($this->getConstructors() as $t) {
            if ($t['id'] === $constructorId) return $t;
        }
        return null;
    }

    public function getDriversByConstructor(string $constructorId, ?string $season = null): array {
        $season = $season ?? $this->getCurrentSeason();
        $data   = $this->fetchJolpica("/$season/constructors/$constructorId/drivers");
        if (!$data) return [];

        $of1 = $this->getOpenF1DriverMap();
        $drivers = [];
        foreach ($data['MRData']['DriverTable']['Drivers'] ?? [] as $d) {
            $code  = $d['code'] ?? strtoupper(substr($d['familyName'], 0, 3));
            $extra = $of1[$code] ?? null;
            $drivers[] = [
                'id'       => $d['driverId'] ?? '',
                'code'     => $code,
                'number'   => $d['permanentNumber'] ?? '?',
                'forename' => $d['givenName'] ?? '',
                'surname'  => $d['familyName'] ?? '',
                'headshot' => $extra['headshot_url'] ?? '',
                'team_color' => $extra ? '#' . $extra['team_colour'] : '#cccccc',
            ];
        }
        return $drivers;
    }

    public function getDriverStandings(?string $season = null): array {
        $season = $season ?? $this->getCurrentSeason();
        $data   = $this->fetchJolpica("/$season/driverStandings");
        if (!$data) return [];
        return $data['MRData']['StandingsTable']['StandingsLists'][0]['DriverStandings'] ?? [];
    }

    public function getConstructorStandings(?string $season = null): array {
        $season = $season ?? $this->getCurrentSeason();
        $data   = $this->fetchJolpica("/$season/constructorStandings");
        if (!$data) return [];
        return $data['MRData']['StandingsTable']['StandingsLists'][0]['ConstructorStandings'] ?? [];
    }

    public function getRaces(?string $season = null): array {
        $season = $season ?? $this->getCurrentSeason();
        $data   = $this->fetchJolpica("/$season");
        if (!$data) return [];
        return $data['MRData']['RaceTable']['Races'] ?? [];
    }

    public function getRaceResults(string $round, ?string $season = null): array {
        $season = $season ?? $this->getCurrentSeason();
        $data   = $this->fetchJolpica("/$season/$round/results");
        if (!$data) return [];
        return $data['MRData']['RaceTable']['Races'][0]['Results'] ?? [];
    }

    public function getLastRaceResults(): array {
        $data = $this->fetchJolpica('/current/last/results');
        if (!$data) return [];
        return $data['MRData']['RaceTable']['Races'][0] ?? [];
    }

    public function getSprintResults(int $round, ?string $season = null): array {
        $season = $season ?? $this->getCurrentSeason();
        $data   = $this->fetchJolpica("/$season/$round/sprint");
        if (!$data) return [];
        return $data['MRData']['RaceTable']['Races'][0] ?? [];
    }

    public function getNextRace(): ?array {
        $today = date('Y-m-d');
        foreach ($this->getRaces() as $race) {
            if ($race['date'] >= $today) return $race;
        }
        return null;
    }

    private function resolvePrice(string $id, string $type, float $seed = 15.0): float {
        $dbPrice = $this->pm->getPrice($id, $type);
        if ($dbPrice !== null) return $dbPrice;
        $this->pm->setPrice($id, $type, $seed);
        return $seed;
    }

    public function getLastRaceRound(): int {
        $data = $this->fetchJolpica('/current/last/results');
        return (int)($data['MRData']['RaceTable']['Races'][0]['round'] ?? 0);
    }


    private function getDefaultDrivers(): array {
        return [
            ['id'=>'norris','code'=>'NOR','number'=>'4','forename'=>'Lando','surname'=>'Norris','nationality'=>'British','dob'=>'1999-11-13','url'=>'','headshot'=>'','team'=>'McLaren','team_color'=>'#F47600','price'=>31.0,'points'=>0],
            ['id'=>'max_verstappen','code'=>'VER','number'=>'33','forename'=>'Max','surname'=>'Verstappen','nationality'=>'Dutch','dob'=>'1997-09-30','url'=>'','headshot'=>'','team'=>'Red Bull Racing','team_color'=>'#3671C6','price'=>30.0,'points'=>0],
            ['id'=>'leclerc','code'=>'LEC','number'=>'16','forename'=>'Charles','surname'=>'Leclerc','nationality'=>'Monégasque','dob'=>'1997-10-16','url'=>'','headshot'=>'','team'=>'Ferrari','team_color'=>'#E8002D','price'=>28.0,'points'=>0],
        ];
    }

    private function getDefaultTeams(): array {
        return [
            ['id'=>'mclaren','name'=>'McLaren','nationality'=>'British','url'=>'','color'=>'#F47600','price'=>32.0,'points'=>0],
            ['id'=>'ferrari','name'=>'Ferrari','nationality'=>'Italian','url'=>'','color'=>'#E8002D','price'=>30.0,'points'=>0],
            ['id'=>'mercedes','name'=>'Mercedes','nationality'=>'German','url'=>'','color'=>'#27F4D2','price'=>28.0,'points'=>0],
            ['id'=>'red_bull','name'=>'Red Bull Racing','nationality'=>'Austrian','url'=>'','color'=>'#3671C6','price'=>26.0,'points'=>0],
        ];
    }
}
