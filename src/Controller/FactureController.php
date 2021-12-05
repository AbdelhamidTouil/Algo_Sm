<?php

namespace App\Controller;
use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
//  qproduit
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;

use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Dompdf\Dompdf;
use Dompdf\Options;


class FactureController extends AbstractController
{
      // for flash message
      private $flashMessage;
      public function __construct(
      FlashBagInterface $flashMessage)
      {
          $this->flashMEssage = $flashMessage;
      }
     // End
    /**
     * @Route("/facture", name="list_facture")
     */
    public function index( Request $request, PaginatorInterface $paginator,  FactureRepository $repo, ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $facture = $repo->findAll();

        $facture = $paginator->paginate(
        $facture,
        $request->query->getInt('page',1),
        6
        );

        return $this->render('facture/index.html.twig', [
            'controller_name' => 'FactureController',
            'facture' =>$facture,
            'qproduit' => $qproduit,
            'counter'=> $count 
        ]);
    }

     
     /**
      * create and update facture
     * @Route("facturecreate", name="facture_create")
     * @Route("/facture{id}edit", name="facture_edit")
     */
    public function form( facture $facture = null, Request $request, EntityManagerInterface $entityManager,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        if(!$facture){
            $facture = new facture;
        }
      
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $facture->setDate(new \DateTimeImmutable());
            
      
            $entityManager->persist($facture);
            $entityManager->flush();  
            $this->flashMEssage->add("success","");
            return $this->redirectToRoute('list_facture');

    }
        return $this->render('facture/create.html.twig', [
            'formFacture' => $form->createView(),
            'editMode' => $facture->getId() !== null,
            "qproduit" => $qproduit,
            'counter'=> $count 
            
        ]);
    }
    /**
      * delete factures
     * @Route("/deletefacture{id}", name="facture_delete")
     */
    public function delete_facture( facture $facture)
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($facture);
        $entityManager->flush();

        return $this->redirectToRoute('list_facture');
    }

    /**
      * showing facture
     * @Route("/facture{id}", name="facture_show")
     */
    public function show_facture(FactureRepository $repo,ProduitRepository $rep,$id)
    {
       
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $facture = $repo->find($id);
        return $this->render('facture/show.html.twig', [
            "facture" => $facture,
            "qproduit" => $qproduit,
            'counter'=> $count,
        
           

        ]);
        
    }
    
    /**
      * Imprimer facture
     * @Route("/facture/{id}", name="facture_imprimer")
     */
    public function imprimer_facture(FactureRepository $repo,$id)
    {
        //on definit les option du pdf
        $pdfOptions = new Options();
        //police par defaut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $facture = $repo->find($id);
        $context =stream_context_create([
            'ssl' =>[
                'verify_peer' => FALSE,
                'verify_peer_name' =>FALSE,
                'allow_self_signed' =>TRUE
            ]
            ]);
        $dompdf->setHttpContext($context);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('facture/imprimer.html.twig',[
            "facture" => $facture
        ]);
       
        $html .= '<link type="text/css" href="fac.css" rel="stylesheet" />';
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        //on genere un nom de fichier
        
        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf",[
            "Attachment" => true
        ]);
        return new Response();
       
    }
    
}
