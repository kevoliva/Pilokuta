<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Poule;
use App\Entity\Serie;
use App\Entity\Partie;
use App\Entity\Creneau;
use App\Entity\Tournoi;
use App\Form\TournoiType;
use App\Form\SerieType;
use App\Service\Calendrier;
use App\Service\PhasesFinales;
use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Model\Calendar;
use App\Service\CalendrierTournoi;
use Jsvrcek\ICS\Utility\Formatter;
use App\Repository\SerieRepository;
use Jsvrcek\ICS\Model\CalendarEvent;
use App\Repository\CreneauRepository;
use App\Repository\TournoiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Model\Relationship\Organizer;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\IsNull;
use Symfony\Component\Validator\Constraints\Length;

/**
* @Route("/tournoi")
*/
class TournoiController extends AbstractController
{
  /**
  * @Route("/", name="tournoi_index", methods={"GET"})
  */
  public function index(TournoiRepository $tournoiRepository): Response
  {
    return $this->render('tournoi/index.html.twig', [
      'tournois' => $tournoiRepository->findAll(),
      ]);
  }
    
    
  public function new(Request $request): Response
  {
    $tournoi = new Tournoi();
    $form = $this->createForm(TournoiType::class, $tournoi);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) 
    {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($tournoi);
      $entityManager->flush();
        
      return $this->redirectToRoute('tournoi_index');
    }
      
