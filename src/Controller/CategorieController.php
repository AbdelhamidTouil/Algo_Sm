<?php
namespace App\Controller;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Form\RegistrationFormType;
// produit
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CategorieController extends AbstractController
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
     * @Route("/categorie", name="categorie_create")
     * @Route("/register{id}edit", name="categorie_edit")
     */
    public function form( Categorie $categorie = null,Request $request,CategorieRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $categorie_list = $repo->findAll();

        $categorie_list = $paginator->paginate(
            $categorie_list,
            $request->query->getInt('page',1),
            6
            );

        if(!$categorie){
            $categorie = new Categorie();
        }
        
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // for date
            $categorie->setCreatedAt(new \DateTimeImmutable());
            
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
            $categorie->setImage($fileName);
        
    
        $entityManager->persist($categorie);
        $entityManager->flush();
        $entityManager = $this->getDoctrine()->getManager();
        $this->flashMEssage->add("success","Enregistrer avec succes");
  
}

           return $this->render('categorie/index.html.twig', [
            'formCategorie' => $form->createView(),
            'editMode' => $categorie->getId() !== null,
            'categorie' =>$categorie_list,
            'qproduit' =>$qproduit,
            'counter'=> $count 
        ]);
    }
  

    /**
      * delete product
     * @Route("/delete/categorie/{id}", name="categorie_delete")
     */
    public function delete_categorie( categorie $categorie,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();

          return $this->redirectToRoute('categorie_create',[
            'qproduit' =>$qproduit,
            'counter'=> $count
        ]);
    }

    /**
       * show categorie
     * @Route("/categorie{id}", name="categorie_show")
     */
    public function show_categorie(CategorieRepository $repo,$id,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $categorie = $repo->find($id);
        $qproduit = $rep->findAll();
       
        return $this->render('categorie/show.html.twig', [
            "categorie" => $categorie,
            'qproduit' =>$qproduit,
            'counter'=> $count 

        ]);
    }
  

     
}


