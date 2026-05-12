<?php

namespace Models;

class Equipo
{
    private int $id;
    private string $nombre;
    private int $id_usuario;
    private int $id_liga;
    private float $presupuesto_restante;
    private int $puntos_totales;
    private int $posicion;
    private string $created_at;
    private string $updated_at;
    private array $pilotos = [];

    public function __construct(
        string $nombre,
        int $id_usuario,
        int $id_liga,
        float $presupuesto_restante = 50000,
        int $puntos_totales = 0
    ) {
        $this->nombre = $nombre;
        $this->id_usuario = $id_usuario;
        $this->id_liga = $id_liga;
        $this->presupuesto_restante = $presupuesto_restante;
        $this->puntos_totales = $puntos_totales;
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    public function getIdLiga(): int
    {
        return $this->id_liga;
    }

    public function getPresupuestoRestante(): float
    {
        return $this->presupuesto_restante;
    }

    public function getPuntosTotales(): int
    {
        return $this->puntos_totales;
    }

    public function getPosicion(): int
    {
        return $this->posicion;
    }

    public function getPilotos(): array
    {
        return $this->pilotos;
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

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setPresupuestoRestante(float $presupuesto): void
    {
        $this->presupuesto_restante = $presupuesto;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setPuntosTotales(int $puntos): void
    {
        $this->puntos_totales = $puntos;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setPosicion(int $posicion): void
    {
        $this->posicion = $posicion;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function addPiloto(array $piloto): void
    {
        $this->pilotos[] = $piloto;
    }

    public function removePiloto(int $id_piloto): bool
    {
        foreach ($this->pilotos as $key => $piloto) {
            if ($piloto['id'] === $id_piloto) {
                unset($this->pilotos[$key]);
                $this->updated_at = date('Y-m-d H:i:s');
                return true;
            }
        }
        return false;
    }

    public function restarPresupuesto(float $cantidad): void
    {
        $this->presupuesto_restante -= $cantidad;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function sumarPresupuesto(float $cantidad): void
    {
        $this->presupuesto_restante += $cantidad;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function sumarPuntos(int $puntos): void
    {
        $this->puntos_totales += $puntos;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'id_usuario' => $this->id_usuario,
            'id_liga' => $this->id_liga,
            'presupuesto_restante' => $this->presupuesto_restante,
            'puntos_totales' => $this->puntos_totales,
            'posicion' => $this->posicion,
            'pilotos' => $this->pilotos,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
