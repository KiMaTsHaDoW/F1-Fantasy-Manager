<?php

namespace Models;

class Escuderia
{
    private int $id;
    private string $nombre;
    private float $presupuesto;
    private string $pais;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        string $nombre,
        float $presupuesto,
        string $pais = ''
    ) {
        $this->nombre = $nombre;
        $this->presupuesto = $presupuesto;
        $this->pais = $pais;
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

    public function getPresupuesto(): float
    {
        return $this->presupuesto;
    }

    public function getPais(): string
    {
        return $this->pais;
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

    public function setPresupuesto(float $presupuesto): void
    {
        $this->presupuesto = $presupuesto;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setPais(string $pais): void
    {
        $this->pais = $pais;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'presupuesto' => $this->presupuesto,
            'pais' => $this->pais,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
