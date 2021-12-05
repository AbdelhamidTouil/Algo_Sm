<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Facture;
use App\Entity\Produit;

use App\Entity\Fournisseur;
use Doctrine\ORM\EntityRepository;
use App\Repository\FactureRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
           
            ->add('title')
            ->add('description')
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    'Vente' => 0,
                    'Achat' => 1,

                )))
            ->add('date' , HiddenType::class)
            //->add('date', HiddenType::class, [  
               // ])
            ->add('reference')
            ->add('paymement')
            ->add('users', EntityType::class,[
                'class' => User::class,
                'choice_label' => 'nom',
                'required'    => false,
            ])
            ->add('clients', EntityType::class,[
                'class' => Client::class,
                'choice_label' => 'nom',
                'required'    => false,

            ])
            ->add('fournisseurs', EntityType::class,[
                'class' => Fournisseur::class,
                'choice_label' => 'nom',
                'required'    => false,
            ])
            ->add('produits', EntityType::class,[
                'class' => Produit::class,
                'choice_label' => 'title',
                
            ])

            ->add('prodselect')  
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);

    }
}
