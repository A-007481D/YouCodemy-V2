<?php

namespace App\models;

use App\entities\User;
use App\config\Database;
use App\models\interfaces\IUserModel;
use PDO;


class UserModel implements IUserModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function createUser(User $user): bool
    {
        $firstName = $user->getFName();
        $lastName = $user->getLName();
        $email = $user->getEmail();
        $password = $user->getPassword();
        $role = $user->getRole();
        $accountStatus = $user->getAccountStatus();

        $insert = "INSERT INTO users (first_name, last_name, email, password, role, account_status) 
               VALUES(:first_name, :last_name, :email, :password, :role, :account_status)";
        $stmt = $this->pdo->prepare($insert);
        $stmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':account_status', $accountStatus, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function findByEmail(string $email): ?User
    {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$res) {
            return null;
        }
        $user = new User($res['first_name'], $res['last_name'], $res['email'], $res['password']);
        $user->setId($res['userid']);
        $user->setRole($res['role']);
        $user->setAccountStatus($res['account_status']);
        return $user;
    }

    public function findByFirstName(string $firstName): ?User
    {
        $query = "SELECT * FROM users WHERE first_name = :first_name LIMIT 1;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['first_name' => $firstName]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$res) {
            return null;
        }

        $user = new User($res['first_name'], $res['last_name'], $res['email'], $res['password']);
        $user->setId($res['userid']);
        $user->setRole($res['role']);
        $user->setAccountStatus($res['account_status']);
        return $user;
    }

    public function findByLastName(string $lastName): ?User {
        $query = "SELECT * FROM users WHERE last_name = :last_name LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['last_name' => $lastName]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$res) {
            return null;
        }
        $user = new User($res['first_name'], $res['last_name'], $res['email'], $res['password']);
        $user->setId($res['userid']);
        $user->setRole($res['role']);
        $user->setAccountStatus($res['account_status']);
        return $user;
    }

    public function getAllUsers(): array
    {
        $query = "SELECT * FROM users";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];

        foreach ($res as $userData) {
            $user = new User($userData['first_name'], $userData['last_name'], $userData['email'], $userData['password']);
            $user->setId($userData['userid']);
            $user->setRole($userData['role']);
            $user->setAccountStatus($userData['account_status']);
            $users[] = $user;
        }

        return $users;
    }


    public function updateStatus(mixed $userId, string $string): void
    {
        $query = "UPDATE users SET account_status = :status WHERE userid = :userid";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status', $string, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function deleteUser(mixed $userId): void
    {
        $query = "DELETE FROM users WHERE userid = :userid";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
