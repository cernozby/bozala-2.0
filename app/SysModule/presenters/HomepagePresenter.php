<?php

namespace App\SysModule\presenters;

use App\model\ResultModel;
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

  public function handleSaveResult($data) : void {
      $data = $this->request->getPost();
      $this->resultModel->saveResult($data);
  }
  public function handleChangeBoolColumn(int $compId, string $column) {
      $this->comp->initId($compId);
      $this->comp->changeBoolColumn($column);
      $this->redrawControl();

  }

  public function handleMakeFinalList($idCategory, $type) {
      $this->category->initId($idCategory);
      $this->category->makeFinalListLead();
  }

  public function handleChangeBoolCategory(int $categoryId, string $column) {
      $this->category->initId($categoryId);
      $this->category->changeBoolColumn($column);
      $this->redrawControl();
  }


  public function handlePrereg(int $categoryId, int $competitorId) {
      $this->competitor->initId($competitorId);
      $this->competitor->changePrereg($categoryId);
      $this->flashMessage('Registrace byla upravena.');
      $this->redrawControl();
  }

  public function renderDefault() {
      $this->template->comp = $this->compModel;
      $this->template->competitors = $this->competitorModel->getByUser($this->userClass->getId());

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

  public function renderAddResult($compId, $categoryId) : void {
      $this->template->compId = $compId;
      $this->template->categoryId = $categoryId;
      $this->template->resultModel = $this->resultModel;

      if ($categoryId && $compId) {
          $this->category->initId($categoryId);
          $this->template->category = $this->category;
          $this->template->competitors = $this->userClass->isAdmin() ?
              $this->category->getPreregCompetitors() :
              $this->category->getPreregCompetitorsByUser($this->userClass->getId());
          $this->template->finalLeadCompetitors = $this->resultModel->getCompetitors($categoryId, ResultModel::LEAD_FI);

      } elseif ($compId) {
          $this->comp->initId($compId);
          if ($this->userClass->isAdmin()) {
              $this->template->categories = $this->comp->getCategory();
          } else {
              $this->template->categories = $this->comp->getCategoryForUser($this->userClass->getId());
          }
      } else {
          if ($this->userClass->isAdmin()) {
              $this->template->comps = $this->compModel->getEditableResultComps();
          } else {
              $this->template->comps = $this->compModel->getEditableResultForUser($this->userClass->getId());
          }
      }
  }

  public function renderResult($compId, $categoryId) : void {
      $this->template->compId = $compId;
      $this->template->categoryId = $categoryId;
      $this->template->resultModel = $this->resultModel;


      if ($categoryId && $compId) {
          $this->category->initId($categoryId);
          $this->template->category = $this->category;
          $this->template->comp = $this->category->getComp();
      } elseif ($compId) {
          $this->comp->initId($compId);
          $this->template->categories = $this->comp->getCategory();
      } else {
          $this->template->comps = $this->compModel->getEditableResultComps();
      }
  }
  
  public function handleLogout() : void {
    $this->user->logout();
    $this->flashMessage('Byl jste úspešně odhlášen');
    $this->redirect(':Public:Homepage:default');
  }

  public function handleChangePasswd() {
      $this->template->changePasswd = true;

  }

    public function handleResetPasswd() : void {
        $this->template->forgetPasswdForm = true;
    }

}
