<?php

namespace App\components;


use App\components\BaseComponent;
use App\model\CategoryModel;
use App\model\CompModel;
use App\model\Constants;
use App\model\UserClass;
use Nette\Application\AbortException;
use \Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;


class formNewCategory extends BaseComponent {
  
  private $compId;
  private $categoryId;

  public function __construct(Presenter $presenter, Container $container, UserClass $userClass, $arg) {
      parent::__construct($presenter, $container, $userClass);
      $arg = explode('x',$arg);
      $this->compId = (int)$arg[0];
      $this->categoryId = isset($arg[1]) ? (int)$arg[1] : null;
  }


    public function render() : void {

    $this->template->compId = $this->compId;
    $this->template->categoryId = $this->categoryId;
    $this->template->setFile(__DIR__ . '/formNewCategory.latte');
    $this->template->render();
  }
  
  public function createComponentNewCategoryForm() : Form {
    $form = new Form();
    $form->addHidden('id_category', $this->categoryId);
    $form->addHidden('comp_id', $this->compId);
    $form->addText('name', 'Jméno:')
      ->setRequired('jméno: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::MAX_LENGTH,Constants::FORM_LONG, 60);
    $form->addSelect('gender', "Kategorie: ", CategoryModel::$genders)
      ->setPrompt('--vyberte-- ')
      ->setRequired('Kategorie: ' . Constants::FORM_MSG_REQUIRED);
    $form->addInteger('year_young', 'Nejmladší rok narození: ')
      ->setRequired('jméno: ' . Constants::FORM_MSG_REQUIRED);
    $form->addInteger('year_old', "Nejstarší rok narození: ")
      ->setRequired('jméno: ' . Constants::FORM_MSG_REQUIRED);
    
    
    if ($this->categoryId) {
      $form->setDefaults($this->container->createService('category')->initID($this->categoryId)->getRowAsArray());
    }
    $form->addSubmit('submit', 'Odeslat');
    $form->onSuccess[] = [$this, 'NewCategoryFormSucceed'];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function NewCategoryFormSucceed(Form $form, ArrayHash $values): void {
    try {
      
      $idCategory = $values->offsetGet('id_category');
      $values->offsetUnset('id_category');
      if ($idCategory) {
        $this->container->createService('category')->initId($idCategory)->update($values);
        $this->presenter->flashMessage('Editace proběhla úspešně.');
      } else {
        $this->container->createService('CategoryModel')->newCategory($values);
        $this->presenter->flashMessage('Operace proběhla úspešně!', 'success');
      }

    } catch (\Exception $e) {
      $this->presenter->flashMessage('Operace se nepovedla!', 'warning');
    }
    $this->presenter->redirect('this');
  }
}
