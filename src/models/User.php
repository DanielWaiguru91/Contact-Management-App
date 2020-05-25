<?php declare(strict_type=1);
#Holds user data from the users table in the database
namespace Models;
use DateTime;
class User{
    #Usser properties
    private $id;
    private $firstname;
    private $lastname;
    private $username;
    private $password;
    private $signupTime;

    public function __construct(array $input){
        $this->id = (int)($input['id'] ?? 0);
        $this->firstname = (string)($input['firstname'] ?? '');
        $this->lastname = (string)($input['lastname'] ?? '');
        $this->username = (string)($input['username'] ?? '');
        $this->password = (string)($input['password'] ?? '');
        $this->signupTime = new DateTime($input['signup_time'] ?? 'now', new \DateTimeZone('UTC'));
    }
    public function getId(): Int
    {
        return $this->id;
    }
    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function getLastname(): string
    {
        return $this->lastname;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function getSignupTime(): DateTime
    {
        return $this->signupTime;
    }
    public function passwordMatches(string $formPassword){
        return password_verify($formPassword, $this->password);
    }
}