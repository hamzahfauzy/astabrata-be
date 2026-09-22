<?php

namespace Libraries;

use Libraries\Database as DB;

class Auth
{

    protected ?object $user = null;
    protected ?array $permissions = null;

    /**
     * Login menggunakan username/email dan password.
     */
    public function attempt(string $identity, string $password): bool
    {
        $user = DB::table('users')
            ->where('email', $identity)
            ->orWhere('username', $identity)
            ->first();

        if (!$user) {
            return false;
        }

        if (!$user->is_active) {
            return false;
        }

        if (!password_verify($password, $user->password)) {
            return false;
        }

        $this->user = $user;

        request()->setUser($user);

        return true;
    }

    /**
     * Membuat bearer token.
     */
    public function login(?string $device = null): string
    {
        $user = request()->user() ?? $this->user;

        if (!$user) {
            throw new \Exception('No authenticated user.');
        }

        $plainToken = bin2hex(random_bytes(32));

        DB::table('user_tokens')->insert([
            'user_id' => $user->id,
            'token' => hash('sha256', $plainToken),
            'device_name' => $device,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'last_used_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'last_login_at' => date('Y-m-d H:i:s')
        ]);

        return $plainToken;
    }

    /**
     * Logout current device.
     */
    public function logout(): void
    {
        $token = request()->bearerToken();

        if (!$token) {
            return;
        }

        DB::table('user_tokens')
            ->where('token', hash('sha256', $token))
            ->delete();
    }
    
    public function user()
    {
        $token = request()->bearerToken();

        if (!$token) {
            return null;
        }

        $user = DB::exec("SELECT u.* FROM users u JOIN user_tokens t ON u.id=t.user_id WHERE t.token=? AND t.expires_at > NOW()", [
            hash('sha256', $token),
        ])->fetchObject();

        if(empty($user))
        {
            throw new \Exception('Token is not valid.');
        }

        $user->roles = DB::table('user_roles')->select('roles.id, roles.name')->where('user_id', $user->id)->leftJoin('roles','roles.id','=','user_roles.role_id')->get();

        unset($user->password);

        $user->permissions = $this->permissions($user);

        return $user;
    }

    public function check(): bool
    {
        return request()->user() !== null;
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function can(string|array $permission): bool
    {
        $permissions = $this->permissions();

        if (in_array('*', $permissions, true)) {
            return true;
        }

        if (is_array($permission)) {
            foreach ($permission as $item) {
                if ($this->can($item)) {
                    return true;
                }
            }

            return false;
        }

        if (in_array($permission, $permissions, true)) {
            return true;
        }

        $parts = explode('.', $permission, 2);

        if (count($parts) !== 2) {
            return false;
        }

        [$module, $action] = $parts;

        return in_array("{$module}.*", $permissions, true)
            || in_array("*.{$action}", $permissions, true);
    }

    public function hasRole(string $role): bool
    {
        $user = request()->user();

        if (!$user) {
            return false;
        }

        $sql = "
            SELECT COUNT(*) AS total
            FROM roles r
            INNER JOIN user_roles ur
                ON ur.role_id = r.id
            WHERE ur.user_id = ?
            AND r.slug = ?
        ";

        $result = DB::exec($sql, [
            $user['id'],
            $role
        ])->fetch();

        return (int)$result['total'] > 0;
    }

    protected function permissions($user = null): array
    {
        if ($this->permissions !== null) {
            return $this->permissions;
        }

        $user = $user ?? request()->user();

        $sql = "
            SELECT DISTINCT p.slug
            FROM permissions p

            LEFT JOIN user_permissions up
                ON up.permission_id = p.id
                AND up.user_id = ?

            LEFT JOIN role_permissions rp
                ON rp.permission_id = p.id

            LEFT JOIN user_roles ur
                ON ur.role_id = rp.role_id
                AND ur.user_id = ?

            WHERE up.user_id IS NOT NULL
            OR ur.user_id IS NOT NULL
        ";

        $permissions = Database::exec($sql, [
            $user->id,
            $user->id
        ])->fetchAll();

        $this->permissions = array_column($permissions, 'slug');

        return $this->permissions;
    }
}