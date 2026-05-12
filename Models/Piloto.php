<?php

namespace Models;

class Piloto
{
    private int $id;
    private string $nombre;
    private string $apellido;
    private int $id_escuderia;
    private int $numero_casco;
    private float $precio;
    private string $pais;
    private bool $activo;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        string $nombre,
        string $apellido,
        int $id_escuderia,
        float $precio,
        int $numero_casco = 0,
        string $pais = '',
        bool $activo = true
    ) {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->id_escuderia = $id_escuderia;
        $this->numero_casco = $numero_casco;
        $this->precio = $precio;
        $this->pais = $pais;
        $this->activo = $activo;
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

    public function getApellido(): string
    {
        return $this->apellido;
    }

    public function getNombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getIdEscuderia(): int
    {
        return $this->id_escuderia;
    }

    public function getNumeroCasco(): int
    {
        return $this->numero_casco;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getPais(): string
    {
        return $this->pais;
    }

    public function isActivo(): bool
    {
        return $this->activo;
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

    public function setApellido(string $apellido): void
    {
        $this->apellido = $apellido;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setIdEscuderia(int $id_escuderia): void
    {
        $this->id_escuderia = $id_escuderia;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setNumeroCasco(int $numero_casco): void
    {
        $this->numero_casco = $numero_casco;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setPrecio(float $precio): void
    {
        $this->precio = $precio;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setPais(string $pais): void
    {
        $this->pais = $pais;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_completo' => $this->getNombreCompleto(),
            'id_escuderia' => $this->id_escuderia,
            'numero_casco' => $this->numero_casco,
            'precio' => $this->precio,
            'pais' => $this->pais,
            'activo' => $this->activo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
