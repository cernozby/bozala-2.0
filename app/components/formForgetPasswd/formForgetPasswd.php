<?php

namespace App\components\formForgetPasswd;

use App\components\BaseComponent;
use App\model\Constants;
use App\model\Email;
use App\model\UserClass;
use http\Exception;
use Nette\Application\AbortException;
use \Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;


class formForgetPasswd extends BaseComponent {



    public function render() : void {
        $this->template->setFile(__DIR__ . '/formForgetPasswd.latte');
        $this->template->render();
    }

    public function __construct(Presenter $presenter, Container $container, UserClass $userClass) {
        parent::__construct($presenter, $container, $userClass);
    }

    /**
     * @return Form
     */
    public function createComponentFormForgetPasswd() : Form {

        $form = new Form();
        $form->addEmail('email', 'Email:')
            ->setRequired('email: '. Constants::FORM_MSG_REQUIRED);

        $form->addSubmit('submit', 'odeslat');
        $form->onSuccess[] = [$this, 'formForgetPasswdSucceeded'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function formForgetPasswdSucceeded(Form $form, ArrayHash $values) : void {
        try {

            /** @var  $userClass UserClass */
            $userClass = $this->presenter->userClass;


            $userClass->existInColumn("email", $values->email);
            if (!$userClass->existInColumn("email", $values->email)) {
                throw new \Exception("Email nenalezen.");
            }

            $newPasswd = Random::generate(6);
            $userClass->update(["passwd" => \Model\Passwords::hash($newPasswd)]);
            Email::resetPasswdMail($values->email, $newPasswd);
            $this->presenter->flashMessage("Vygenerování hesla probehlo uspesne a bude na emialu.");

        } catch (\Exception $e) {
            $this->presenter->flashMessage($e->getMessage());
            $this->redirect('this', ['do' => 'Login']);
        }



    }
}
