<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ingredient;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Ingredient controller.
 *
 * @Route("ingredient")
 */
class IngredientController extends Controller
{
    /**
     * Lists all ingredient entities.
     *
     * @Route("/", name="ingredient_index")
     * @Method("GET")
     */
    public function indexAction(UserInterface $users)
    {
        $em = $this->getDoctrine()->getManager();

        $ingredients = $em->getRepository('AppBundle:Ingredient')->findBy(array("user" => $users->getUsername()));

        return $this->render('ingredient/index.html.twig', array(
            'ingredients' => $ingredients,
        ));
    }

    /**
     * Creates a new ingredient entity.
     *
     * @Route("/new", name="ingredient_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, UserInterface $users)
    {
        $ingredient = new Ingredient();
        $form = $this->createForm('AppBundle\Form\IngredientType', $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $userEntity = $em->getRepository('AppBundle:Users')->findOneBy(array("name" => $users->getUsername()));
            $ingredient->setUser($userEntity);
            $em->persist($ingredient);
            $em->flush();

            return $this->redirectToRoute('ingredient_show', array('id' => $ingredient->getId()));
        }

        return $this->render('ingredient/new.html.twig', array(
            'ingredient' => $ingredient,
            'form' => $form->createView(),
            'error' => array(
                "messageKey" => ""
            )
        ));
    }

    /**
     * Finds and displays a ingredient entity.
     *
     * @Route("/{id}", name="ingredient_show")
     * @Method("GET")
     */
    public function showAction(Ingredient $ingredient)
    {
        $deleteForm = $this->createDeleteForm($ingredient);

        return $this->render('ingredient/show.html.twig', array(
            'ingredient' => $ingredient,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ingredient entity.
     *
     * @Route("/{id}/edit", name="ingredient_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Ingredient $ingredient, UserInterface $users)
    {
        if ($ingredient->getUser()->getUsername() != $users->getUsername()) {
            return $this->redirectToRoute('recipe_index');
        }

        $deleteForm = $this->createDeleteForm($ingredient);
        $editForm = $this->createForm('AppBundle\Form\IngredientType', $ingredient);
        $editForm->handleRequest($request);

        $errors = array(
            "messageKey" => ""
        );

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                // 4) save the ingredient
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($ingredient);
                $entityManager->flush();
            }
            catch(UniqueConstraintViolationException $e){
                $errors = array(
                    "messageKey" => "inv.ingredient.duplicate",
                );
                return $this->render(
                    'ingredient/new.html.twig',
                    ['form' => $editForm->createView(), 'error'=> $errors]
                );
            }

            return $this->redirectToRoute('ingredient_edit', array('id' => $ingredient->getId()));
        }

        return $this->render('ingredient/edit.html.twig', array(
            'ingredient' => $ingredient,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'error'=> $errors
        ));
    }

    /**
     * Deletes a ingredient entity.
     *
     * @Route("/{id}/delete", name="ingredient_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, Ingredient $ingredient, UserInterface $users)
    {
        if ($ingredient->getUser()->getUsername() != $users->getUsername()) {
            return $this->redirectToRoute('recipe_index');
        }

        $form = $this->createDeleteForm($ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ingredient);
            $em->flush();
        }

        return $this->redirectToRoute('ingredient_index');
    }

    /**
     * Creates a form to delete a ingredient entity.
     *
     * @param Ingredient $ingredient The ingredient entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ingredient $ingredient)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ingredient_delete', array('id' => $ingredient->getId())))
            ->getForm()
        ;
    }
}
