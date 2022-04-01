<?php

namespace App\components\formChangePasswd;

use App\components\BaseComponent;
use App\model\Constants;
use App\model\Email;
use App\model\UserClass;
use http\Exception;
use Nette\Application\AbortException;
use \Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;


class formChangePasswd extends BaseComponent {

    public function render() : void {
        $this->template->setFile(__DIR__ . '/formChangePasswd.latte');
        $this->template->render();
    }

    public function __construct(Presenter $presenter, Container $container, UserClass $userClass) {
        parent::__construct($presenter, $container, $userClass);
    }

    /**
     * @return Form
     */
    public function createComponentFormChangePasswd() : Form {

        $form = new Form();
        $form->addPassword('old_passwd', 'Heslo staré:')
            ->setRequired('heslo: ' . Constants::FORM_MSG_REQUIRED);
        $form->addPassword('new_passwd', 'Heslo nové:')
            ->setRequired('heslo: ' . Constants::FORM_MSG_REQUIRED);
        $form->addPassword('passwd_verify', 'Heslo pro kontrolu:')
            ->setRequired('Zadejte prosím heslo ještě jednou pro kontrolu')
            ->addRule($form::EQUAL, 'Hesla se neshodují', $form['new_passwd'])
            ->setOmitted();

        $form->addSubmit('submit', 'Změnit');
        $form->onSuccess[] = [$this, 'formChangePasswdSucceeded'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws AbortExceptionss
     */
    public function formChangePasswdSucceeded(Form $form, ArrayHash $values) : void {
        try {
            $hash = \Model\Passwords::hash($values->old_passwd);
            if (\Model\Passwords::hash($values->old_passwd) !== $this->presenter->userClass->get('passwd')) {
                throw new \Exception("Aktuální heslo je chybné.");
            }

            $this->presenter->userClass->update(["passwd" => \Model\Passwords::hash($values->new_passwd)]);
            Email::changePasswdMail($this->presenter->userClass->get('email'), $values->new_passwd);

            $this->presenter->flashMessage("Změna heslo probehla uspesne", 'error');

        } catch (\Exception $e) {
            $this->presenter->flashMessage($e->getMessage());
            $this->redirect('this', ['do' => 'Login']);
        }



    }
}
