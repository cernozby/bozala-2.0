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
use Nette\Forms\Form as FormAlias;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Nette\Utils\Random;


class formNewComp extends BaseComponent {
  
  private $compId;

  public function __construct(Presenter $presenter, Container $container, UserClass $userClass, $compId) {
      parent::__construct($presenter, $container, $userClass);
      $this->compId = $compId == 0 ? null: $compId;
  }

  public function render() : void {
        $this->template->compId = $this->compId;
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
      ->setDefaultValue(0);
    $form->addSelect('speed_final', "Finále: ", array(0 => 'Ne', 1 => 'Ano'))
        ->setDefaultValue(0);
    $form->addSelect('lead_final', "Finále: ", array(0 => 'Ne', 1 => 'Ano'))
        ->setDefaultValue(0);
    $form->addSelect('boulder_result', "Typ výsledků: ", CompModel::$result_type)
        ->setDefaultValue(CompModel::AMATEUR_RESULT);

    $form->addInteger("boulder_final_competitors", 'Závodníků ve finále:');
    $form->addInteger("lead_final_competitors", 'Závodníků ve finále:');
    $form->addUpload('propo', 'Propozice: ');

    $form->addCheckbox("isLead")->setOmitted();
    $form->addCheckbox("isBoulder")->setOmitted();
    $form->addCheckbox("isSpeed")->setOmitted();

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
        /** @var FileUpload $propo */
      $propo = $values->offsetGet('propo');
      if ($propo->isOk()) {
          $name = '/pdf/' . Random::generate(10) . '-propozice.pdf';
          $path = __DIR__ . '/../../../www/' . $name;
          $propo->move($path);
          $values->offsetSet('propo_path', $this->getPresenter()->template->baseUrl . $name);

      }

      $compId = $values->offsetGet('id_comp');
      $values->offsetUnset('id_comp');
      $values->offsetSet('user_id', $this->userClass->getId());
      $values->offsetUnset('propo');


      if ($compId) {
        $comp = $this->container->createService('Comp')->initId($compId);
        if ($comp->get('propo_path')) {
            $name = array_reverse(explode('/', $comp->get('propo_path')))[0];
            FileSystem::delete("../www/pdf/" . $name);
        }
        $comp->update($values);
        $this->presenter->flashMessage('Editace proběhla úspešně.');
      } else {
        $this->container->createService('CompModel')->newComp($values);
        $this->presenter->flashMessage('Závod byl úspešně vytvořen!.');
      }
    } catch (\Exception $e) {
      $this->presenter->flashMessage('Při vytváření závodu se vyskytla chyba!.'. $e->getMessage() );
    }
    $this->presenter->redirect('this');
    
  }
}
