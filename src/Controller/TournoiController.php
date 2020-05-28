<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Entity\Partie;
use App\Entity\User;
use App\Entity\Serie;
use App\Entity\Poule;
use App\Form\TournoiType;
use App\Repository\TournoiRepository;
use App\Repository\CreneauRepository;
use App\Entity\Creneau;
use App\Service\Calendrier;
use App\Service\CalendrierTournoi;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Model\Relationship\Organizer;

use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\CalendarExport;

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
    
    /**
    * @Route("/new", name="tournoi_new", methods={"GET","POST"})
    */
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
        'tournoi' => $tournoi,
        'form' => $form->createView(),
        ]);
    }
      
      /**
      * @Route(":{id}", name="tournoi_show", methods={"GET"})
      */
      public function show(Tournoi $tournoi): Response
      {
        return $this->render('tournoi/show.html.twig', [
          'tournoi' => $tournoi,
          ]);
      }
        
        /**
        * @Route(":{id}/calendrier", name="tournoi_show_calendrier", methods={"GET"})
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
            $poules=$serie->getPoules();
            foreach($poules as $key => $poule)
            {
              $leLibelle=$poule->getLibelle();
              $lesPoules[$leLibelle]=$leLibelle;
            }
          }
          $parties=array();
<<<<<<< HEAD
          $lesUsers=array();
          foreach ($creneaux as $key => $creneau) 
          { 
            if(($users=$creneau->getUser())!=null)
            {
              $userNom=$users->getNom();
              $lesUsers[$userNom]=$userNom;
            }
            $date=$creneau->getDateEtHeure()->format("d/m/Y");
            $heure=$creneau->getDateEtHeure()->format("H:i");
            
=======
          foreach ($creneaux as $key => $creneau) 
          {
            $date=$creneau->getDateEtHeure()->format("d/m/Y");
            $heure=$creneau->getDateEtHeure()->format("H:i");
>>>>>>> 356b6b5ebb877bcfe643ecfee0797080216bca3d
            if(($partie=$creneau->getPartie())!=null)
            {
              $eqs=$partie->getEquipes();
              $eq1=$eqs[0]->getId();
              $eq2=$eqs[1]->getId();
<<<<<<< HEAD
             
=======
>>>>>>> 356b6b5ebb877bcfe643ecfee0797080216bca3d
            }
            $partieStr=($creneau->getCommentaire()==null || $creneau->getCommentaire()=="" ?($creneau->getPartie()!=null? $eq1."-".$eq2 :"N/A"):$creneau->getCommentaire());
            $aAjouter=array($heure=>$partieStr);
            $parties[$date]=(isset($parties[$date]) && is_array($parties[$date]) ? array_merge($parties[$date],$aAjouter):$aAjouter);
          }
<<<<<<< HEAD

          $event=array();
          foreach($parties as $key => $partieStr)
          {
            foreach($partieStr as $cle => $valeur)
            {
              $evenements[]=$valeur;
            }
             
          }



=======
          
>>>>>>> 356b6b5ebb877bcfe643ecfee0797080216bca3d
          $cal=new CalendrierTournoi($parties);
          $textCalendrier=$cal->getCalendrier($id);
          
          // Récupérer joueurs tournoi
<<<<<<< HEAD
          
=======

>>>>>>> 356b6b5ebb877bcfe643ecfee0797080216bca3d
          $joueursRepository = $this->getDoctrine()->getRepository(User::class);
          
          $joueurs = $joueursRepository->getJoueursByTournoi($tournoi);
          
          
          //Envoi à la vue des informations
          return $this->render('tournoi/calendrier.html.twig', [
<<<<<<< HEAD
            'controller_name' => 'TournoiController', 'calendrier' => $textCalendrier,'time'=>$time, "tournoi" => $tournoi, "joueurs" => $joueurs, "series" =>$series, "poules"=>$lesPoules, "users"=>$lesUsers, 'evenements'=>$evenements
            ]);
        }
          
          
          
          
          /**
          * @Route(":{id}/calendrier/download", name="tournoi_download_calendrier")
=======
            'controller_name' => 'TournoiController', 'calendrier' => $textCalendrier,'time'=>$time, "tournoi" => $tournoi, "joueurs" => $joueurs, "series" =>$series, "poules"=>$lesPoules
            ]);
        }
          
          /**
          * @Route(":{id}/calendrier/exportation", name="tournoi_export_calendrier")
          */
          
          public function choisirExport(Tournoi $tournoi): Response
          {
           


            $series=$tournoi->getSeries();
            

            

           
            $joueursRepository = $this->getDoctrine()->getRepository(User::class);
          
            $joueurs = $joueursRepository->getJoueursByTournoi($tournoi);

            

            return $this->render('tournoi/exportation.html.twig', [
              'tournoi' => $tournoi, 'joueurs' => $joueurs,  "series" =>$series
              ]);
          }
          
           
          /**
          * @Route(":{id}/calendrier/exportation/download", name="tournoi_download_calendrier")
>>>>>>> 356b6b5ebb877bcfe643ecfee0797080216bca3d
          */
          
          public function exporterCalendrier(Tournoi $tournoi)
          {
            $this->genererCalendrier($tournoi);
            
<<<<<<< HEAD
            return $this->render('tournoi/calendrier.html.twig', [
=======
            return $this->render('tournoi/exportation.html.twig', [
>>>>>>> 356b6b5ebb877bcfe643ecfee0797080216bca3d
              'tournoi' => $tournoi,
              ]);
          }
            
            public function genererCalendrier(Tournoi $tournoi)
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
              
              
              $filee = "Calendrier - ".$tournoi->getLibelle().".ics";
              $f = fopen($filee, "w") or die("Unable to open file!");
              fwrite($f, $leCalendrier);
              
              fclose($f);
              
              header('Content-Description: File Transfer');
              header('Content-Disposition: attachment; filename='.basename($filee));
              header('Expires: 0');
              header('Cache-Control: must-revalidate');
              header('Pragma: public');
              header('Content-Length: ' . filesize($filee));
              header("Content-Type: text/plain");
              
              readfile($filee);
              
              unlink($filee);
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
}