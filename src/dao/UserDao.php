<?php


class UserDao extends Dao {
    public function insertNewUser(NewUser $newUser) {
        $this->query("INSERT INTO User (name, surname, email, password) VALUES (?, ?, ?, ?)", [
            $this->sanitize($newUser->name),
            $this->sanitize($newUser->surname),
            $this->sanitize($newUser->email),
            $this->sanitize(password_hash($newUser->password, PASSWORD_BCRYPT))
        ]);
    }
}