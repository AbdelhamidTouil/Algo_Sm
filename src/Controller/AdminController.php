<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//  qproduit
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;

class AdminController extends AbstractController
{
    /**
     * displaying dashboord
     * @Route("/admin", name="dashbord")
     */
    public function index(ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit =$rep->findAll();
        return $this->render('admin/dashbord.html.twig', [
            'controller_name' => 'AdminController',
            'qproduit' =>$qproduit,
            'counter'=> $count 
            
        ]);
    }

     
















    
}
