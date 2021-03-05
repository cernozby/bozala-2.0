<?php
namespace App\components;
use App\model\UserModel;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Nette\Security\User;

class BaseComponent extends \Nette\Application\UI\Control {

  /**
   *
   * @var Presenter
   */
  public $presenter;
  public $container;
  public $user;
  
  /**
   * @var UserModel
   */
  public $userModel;

  public function __construct(Presenter $presenter, Container $container, User $user) {
    $this->presenter = $presenter;
    $this->container = $container;
    $this->user = $user;
  
    $this->userModel = $this->container->createService('userModel');
  
  }
}
