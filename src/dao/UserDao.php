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
        if (empty($records)) {
            return null;
        } else {
            return $records[0];
        }
    }
}