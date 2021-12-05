<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Form\FormError;

class UserController extends AbstractController
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
     * @Route("/user", name="user_create")
     * @Route("/register{id}edit", name="user_edit")
     */
    public function form( User $user = null,Request $request, UserPasswordEncoderInterface $userPasswordEncoderInterface,UserRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $user_list = $repo->findAll();

        $user_list = $paginator->paginate(
            $user_list,
            $request->query->getInt('page',1),
            6
            );

        if(!$user){
            $user = new User();
        }
        
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

           $file = $user->getImage();
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
            $user->setImage($fileName);
           
            // encode the plain password
            $user->setPassword(
            $userPasswordEncoderInterface->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //On genere le token d'activation
            $user->setActivationToken(md5(uniqid()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->flashMEssage->add("success","Enregistrer avec succes");
            // do anything else you need here, like send an email

        }

           return $this->render('user/index.html.twig', [
            'registrationForm' => $form->createView(),
            'editMode' => $user->getId() !== null,
            'user' =>$user_list,
            'qproduit' =>$qproduit,
            'counter'=> $count 
        ]);
    }
  

    /**
      * delete product
     * @Route("/delete/user/{id}", name="user_delete")
     */
    public function delete_user( user $user,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

          return $this->redirectToRoute('user_create',[
            'qproduit' =>$qproduit,
            'counter'=> $count
        ]);
    }


/**
     * Show
     * @Route("/user{id}", name="user_show")
     */
    public function show_user( user $user = null, Request $request, EntityManagerInterface $entityManager,UserRepository $repo,ProduitRepository $rep,PaginatorInterface $paginator,$id)
    {
        $count =$rep->lowquantitycount();
        $qproduit = $rep->findAll();
        $user = $repo->find($id);

        if(!$user){
            $user = new user;
        }
        
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
           
         

        $entityManager->persist($user);
        $entityManager->flush(); 
        
    }
        return $this->render('user/show.html.twig', [
            'registrationForm' => $form->createView(),
            'editMode' => $user->getId() !== null,
            "user" => $user,
            'qproduit' =>$qproduit,
            'counter'=> $count
           
        ]);
    }
  

      /**
       * show user photo
     * @Route("/photo{id}", name="photo")
     */
    public function photo(UserRepository $repo,$id,ProduitRepository $rep)
    {
        $count =$rep->lowquantitycount();
        $user = $repo->find($id);
        $qproduit = $rep->findAll();
       
        return $this->render('user/photo.html.twig', [
            "user" => $user,
            'qproduit' =>$qproduit,
            'counter'=> $count 

        ]);
    }


        /**
      * Activation Token
     * @Route("/activation/{token}", name="activation")
     */
   /* public function activation($token,UserRepository $userRepo)
    {
       //on vérifier si un utilisateur a ce token
       $user = $userRepo->findOneBy(['activation_token'=>$token]);
       // si aucun utilisateur néxiste avec ce token
       if(!$user)
       {
           //Erreur 404
           throw $this->createNotFoundException('cet utilisateur nexite pas');
       }
       //on supprime le token
       $user->setActivationToken(null);
       $entityManager = $this->getDoctrine()->getManager();
       $entityManager->persist($user);
       $entityManager->flush();
       //on envoie un message flush
       $this->addFlash('message','vous avez bien activé votre compte');
       //on retourn un message a l'acceuil
          return $this->redirectToRoute('dashbord');
    }*/
}


