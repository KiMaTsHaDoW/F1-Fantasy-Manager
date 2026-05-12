<?php

namespace Models;

class Usuario
{
    private int $id;
    private string $nombre;
    private string $email;
    private string $contrasena;
    private string $rol;
    private float $saldo_presupuesto;
    private bool $activo;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        string $nombre,
        string $email,
        string $contrasena,
        string $rol = 'usuario',
        float $saldo_presupuesto = 50000,
        bool $activo = true
    ) {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->contrasena = password_hash($contrasena, PASSWORD_BCRYPT);
        $this->rol = $rol;
        $this->saldo_presupuesto = $saldo_presupuesto;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public function getSaldoPresupuesto(): float
    {
        return $this->saldo_presupuesto;
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

    public function setEmail(string $email): void
    {
        $this->email = $email;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setContrasena(string $contrasena): void
    {
        $this->contrasena = password_hash($contrasena, PASSWORD_BCRYPT);
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setSaldoPresupuesto(float $saldo): void
    {
        $this->saldo_presupuesto = $saldo;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->contrasena);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'rol' => $this->rol,
            'saldo_presupuesto' => $this->saldo_presupuesto,
            'activo' => $this->activo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
