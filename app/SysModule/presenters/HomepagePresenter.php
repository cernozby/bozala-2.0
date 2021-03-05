<?php

namespace App\SysModule\presenters;

use App\Presenters\BasePresenter;

Class HomepagePresenter extends BasePresenter
{
  public function startup() : void {
    if (!$this->user->isLoggedIn()) {
      $this->flashMessage('Nedostatečná oprávnění');
      $this->redirect(':Public:Homepage:default');
    }
    
    parent::startup();
    bdump($this->user);
  }
  
  public function handleLogout() : void {
    $this->user->logout();
    $this->flashMessage('Byl jste úspešně odhlášen');
    $this->redirect(':Public:Homepage:default');
  }
}
