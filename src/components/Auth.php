<?php declare(strict_types=1);
namespace Components;
use DateTime;
use Models\User;

class Auth{
    public static function userIsAuthenticated(): bool{
        return isset($_SESSION['userid']);
    }
    public static function getLastLogin(): DateTime
    {
        return DateTime::createFromFormat('U', (string)($_SESSION['loginTime'] ?? ''));
    }
    #returns user instance
    public static function getUser(): ?User
    {
        if(self::userIsAuthenticated()){
            return Database::getUserById((int)$_SESSION['userid']);
        }
        return null;
    }
    /**
     * Modify SESSION data
     */
    #Authenticate a user
    public static function authenticate(int $id){
        $_SESSION['userid'] = $id;
        $_SESSION['loginTime'] = time();
    }
    #De-Authenticate user 
    public static function logout()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            session_destroy();
        }
    }
}
