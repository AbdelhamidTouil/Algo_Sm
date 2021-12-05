<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ProduitController extends AbstractController
{
      // for flash message
      private $flashMessage;
      public function __construct(
      FlashBagInterface $flashMessage)
      {
          $this->flashMEssage = $flashMessage;
      }// End
     /**
     * create and update product
     * @Route("produit", name="produit_create")
     * @Route("/produit{id}edit", name="produit_edit")
     */
    public function form( Produit $produit = null, Request $request, ProduitRepository $repo, EntityManagerInterface $entityManager,ProduitRepository $rep,PaginatorInterface $paginator)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $produit_list = $repo->findAll();

        $produit_list = $paginator->paginate(
            $produit_list,
            $request->query->getInt('page',1),
            6
            );

        if(!$produit){
            $produit = new produit;
        }
        
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $entityManager =$this->getDoctrine()->getManager();
            $produit->setImage($fileName);
        
    
        $entityManager->persist($produit);
        $entityManager->flush();
        $entityManager = $this->getDoctrine()->getManager();
        $this->flashMEssage->add("success","Enregistrer avec succes");
  
}
        return $this->render('produit/index.html.twig', [
            'formProduit' => $form->createView(),
            'editMode' => $produit->getId() !== null,
            'produit' => $produit_list,
            'qproduit' => $qproduit,
            'counter'=> $count 
        ]);
    }

    /**
      * delete product
     * @Route("/deleteproduit{id}", name="produit_delete")
     */
    public function delete_produit( produit $produit,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($produit);
        $entityManager->flush();

        return $this->redirectToRoute('produit_create',[
            'qproduit' =>$qproduit,
            'counter'=> $count
        ]);
    }

    /**
       * show product
     * @Route("/produit{id}", name="produit_show")
     */
    public function show_produit(ProduitRepository $repo,$id,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $produit = $repo->find($id);
        $qproduit = $repo->findAll();
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'qproduit' => $qproduit,
            'counter'=> $count 
               ]);
    }


}
