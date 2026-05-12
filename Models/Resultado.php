<?php

namespace Models;

class Resultado
{
    private int $id;
    private int $id_carrera;
    private int $id_piloto;
    private int $posicion_llegada;
    private int $puntos;
    private int $vueltas_completadas;
    private bool $retirado;
    private string $tiempo_total;
    private string $created_at;
    private string $updated_at;

    // F1 Scoring system: positions 1-10 score points
    private static array $PUNTOS_F1 = [25, 18, 15, 12, 10, 8, 6, 4, 2, 1];

    public function __construct(
        int $id_carrera,
        int $id_piloto,
        int $posicion_llegada = 0,
        int $vueltas_completadas = 0,
        bool $retirado = false,
        string $tiempo_total = ''
    ) {
        $this->id_carrera = $id_carrera;
        $this->id_piloto = $id_piloto;
        $this->posicion_llegada = $posicion_llegada;
        $this->vueltas_completadas = $vueltas_completadas;
        $this->retirado = $retirado;
        $this->tiempo_total = $tiempo_total;
        $this->puntos = $this->calcularPuntos($posicion_llegada, $retirado);
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getIdCarrera(): int
    {
        return $this->id_carrera;
    }

    public function getIdPiloto(): int
    {
        return $this->id_piloto;
    }

    public function getPosicionLlegada(): int
    {
        return $this->posicion_llegada;
    }

    public function getPuntos(): int
    {
        return $this->puntos;
    }

    public function getVueltasCompletadas(): int
    {
        return $this->vueltas_completadas;
    }

    public function isRetirado(): bool
    {
        return $this->retirado;
    }

    public function getTiempoTotal(): string
    {
        return $this->tiempo_total;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setPosicionLlegada(int $posicion): void
    {
        $this->posicion_llegada = $posicion;
        $this->puntos = $this->calcularPuntos($posicion, $this->retirado);
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setVueltasCompletadas(int $vueltas): void
    {
        $this->vueltas_completadas = $vueltas;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setRetirado(bool $retirado): void
    {
        $this->retirado = $retirado;
        $this->puntos = $this->calcularPuntos($this->posicion_llegada, $retirado);
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setTiempoTotal(string $tiempo): void
    {
        $this->tiempo_total = $tiempo;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    /**
     * Calculate F1 points based on position
     * Positions 1-10 score points according to current F1 scoring system
     * Bonus for fastest lap: +1 point (not included in this calculation)
     */
    private function calcularPuntos(int $posicion, bool $retirado): int
    {
        if ($retirado || $posicion <= 0 || $posicion > 20) {
            return 0;
        }

        if ($posicion <= count(self::$PUNTOS_F1)) {
            return self::$PUNTOS_F1[$posicion - 1];
        }

        return 0;
    }

    /**
     * Add bonus point for fastest lap
     */
    public function addPuntoVueltaRapida(): void
    {
        if (!$this->retirado && $this->posicion_llegada > 0 && $this->posicion_llegada <= 10) {
            $this->puntos += 1;
            $this->updated_at = date('Y-m-d H:i:s');
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'id_carrera' => $this->id_carrera,
            'id_piloto' => $this->id_piloto,
            'posicion_llegada' => $this->posicion_llegada,
            'puntos' => $this->puntos,
            'vueltas_completadas' => $this->vueltas_completadas,
            'retirado' => $this->retirado,
            'tiempo_total' => $this->tiempo_total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
