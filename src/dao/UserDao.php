<?php


class UserDao extends Dao {
    /**
     * Insert a new user account (role = USER)
     * @param NewUser $newUser
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

        $this->query($sql, $params);
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