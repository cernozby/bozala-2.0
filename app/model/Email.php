<?php
/**
 * author Zbysa
 */
namespace App\model;

use Nette\Mail;
use Nette\Mail\SendmailMailer;


class Email
{

    const MAIN_MAIL = 'Zbysa.Cernohous@seznam.cz';
    const FOOTER = "\n\n S pozdravem \n\n Web team\n";

    private static function sendMail($from , $to, $subj, $body) {
        try {
            $mail = new Mail\Message;
            $mail->setFrom($from)
                ->addTo($to)
                ->setSubject($subj)
                ->setBody($body);

            $mailer = new Mail\SendmailMailer;
            $mailer->send($mail);
        } catch (\Exception $e) {
            throw new Mail\SendException("Email se nepovedlo odeslat, kontaktujte prosím správce webu. ");
        }
    }

    public static function resetPasswdMail($to, $newPasswd) {
        $text = "Dobrý den,\n\nVaše heslo bylo změněno na {$newPasswd}." . self::FOOTER;
        self::sendMail(self::MAIN_MAIL, $to, "Změna hesla", $text);
    }

    public static function changePasswdMail($to, $newPasswd) {
        $text = "Dobrý den,\n\nVaše nové hesle je {$newPasswd}." . self::FOOTER;
        self::sendMail(self::MAIN_MAIL, $to, "Změna hesla", $text);

    }

}