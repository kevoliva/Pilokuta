<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Entity\Partie;
use App\Form\TournoiType;
use App\Repository\TournoiRepository;
use App\Repository\CreneauRepository;
use App\Entity\Creneau;
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

    if ($form->isSubmitted() && $form->isValid()) {
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
  * @Route("/show/{id}", name="tournoi_show", methods={"GET"})
  */
  public function show(Tournoi $tournoi): Response
  {
    return $this->render('tournoi/show.html.twig', [
      'tournoi' => $tournoi,
    ]);
  }

  /**
  * @Route("/show/{id}/calendrier", name="tournoi_show_calendrier", methods={"GET"})
  */
  public function calendrierTournoi(Tournoi $tournoi): Response
  {
    return $this->render('tournoi/calendrier.html.twig', [
      'tournoi' => $tournoi,
    ]);
  }

  /**
  * @Route("/show/{id}/calendrier/download", name="tournoi_download_calendrier")
  */

  public function exporterCalendrier(Tournoi $tournoi)
  {
    $this->genererCalendrier($tournoi);

    return $this->render('tournoi/calendrier.html.twig', [
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

    foreach ($creneaux as $creneau) {

      $partie = $partieRepository->getPartieByCreneau($creneau);

      if(count($partie)==1){
        $event = new CalendarEvent();
        
        $dateDeb=$creneau->getLaDate();


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
      }else if($creneau->getCommentaire()!=null){
        $event = new CalendarEvent();
        
        $dateDeb=$creneau->getLaDate();


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
  * @Route("/{id}/edit", name="tournoi_edit", methods={"GET","POST"})
  */
  public function edit(Request $request, Tournoi $tournoi): Response
  {
    $form = $this->createForm(TournoiType::class, $tournoi);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('tournoi_index');
    }

    return $this->render('tournoi/edit.html.twig', [
      'tournoi' => $tournoi,
      'form' => $form->createView(),
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
}
