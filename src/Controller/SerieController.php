<?php

namespace App\Controller;

use App\Entity\Poule;
use App\Entity\Serie;
use App\Entity\Tournoi;
use App\Form\SerieType;
use App\Form\PouleType;
use App\Repository\PouleRepository;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/serie")
 */
class SerieController extends AbstractController
{
    /**
     * @Route("/", name="serie_index", methods={"GET"})
     */
    public function index(SerieRepository $serieRepository): Response
    {
        return $this->render('serie/index.html.twig', [
            'series' => $serieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="serie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($serie);
            $entityManager->flush();

            return $this->redirectToRoute('serie_index');
        }

        return $this->render('serie/new.html.twig', [
            'serie' => $serie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="serie_show", methods={"GET"})
     */
    public function show(Serie $serie): Response
    {
        return $this->render('serie/show.html.twig', [
            'serie' => $serie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="serie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Serie $serie): Response
    {
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        $serie->clearPoules(); // La ligne magique
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('serie_index');
        }

        return $this->render('serie/edit.html.twig', [
            'serie' => $serie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="serie_delete", methods={"GET","DELETE"})
     */
    public function delete(Request $request, Serie $serie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $idTournoi=$serie->getTournoi()->getId();
            $serie->clearPoules();
            $entityManager->remove($serie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('series_index_tournoi', ['idTournoi' => $idTournoi]);
    }

        /**
        * @Route("/{idSerie}/poules", name="poules_index_serie", methods={"GET", "POST"})
        */
        public function getPouleBySerie(PouleRepository $repositoryPoule, $idSerie): Response
        {
            $poules = $repositoryPoule->findPoulesBySerie($idSerie);
            return $this->render('poule/index.html.twig', [
                'poules' => $poules,
                ]);
        }
   
    
        /**
        * @Route("/modifier/{id}", name="modify_serie", methods={"GET", "POST"})
        */
        public function indexModifSerie(Request $request, EntityManagerInterface $manager, Serie $serie)
        {
            //Création du formulaire permettant de modifier une serie
            $formulaireSerie = $this->createForm(SerieType::class, $serie);
    
            /* On demande au formulaire d'analyser la dernière requête Http. Si le tableau POST contenu dans cette requête contient
            des variables nom, activite, etc. Alors la méthode handleRequest() recupère les valeurs de ces variables et les
            affecte à l'objet $serie. */
            $formulaireSerie->handleRequest($request);
    
            if ($formulaireSerie->isSubmitted() && $formulaireSerie->isValid())
            {
                //Enregistrer la série en base de données
                $manager->persist($serie);
                $manager->flush();
    
                //Rediriger l'utilisateur vers la page d'accueil
                return $this->redirectToRoute('series_index_tournoi', ['idTournoi' => $serie->getTournoi()->getId()]);
            }
    
            //Afficher la page présentant le formulaire d'ajout d'une serie
            return $this->render('serie/ajoutModifSerie.html.twig', ['vueFormulaire' => $formulaireSerie->createView(), 
            'action'=>"modifier"]);
        }

            /**
            * @Route("/{idSerie}/poules", name="poules_index_serie", methods={"GET", "POST"})
            */
            public function getPoulesBySerie(PouleRepository $repositoryPoule, SerieRepository $repositorySerie, $idSerie): Response
            {
                $serie = $repositorySerie->find($idSerie);
              $poules = $repositoryPoule->findPoulesBySerie($idSerie);
              $poule = New Poule();
              $poule->setSerie($serie);

              return $this->render('poule/index.html.twig', [
                'poules' => $poules,
                'pouleAjout'=>$poule
                ]);
            }

             /**
        * @Route("/{idSerie}/poule/ajouter", name="add_poule_serie", methods={"GET", "POST"})
        */
        public function indexAjoutPoule(Request $request, EntityManagerInterface $manager, $idSerie, SerieRepository $repositorySerie)
        {
            //Création d'une poule vierge qui sera remplie par le formulaire
            $poule = new Poule();

            $serie = $repositorySerie->find($idSerie);

            $poule->setSerie($serie);
            
            //Création du formulaire permettant de saisir une poule
            $formulairePoule = $this->createForm(PouleType::class, $poule);
    
            /* On demande au formulaire d'analyser la dernière requête Http. Si le tableau POST contenu dans cette requête contient
            des variables nom, activite, etc. Alors la méthode handleRequest() recupère les valeurs de ces variables et les
            affecte à l'objet $poule. */
            $formulairePoule->handleRequest($request);
    
            if ($formulairePoule->isSubmitted() && $formulairePoule->isValid())
            {
                //Enregistrer la poule en base de données
                $manager->persist($poule);
                $manager->flush();
    
                //Rediriger l'utilisateur vers la page des poules
                return $this->redirectToRoute('poules_index_serie', ['idSerie' => $idSerie]);
            }
    
            //Afficher la page présentant le formulaire d'ajout d'une série
            return $this->render('poule/ajoutModifPoule.html.twig', ['vueFormulaire' => $formulairePoule->createView(), 
            'action'=>"ajouter"]);
        }
}
