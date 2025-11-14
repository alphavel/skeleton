<?php

namespace App\Models;

use Alphavel\Database\Model;
use Alphavel\Database\QueryBuilder;

/**
 * User Model
 * Exemplo de implementação de Model com Active Record
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $role
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @method static User|null find(int $id)
 * @method static User findOrFail(int $id)
 * @method static User[] all()
 * @method static QueryBuilder where(string $column, mixed $operator, mixed $value = null)
 * @method static QueryBuilder active() Scope: usuários ativos
 * @method static User|null byEmail(string $email) Busca por email
 * @method static User[] recent(int $limit = 10) Usuários recentes
 * @method static User[] byRole(string $role) Usuários por role
 */
class User extends Model
{
    protected static $table = 'users';

    protected static $primaryKey = 'id';

    /**
     * Scope: busca apenas usuários ativos
     */
    public static function active(): QueryBuilder
    {
        return static::where('status', 'active');
    }

    /**
     * Scope: busca por email
     */
    public static function byEmail(string $email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * Busca usuários recentes
     */
    public static function recent(int $limit = 10): array
    {
        $results = static::query()
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();

        return static::hydrate($results);
    }

    /**
     * Busca usuários por role
     */
    public static function byRole(string $role): array
    {
        $results = static::query()
            ->where('role', $role)
            ->get();

        return static::hydrate($results);
    }

    /**
     * Retorna nome completo
     */
    public function fullName(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }

    /**
     * Verifica se usuário é admin
     */
    public function isAdmin(): bool
    {
        return ($this->role ?? '') === 'admin';
    }

    /**
     * Retorna dados do usuário sem senha (para APIs)
     */
    public function toPublicArray(): array
    {
        $data = $this->toArray();
        unset($data['password']);

        return $data;
    }
}
