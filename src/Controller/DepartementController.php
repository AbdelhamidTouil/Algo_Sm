<?php

namespace App\Controller;
use App\Entity\Departement;
use App\Form\DepartementType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DepartementRepository;
//  Produit
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class DepartementController extends AbstractController
{
      // for flash message
    private $flashMessage;
    public function __construct(
    FlashBagInterface $flashMessage)
 {
     $this->flashMEssage = $flashMessage;
 }// End
 /**
     * create and update departement
     * @Route("departement", name="departement_create")
     * @Route("/departement{id}edit", name="departement_edit")
     */
    public function form( departement $departement = null, Request $request, EntityManagerInterface $entityManager,DepartementRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $departement_list =$repo->findAll();

        $departement_list = $paginator->paginate(
            $departement_list,
            $request->query->getInt('page',1),
            6
            );

        if(!$departement){
            $departement = new departement;
        }
        
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

      $entityManager->persist($departement);
      $entityManager->flush(); 
      $this->flashMEssage->add("success","Enregistrer avec succes"); 
    }
        return $this->render('departement/index.html.twig', [
            'formDepartement' => $form->createView(),
            'editMode' => $departement->getId() !== null,
            'departement' =>$departement_list,
            'qproduit' =>$qproduit,
            'counter'=> $count
           
        ]);
    }


// end create and update

    /**
      * delete departements
     * @Route("/deletedepartement{id}", name="departement_delete")
     */
    public function delete_departement( departement $departement,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($departement);
        $entityManager->flush();

        return $this->redirectToRoute('departement_create',[
            'qproduit' =>$qproduit,
            'counter'=> $count
        ]);
    }






/**
     * create and update departement
     * @Route("/departement{id}", name="departement_show")
     */
    public function show_departemente( departement $departement = null, Request $request, EntityManagerInterface $entityManager,DepartementRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator,$id)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $departement = $repo->find($id);

        

        if(!$departement){
            $departement = new departement;
        }
        
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // for date
            $departement->setCreatedAt(new \DateTimeImmutable());

      $entityManager->persist($departement);
      $entityManager->flush(); 
    
    }
        return $this->render('departement/show.html.twig', [
            'formDepartement' => $form->createView(),
            'editMode' => $departement->getId() !== null,
            "departement" => $departement,
            'qproduit' =>$qproduit,
            'counter'=> $count
           
        ]);
    }

}
