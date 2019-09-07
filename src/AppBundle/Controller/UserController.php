<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Recipe controller.
 *
 * @Route("admin/user")
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction(UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:Users')->findAll();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new recipe entity.
     *
     * @Route("/new", name="recipe_new")
     * @Method({"GET", "POST"})
     */
   /* public function newAction(Request $request, UserInterface $users)
    {
        $recipe = new Recipe();
        $form = $this->createForm('AppBundle\Form\RecipeType', $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $userEntity = $em->getRepository('AppBundle:Users')->findOneBy(array("name" => $users->getUsername()));
            $recipe->setUser($userEntity);
            $em->persist($recipe);
            $em->flush();

            return $this->redirectToRoute('recipe_show', array('id' => $recipe->getId()));
        }

        return $this->render('recipe/new.html.twig', array(
            'recipe' => $recipe,
            'form' => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(Users $user, UserInterface $users)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
            'current_user' => $user->getUsername(),
        ));
    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/{id}/edit", name="recipe_edit")
     * @Method({"GET", "POST"})
     */
    /*public function editAction(Request $request, Recipe $recipe, UserInterface $users)
    {
        if ($recipe->getUser()->getUsername() != $users->getUsername()) {
            return $this->redirectToRoute('recipe_index');
        }

        $deleteForm = $this->createDeleteForm($recipe);
        $editForm = $this->createForm('AppBundle\Form\RecipeType', $recipe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recipe_show', array('id' => $recipe->getId()));
        }

        return $this->render('recipe/edit.html.twig', array(
            'recipe' => $recipe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }*/

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}/delete", name="user_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, Users $user, UserInterface $users)
    {
       /* if ($recipe->getUser()->getUsername() != $users->getUsername()) {
            return $this->redirectToRoute('recipe_index');
        }*/


        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($user->getRecipes() as $recipe){
                $em ->remove($recipe);
            }
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('recipe_index');
    }

    /**
     * Creates a form to delete a recipe entity.
     *
     * @param User $user The recipe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Users $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getName())))
            ->getForm();
    }

}
