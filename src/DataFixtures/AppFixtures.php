<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Tournoi;
use App\Entity\Partie;
use App\Entity\Serie;
use App\Entity\User;
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

    $joueur = new User();
    $joueur->setEmail("marco@polo.fr");
    $joueur->setRoles(['ROLE_USER']);
    $joueur->setPassword('$2y$10$LYa1ESfqz5kP2rJCQ/md5O9YciYOkAi.//fk8MHMqiq5JYS.6.M5G');
    $joueur->setPrenom("Marco");
    $joueur->setNom("Polo");
    $joueur->setTelephone("0600000000");

    $manager->persist($joueur);

    $creneau = new Creneau();
    $creneau->setTournoi($tournoi);
    $creneau->setLaDate(new \DateTime('2020-05-13T17:45:00Z'));
    $creneau->setHeureDebut('14');
    $creneau->setMinuteDebut('00');
    $creneau->setDuree(60);
    $creneau->setCommentaire(NULL);
    $creneau->setUser($joueur);

    $manager->persist($creneau);

    $creneau3 = new Creneau();
    $creneau3->setTournoi($tournoi);
    $creneau3->setLaDate(new \DateTime('2020-05-12T14:00:00Z'));
    $creneau3->setHeureDebut('16');
    $creneau3->setMinuteDebut('30');
    $creneau3->setDuree(60);
    $creneau3->setCommentaire('Créneau indisponible');

    $manager->persist($creneau3);

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
    $equipe->setLibelle("18");
    $equipe->addUser($joueur);
    $equipe->setPoule($poule);

    $manager->persist($equipe);

    $equipe2 = new Equipe();
    $equipe2->setLibelle("27");
    $equipe2->addUser($joueur);
    $equipe2->setPoule($poule);

    $manager->persist($equipe2);


    $partie = new Partie();
    $partie->addEquipe($equipe);
    $partie->addEquipe($equipe2);
    $partie->setCreneau($creneau);
    $partie->setScoreEquipe1(20);
    $partie->setScoreEquipe2(30);

    $manager->persist($partie);

    $tournoi2 = new Tournoi();
    $tournoi2->setLibelle('Tournoi 2v2');
    $tournoi2->setDateDebut(new \DateTime('now'));
    $tournoi2->setDateFin(new \DateTime('now'));
    $tournoi2->setNbJoueursParEquipe(2);
    $tournoi2->setEtat('Terminé');

    $manager->persist($tournoi2);

    $creneau2 = new Creneau();
    $creneau2->setTournoi($tournoi2);
    $creneau2->setLaDate(new \DateTime('2020-05-13T11:45:00Z'));
    $creneau2->setHeureDebut('18H00');
    $creneau2->setDuree(90);
    $creneau2->setCommentaire(NULL);

    $manager->persist($creneau2);

    $manager->flush();
  }
}
