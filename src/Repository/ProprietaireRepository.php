<?php

namespace App\Repository;

use App\Entity\Proprietaire;
use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Proprietaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proprietaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proprietaire[]    findAll()
 * @method Proprietaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProprietaireRepository extends ServiceEntityRepository
{
    /**
     * ProprietaireRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proprietaire::class);
    }

    /**
     * @param $propretary
     * @param $action
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function savePropretary($propretary, $action)
    {
        $is_save = false;
        if ($propretary instanceof Proprietaire) {
            if ($action == 'new') {
                $this->_em->persist($propretary);
            }
            $this->_em->flush();
            $is_save = true;
        }

        return $is_save;
    }

    /**
     * @param $propretary
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deletePropretary($propretary)
    {
        $is_deleted = false;
        if ($propretary instanceof Proprietaire) {
            $this->_em->remove($propretary);
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
    public function listPropretary($page, $nb_max_page, $search, $order_by)
    {
        $order_by   = $order_by ? $order_by : "p.id DESC";
        $propretary = $this->getEntityName();

        $dql = "SELECT 
                    m.marque marque,
                    p.nom nom,
                    p.prenom prenom,
                    p.addresse addresse,
                    p.code_postal code_postal,
                    p.ville ville,
                    p.tel tel,
                    p.id propertary_id
                FROM $propretary p
                LEFT JOIN p.voiture v 
                LEFT JOIN v.modele m
                WHERE m.marque LIKE :search 
                    OR p.nom LIKE :search 
                    OR p.prenom LIKE :search
                    OR p.addresse LIKE :search
                    OR p.code_postal LIKE :search
                    OR p.ville LIKE :search
                    OR p.tel LIKE :search
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

        $dql = "SELECT COUNT (p) total_number 
                FROM $modele p 
                LEFT JOIN p.voiture v
                LEFT JOIN v.modele m
                WHERE m.marque LIKE :search 
                    OR p.nom LIKE :search 
                    OR p.prenom LIKE :search
                    OR p.addresse LIKE :search
                    OR p.code_postal LIKE :search
                    OR p.ville LIKE :search
                    OR p.tel LIKE :search";

        $_query = $this->_em->createQuery($dql);
        $_query->setParameter('search', "%$search%");

        return $_query->getOneOrNullResult()['total_number'];
    }

    /**
     * @return int|mixed|string
     */
    public function selectionnerVoiture()
    {
        $voiture = Voiture::class;
        $dql     = "SELECT 
                        m.modele modele,
                        m.marque marque,
                        v.id voiture_id 
                    FROM $voiture v 
                    LEFT JOIN v.modele m";
        $query   = $this->_em->createQuery($dql);

        return $query->getResult();
    }
}
