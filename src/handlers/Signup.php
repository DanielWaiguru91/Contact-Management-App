<?php declare(strict_types=1);
namespace Handlers;

use Components\Auth;
use Components\Database;
use Components\Template;

class Signup extends Handler
{
    public function handle(): string
    {
        if (Auth::userIsAuthenticated()) {
            $this->requestRedirect('/profile');
            return '';
        }
        $formError = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formError = $this->handleSignup();
            if (!$formError) {
                return '';
            }
        }

        return (new Template('signup-form'))->render([
            'formError' => $formError,
            'formUsername' => $_POST['username'] ?? ''
        ]);
    }

    private function handleSignup(): ?array
    {
        $formError = null;
        $formFirstname = trim($_POST['firstname']);
        $formLastname = trim($_POST['lastname']);
        $formUsername = trim($_POST['username'] ?? '');
        $formPassword = trim($_POST['password'] ?? '');
        $formPasswordVerify = $_POST['passwordVerify'] ?? '';
        if (!$formFirstname || strlen($formFirstname) < 5) {
            $formError = [
                'firstname' => 'Firstname should be at least 5 characters long.'
            ];
        }elseif(!$formLastname || strlen($formLastname) < 5){
            $formError = [
                'lastname' => 'lastname should be at least 5 characters long'
            ];
        }
        elseif(!$formUsername || strlen($formUsername) < 5){
            $formError = ['username' => 'Username should be at least 5 characters long.'];
        } 
        elseif (!ctype_alnum($formUsername)) {
            $formError = [
                'username' => 'The username should contain only numbers and letters.'
            ];
        } elseif (!$formPassword) {
            $formError = [
                'password' => 'Please enter a password of at least 6 characters.'
            ];
        } elseif ($formPassword !== $formPasswordVerify) {
            $formError = [
                'passwordVerify' => 'The passwords doesn\'t match.'
            ];
        } else {
            $stmt = Database::instance()->addUser(strtolower($formFirstname), strtolower($formLastname),strtolower($formUsername), $formPassword);
            if (!$stmt->rowCount()) {
                list(, , $error) = $stmt->errorInfo();
                $formError = ['username' => $error];
            } else {
                Auth::authenticate((int)Database::instance()->pdo->lastInsertId());
                $this->requestRedirect('/profile');
            }
        }
        return $formError;
    }
}