<?php

namespace App\Service;

use App\Entity\LockableEntityInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class LockHelper
{
    private const LOCK_DURATION = 5 * 60;

    private \Redis $redis;
    private Security $security;

    public function __construct(\Redis $redis, Security $security)
    {
        $this->redis = $redis;
        $this->security = $security;
    }

    public function releaseLock(LockableEntityInterface $entity): void
    {
        $key = $this->getKey($entity);
        if ($this->redis->ttl($key) > 0) {
            $this->redis->del($key);
        }
    }

    /**
     * @return bool true if the lock is acquired by logged user
     */
    public function acquireLock(LockableEntityInterface $entity): ?User
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AuthenticationException('Not logged.');
        }

        /** @var User $user */
        $user = $this->security->getUser();

        $key = $this->getKey($entity);
        $this->redis->setnx($key, serialize($user));
        if ($this->redis->ttl($key) < 0) {
            $this->redis->expire($key, self::LOCK_DURATION);
        }
        $lockedBy = unserialize($this->redis->get($key), [User::class]);
        if (!$lockedBy instanceof User) {
            $lockedBy = null;
        }
        $lockedByMe = $lockedBy !== null && $lockedBy->getId()->equals($user->getId());
        if ($lockedByMe) {
            // refresh expire
            $this->redis->expire($key, self::LOCK_DURATION);
        }

        return $lockedBy;
    }

    private function getKey(LockableEntityInterface $entity): string
    {
        return sprintf('lock:%s:%s', str_replace('\\', '/', get_class($entity)), $entity->getId()->toString());
    }
}
