<?php

namespace Models;

class Liga
{
    private int $id;
    private string $nombre;
    private string $tipo; // 'publica' or 'privada'
    private int $id_admin;
    private int $temporada;
    private string $descripcion;
    private int $max_participantes;
    private bool $activa;
    private string $created_at;
    private string $updated_at;
    private array $participantes = [];

    public function __construct(
        string $nombre,
        string $tipo,
        int $id_admin,
        int $temporada = 2025,
        string $descripcion = '',
        int $max_participantes = 20,
        bool $activa = true
    ) {
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->id_admin = $id_admin;
        $this->temporada = $temporada;
        $this->descripcion = $descripcion;
        $this->max_participantes = $max_participantes;
        $this->activa = $activa;
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

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getIdAdmin(): int
    {
        return $this->id_admin;
    }

    public function getTemporada(): int
    {
        return $this->temporada;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getMaxParticipantes(): int
    {
        return $this->max_participantes;
    }

    public function isActiva(): bool
    {
        return $this->activa;
    }

    public function getParticipantes(): array
    {
        return $this->participantes;
    }

    public function getNumeroParticipantes(): int
    {
        return count($this->participantes);
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

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setActiva(bool $activa): void
    {
        $this->activa = $activa;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function addParticipante(array $equipo): bool
    {
        if ($this->getNumeroParticipantes() >= $this->max_participantes) {
            return false;
        }
        $this->participantes[] = $equipo;
        $this->updated_at = date('Y-m-d H:i:s');
        return true;
    }

    public function removeParticipante(int $id_equipo): bool
    {
        foreach ($this->participantes as $key => $equipo) {
            if ($equipo['id'] === $id_equipo) {
                unset($this->participantes[$key]);
                $this->updated_at = date('Y-m-d H:i:s');
                return true;
            }
        }
        return false;
    }

    public function isPublica(): bool
    {
        return $this->tipo === 'publica';
    }

    public function isPrivada(): bool
    {
        return $this->tipo === 'privada';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'id_admin' => $this->id_admin,
            'temporada' => $this->temporada,
            'descripcion' => $this->descripcion,
            'max_participantes' => $this->max_participantes,
            'numero_participantes' => $this->getNumeroParticipantes(),
            'activa' => $this->activa,
            'participantes' => $this->participantes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
