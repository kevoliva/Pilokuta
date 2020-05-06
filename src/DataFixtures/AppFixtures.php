<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Tournoi;
use App\Entity\Partie;
use App\Entity\Serie;
use App\Entity\Joueur;
use App\Entity\Equipe;
use App\Entity\Creneau;
use App\Entity\Poule;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {
    // $product = new Product();
    // $manager->persist($product);



    $tournoi = new Tournoi();
    $tournoi->setLibelle('Tournoi 1v1');
    $tournoi->setDateDebut(new \DateTime('now'));
    $tournoi->setDateFin(new \DateTime('now'));
    $tournoi->setNbJoueursParEquipe(2);
    $tournoi->setEtat('En cours');

    $manager->persist($tournoi);

    $joueur = new Joueur();
    $joueur->setNom("Marco");
    $joueur->setPrenom("Polo");
    $joueur->setTelephone("0600000000");

    $manager->persist($joueur);

    $creneau = new Creneau();
    $creneau->setTournoi($tournoi);
    $creneau->setLaDate(new \DateTime('now'));
    $creneau->setHeureDebut('14H00');
    $creneau->setDuree(60);
    $creneau->setCommentaire('Blablabla');
    $creneau->setJoueur($joueur);

    $manager->persist($creneau);

    $serie = new Serie();
    $serie->setLibelle("Série 1");
    $serie->setTournoi($tournoi);

    $manager->persist($serie);

    $serie2 = new Serie();
    $serie2->setLibelle("Série 2");
    $serie2->setTournoi($tournoi);

    $manager->persist($serie2);

    $poule = new Poule();
    $poule->setLibelle("Poule 1");
    $poule->setSerie($serie);

    $manager->persist($poule);

    $poule2 = new Poule();
    $poule2->setLibelle("Poule 2");
    $poule2->setSerie($serie);

    $manager->persist($poule2);

    $equipe = new Equipe();
    $equipe->setLibelle("La team");
    $equipe->addJoueur($joueur);
    $equipe->setPoule($poule);

    $manager->persist($equipe);

    $equipe2 = new Equipe();
    $equipe2->setLibelle("Yo");
    $equipe2->addJoueur($joueur);
    $equipe2->setPoule($poule);

    $manager->persist($equipe2);


    $partie = new Partie();
    $partie->addEquipe($equipe);
    $partie->addEquipe($equipe2);
    $partie->setCreneau($creneau);
    $partie->setScoreEquipe1(20);
    $partie->setScoreEquipe2(30);

    $manager->persist($partie);

    $manager->flush();
  }
}
