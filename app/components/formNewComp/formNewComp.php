<?php

namespace App\components\formNewCompetitor;

use App\components\BaseComponent;
use App\model\CompModel;
use App\model\Constants;
use App\model\UserClass;
use Nette\Application\AbortException;
use \Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;


class formNewComp extends BaseComponent {
  
  private $compId;
  
  public function render($compId = null) : void {
    $this->compId = $compId;
    $this->template->compId = $compId;
    $this->template->setFile(__DIR__ . '/formNewComp.latte');
    $this->template->render();
  }
  
  public function createComponentNewCompForm() : Form {
    $form = new Form();
    $form->addHidden('id_comp');
    $form->addText('name', 'Jméno:')
      ->setRequired('jméno: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::MAX_LENGTH,Constants::FORM_LONG, 60);
    $form->addText('date', 'Datum: ')
      ->setRequired('Datum: : ' . Constants::FORM_MSG_REQUIRED)
      ->setHtmlType('date');
    $form->addInteger('boulder', 'Počet bouldrů: ');
    $form->addInteger('speed', "Počet pokusů: ");
    $form->addInteger('lead', "Počet cest: ");
    $form->addSelect('boulder_final', "Finále?: ", array(0 => 'Ne', 1 => 'Ano'))
      ->setPrompt('--vyberte-- ');
    $form->addSelect('speed_final', "Finále?: ", array(0 => 'Ne', 1 => 'Ano'))
      ->setPrompt('--vyberte-- ');
    $form->addSelect('lead_final', "Finále?: ", array(0 => 'Ne', 1 => 'Ano'))
      ->setPrompt('--vyberte-- ');
    $form->addSelect('boulder_result', "Typ výsledků: ", CompModel::$result_type)
      ->setPrompt('--vyberte-- ');
    $form->addSelect('speed_result', "Typ výsledků: ", CompModel::$result_type)
      ->setPrompt('--vyberte-- ');
    $form->addSelect('lead_result', "Typ výsledků: ", CompModel::$result_type)
      ->setPrompt('--vyberte-- ');
    
    if ($this->compId) {
      $data = $this->container->createService('Comp')->initId($this->compId)->getRowAsArray();
      $data['date'] = $data['date']->format('Y-m-d');
      $form->setDefaults($data);
    }
    
    
    $form->addSubmit('submit', 'Odeslat');
    $form->onSuccess[] = [$this, 'newCompFormSucceed'];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function newCompFormSucceed(Form $form, ArrayHash $values): void {
    try {
      
      $compId = $values->offsetGet('id_comp');
      $values->offsetUnset('id_comp');
      $values->offsetSet('user_id', $this->userClass->getId());
      
      if ($compId) {
        $this->container->createService('Comp')->initId($compId)->update($values);
        $this->presenter->flashMessage('Editace proběhla úspešně.');
      } else {
        $this->container->createService('CompModel')->newComp($values);
        $this->presenter->flashMessage('Registrace proběhla úspešně.');
      }
    } catch (\Exception $e) {
      $this->presenter->flashMessage('Neúspešná registrace zavodnika'. $e->getMessage() );
    }
    $this->presenter->redirect('this');
    
  }
}
