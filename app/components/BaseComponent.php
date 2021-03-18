<?php
namespace App\components;
use App\model\UserClass;
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
  public Container $container;
  
  /**
   * @var UserModel
   */
  public $userModel;
  
  public UserClass $userClass;

  public function __construct(Presenter $presenter, Container $container, UserClass $userClass) {
    $this->presenter = $presenter;
    $this->container = $container;
    $this->userClass = $userClass;
    $this->userModel = $this->container->createService('UserModel');
  }
}
