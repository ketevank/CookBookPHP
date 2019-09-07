<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Users;
use AppBundle\Form\UserType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SetupController extends Controller
{
    /**
     * @Route("/setup", name="admin_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) build the form
        $user = new Users();
        $user->setName("admin");
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() &&
            $entityManager->getRepository('AppBundle:Users')
                ->findOneBy(array("name" => "admin")) == null/*&& $form->isValid()*/) { //TODO FIX THIS
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // 4) save the User!
            $user->setRoles([$user::ROLE_ADMIN]);
            $entityManager->persist($user);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render(
            'registration/register.html.twig',
            ['form' => $form->createView()]
        );
    }
}
