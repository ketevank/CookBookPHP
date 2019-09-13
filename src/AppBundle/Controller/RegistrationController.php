<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Users;
use AppBundle\Form\UserType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new Users();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        $errors = array(
            "messageKey" => "",
            "messageData" => array()
        );

        if ($form->isSubmitted() && $form->isValid()) { //TODO FIX THIS
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            try {
                // 4) save the User!
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
            catch(UniqueConstraintViolationException $e){
                $errors = array(
                    "messageKey" => "inv.user.duplicate",
                );
                return $this->render(
                    'registration/register.html.twig',
                    ['form' => $form->createView(), 'error'=> $errors]
                );
            }

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render(
            'registration/register.html.twig',
            ['form' => $form->createView(), 'error'=> $errors]
        );
    }
}
