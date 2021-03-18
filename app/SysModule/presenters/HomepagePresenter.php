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
  }
  
  public function renderListOfComps() : void {
    $this->template->comps =  $this->compModel->getAllAsObj();
  }
  
  public function renderPrereg($competitorId) : void {
  
  }
  
  public function renderListOfCompetitors() : void {
    $this->template->competitors = $this->competitorModel->getByUser($this->userClass->getId());
  }
  
  public function handleLogout() : void {
    $this->user->logout();
    $this->flashMessage('Byl jste úspešně odhlášen');
    $this->redirect(':Public:Homepage:default');
  }
}
