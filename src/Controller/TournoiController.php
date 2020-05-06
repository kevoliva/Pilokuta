<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Form\TournoiType;
use App\Repository\TournoiRepository;
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
  * @Route("/show/{id}/calendrier/download", name="tournoi_download_calendrier", methods={"GET"})
  */
  public function exporterCalendrier(Tournoi $tournoi): Response
  {


    //setup an event
    $eventOne = new CalendarEvent();
    $eventOne->setStart(new \DateTime('2020-05-06T12:45:00Z'))
    ->setSummary('Match football')
    ->setUid('event-uid');

    //add an Attendee
    $attendee = new Attendee(new Formatter());
    $attendee->setValue('moe@example.com')
    ->setName('Moe Smith');
    $eventOne->addAttendee($attendee);

    //set the Organizer
    $organizer = new Organizer(new Formatter());
    $organizer->setValue('heidi@example.com')
    ->setName('Heidi Merkell')
    ->setLanguage('de');
    $eventOne->setOrganizer($organizer);

    //new event
    $eventTwo = new CalendarEvent();
    $eventTwo->setStart(new \DateTime('2020-05-05T10:37:37Z'))
    ->setSummary('Rendez-vous dentiste')
    ->setUid('event-uid2');

    //setup calendar
    $calendar = new Calendar();
    $calendar->setProdId('-//My Company//Cool Calendar App//EN')
    ->addEvent($eventOne)
    ->addEvent($eventTwo);

    //setup exporter
    $calendarExport = new CalendarExport(new CalendarStream, new Formatter());
    $calendarExport->addCalendar($calendar);

    //output .ics formatted text
    $leCalendrier = $calendarExport->getStream();

    $file = "Mon calendrier.ics";
    $txt = fopen($file, "w") or die("Unable to open file!");
    fwrite($txt, $leCalendrier);
    fclose($txt);

    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    header("Content-Type: text/plain");
    readfile($file);

    unlink($file);

    return $this->render('tournoi/calendrier.html.twig', [
      'tournoi' => $tournoi,
    ]);
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
