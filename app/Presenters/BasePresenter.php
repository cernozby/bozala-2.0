<?php

namespace App\Presenters;
use App\components\formChangePasswd\formChangePasswd;
use App\components\formForgetPasswd\formForgetPasswd;
use App\components\formLogin\formLogin;
use App\components\formNewCategory;
use App\components\formNewCompetitor\formNewComp;
use App\components\formNewCompetitor\formNewCompetitor;
use App\components\formRegistration\formRegistration;
use App\components\MyHelpers;
use App\model\Category;
use App\model\CategoryModel;
use App\model\Comp;
use App\model\Competitor;
use App\model\CompetitorModel;
use App\model\CompModel;
use App\model\Prereg;
use App\model\PreregModel;
use App\model\Result;
use App\model\ResultModel;
use App\model\UserClass;
use App\model\UserModel;
use Nette\Application\UI\Multiplier;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;

abstract class BasePresenter extends Presenter {

  public Container $container;

  public UserModel $userModel;
  
  public UserClass $userClass;
  
  public CompModel $compModel;
  
  public CompetitorModel $competitorModel;
  
  public Comp $comp;
  
  public Competitor $competitor;
  
  public CategoryModel $categoryModel;
  
  public Category $category;

  public Prereg $prereg;

  public PreregModel $preregModel;

  public Result $result;

  public ResultModel $resultModel;

  public function __construct(Container $container,
                              UserModel $userModel,
                              UserClass $userClass,
                              CompModel $compModel,
                              CompetitorModel $competitorModel,
                              Comp $comp,
                              Competitor $competitor,
                              Category $category,
                              CategoryModel $categoryModel,
                              Prereg $prereg,
                              PreregModel $preregModel,
                              Result $result,
                              ResultModel $resultModel)
  {
    $this->container = $container;
    $this->userModel = $userModel;
    $this->userClass = $userClass;
    $this->comp = $comp;
    $this->competitor = $competitor;
    $this->competitorModel = $competitorModel;
    $this->compModel = $compModel;
    $this->category = $category;
    $this->categoryModel = $categoryModel;
    $this->prereg = $prereg;
    $this->preregModel = $preregModel;
    $this->result = $result;
    $this->resultModel = $resultModel;
    parent::__construct();
  }
  
  
  public function startup() {
    parent::startup();
    if(isset($this->user) && $this->user->getId()) {
      $this->userClass->initId($this->user->getId());
      $this->template->userClass = $this->userClass;
    }
  
    foreach (get_class_methods(MyHelpers::class) as $helper) {
      if ($helper !== '__construct') {
        $this->template->addFilter($helper, MyHelpers::class . "::" .$helper);
      }
    }

  }
  
  
  public function handleDeleteItem($type, $id) {

    try {
      $instance = $this->container->createService($type);
      $instance->initId($id);
      $instance->delete();
    } catch (Exception $e) {
      $this->flashMessage('Něco se pokazilo. Zkuste obnovit stránku', 'danger');
    }
    $this->flashMessage('Úspěšně smazáno', 'success');
/*    $this->redirect('this');*/
  }
  
  public function createComponentFormLoginControl(): formLogin {
    return new formLogin($this->presenter, $this->container, $this->userClass);
  }
  public function createComponentFormRegistrationControl(): formRegistration {
    return new formRegistration($this->presenter, $this->container, $this->userClass);
  }
  
  public function createComponentFormNewCompetitorControl(): formNewCompetitor {
    return new formNewCompetitor($this->presenter, $this->container, $this->userClass);
  }
  
  public function createComponentFormNewCompControl(): Multiplier {
      return new Multiplier(function ($arg) {
          return new formNewComp($this->presenter, $this->container, $this->userClass, $arg);
      });
  }
  
  public function createComponentFormNewCategoryControl(): Multiplier {
      return new Multiplier(function ($arg) {
          return new formNewCategory($this->presenter, $this->container, $this->userClass, $arg);
      });
  }

  public function createComponentFormChangePasswdControl(): FormChangePasswd {
      return new formChangePasswd($this->presenter, $this->container, $this->userClass);
  }

    public function createComponentFormForgetPasswdControl(): formForgetPasswd {
        return new formForgetPasswd($this->presenter, $this->container, $this->userClass);
    }
}
