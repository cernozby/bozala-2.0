<?php
namespace App\model;

use App\model\Constants;
use Nette;

class UserManager implements Nette\Security\IAuthenticator
{
  private $database;
  private $passwords;
  
  
  public function __construct(Nette\Database\Context $database, Nette\Security\Passwords $passwords)
  {
    $this->database = $database;
    $this->passwords = $passwords;
  }
  
  public function authenticate(array $credentials): Nette\Security\IIdentity
  {
    [$username, $password] = $credentials;
    
    $row = $this->database->table('user')
      ->where('email', $username)->fetch();
    
    if (!$row) {
      throw new Nette\Security\AuthenticationException('Zadaný email nebyl nalezen.');
    }
    
    if (!$this->passwords->verify($password, $row->passwd)) {
      throw new Nette\Security\AuthenticationException('Nesprávné heslo.');
    }
    
    
    return new Nette\Security\Identity($row->id_user, ['first_name' => $row->first_name, 'last_name' => $row->last_name, 'role' => Constants::$user[$row->role], 'email' => $row->email]);
  }
  
}