<?php

namespace App\Controller;

use App\Constant\StatusValidation;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/user")
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    private $user_repository;
    private $translator;

    /**
     * UserController constructor.
     * @param UserRepository $user_repository
     * @param TranslatorInterface $translator
     */
    public function __construct(UserRepository $user_repository, TranslatorInterface $translator)
    {
        $this->user_repository = $user_repository;
        $this->translator      = $translator;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $is_user_saved = $this->user_repository->saveUser($user, 'new');
            if ($is_user_saved == StatusValidation::EXIST_STATUS) {
                $this->addFlash('danger', $this->translator->trans('bo.exist'));
                return $this->redirectToRoute('user_new');
            } else {
                $this->addFlash('success', $this->translator->trans('bo.add.succefuly'));
            }
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $is_user_saved = $this->user_repository->saveUser($user, 'edit');
            if ($is_user_saved == StatusValidation::EXIST_STATUS) {
                $this->addFlash('danger', $this->translator->trans('bo.exist.email'));
                return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
            } else {
                $this->addFlash('success', $this->translator->trans('bo.update.succefuly'));
            }
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="user_delete")
     * @param User $user
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(User $user): Response
    {
        $is_deleted = $this->user_repository->deleteUser($user);

        if ($is_deleted) {
            $this->addFlash('success', $this->translator->trans('bo.delete.succefuly'));
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/ajax-list", name="user_ajax_list")
     * @param Request $request
     * @param UserRepository $user_repository
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function ajaxList(Request $request)
    {
        $page        = $request->query->get('start');
        $nb_max_page = $request->query->get('length');
        $search      = $request->query->get('search')['value'];
        $order_by    = $request->query->get('order_by');

        $datas = $this->user_repository->listUser($page, $nb_max_page, $search, $order_by);

        return new JsonResponse([
            'recordsTotal'    => $datas[1],
            'recordsFiltered' => $datas[1],
            'data'            => array_map(function ($_val) {
                return array_values($_val);
            }, $datas[0])
        ]);
    }
}
