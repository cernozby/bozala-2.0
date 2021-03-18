<?php

namespace App\Presenters;
use App\components\formLogin\formLogin;
use App\components\formNewCompetitor\formNewCategory;
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
use App\model\UserClass;
use App\model\UserModel;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;

abstract class BasePresenter extends Presenter {
  
  
  /**
   * @var UserModel
   */
  
  /**
   * @var Container
   */
  public $container;
  
  /**
   * @var UserModel
   */
  public $userModel;
  
  public UserClass $userClass;
  
  public CompModel $compModel;
  
  public CompetitorModel $competitorModel;
  
  public Comp $comp;
  
  public Competitor $competitor;
  
  public CategoryModel $categoryModel;
  
  public Category $category;
  
  
  
  public function __construct(Container $container,
                              UserModel $userModel,
                              UserClass $userClass,
                              CompModel $compModel,
                              CompetitorModel $competitorModel,
                              Comp $comp,
                              Competitor $competitor,
                              Category $category,
                              CategoryModel $categoryModel
  ) {
    $this->container = $container;
    $this->userModel = $userModel;
    $this->userClass = $userClass;
    $this->comp = $comp;
    $this->competitor = $competitor;
    $this->competitorModel = $competitorModel;
    $this->compModel = $compModel;
    $this->category = $category;
    $this->categoryModel = $categoryModel;
    parent::__construct();
  }
  
  
  public function startup() {
    parent::startup(); // TODO: Change the autogenerated stub
    
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
  
  public function createComponentFormNewCompControl(): formNewComp {
    return new formNewComp($this->presenter, $this->container, $this->userClass);
  }
  
  public function createComponentFormNewCategoryControl(): formNewCategory {
    return new formNewCategory($this->presenter, $this->container, $this->userClass);
  }
}
