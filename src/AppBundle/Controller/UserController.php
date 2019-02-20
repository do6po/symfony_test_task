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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function showAction(Request $request)
    {
        return new JsonResponse(['action' => 'show']);
    }
}