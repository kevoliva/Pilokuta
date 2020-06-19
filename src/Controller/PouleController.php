<?php

namespace App\Controller;

use App\Entity\Poule;
use App\Entity\Equipe;
use App\Form\PouleType;
use App\Form\EquipeType;
use App\Repository\PouleRepository;
use App\Repository\EquipeRepository;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/poule")
 */
class PouleController extends AbstractController
{

    /**
     * @Route("/new", name="poule_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $poule = new Poule();
        $form = $this->createForm(PouleType::class, $poule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($poule);
            $entityManager->flush();

            return $this->redirectToRoute('poule_index');
        }

        return $this->render('poule/new.html.twig', [
            'poule' => $poule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="poule_show", methods={"GET"})
     */
    public function show(Poule $poule): Response
    {
        return $this->render('poule/show.html.twig', [
            'poule' => $poule,
        ]);
    }

    /**
     * @Route("/{id}", name="poule_delete", methods={"GET","DELETE"})
     */
    public function delete(Request $request, Poule $poule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$poule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $idSerie=$poule->getSerie()->getId();
            $poule->clearEquipes();
            $entityManager->remove($poule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('poules_index_serie', ['idSerie' => $idSerie]);
    }

    /**
    * @Route("/{idPoule}/equipes", name="equipes_index_poule", methods={"GET", "POST"})
    */
    public function getEquipeByPoule(PouleRepository $repositoryPoule,EquipeRepository $repositoryEquipe, $idPoule): Response
    {        
        $poule = $repositoryPoule->find($idPoule);       
        $equipes = $repositoryEquipe->findEquipesByPoule($idPoule);
        $equipe = New Equipe();
        $equipe->setPoule($poule);

        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipes,
            'equipeAjout'=>$equipe
            ]);
    }


        /**
        * @Route("/{idPoule}/equipe/ajouter", name="add_poule_equipe", methods={"GET", "POST"})
        */
        public function indexAjoutEquipe(Request $request, EntityManagerInterface $manager, pouleRepository $repositoryPoule, $idPoule)
        {
            //Création d'une poule vierge qui sera remplie par le formulaire
            $poule = new Poule();
            $poule = $repositoryPoule->find($idPoule);
            $equipe = new Equipe();
            $equipe->setPoule($poule);
            //Création du formulaire permettant de saisir une poule
            $formulaireEquipe = $this->createForm(EquipeType::class, $equipe);
    
            /* On demande au formulaire d'analyser la dernière requête Http. Si le tableau POST contenu dans cette requête contient
            des variables nom, activite, etc. Alors la méthode handleRequest() recupère les valeurs de ces variables et les
            affecte à l'objet $poule. */
            $formulaireEquipe->handleRequest($request);

    
            if ($formulaireEquipe->isSubmitted() && $formulaireEquipe->isValid())
            {
                //Enregistrer la poule en base de données
                $manager->persist($equipe);
                $manager->flush();
    
                //Rediriger l'utilisateur vers la page des séries
                return $this->redirectToRoute('poules_index_serie', ['idSerie' => $poule->getSerie()->getId()]);
            }
    
            //Afficher la page présentant le formulaire d'ajout d'une série
            return $this->render('equipe/ajoutModifEquipe.html.twig', ['vueFormulaire' => $formulaireEquipe->createView(), 
            'action'=>"ajouter"]);
        }
    
    
        /**
        * @Route("/modifier/{id}", name="modify_poule", methods={"GET", "POST"})
        */
        public function indexModifPoule(Request $request, EntityManagerInterface $manager, Poule $poule)
        {
            //Création du formulaire permettant de modifier une poule
            $formulairePoule = $this->createForm(PouleType::class, $poule);
    
            /* On demande au formulaire d'analyser la dernière requête Http. Si le tableau POST contenu dans cette requête contient
            des variables nom, activite, etc. Alors la méthode handleRequest() recupère les valeurs de ces variables et les
            affecte à l'objet $poule. */
            $formulairePoule->handleRequest($request);
    
            if ($formulairePoule->isSubmitted() && $formulairePoule->isValid())
            {
                //Enregistrer la série en base de données
                $manager->persist($poule);
                $manager->flush();
    
                //Rediriger l'utilisateur vers la page d'accueil
                return $this->redirectToRoute('poules_index_serie', ['idSerie' => $poule->getSerie()->getId()]);
            }
    
            //Afficher la page présentant le formulaire d'ajout d'une serie
            return $this->render('poule/ajoutModifPoule.html.twig', ['vueFormulaire' => $formulairePoule->createView(), 
            'action'=>"modifier"]);
        }

}
