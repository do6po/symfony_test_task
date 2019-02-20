<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 08.02.19
 * Time: 18:30
 */

namespace AppBundle\Controller;


use AppBundle\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return JsonResponse
     */
    public function indexAction()
    {
        return new JsonResponse(
            $this->userService->findAll()
        );
    }

    public function createAction(Request $request)
    {
        $name = $request->get('name');
        $email = $request->get('email');

        return new JsonResponse(
            $this->userService->add($name, $email)
        );
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function editAction(int $id, Request $request)
    {
        $name = $request->get('name');
        $email = $request->get('email');

        return new JsonResponse(
            $this->userService->edit($id, $name, $email)
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function deleteAction(int $id)
    {
        return new JsonResponse(
            $this->userService->delete($id)
        );
    }
}