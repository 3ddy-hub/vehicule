<?php

namespace App\Controller;

use App\Entity\Proprietaire;
use App\Form\ProprietaireType;
use App\Repository\ProprietaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/proprietaire")
 */
class ProprietaireController extends AbstractController
{
    private $propretary_repository;
    private $translator;

    /**
     * ProprietaireController constructor.
     * @param ProprietaireRepository $proprietaire_repository
     * @param TranslatorInterface $translator
     */
    public function __construct(ProprietaireRepository $proprietaire_repository, TranslatorInterface $translator)
    {
        $this->propretary_repository = $proprietaire_repository;
        $this->translator            = $translator;
    }

    /**
     * @Route("/", name="proprietaire_index")
     */
    public function index()
    {
        return $this->render('proprietaire/index.html.twig');
    }

    /**
     * @Route("/new", name="proprietaire_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $proprietaire = new Proprietaire();
        $form         = $this->createForm(ProprietaireType::class, $proprietaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $is_create = $this->propretary_repository->savePropretary($proprietaire, 'new');
            if ($is_create) {
                return $this->redirectToRoute('proprietaire_index');
            }
        }

        return $this->render('proprietaire/new.html.twig', [
            'proprietaire' => $proprietaire,
            'form'         => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="proprietaire_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Proprietaire $proprietaire): Response
    {
        $form = $this->createForm(ProprietaireType::class, $proprietaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $is_update = $this->propretary_repository->savePropretary($proprietaire, 'update');
            if ($is_update) {
                return $this->redirectToRoute('proprietaire_index');
            }
        }

        return $this->render('proprietaire/edit.html.twig', [
            'proprietaire' => $proprietaire,
            'form'         => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="proprietaire_delete")
     */
    public function delete(Proprietaire $proprietaire)
    {
        $status_deleted = $this->propretary_repository->deletePropretary($proprietaire);
        if ($status_deleted) {
            $this->addFlash('success', $this->translator->trans('bo.delete.succefuly'));
        }

        return $this->redirectToRoute('proprietaire_index');
    }

    /**
     * @Route("/list-ajax", name="propretary_ajax_list")
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function ajaxList(Request $request)
    {
        $page        = $request->query->get('start');
        $nb_max_page = $request->query->get('length');
        $search      = $request->query->get('search')['value'];
        $order_by    = $request->query->get('order_by');
        $datas       = $this->propretary_repository->listModele($page, $nb_max_page, $search, $order_by);

        return new JsonResponse([
            'recordsTotal'    => $datas[1],
            'recordsFiltered' => $datas[1],
            'data'            => array_map(function ($_val) {
                return array_values($_val);
            }, $datas[0])
        ]);
    }
}
