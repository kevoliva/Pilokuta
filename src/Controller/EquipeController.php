<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\UserRepository;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/equipe")
 */
class EquipeController extends AbstractController
{

    /**
     * @Route("/new", name="equipe_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipe);
            $entityManager->flush();

            return $this->redirectToRoute('equipe_index');
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="equipe_show", methods={"GET"})
     */
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    /**
     * @Route("/{id}", name="equipe_delete", methods={"GET","DELETE"})
     */
    public function delete(Request $request, Equipe $equipe): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $idPoule=$equipe->getPoule()->getId();
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('equipes_index_poule', ['idPoule' => $idPoule]);
    }


     /**
        * @Route("/modifier/{id}", name="modify_equipe", methods={"GET", "POST"})
        */
        public function indexModifPoule(Request $request, EntityManagerInterface $manager, Equipe $equipe)
        {
            //Création du formulaire permettant de modifier une poule
            $formulairePoule = $this->createForm(EquipeType::class, $equipe);
    
            /* On demande au formulaire d'analyser la dernière requête Http. Si le tableau POST contenu dans cette requête contient
            des variables nom, activite, etc. Alors la méthode handleRequest() recupère les valeurs de ces variables et les
            affecte à l'objet $equipe. */
            $formulairePoule->handleRequest($request);
    
            if ($formulairePoule->isSubmitted() && $formulairePoule->isValid())
            {
                //Enregistrer la série en base de données
                $manager->persist($equipe);
                $manager->flush();
    
                //Rediriger l'utilisateur vers la page d'accueil
                return $this->redirectToRoute('equipes_index_poule', ['idPoule' => $equipe->getPoule()->getId()]);
            }
    
            //Afficher la page présentant le formulaire d'ajout d'une serie
            return $this->render('equipe/ajoutModifEquipe.html.twig', ['vueFormulaire' => $formulairePoule->createView(), 
            'action'=>"modifier"]);
        }

        /**
        * @Route("/{idEquipe}/user/ajouter", name="add_equipe_user", methods={"GET", "POST"})
        */
        public function indexAjoutUser(Request $request, EntityManagerInterface $manager, equipeRepository $repositoryEquipe, $idEquipe)
        {
            //Création d'une poule vierge qui sera remplie par le formulaire
            $equipe = new Equipe();
            $equipe = $repositoryEquipe->find($idEquipe);
            $user = new User();
            $user->setUser($user);
    
            //Création du formulaire permettant de saisir un user
            $formulaireUser = $this->createForm(UserType::class, $user);
    
            /* On demande au formulaire d'analyser la dernière requête Http. Si le tableau POST contenu dans cette requête contient
            des variables nom, activite, etc. Alors la méthode handleRequest() recupère les valeurs de ces variables et les
            affecte à l'objet $poule. */
            $formulaireUser->handleRequest($request);

    
            if ($formulaireUser->isSubmitted() && $formulaireUser->isValid())
            {
                //Enregistrer la poule en base de données
                $manager->persist($user);
                $manager->flush();
    
                //Rediriger l'utilisateur vers la page des equipes
                return $this->redirectToRoute('equipes_index_poule', ['idPoule' => $equipe->getPoule()->getId()]);
            }
    
            //Afficher la page présentant le formulaire d'ajout d'une equipe
            return $this->render('equipe/ajoutModifEquipe.html.twig', ['vueFormulaire' => $formulaireUser->createView(), 
            'action'=>"ajouter"]);
        }  
}
