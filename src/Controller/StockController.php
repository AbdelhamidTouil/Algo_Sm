<?php
namespace App\Controller;
use App\Entity\Stock;
use App\Form\StockType;
use App\Repository\StockRepository;
//  qproduit
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

class StockController extends AbstractController
{
      // for flash message
      private $flashMessage;
      public function __construct(
      FlashBagInterface $flashMessage)
      {
          $this->flashMEssage = $flashMessage;
      }// End
     /**
     * create and update stock
     * @Route("stock", name="stock_create")
     * @Route("/stock{id}edit", name="stock_edit")
     */
    public function form( stock $stock = null, Request $request, EntityManagerInterface $entityManager,StockRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $stock_list =$repo->findAll();

        $stock_list = $paginator->paginate(
            $stock_list,
            $request->query->getInt('page',1),
            6
            );

        if(!$stock){
            $stock = new stock;
        }
        
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // for date
            $stock->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($stock);
        $entityManager->flush();  
        $this->flashMEssage->add("success","Enregistrer avec succes");

        }
        return $this->render('stock/index.html.twig', [
            'formStock' => $form->createView(),
            'editMode' => $stock->getId() !== null,
            'stock' => $stock_list,
            "qproduit" => $qproduit,
            'counter'=> $count 
            
        ]);


        
    }

    /**
      * delete product
     * @Route("/deletestock{id}", name="stock_delete")
     */
    public function delete_stock( stock $stock,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($stock);
        $entityManager->flush();

        return $this->redirectToRoute('stock_create',[
            'qproduit' =>$qproduit,
            'counter'=> $count
        ]);
    }


   /**
     * create and update stock
     * @Route("/stock{id}", name="stock_show")
     */
    public function show_stock( stock $stock = null, Request $request, EntityManagerInterface $entityManager,StockRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator,$id)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $stock = $repo->find($id);

        

        if(!$stock){
            $stock = new stock;
        }
        
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // for date
            $stock->setCreatedAt(new \DateTimeImmutable());

      $entityManager->persist($stock);
      $entityManager->flush(); 
    
    }
        return $this->render('stock/show.html.twig', [
            'formStock' => $form->createView(),
            'editMode' => $stock->getId() !== null,
            "stock" => $stock,
            'qproduit' =>$qproduit,
            'counter'=> $count
           
        ]);
    }
}
