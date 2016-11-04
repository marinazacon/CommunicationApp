<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Form\Element;


class MailController extends AbstractActionController
{
    public function indexAction()
    {
        $form = $this->getServiceLocator()->get('MailForm');
        $user = $this->getUserInfoFromSession();

        $form->get('fromUserName')->setAttribute('value', $user->name);

        $select = new Element\Select('comboToUsers');
        $select->setLabel('To User');
        $select->setValueOptions($this->getServiceLocator()->get('UserTable')->getUsersNames());
        if (empty($this->getServiceLocator()->get('UserTable')->getUsersNames()))
        {
            $form->get('sendMail')->setAttribute('disabled', 'disabled');
        }

        $form->add($select);

        $viewModel = new ViewModel(array('form' =>
            $form));
        return $viewModel;
    }

    public function getUserInfoFromSession()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $authService = $this->getServiceLocator()->get('AuthService');

        $userEmail = $authService->getStorage()->read();
        $user = $userTable->getUserByEmail($userEmail);

        return $user;
    }

    public function processAction()
    {
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get('MailForm');
        $form->setData($post);

        if (!$form->isValid()) {
            $model = new ViewModel(array(
                'error' => true,
                'form' => $form,
            ));
            $model->setTemplate('users/mail/index');
            return $model;
        }

        $fromUserId = $this->getUserInfoFromSession()->id;
        $data = $this->getRequest()->getPost()->toArray();
        $toUserId = $data['comboToUsers'];
        $msgSubj = $data['subject'];
        $msgText = $data['message'];

        $viewModel = new ViewModel();
        //if($this->sendOfflineMessage($msgSubj, $msgText, $fromUserId, $toUserId))
        if($this->sendMimeMessage($msgSubj, $msgText, $fromUserId, $toUserId))
        {
            $viewModel->setTemplate('users/mail/confirm');
        } else
        {
            $viewModel->setTemplate('users/mail/error');
        }
        return $viewModel;
    }

    protected function sendOfflineMessage($msgSubj, $msgText, $fromUserId, $toUserId)
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $fromUser = $userTable->getUser($fromUserId);
        $toUser = $userTable->getUser($toUserId);
        $mail = new \Zend\Mail\Message();
        $mail->setFrom($fromUser->email, $fromUser->name);
        $mail->addTo($toUser->email, $toUser->name);
        $mail->setSubject($msgSubj);
        $mail->setBody($msgText);
        $transport = new \Zend\Mail\Transport\Sendmail();
        $transport->send($mail);
        return true;
    }

    protected function sendMimeMessage($msgSubj, $msgText, $fromUserId, $toUserId)
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $fromUser = $userTable->getUser($fromUserId);
        $toUser = $userTable->getUser($toUserId);

        // Set parts of the body
        $text = new MimePart($msgText);
        $text->type = "text/plain";
        //$html = new MimePart($htmlMarkup);
        //$html->type = "text/html";
        //$image = new MimePart(fopen($pathToImage, 'r'));
        //$image->type = "image/jpeg";
        //$body->setParts(array($text, $html, $image));
        $body = new MimeMessage();
        $body->setParts(array($text));

        //Create Mail
        $mail = new Message();
        $mail->setFrom($fromUser->email, $fromUser->name);
        $mail->addTo($toUser->email, $toUser->name);
        $mail->setSubject($msgSubj);
        $mail->setBody($body);

        //Send Mail
        $transport = new \Zend\Mail\Transport\Sendmail();
        $transport->send($mail);
        return true;
    }

}