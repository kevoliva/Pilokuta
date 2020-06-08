<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use Doctrine\ORM\EntityManager;
use App\Repository\SerieRepository;
use App\Controller\TournoiController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @Route("/{id}/editer", name="serie_editer", methods={"GET","POST"})
     */
    public function modifierSerie(Request $request, EntityManagerInterface $entityManager,Serie $serie)
    {
        //Création d'un formulaire permettant de saisir une serie
        $formulaireSerie = $this->createForm(SerieType::class,$serie);
  
       $formulaireSerie->handleRequest($request);

        if ($formulaireSerie->isSubmitted()){

            //Enregistrer la serie en base de données
            $entityManager->persist($serie);
            $entityManager->flush();

            //Rediriger l'utilisateur vers la page d'accueil
            return $this->redirectToRoute('serie_index');

        }

        // Création de la représentation graphique du formulaire
        $vueFormulaire=$formulaireSerie->createView();

        //Afficher le formulaire pour créer une entreprise
        return $this->render('serie/ModifSerie.html.twig',['form' => $vueFormulaire]);
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
    public function edit($id, Request $request, EntityManagerInterface $entityManager)
      {
        if (null === $serie = $entityManager->getRepository(Serie::class)->find($id)) {
          throw $this->createNotFoundException('No serie found for id '.$id);
        }
        
        $originalPoules = new ArrayCollection();
        
        // Create an ArrayCollection of the current Poule objects in the database
        foreach ($serie->getPoules() as $poule) {
          $originalPoules->add($poule);
        }
        
        $editForm = $this->createForm(SerieType::class, $serie);
        
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
          // remove the relationship between the Serie and the poule
          foreach ($originalPoules as $poule) {
            if (false === $serie->getPoules()->contains($poule)) {
              // remove the serie from the poule
              $poule->getSeries()->removeElement($serie);
              
              // if it was a many-to-one relationship, remove the relationship like this
              // $poule->setSerie(null);
              
              $entityManager->persist($poule);
              
              // if you wanted to delete the Serie entirely, you can also do that
              // $entityManager->remove($poule);
            }
          }
          
          $entityManager->persist($serie);
          $entityManager->flush();
          
          // redirect back to some edit page
          return $this->redirectToRoute('serie_index');
        }
        return $this->render('serie/edit.html.twig', [
          'form' => $editForm->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="serie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Serie $serie ): Response
    {
         
        if ($this->isCsrfTokenValid('delete'.$serie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($serie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('serie_index');
    }
}
