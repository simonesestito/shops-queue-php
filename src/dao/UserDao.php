<?php


class UserDao extends Dao {
    /**
     * Insert a new user account (role = USER)
     * @param NewUser $newUser
     * @return array Newly created user, from UserWithRole view
     */
    public function insertNewUser(NewUser $newUser) {
        // Find the ID of the given role
        $roles = $this->query("SELECT * FROM Role WHERE name = 'USER'");
        if (empty($roles)) {
            throw new RuntimeException("Unable to find USER role");
        }
        $roleId = $roles[0]['id'];

        $params = [
            $newUser->name,
            $newUser->surname,
            $newUser->email,
            password_hash($newUser->password, PASSWORD_BCRYPT),
            $roleId
        ];

        $sql = "INSERT INTO User (name, surname, email, password, roleId) VALUES (?, ?, ?, ?, ?)";
        $id = $this->query($sql, $params);

        $result = $this->query("SELECT * FROM UserWithRole WHERE id = ?", [$id]);
        return $result[0];
    }

    /**
     * Insert a new shop owner
     * @param NewUser $newUser
     * @param int $shopId
     */
    public function insertShopOwner(NewUser $newUser, int $shopId) {
        // Find the ID of the given role
        $roles = $this->query("SELECT * FROM Role WHERE name = 'OWNER'");
        if (empty($roles)) {
            throw new RuntimeException("Unable to find OWNER role");
        }
        $roleId = $roles[0]['id'];

        $params = [
            $newUser->name,
            $newUser->surname,
            $newUser->email,
            password_hash($newUser->password, PASSWORD_BCRYPT),
            $roleId,
            $shopId
        ];

        $sql = "INSERT INTO User (name, surname, email, password, roleId, shopId) VALUES (?, ?, ?, ?, ?, ?)";
        $id = $this->query($sql, $params);

        $result = $this->query("SELECT * FROM UserWithRole WHERE id = ?", [$id]);
        return $result[0];
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