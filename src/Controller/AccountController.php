<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */

class AccountController extends BaseController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function index()
    {
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    /**
     * @Route("/api/account", name="api_account")
     */

    public function accountApi()
    {
        $user = $this->getUser();

        return $this->json($user, 200, [],[
            'groups' => ['main']
        ]);
    }
}
