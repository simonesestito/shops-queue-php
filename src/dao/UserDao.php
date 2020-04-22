<?php


class UserDao extends Dao {
    /**
     * Insert a new user
     * @param NewUser $newUser
     * @throws DuplicateEntityException
     */
    public function insertNewUser(NewUser $newUser) {
        $this->query("INSERT INTO User (name, surname, email, password) VALUES (?, ?, ?, ?)", [
            $this->sanitize($newUser->name),
            $this->sanitize($newUser->surname),
            $this->sanitize($newUser->email),
            $this->sanitize(password_hash($newUser->password, PASSWORD_BCRYPT))
        ]);
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