<?php
/**
 * vim:ft=php et ts=4 sts=4
 * @version
 * @todo
 */

namespace App\EventListener;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: User::class)]
// #[AsEntityListener(event: Events::postPersist, entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, entity: User::class)]
class UserListener extends AbstractController
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        // Set default password if not provided
        if (is_null($user->getPlainPassword())) {
            $user->setPlainPassword('111111');
        }
        
        // Hash password and clear plain password
        $user->setPassword($this->hasher->hashPassword($user, $user->getPlainPassword()));
        $user->eraseCredentials();
        
        // Set isAdmin based on roles
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $user->setIsAdmin(true);
        }
    }

    public function postPersist(User $user, LifecycleEventArgs $event): void
    {
    }
    
    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        // Handle password updates
        if ($event->hasChangedField('plainPassword')) {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        }
        
        // Update isAdmin based on roles
        if ($event->hasChangedField('roles')) {
            $user->setIsAdmin(in_array('ROLE_ADMIN', $user->getRoles()));
        }
    }
}
