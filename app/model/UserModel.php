<?php

namespace App\model;
use Exception;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;

/**
 * Class UserModel
 * @package App\model
 */
class UserModel extends BaseModel
{
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'user';
    parent::__construct($database, $container, $linkGenerator);
  }
  
  public function newUser($data) : void {
    $data['passwd'] = \Model\Passwords::hash($data['passwd']);
    $this->db->table($this->table)->insert($data);
  }
}