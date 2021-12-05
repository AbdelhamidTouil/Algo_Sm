<?php

namespace App\Controller;
use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
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

class ClientController extends AbstractController
{
  // for flash message
  private $flashMessage;
  public function __construct(
  FlashBagInterface $flashMessage)
  {
      $this->flashMEssage = $flashMessage;
  }// End
     /**
     * create and update client
     * @Route("client", name="client_create")
     * @Route("/client{id}edit", name="client_edit")
     */
    public function form( client $client = null, Request $request, EntityManagerInterface $entityManager,ClientRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $client_list =$repo->findAll();

        $client_list = $paginator->paginate(
            $client_list,
            $request->query->getInt('page',1),
            6
            );

        if(!$client){
            $client = new client;
        }
        
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // for date
            $client->setCreatedAt(new \DateTimeImmutable());

      $entityManager->persist($client);
      $entityManager->flush();  
      $this->flashMEssage->add("success","Enregistrer avec succes");

    }
        return $this->render('client/index.html.twig', [
            'formClient' => $form->createView(),
            'editMode' => $client->getId() !== null,
            'client' =>$client_list,
            'qproduit' =>$qproduit,
            'counter'=> $count
        ]);
    }
    /**
      * delete clients
     * @Route("/deleteclient{id}", name="client_delete")
     */
    public function delete_client( client $client,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($client);
        $entityManager->flush();

        return $this->redirectToRoute('client_create',[
            'qproduit' =>$qproduit,
            'counter'=> $count
        ]);
    }

   /**
     * create and update client
     * @Route("/client{id}", name="client_show")
     */
    public function show_client( client $client = null, Request $request, EntityManagerInterface $entityManager,ClientRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator,$id)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $client = $repo->find($id);

        

        if(!$client){
            $client = new client;
        }
        
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // for date
            $client->setCreatedAt(new \DateTimeImmutable());

      $entityManager->persist($client);
      $entityManager->flush(); 
    
    }
        return $this->render('client/show.html.twig', [
            'formClient' => $form->createView(),
            'editMode' => $client->getId() !== null,
            "client" => $client,
            'qproduit' =>$qproduit,
            'counter'=> $count
           
        ]);
    }
}
