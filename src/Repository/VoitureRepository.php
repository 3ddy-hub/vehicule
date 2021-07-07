<?php

namespace App\Repository;

use App\Constant\StatusValidation;
use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Voiture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voiture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voiture[]    findAll()
 * @method Voiture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoitureRepository extends ServiceEntityRepository
{
    /**
     * VoitureRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    /**
     * @param $voiture
     * @param $action
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveVoiture($voiture, $action)
    {
        $is_save = false;
        if ($voiture instanceof Voiture) {
            $exist = $this->findOneBy(['immatriculation' => $voiture->getImmatriculation()]);
            $is_im_xist = $exist !== null ? $exist->getImmatriculation() : false;
            if ($action == 'new' && !$is_im_xist) {
                $this->_em->persist($voiture);
                $is_save = true;
            } elseif ($action == 'update' &&
                ($exist === $voiture ||
                    $exist->getImmatriculation() !== $voiture->getImmatriculation())) {
                $is_save = true;
            }
            if ($is_save)
                $this->_em->flush();
        }

        return $is_save;
    }

    /**
     * @param $voiture
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteVoiture($voiture)
    {
        $is_deleted = false;
        if ($voiture instanceof Voiture) {
            $this->_em->remove($voiture);
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
    public function listVoiture($page, $nb_max_page, $search, $order_by)
    {
        $order_by = $order_by ? $order_by : "v.id DESC";
        $voiture  = $this->getEntityName();

        $dql = "SELECT 
                    m.modele modele,
                    v.immatriculation immatriculation,
                    v.couleur couleur,
                    v.kilometrage kilometrage,
                    v.id voiture_id
                FROM $voiture v 
                LEFT JOIN v.modele m
                WHERE v.immatriculation LIKE :search 
                    OR m.modele LIKE :search 
                    OR v.couleur LIKE :search 
                    OR v.kilometrage LIKE :search
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
        $voiture = $this->getEntityName();

        $dql = "SELECT COUNT (v) total_number 
                FROM $voiture v 
                LEFT JOIN v.modele m  
                WHERE v.immatriculation LIKE :search 
                    OR m.modele LIKE :search 
                    OR v.couleur LIKE :search 
                    OR v.kilometrage LIKE :search";

        $_query = $this->_em->createQuery($dql);
        $_query->setParameter('search', "%$search%");

        return $_query->getOneOrNullResult()['total_number'];
    }
}
