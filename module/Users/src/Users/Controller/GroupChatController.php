<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;


class GroupChatController extends AbstractActionController
{
    public function indexAction()
    {
        $user = $this->getUserInfoFromSession();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $messageTest = $request->getPost()->get('message');
            $fromUserId = $user->id;
            $this->sendMessage($messageTest, $fromUserId);
            // to prevent duplicate entries on refresh
            return $this->redirect()->toRoute('users/group-chat');
        }

        //Prepare Send Message Form
        $form = new \Zend\Form\Form();
        $form->add(array(
            'name' => 'message',
            'attributes' => array(
                'type' => 'text',
                'id' => 'messageText',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Message',
            ),
        ));

        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Send'
            ),
        ));

        $form->add(array(
            'name' => 'refresh',
            'attributes' => array(
                'type' => 'button',
                'id' => 'btnRefresh',
                'value' => 'Refresh'
            ),
        ));

        $viewModel
            = new ViewModel(array('form' => $form,
            'userName' => $user->name));
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

    public function messageListAction()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $chatMessageTG = $this->getServiceLocator()->get('ChatMessagesTableGateway');
        $chatMessages = $chatMessageTG->select();
        $messageList = array();
        foreach($chatMessages as $chatMessage) {
            $fromUser = $userTable->getUser($chatMessage->user_id);
            $messageData = array();
            $messageData['user'] = $fromUser->name;
            $messageData['time'] = $chatMessage->stamp;
            $messageData['data'] = $chatMessage->message;
            $messageList[] = $messageData;
        }
        $viewModel = new ViewModel(array('messageList' =>$messageList));
        $viewModel->setTemplate('users/group-chat/message-list');
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    protected function sendMessage($messageTest, $fromUserId)
    {
        $chatMessageTG = $this->getServiceLocator()->get('ChatMessagesTableGateway');
        $data = array(
                'user_id' => $fromUserId,
                'message' => $messageTest,
                'stamp' => NULL);
        $chatMessageTG->insert($data);
        return true;
    }

}