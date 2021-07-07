<?php

namespace App\Controller;

use App\Entity\Modele;
use App\Form\ModeleType;
use App\Repository\ModeleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/modele")
 * Class ModeleController
 * @package App\Controller
 */
class ModeleController extends AbstractController
{
    private $modele_repository;
    private $translator;

    /**
     * ModeleController constructor.
     * @param ModeleRepository $modele_repository
     * @param TranslatorInterface $translator
     */
    public function __construct(ModeleRepository $modele_repository, TranslatorInterface $translator)
    {
        $this->modele_repository = $modele_repository;
        $this->translator        = $translator;
    }

    /**
     * @Route("/", name="modele_index")
     */
    public function index()
    {
        return $this->render('modele/index.html.twig');
    }

    /**
     * @Route("/new", name="modele_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $modele = new Modele();
        $form   = $this->createForm(ModeleType::class, $modele);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $is_create = $this->modele_repository->saveModele($modele, 'new');
            if ($is_create) {
                $this->addFlash('success', $this->translator->trans('bo.add.succefuly'));
            }

            return $this->redirectToRoute('modele_index');
        }

        return $this->render('modele/new.html.twig', [
            'modele' => $modele,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="modele_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Modele $modele): Response
    {
        $form = $this->createForm(ModeleType::class, $modele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $is_update = $this->modele_repository->saveModele($modele, 'update');
            if ($is_update) {
                $this->addFlash('success', $this->translator->trans('bo.update.succefuly'));
            }
            return $this->redirectToRoute('modele_index');
        }

        return $this->render('modele/edit.html.twig', [
            'modele' => $modele,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="modele_delete")
     */
    public function delete(Modele $modele)
    {
        $status_deleted = $this->modele_repository->deleteVoiture($modele);
        if ($status_deleted) {
            $this->addFlash('success', $this->translator->trans('bo.delete.succefuly'));
        }

        return $this->redirectToRoute('proprietaire_index');
    }

    /**
     * @Route("/list-ajax", name="model_ajax_list")
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
        $datas       = $this->modele_repository->listModele($page, $nb_max_page, $search, $order_by);

        return new JsonResponse([
            'recordsTotal'    => $datas[1],
            'recordsFiltered' => $datas[1],
            'data'            => array_map(function ($_val) {
                return array_values($_val);
            }, $datas[0])
        ]);
    }
}