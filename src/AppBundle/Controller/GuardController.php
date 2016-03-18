<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class GuardController extends Controller
{
    /**
     * @return RedirectResponse|Response
     */
    public function loginAction()
    {
        $user = $this->getUser();

        // already authenticated
        if ($user instanceof UserInterface) {
            return $this->redirectToRoute('app_reporting');
        }
        $exception = $this
            ->get('security.authentication_utils')
            ->getLastAuthenticationError();
        $error = $exception ? $exception->getMessage() : null;
        $form = $this->createForm(LoginType::class);

        return $this->render('@App/Guard/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    public function loginCheckAction()
    {
        // will never be executed
        return new Response();
    }
}
