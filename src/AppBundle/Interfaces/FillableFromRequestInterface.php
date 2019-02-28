<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 28.02.19
 * Time: 14:26
 */

namespace AppBundle\Interfaces;


use Symfony\Component\HttpFoundation\Request;

interface FillableFromRequestInterface
{
    /**
     * @param Request $request
     * @return self
     */
    public function fillByRequest(Request $request);
}