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

  public function handleChangeBoolColumn(int $compId, string $column) {
      $this->comp->initId($compId);
      $this->comp->changeBoolColumn($column);
      $this->redrawControl();

  }

  public function handlePrereg(int $categoryId, int $competitorId) {
      $this->competitor->initId($competitorId);
      $this->competitor->changePrereg($categoryId);
      $this->flashMessage('Registrace byla upravena.');
      $this->redrawControl();
  }

    public function renderListOfPrereg($compId) : void {
        $this->template->compId = $compId;
        if (!$compId) {
            $this->template->comps = $this->compModel->getVisiblePreregComp();
        } else {
            $this->comp->initId($compId);
            $this->template->categories = $this->comp->getCategory();
        }

    }

  public function renderListOfComps() : void {
    $this->template->comps = $this->compModel->getAllAsObj();
  }
  
  public function renderPrereg($competitorId) : void {
      $this->competitor->initId($competitorId);
      $this->template->categories = $this->competitor->getCategoryToPrereg();
      $this->template->competitor = $this->competitor;
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
