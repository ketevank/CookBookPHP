<?php
// src/AppBundle/Form/UserType.php
/*
 * formularz danych użytkownika (rejestracja)
 */
namespace AppBundle\Form;

use AppBundle\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
$builder
->add('name', TextType::class)
->add('password', RepeatedType::class, [
'type' => PasswordType::class,
'first_options'  => ['label' => 'Password'],
'second_options' => ['label' => 'Repeat Password'],
])
;
}

public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
'data_class' => Users::class,
]);
}
}
