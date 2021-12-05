<?php

namespace App\Controller;
use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
//  Produit
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FournisseurController extends AbstractController
{
  // for flash message
  private $flashMessage;
  public function __construct(
  FlashBagInterface $flashMessage)
  {
      $this->flashMEssage = $flashMessage;
  }// End
     /**
     * create and update fournisseur
     * @Route("fournisseur", name="fournisseur_create")
     * @Route("/fournisseur{id}edit", name="fournisseur_edit")
     */
    public function form( fournisseur $fournisseur = null, Request $request, EntityManagerInterface $entityManager,FournisseurRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $fournisseur_list =$repo->findAll();

        $fournisseur_list = $paginator->paginate(
            $fournisseur_list,
            $request->query->getInt('page',1),
            6
            );

        if(!$fournisseur){
            $fournisseur = new fournisseur;
        }
        
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // for date
            $fournisseur->setCreatedAt(new \DateTimeImmutable());

      $entityManager->persist($fournisseur);
      $entityManager->flush();  
      $this->flashMEssage->add("success","Enregistrer avec succes");

    }
        return $this->render('fournisseur/index.html.twig', [
            'formFournisseur' => $form->createView(),
            'editMode' => $fournisseur->getId() !== null,
            'fournisseur' =>$fournisseur_list,
            'qproduit' =>$qproduit,
            'counter'=> $count
        ]);
    }
    /**
      * delete fournisseur
     * @Route("/deletefournisseur{id}", name="fournisseur_delete")
     */
    public function delete_fournisseur( fournisseur $fournisseur,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($fournisseur);
        $entityManager->flush();

        return $this->redirectToRoute('fournisseur_create',[
            'qproduit' =>$qproduit,
            'counter'=> $count
        ]);
    }

/**
     * create and update fournisseur
     * @Route("/fournisseur{id}", name="fournisseur_show")
     */
    public function show_fournisseur( fournisseur $fournisseur = null, Request $request, EntityManagerInterface $entityManager,FournisseurRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator,$id)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $fournisseur = $repo->find($id);

        

        if(!$fournisseur){
            $fournisseur = new fournisseur;
        }
        
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // for date
            $fournisseur->setCreatedAt(new \DateTimeImmutable());

      $entityManager->persist($fournisseur);
      $entityManager->flush(); 
    
    }
        return $this->render('fournisseur/show.html.twig', [
            'formFournisseur' => $form->createView(),
            'editMode' => $fournisseur->getId() !== null,
            "fournisseur" => $fournisseur,
            'qproduit' =>$qproduit,
            'counter'=> $count
           
        ]);
    }

}
