<?php

namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\Security\User;

/**
 * Class User
 **/
class UserClass extends BaseFactory
{
  
  /**
   * UserClass constructor.
   * @param Explorer $database
   * @param Container $container
   * @param LinkGenerator $linkGenerator
   */
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'user';
    parent::__construct($database, $container, $linkGenerator);
  }
  
  /**
   * @return string
   */
  public function getFullName() : string {
    return $this->get('first_name') . $this->get('last_name');
  }
  
  /**
   * @return string
   */
  public function getRole() : string {
    return Constants::$user[$this->get('role')];
  }
  
  /**
   * @return bool
   */
  public function isAdmin() : bool {
    return $this->get('role') === Constants::USER_ADMIN;
  }
  
  /**
   * @return bool
   */
  public function isUser() : bool {
    return $this->get('role') === Constants::USER_USER;
  }
  
  /**
   * @param string $email
   * @return UserClass|null
   */
  public function initByEmail(string  $email) :? UserClass {
    $result = $this->getTable()->select('id_user')->where('emial = ?', $email)->fetch();
    if ($result) {
      return $this->initId($result->is_user);
    }
    return null;
  }
  
  /**
   * @param string $passwd
   */
  public function changePasswd(string $passwd) : void {
    $this->update(['passwd' => \Model\Passwords::hash($passwd)]);
  }
}