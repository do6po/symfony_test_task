<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 08.02.19
 * Time: 18:30
 */

namespace AppBundle\Controller\Users;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        return new JsonResponse(['action' => 'index']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function showAction(Request $request)
    {
        return new JsonResponse(['action' => 'show']);
    }
}