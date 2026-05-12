<?php

namespace Models;

class Carrera
{
    private int $id;
    private string $nombre;
    private string $circuito;
    private string $fecha_carrera;
    private int $temporada;
    private string $pais;
    private int $ronda;
    private bool $completada;
    private string $created_at;
    private string $updated_at;
    private array $resultados = [];

    public function __construct(
        string $nombre,
        string $circuito,
        string $fecha_carrera,
        int $temporada = 2025,
        string $pais = '',
        int $ronda = 0,
        bool $completada = false
    ) {
        $this->nombre = $nombre;
        $this->circuito = $circuito;
        $this->fecha_carrera = $fecha_carrera;
        $this->temporada = $temporada;
        $this->pais = $pais;
        $this->ronda = $ronda;
        $this->completada = $completada;
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

    public function getCircuito(): string
    {
        return $this->circuito;
    }

    public function getFechaCarrera(): string
    {
        return $this->fecha_carrera;
    }

    public function getTemporada(): int
    {
        return $this->temporada;
    }

    public function getPais(): string
    {
        return $this->pais;
    }

    public function getRonda(): int
    {
        return $this->ronda;
    }

    public function isCompletada(): bool
    {
        return $this->completada;
    }

    public function getResultados(): array
    {
        return $this->resultados;
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

    public function setCircuito(string $circuito): void
    {
        $this->circuito = $circuito;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setFechaCarrera(string $fecha_carrera): void
    {
        $this->fecha_carrera = $fecha_carrera;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setPais(string $pais): void
    {
        $this->pais = $pais;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setRonda(int $ronda): void
    {
        $this->ronda = $ronda;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setCompletada(bool $completada): void
    {
        $this->completada = $completada;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function addResultado(array $resultado): void
    {
        $this->resultados[] = $resultado;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function removeResultado(int $id_piloto): bool
    {
        foreach ($this->resultados as $key => $resultado) {
            if ($resultado['id_piloto'] === $id_piloto) {
                unset($this->resultados[$key]);
                $this->updated_at = date('Y-m-d H:i:s');
                return true;
            }
        }
        return false;
    }

    public function marcarCompletada(): void
    {
        $this->completada = true;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'circuito' => $this->circuito,
            'fecha_carrera' => $this->fecha_carrera,
            'temporada' => $this->temporada,
            'pais' => $this->pais,
            'ronda' => $this->ronda,
            'completada' => $this->completada,
            'resultados' => $this->resultados,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
