<?php

/**
 * Created by PhpStorm.
 * User: sebastien.nexon
 * Date: 17/01/2018
 * Time: 15:35
 */

namespace AppBundle\Security;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ClientVoter extends Voter
{
    const VIEW = 'client_show';
    const EDIT = 'client_edit';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if(!in_array($attribute, array(self::VIEW,self::EDIT)))
        {
            return false;
        }

        if(!$subject instanceof Client)
        {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($this->decisionManager->decide($token, array('ROLE_SUPER_ADMIN'))) {
            return true;
        }

        if (!$user instanceof User) {
            return false;
        }

        $client = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($client, $user);

            case self::EDIT:
                return $this->canEdit($client, $user);

        }
        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Client $client, User $user)
    {
        if($this->canEdit($client, $user))
        {
            return true;
        }
        return false;
    }

    private function canEdit(Client $client, User $user)
    {
        if($user == $client->getCreator()) {
            return true;
        }
        return false;
    }



}