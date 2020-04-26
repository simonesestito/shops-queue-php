<?php


class UserDao extends Dao {
    /**
     * Insert a new user account
     * @param NewUser $newUser
     * @return int ID of the newly created user
     */
    public function insertNewUser(NewUser $newUser) {
        // Find the ID of the given role
        $role = $newUser->role;
        $roles = $this->query("SELECT * FROM Role WHERE name = ?", [$role]);
        if (empty($roles)) {
            throw new RuntimeException("Unable to find role: $role");
        }
        $roleId = $roles[0]['id'];

        $params = [
            $newUser->name,
            $newUser->surname,
            $newUser->email,
            password_hash($newUser->password, PASSWORD_BCRYPT),
            $roleId,
            $newUser->shopId,
        ];

        $sql = "INSERT INTO User (name, surname, email, password, roleId, shopId) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->query($sql, $params);
    }

    /**
     * Find a user by his email address
     * @param string $email
     * @return array|null Single associative array
     */
    public function getUserByEmail(string $email) {
        $records = $this->query("SELECT * FROM UserWithRole WHERE email = ?", [$email]);
        return @$records[0];
    }

    /**
     * Find a user by ID
     * @param int $id
     * @return array|null Single associative array
     */
    public function getUserById(int $id) {
        $records = $this->query("SELECT * FROM UserWithRole WHERE id = ?", [$id]);
        return @$records[0];
    }
}