    return $this->render('tournoi/new.html.twig', [
      'form' => $form->createView(),
      ]);
  }
      
  /**
  * @Route("/edit/{id}", name="tournoi_edit", methods={"GET","POST"})
  */
  public function edit($id, Request $request, EntityManagerInterface $entityManager)
  {
    if (null === $tournoi = $entityManager->getRepository(Tournoi::class)->find($id)) 
    {
      throw $this->createNotFoundException('No tournoi found for id '.$id);
    }
        
    $originalSeries = new ArrayCollection();
        
    // Create an ArrayCollection of the current Serie objects in the database
    foreach ($tournoi->getSeries() as $serie) 
    {
      $originalSeries->add($serie);
    }
        
    $editForm = $this->createForm(TournoiType::class, $tournoi);
        
    $editForm->handleRequest($request);
        
    if ($editForm->isSubmitted() && $editForm->isValid()) 
    {
      // remove the relationship between the Serie and the Tournoi
      foreach ($originalSeries as $serie) 
      {
        if (false === $tournoi->getSeries()->contains($serie)) 
        {
          // remove the tournoi from the Serie
          $serie->getTournois()->removeElement($tournoi);
              
          // if it was a many-to-one relationship, remove the relationship like this
          // $serie->settournoi(null);
        
          $entityManager->persist($serie);
              
          // if you wanted to delete the Serie entirely, you can also do that
          // $entityManager->remove($serie);
        }
      }
          
      $entityManager->persist($tournoi);
      $entityManager->flush();
          
      // redirect back to some edit page
      return $this->redirectToRoute('serie_index');
    }
    return $this->render('tournoi_edit', ['id' => $id]);
  }
      
  /**
   * @Route("/new", name="tournoi_new", methods={"GET","POST"})
  */
  public function indexAjoutTournoi(Request $request, EntityManagerInterface $manager)
  {
    //Création d'un tournoi vierge qui sera rempli par le formulaire
    $tournoi = new Tournoi();
        
    //Création du formulaire permettant de saisir un tournoi
    $formulaireTournoi = $this->createForm(TournoiType::class, $tournoi);
        
    /* On demande au formulaire d'analyser la dernière requête Http. Si le tableau POST contenu dans cette requête contient
    des variables nom, descriptif, etc. Alors la méthode handleRequest() recupère les valeurs de ces variables et les
    affecte à l'objet $entreprise. */
    $formulaireTournoi->handleRequest($request);
        
    if ($formulaireTournoi->isSubmitted() && $formulaireTournoi->isValid())
    {
      //Enregistrer le stage en base de données
      $manager->persist($tournoi);
      $manager->flush();
  
      $idTournoi = $tournoi->getId();
      //Rediriger l'utilisateur vers la page d'accueil
      return $this->redirectToRoute('series_index_tournoi', ['idTournoi' => $idTournoi]);
    }
        
    //Afficher la page présentant le formulaire d'ajout d'un tournoi
    return $this->render('tournoi/ajoutTournoi.html.twig', ['vueFormulaire' => $formulaireTournoi->createView(), 
    ]);      
  }
      
    /**
    * @Route("/{id}", name="tournoi_show", methods={"GET"})
    */
    public function show(Tournoi $tournoi): Response
    {
      return $this->render('tournoi/show.html.twig', [
      'tournoi' => $tournoi, 
      ]);
    }    
    

    /**
    * @Route("/{id}/calendrier", name="tournoi_show_calendrier", methods={"GET"})
    */
    public function calendrier($id,$time=NULL, User $user)
    {
      //trouver le tournoi correspondant
      $tournoi=$this->getDoctrine()->getRepository(Tournoi::class)->find($id);
          
      $creneaux=$tournoi->getCreneau();
      $series=$tournoi->getSeries();
          
      $lesPoules=array();
      foreach($series as $key => $serie)
      {
        $poules = $serie->getPoules();
        foreach($poules as $key => $poule)
        {
          $lesPoules[]=$poule;
        }
      }
  
      $parties=array();
      $lesUsers=array();
      $lesCommentaires=array();
      foreach ($creneaux as $key => $creneau) 
      { 
        $unCommentaire=$creneau->getCommentaire();
        $lesCommentaires[]=$unCommentaire;
        
        if(($users=$creneau->getUser())!=null)
        {
          $userNom=$users->getNom();
          $lesUsers[$userNom]=$userNom;
        }
        $date=$creneau->getDateEtHeure()->format("d/m/Y");
        $heure=$creneau->getDateEtHeure()->format("H:i");
  
        if(($partie=$creneau->getPartie())!=null)
        {
          $eqs=$partie->getEquipes();
          $eq1=$eqs[0]->getId();
          $eq2=$eqs[1]->getId();
        }
        $partieStr=($creneau->getCommentaire()==null || $creneau->getCommentaire()=="" ?($creneau->getPartie()!=null? $eq1."-".$eq2 :"N/A"):$creneau->getCommentaire());
        $aAjouter=array($heure=>$partieStr);
        $parties[$date]=(isset($parties[$date]) && is_array($parties[$date]) ? array_merge($parties[$date],$aAjouter):$aAjouter);
      }
          
      $motif="/^[0-9]/";
      $evenements=array();
      foreach($parties as $partieStr)
      {
        foreach($partieStr as $valeur)
        {
          
            if(preg_match($motif, $valeur))
            {
              $evenements[]=$valeur;
            } 
           
        }
      }
          
          
          
      $cal=new CalendrierTournoi($parties);
      $textCalendrier=$cal->getCalendrier($id);
     
      // Récupérer joueurs tournoi
          
      $joueursRepository = $this->getDoctrine()->getRepository(User::class);
          
      $joueurs = $joueursRepository->getJoueursByTournoi($tournoi);
          
          
      //Envoi à la vue des informations
      return $this->render('tournoi/calendrier.html.twig', [
      'controller_name' => 'TournoiController', 'calendrier' => $textCalendrier,'time'=>$time, "tournoi" => $tournoi, "joueurs" => $joueurs, "series" =>$series, "poules"=>$lesPoules, "users"=>$lesUsers, 'evenements'=>$evenements, 'commentaires'=>$lesCommentaires
      ]);
    }
          
          
          /**
          * @Route("/{id}/calendrier/exportation", name="tournoi_export_calendrier")
          */
          public function choisirExport(Tournoi $tournoi): Response
          {
            
            $series=$tournoi->getSeries();
            
            $poules = [];
            foreach($series as $key => $serie)
            {
              $poule = $serie->getPoules();
              array_push($poules,$poule);
            }
            
            $joueursRepository = $this->getDoctrine()->getRepository(User::class);
            
            $joueurs = $joueursRepository->getJoueursByTournoi($tournoi);
            
            
            
            return $this->render('tournoi/exportation.html.twig', [
              'tournoi' => $tournoi, 'joueurs' => $joueurs,  "series" => $series, "poules"=> $poules
              ]);
            }
            
            /**
            * @Route("/{id}/calendrier/exportation/download", name="tournoi_download_calendrier")
            */
            
            public function exporterCalendrier(Tournoi $tournoi)
            {
              $partieRepository = $this->getDoctrine()->getRepository(Partie::class);
              
              $creneauRepository = $this->getDoctrine()->getRepository(Creneau::class);
              
              $creneaux = $creneauRepository->getCreneauByTournoi($tournoi);
              
              $calendar = new Calendar();
              $calendar->setProdId('-//My Company//Cool Calendar App//EN');
              $calendar->setTimezone(new \DateTimeZone('Europe/Paris'));
              $id = 1;
              
              foreach ($creneaux as $creneau) 
              {
                
                $partie = $partieRepository->getPartieByCreneau($creneau);
                
                if(count($partie)==1)
                {
                  $event = new CalendarEvent();
                  
                  $dateDeb=$creneau->getDateEtHeure();
                  
                  $event->setStart($dateDeb);
                  
                  $dateFin=$event->getEnd();
                  $dateFin->setTimestamp($dateFin->getTimestamp()+(($creneau->getDuree()-30)*60));
                  
                  $event->setEnd($dateFin);
                  
                  $equipes=$partie[0]->getEquipes();
                  
                  $event->setSummary($equipes[0]->getLibelle()."-".$equipes[1]->getLibelle());
                  $event->setUid('event-uid'.$id);
                  
                  
                  $calendar->addEvent($event);
                  unset($event);
                  $id++;
                  
                }else if($creneau->getCommentaire()!=null)
                {
                  $event = new CalendarEvent();
                  
                  $dateDeb=$creneau->getDateEtHeure();
                  
                  
                  $event->setStart($dateDeb);
                  
                  $dateFin=$event->getEnd();
                  $dateFin->setTimestamp($dateFin->getTimestamp()+(($creneau->getDuree()-30)*60));
                  
                  $event->setEnd($dateFin);
                  
                  $event->setSummary($creneau->getCommentaire());
                  $event->setUid('event-uid'.$id);
                  
                  
                  $calendar->addEvent($event);
                  unset($event);
                  $id++;
                }
              }
              
              $calendarExport = new CalendarExport(new CalendarStream, new Formatter());
              $calendarExport->addCalendar($calendar);
              //output .ics formatted text
              $leCalendrier = $calendarExport->getStream();
              
              
              $file = "Calendrier - ".$tournoi->getLibelle().".ics";
              $f = fopen($file, "w") or die("Unable to open file!");
              fwrite($f, $leCalendrier);
              
              fclose($f);
              
              header('Content-Description: File Transfer');
              header('Content-Disposition: attachment; filename='.basename($file));
              header('Expires: 0');
              header('Cache-Control: must-revalidate');
              header('Pragma: public');
              header('Content-Length: ' . filesize($file));
              header("Content-Type: text/plain");
              
              readfile($file);
              unlink($file);
              exit;
            }
            
            /**
            * @Route("/{id}/phasesFinales/{indic}", name="phasesFinales")
            */
            public function phasesFinales($id,$indic)
            {
              
              //trouver le tournoi correspondant
              $tournoi=$this->getDoctrine()->getRepository(Tournoi::class)->find($id);
              
              $parties=array();
              $eqPoss=array();
              $nbCols = $indic;
              for ($i=1; $i < pow(2,$nbCols); $i++) { 
                $parties[$i]=array("equipes" => array(null,null),
                "scores" => array(null,null)
              );
              $eqPoss[]=$i;
            }
            $eqPoss[]=pow(2,$nbCols);
            $PF=new PhasesFinales($parties,$eqPoss);
            if($indic!=0){
              
              $phasesFinales=$PF->getPhasesFinales();
            }else{
              $phasesFinales=$PF->getChoix();
              $phasesFinales=preg_replace('!aaa(\d+)bbb!',$this->generateUrl('phasesFinales',['id'=>$id,'indic'=>'0']).'${1}',$phasesFinales);
            }
            
            //Envoi à la vue des informations
            return $this->render('tournoi/phasesfinales.html.twig', [
              'controller_name' => 'GestionPeloteController', 'tournoi' => $tournoi,'phasesFinales' => $phasesFinales
              ]);
            }
            
            
            
            /**
            * @Route("/{id}", name="tournoi_delete", methods={"DELETE"})
            */
            public function delete(Request $request, Tournoi $tournoi): Response
            {
              if ($this->isCsrfTokenValid('delete'.$tournoi->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($tournoi);
                $entityManager->flush();
              }
              
              return $this->redirectToRoute('tournoi_index');
            }
            
            /**
            * @Route("/{idTournoi}/series", name="series_index_tournoi", methods={"GET", "POST"})
            */
            public function getSerieByTournoi(SerieRepository $repositorySerie, $idTournoi, TournoiRepository $repositoryTournoi): Response
            {
              $tournoi = $repositoryTournoi->find($idTournoi);
              $series = $repositorySerie->findSeriesByTournoi($idTournoi);
              
              $serie = New Serie();

              $serie->setTournoi($tournoi);

              return $this->render('serie/index.html.twig', [
                'series' => $series,
                'serieAjout'=>$serie
                ]);
            }

        /**
        * @Route("/{idTournoi}/serie/ajouter", name="add_serie_tournoi", methods={"GET", "POST"})
        */
        public function indexAjoutSerie(Request $request, EntityManagerInterface $manager, $idTournoi, TournoiRepository $repositoryTournoi)
        {
            //Création d'une serie vierge qui sera remplie par le formulaire
            $serie = new Serie();

            $tournoi = $repositoryTournoi->findOneById($idTournoi);

            $serie->setTournoi($tournoi);

            //Création du formulaire permettant de saisir une serie
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
    
                //Rediriger l'utilisateur vers la page des séries
                return $this->redirectToRoute('series_index_tournoi', ['idTournoi' => $idTournoi]);
            }
    
            //Afficher la page présentant le formulaire d'ajout d'une série
            return $this->render('serie/ajoutModifSerie.html.twig', ['vueFormulaire' => $formulaireSerie->createView(), 
            'action'=>"ajouter"]);
        }
}
            