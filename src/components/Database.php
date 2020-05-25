<?php declare(strict_types=1);
namespace Components;
use Models\User;
use PDO;
use PDOStatement;

class Database{
    public $pdo;
    /**
     * Instantiate PDO object
     * Create database connection
     */
    private function __construct()
    {
        $dsn = "mysql:host=localhost;port=3306;dbname=contactapp;charset=utf8mb4";
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $this->pdo = new PDO($dsn, "root", "", $options);
    }
    /**
     * Singleton pattern
     */
    public static function instance(){
        static $instance;
        if (is_null($instance)) {
            $instance = new static();
        }
        return $instance;
    }
    /**
     * Add users
     */
    public function addUser($firstname, $lastname, $username, $password):PDOStatement
    {
        $addStmt = $this->pdo->prepare("INSERT INTO users(`firstname`, `lastname`, `username`, `password`) VALUES(:firstname, :lastname, :username, :password)");
        $addStmt->execute([
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':username' => $username,
            ':password' => password_hash($password, PASSWORD_BCRYPT)
        ]);
        return $addStmt;
    }
    public function getUserByUsername(string $formUsername): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        if ($stmt->execute([':username' => $formUsername]) && ($data = $stmt->fetch(PDO::FETCH_ASSOC))) {
            return new User($data);
        }
        return null;
    }
    public function getUserById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute() && ($data = $stmt->fetch(PDO::FETCH_ASSOC))) {
            return new User($data);
        }
        return null;
    }
    //Contact Table
    public function getContacts(int $uid): PDOStatement
    {
        $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE user_id = :uid");
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
    public function getContactById(int $ownerId, int $contactId): ?array{
        $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE id = :cid AND user_id = :uid");
        $stmt->bindParam(':cid', $contactId, PDO::PARAM_INT);
        $stmt->bindParam('uid', $ownerId, PDO::PARAM_INT);
        if ($stmt->execute() && ($data = $stmt->fetch(PDO::FETCH_ASSOC))) {
            return $data;
        }
        return null;
    }
    public function addContact(
        int $ownerId,
        string $name,
        string $email,
        string $phone,
        string $address
    ): PDOStatement
    {
        $stmt = $this->pdo->prepare("INSERT INTO contacts (user_id, `name`, phone, email, address) " .
            "VALUES (:uid, :name, :phone, :email, :address)");
        $stmt->bindParam(':uid', $ownerId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->execute();
        return $stmt;
    }
    public function updateContact(
        int $contactId,
        int $ownerId,
        string $name,
        string $email,
        string $phone,
        string $address
    ): PDOStatement
    {
        $stmt = $this->pdo->prepare(
            "UPDATE contacts SET `name` = :name, phone = :phone, email = :email, address = :address "
            . "WHERE id = :cid and user_id = :uid"
        );
        $stmt->bindParam(':cid', $contactId, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $ownerId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->execute();
        return $stmt;
    }

    public function deleteContactById(int $ownerId, int $contactId): PDOStatement
    {
        $stmt = $this->pdo->prepare("DELETE FROM contacts WHERE id = :cid and user_id = :uid");
        $stmt->bindParam(':cid', $contactId, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $ownerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

}
