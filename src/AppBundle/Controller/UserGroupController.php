<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 15.03.19
 * Time: 12:44
 */

namespace AppBundle\Controller;


use AppBundle\Entity\UserGroup;
use AppBundle\Exceptions\RequestValidationErrorException;
use AppBundle\Services\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserGroupController extends BasicController
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

    public function indexAction()
    {
        return new JsonResponse(
            $this->userService->findGroupsAll()
        );
    }

    /**
     * @ParamConverter("group", class="AppBundle\Repository\UserGroupRepository")
     *
     * @param Request $request
     * @param UserGroup $group
     * @return JsonResponse
     * @throws RequestValidationErrorException
     */
    public function createAction(Request $request, UserGroup $group)
    {
        $group->fillByRequest($request);
        $this->validate($group);

        return new JsonResponse(
            $this->userService->addGroup($group)
        );
    }

    /**
     * @param Request $request
     * @param UserGroup $group
     * @return JsonResponse
     * @throws RequestValidationErrorException
     */
    public function editAction(Request $request, UserGroup $group)
    {
        $group->fillByRequest($request);

        $this->validate($group);

        return new JsonResponse(
            $this->userService->editGroup($group)
        );
    }


    public function showAction(UserGroup $group)
    {
        $users = $this->userService->findAllUserInGroup($group);

        return new JsonResponse(
            $users->toArray()
        );
    }

    /**
     * @param Request $request
     * @param UserGroup $group
     * @return JsonResponse
     * @throws \AppBundle\Exceptions\NotFoundHttpException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function userAddAction(Request $request, UserGroup $group)
    {
        $userId = $request->get('user_id');
        $this->userService->addUserToGroup($group, $userId);

        return new JsonResponse(['added' => true]);
    }

    /**
     * @param Request $request
     * @param UserGroup $group
     * @return JsonResponse
     * @throws \AppBundle\Exceptions\NotFoundHttpException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function userDelAction(Request $request, UserGroup $group)
    {
        $userId = $request->get('user_id');
        $this->userService->delUserFromGroup($group, $userId);

        return new JsonResponse(['deleted' => true]);
    }

    /**
     * @param UserGroup $group
     * @return JsonResponse
     */
    public function deleteAction(UserGroup $group)
    {
        $this->userService->deleteGroup($group);

        return new JsonResponse(['delete' => true]);
    }
}