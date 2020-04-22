<?php


class UserDao extends Dao {
    /**
     * Insert a new user account
     * @param NewUser $newUser
     * @param string $role One of the known user roles
     * @param int|null $shopId If role is OWNER
     */
    public function insertNewUser(NewUser $newUser, string $role, int $shopId = null) {
        // Find the ID of the given role
        $roles = $this->query("SELECT * FROM Role WHERE name = ?", [$role]);
        if (empty($roles)) {
            throw new RuntimeException("Unknown role: $role");
        }
        $roleId = $roles[0]['id'];

        $params = [
            $newUser->name,
            $newUser->surname,
            $newUser->email,
            password_hash($newUser->password, PASSWORD_BCRYPT),
            $roleId
        ];

        if ($shopId == null) {
            $sql = "INSERT INTO User (name, surname, email, password, roleId) VALUES (?, ?, ?, ?, ?)";
        } else {
            $sql = "INSERT INTO User (name, surname, email, password, roleId, shopId) VALUES (?, ?, ?, ?, ?, ?)";
            $params[] = $shopId;
        }

        $this->query($sql, $params);
    }

    /**
     * Find a user by his email address
     * @param string $email
     * @return array|null Single associative array
     */
    public function getUserByEmail(string $email) {
        $sql = "SELECT User.*, Role.name AS roleName
                FROM User, Role
                WHERE User.roleId = Role.id
                AND email = ?";
        $records = $this->query($sql, [$email]);
        return @$records[0];
    }

    /**
     * Find a user by ID
     * @param int $id
     * @return array|null Single associative array
     */
    public function getUserById(int $id) {
        $sql = "SELECT User.*, Role.name AS roleName
                FROM User, Role
                WHERE User.roleId = Role.id
                AND User.id = ?";
        $records = $this->query($sql, [$id]);
        return @$records[0];
    }
}