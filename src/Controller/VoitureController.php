<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/voiture")
 * Class VoitureController
 * @package App\Controller
 */
class VoitureController extends AbstractController
{
    private $voirture_repository;
    private $translator;

    /**
     * VoitureController constructor.
     * @param VoitureRepository $voiture_repository
     * @param TranslatorInterface $translator
     */
    public function __construct(VoitureRepository $voiture_repository, TranslatorInterface $translator)
    {
        $this->voirture_repository = $voiture_repository;
        $this->translator          = $translator;
    }

    /**
     * @Route("/", name="voiture_index", methods={"GET"})
     */
    public function index()
    {
        return $this->render('voiture/index.html.twig');
    }

    /**
     * @Route("/new", name="voiture_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $voiture = new Voiture();
        $form    = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $is_create = $this->voirture_repository->saveVoiture($voiture, 'new');
            if ($is_create) {
                $this->addFlash('success', $this->translator->trans('bo.add.succefuly'));
            }
            return $this->redirectToRoute('voiture_index');
        }

        return $this->render('voiture/new.html.twig', [
            'voiture' => $voiture,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="voiture_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Voiture $voiture): Response
    {
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $is_update = $this->voirture_repository->saveVoiture($voiture, 'update');
            if ($is_update) {
                $this->addFlash('success', $this->translator->trans('bo.update.succefuly'));
            }
            return $this->redirectToRoute('voiture_index');
        }

        return $this->render('voiture/edit.html.twig', [
            'voiture' => $voiture,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="voiture_delete", methods={"POST"})
     */
    public function delete(Request $request, Voiture $voiture): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $voiture->getId(), $token)) {
            $status_deleted = $this->voirture_repository->deleteVoiture($voiture);
            if ($status_deleted) {
                $this->addFlash('success', $this->translator->trans('bo.delete.succefuly'));
            }
        }

        return $this->redirectToRoute('voiture_index');
    }

    /**
     * @Route("/ajax-list", name="voiture_ajax_list")
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
        $datas = $this->voirture_repository->listVoiture($page, $nb_max_page, $search, $order_by);

        return new JsonResponse([
            'recordsTotal'    => $datas[1],
            'recordsFiltered' => $datas[1],
            'data'            => array_map(function ($_val) {
                return array_values($_val);
            }, $datas[0])
        ]);
    }
}
