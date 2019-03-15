<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 08.02.19
 * Time: 18:30
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Exceptions\NotFoundHttpException;
use AppBundle\Exceptions\RequestValidationErrorException;
use AppBundle\Services\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BasicController
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

    /**
     * @ParamConverter("user", class="AppBundle\Repository\UserRepository")
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws RequestValidationErrorException
     */
    public function createAction(Request $request, User $user)
    {
        $user->fillByRequest($request);

        $this->validate($user);

        return new JsonResponse(
            $this->userService->add($user)
        );
    }

    /**
     * @param Request $request
     * @param User|null $user
     * @return JsonResponse
     * @throws NotFoundHttpException
     * @throws RequestValidationErrorException
     */
    public function editAction(Request $request, User $user = null)
    {
        if (is_null($user)) {
            throw new NotFoundHttpException([
                'error' => sprintf('User with id: %s - not found!', $request->get('id'))
            ]);
        }

        $user->fillByRequest($request);

        $this->validate($user);

        return new JsonResponse(
            $this->userService->edit($user)
        );
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function deleteAction(User $user)
    {
        $this->userService->delete($user);

        return new JsonResponse(['delete' => true]);
    }
}