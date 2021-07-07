<?php

namespace App\Repository;

use App\Entity\Modele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Modele|null find($id, $lockMode = null, $lockVersion = null)
 * @method Modele|null findOneBy(array $criteria, array $orderBy = null)
 * @method Modele[]    findAll()
 * @method Modele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeleRepository extends ServiceEntityRepository
{
    private $voiture_repository;

    /**
     * ModeleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modele::class);
    }

    /**
     * @param $model
     * @param $action
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveModele($model, $action)
    {
        $is_save = false;
        if ($model instanceof Modele) {
            if ($action == 'new') {
                $this->_em->persist($model);
            }
            $this->_em->flush();
            $is_save = true;
        }

        return $is_save;
    }

    /**
     * @param $modele
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteVoiture($modele)
    {
        $is_deleted = false;
        if ($modele instanceof Modele) {
             $this->_em->remove($modele);
            $this->_em->flush();
            $is_deleted = true;
        }

        return $is_deleted;
    }

    /**
     * @param $page
     * @param $nb_max_page
     * @param $search
     * @param $order_by
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listModele($page, $nb_max_page, $search, $order_by)
    {
        $order_by = $order_by ? $order_by : "m.id DESC";
        $modele   = $this->getEntityName();

        $dql = "SELECT 
                    m.modele modele,
                    m.marque marque,
                    m.puissance puissance,
                    m.carburant carburant,
                    m.id model_id
                FROM $modele m
                WHERE  m.modele LIKE :search 
                    OR m.marque LIKE :search 
                    OR m.puissance LIKE :search
                    OR m.carburant LIKE :search
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
        $modele = $this->getEntityName();

        $dql = "SELECT COUNT (m) total_number 
                FROM $modele m 
                WHERE m.modele LIKE :search 
                    OR m.marque LIKE :search 
                    OR m.puissance LIKE :search
                    OR m.carburant LIKE :search";

        $_query = $this->_em->createQuery($dql);
        $_query->setParameter('search', "%$search%");

        return $_query->getOneOrNullResult()['total_number'];
    }
}
