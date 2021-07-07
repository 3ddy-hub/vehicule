<?php

namespace App\Repository;

use App\Constant\StatusValidation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param $user
     * @param $action
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveUser($user, $action)
    {
        $user_exist = $this->findOneBy([
            'email'      => $user->getEmail(),
            'is_deleted' => false
        ]);

        $user_has_account = $this->findOneBy([
            'email'      => $user->getEmail(),
            'is_deleted' => true
        ]);

        $status = StatusValidation::CREATE_STATUS;
        if ($action == 'new') {
            if ($user_exist) {
                $status = StatusValidation::EXIST_STATUS;
            } elseif ($user_has_account) {
                $user_has_account->setIsDeleted(false);
            } else {
                $user->setDateUpdate(new \DateTime());
                $user->setPassword(password_hash('123456789', 'argon2i'));
                $this->_em->persist($user);
            }
        } else {
            if (
                ($user_exist and $user_exist->getEmail() === $user->getEmail()) or
                ($user_has_account and $user_has_account->getEmail() === $user->getEmail())
            ) {
                $status = StatusValidation::EXIST_STATUS;
            } else {
                $user->setDateUpdate(new \DateTime());
                $status = StatusValidation::UPDATE_STATUS;
            }
        }
        if ($status !== StatusValidation::EXIST_STATUS) {
            $this->_em->flush();
        }

        return $status;
    }

    /**
     * @param $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteUser($user)
    {
        $status = false;
        if ($user) {
            $user->setIsDeleted(true);
            $user->setDateUpdate(new \DateTime());
            $status = true;
        }
        $this->_em->flush();

        return $status;
    }

    /**
     * @param $page
     * @param $nb_max_page
     * @param $search
     * @param $order_by
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listUser($page, $nb_max_page, $search, $order_by)
    {
        $order_by = $order_by ? $order_by : "u.date_update DESC";
        $user     = $this->getEntityName();

        $dql = "SELECT 
                    u.profil profil,
                    u.username username,
                    u.email email,
                    u.roles role,
                    DATE_FORMAT(u.date_add,'%d/%m/%Y %H:%i') date_create,
                    u.id user_id
                FROM $user u
                WHERE u.is_deleted = 0 
                AND (u.username LIKE :search 
                OR u.email LIKE :search 
                OR u.roles LIKE :search)
                ORDER BY $order_by";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('search', "%$search%")
            ->setFirstResult($page)
            ->setMaxResults($nb_max_page);

        return [$query->getResult(), $this->compteData($search)];
    }

    /**
     * @param $search
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function compteData($search)
    {
        $user = $this->getEntityName();

        $dql = "SELECT COUNT (u) total_number 
                 FROM $user u 
                 WHERE u.is_deleted = 0 
                 AND (u.username LIKE :search 
                 or DATE_FORMAT(u.date_add,'%d/%m/%Y') LIKE :search
                 OR u.email LIKE :search 
                 OR u.roles LIKE :search)";

        $_query = $this->_em->createQuery($dql);
        $_query->setParameter('search', "%$search%");

        return $_query->getOneOrNullResult()['total_number'];
    }
}
