<?php
namespace App\Service;
    /************************************************
    *                                               *
    *     Calendrier v2 : affichage par tournoi     *
    *                                               *
    ************************************************/

    class Calendrier
    {
        //Numéros des Jours (début-fin)
        public $jDeb=0;
        public $jFin=0;
        //Noms des Jours (début-fin)
        public $LjDeb="";
        public $LjFin="";
        //Noms des Mois (début-fin)
        public $mDeb="";
        public $mFin="";
        //Années (début-fin)
        public $aDeb=0;
        public $aFin=0;
        //Numéros des Semaines (début-fin)
        public $nSemDeb=0;
        public $nSemFin=0;
        //Créneaux disponibles rangés par Jour
        public $creneaux = array(
                                "lundi"=>array( //Lundi
                                    array(19,00)
                                ),
                                "mardi"=>array( //Mardi
                                    array(19,00),
                                    array(19,30)
                                ),
                                "mercredi"=>array(  //Mercredi
                                    array(19,00)
                                ),
                                "jeudi"=>array( //Jeudi
                                    array(19,00),
                                    array(19,30)
                                ),
                                "vendredi"=>array(  //Vendredi
                                    array(19,00)
                                ),
                                "samedi"=>array(    //Samedi
                                    array(19,00),
                                    array(19,30)
                                ),
                                "dimanche"=>array(  //Dimanche
                                    array(19,00)
                                )
                            );
        public $valeurs=array();

        //Référentiel recensant la formation de la première semaine de l'année
        public $jours = array();

        //Noms des Jours dans une Semaine
        public $joursSemaine = array("lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche");
        public $artungGrenadeSem53=false; //Si on tombe sur la semaine 53... Oof

        /**
        *   Fonction getLibelJourByDate(int numJour,String mois,int annee)
        *   Demande : le numéro du jour, le NOM du mois et l'année.
        *   Retourne : le NOM du jour de la semaine correspondant à la date fournie
        */
        function getLibelJourByDate($numJour,$mois,$annee)
        {
            return array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi")[date('w',mktime(0, 0, 0, array_search($mois, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1, $numJour ,$annee))];
        }

        /**
        *   Fonction getNumSemaineByDate(int numJour,String mois,int annee)
        *   Demande : le numéro du jour, le NOM du mois et l'année.
        *   Retourne : le numéro de la semaine correspondant à la date fournie
        */
        function getNumSemaineByDate($numJour,$mois,$annee)
        {
            return date('W',mktime(0, 0, 0, array_search($mois, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1, $numJour ,$annee));
        }

        /**
        *   Fonction Calendrier::__construct(int jourDeb,String moisDeb,int anneeDeb,int jourFin,String moisFin,int anneeFin) 
        *   Description : Constructeur de la classe
        *   Demande : les dates de début et de fin (jour/mois/année).
        *   Initialise : $jDeb,$jFin,$LjDeb,$LjFin,$mDeb,$mFin,$aDeb,$aFin,$nSemDeb,$nSemFin
        *   Condition : Les valeurs reçues sont réalistes
        *   Retourne : void
        */
        public function __construct($dateDeb,$dateFin,$donnees)
        {
                $this->jDeb=intval(date('j',$dateDeb));
                $this->jFin=intval(date('j',$dateFin));

                $this->mDeb=array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre")[date('n',$dateDeb)-1];
                $this->mFin=array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre")[date('n',$dateFin)-1];

                $this->aDeb=intval(date('Y',$dateDeb));
                $this->aFin=intval(date('Y',$dateFin));

                $this->LjDeb=$this->getLibelJourByDate($this->jDeb,$this->mDeb,$this->aDeb);
                $this->LjFin=$this->getLibelJourByDate($this->jFin,$this->mFin,$this->aFin);
                $this->nSemDeb=(intval($this->getNumSemaineByDate($this->jDeb,$this->mDeb,$this->aDeb)));
                $this->nSemFin=intval($this->getNumSemaineByDate($this->jFin,$this->mFin,$this->aFin));
                if($this->nSemDeb==53){
                    $this->nSemDeb=1;
                    $this->artungGrenadeSem53=true;
                }

                //echo "Tournoi du $this->LjDeb $this->jDeb $this->mDeb $this->aDeb (Semaine $this->nSemDeb) au $this->LjFin $this->jFin $this->mFin $this->aFin (Semaine $this->nSemFin)";
                
                $this->valeurs=array();

                for ($i=$this->aDeb; $i <= $this->aFin; $i++) {                     //Année
                    $arrayAnn=array();
                    //echo $i." : \r ";
                    foreach (array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre") as $key => $mois) {                                                     //Mois de l'année
                        $arrayAnn[$mois]=array();
                        for($j=1;$j<=date('t',mktime(0,0,0,$key+1,1,$i));$j++){     //Jour du mois
                            $arrayAnn[$mois][$j]=array();
                            for($k=19*2;$k<22*2;$k++){                              //Créneau du jour
                                if($k%2==1){
                                    $cle=intval($k/2)."h".($k%2)*30;
                                }else{
                                    $cle=intval($k/2)."h00";
                                }
                                $arrayAnn[$mois][$j][$cle]="N/A";
                            }
                        }
                    }
                    $this->valeurs[$i]=$arrayAnn;
                }
                foreach ($donnees as $date => $match) {
                    $jourCh=intval(explode('-', $date)[0]);
                    $moisCh=explode('-',$date)[1];
                    $anneeCh=intval(explode('-',$date)[2]);
                    $heureCompl=explode('-',$date)[3];
                    $heureCh=intval(explode('h',explode('-',$date)[3])[0]);
                    $minuteCh=intval(explode('h',explode('-',$date)[3])[1]);
                    $this->valeurs[$anneeCh][$moisCh][$jourCh][$heureCompl]=$match;
                }
                //print_r($this->valeurs);
                /*$this->jours=array(
                    "lundi"=>intval(date('N',mktime(0,0,0,1,0,$this->aDeb)))-1,
                    "mardi"=>intval(date('N',mktime(0,0,0,1,1,$this->aDeb)))-1,
                    "mercredi"=>intval(date('N',mktime(0,0,0,1,2,$this->aDeb)))-1,
                    "jeudi"=>intval(date('N',mktime(0,0,0,1,3,$this->aDeb)))-1,
                    "vendredi"=>intval(date('N',mktime(0,0,0,1,4,$this->aDeb)))-1,
                    "samedi"=>intval(date('N',mktime(0,0,0,1,5,$this->aDeb)))-1,
                    "dimanche"=>intval(date('N',mktime(0,0,0,1,6,$this->aDeb)))-1
                );*/
                /*$this->jours=array(
                    "lundi"=>-1,
                    "mardi"=>-0,
                    "mercredi"=>1,
                    "jeudi"=>2,
                    "vendredi"=>3,
                    "samedi"=>4,
                    "dimanche"=>5
                );*/

                $premJour=date('N',mktime(0,0,0,1,1,$this->aDeb));
                foreach ($this->joursSemaine as $cle => $jour) {
                    $this->jours[$jour]=$cle-$premJour+2;
                }
                //print_r($this->jours);
        }

        /**
        *   Fonction Calendrier::getCalendrier()
        *   Demande : void
        *   Affiche : Un calendrier horizontal (les libellés de jours sont en colonne), allant de la date de début à la date de fin
        *   Retourne : string
        */
        public function getCalendrier($idTournoi)
        {
            //$table="<table class=\"tg\"><tr><td class=\"tg-0pky\"></td>";
            $table="";
            if(intval($this->nSemFin)-intval($this->nSemDeb)+((intval($this->aFin)-intval($this->aDeb))*52)<=7){
                $table="<table class=\"tg\"><tr><td class=\"tg-0pky\"></td>";
                if($this->artungGrenadeSem53===true){
                    $this->nSemDeb--;
                }
                for($i=$this->nSemDeb;$i<=$this->nSemFin+($this->aFin-$this->aDeb)*52;$i++){
                        if($i!==52){
                            $table.="<td class=\"tg-0pky\">S".(($i)%52)."</td>";
                        } else{
                            $table.="<td class=\"tg-0pky\">S52</td>";
                        }
                }
                $table.="</tr>";
                foreach ($this->jours as $key => $jour) {
                    $table.="<tr><td class=\"tg-0pky-jour\">$jour</td>";
                    for($i=$this->nSemDeb;$i<=$this->nSemFin+($this->aFin-$this->aDeb)*52;$i++){
                        $timeAct=mktime(19,0,0,1,(($i-1)*7)+$key,$this->aDeb);
                        echo $timeAct."-".$key."-".$jour." ";

                        $moisAct=array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre")[date('n',$timeAct)-1];

                        $jourAct=date('d',$timeAct);

                        $timeDeb=mktime(0,0,0,array_search($this->mDeb, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jDeb,$this->aDeb);
                        $timeFin=mktime(23,59,59,array_search($this->mFin, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jFin,$this->aFin);
                        if($timeAct>=$timeDeb && $timeAct<=$timeFin){
                            $table.="<td class=\"tg-0pky\">$jourAct-$moisAct</td>";
                        }else{
                            $table.="<td class=\"tg-0pky-fill\"></td>";
                        }
                    }
                    $table.="</tr>";
                    foreach ($this->creneaux[$jour] as  $creneau) {
                        if($creneau[1]>9){
                            $table.="<tr>
                                    <td class=\"tg-0pky-cren\">$creneau[0]h$creneau[1]</td>";
                        }else{
                            $table.="<tr>
                                    <td class=\"tg-0pky-cren\">$creneau[0]h0$creneau[1]</td>";
                        }
                        
                        for($i=$this->nSemDeb;$i<=$this->nSemFin+($this->aFin-$this->aDeb)*52;$i++){

                            $timeDeb=mktime($creneau[0],$creneau[1],0,array_search($this->mDeb, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jDeb,$this->aDeb);
                            $timeFin=mktime($creneau[0],$creneau[1],0,array_search($this->mFin, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jFin,$this->aFin);

                            $timeAct=mktime($creneau[0],$creneau[1],0,1,(($i-1)*7)+$key,$this->aDeb);
                            if($timeAct>=$timeDeb && $timeAct<=$timeFin){
                                $table.="<td class=\"tg-0pky\"><a href=\"%s/$timeAct\" style=\"width: 100%;height: 100%\"><div style=\"width: 100%;height: 100%\"></div></a></td>";
                            }else{
                                $table.="<td class=\"tg-0pky-fill\"></td>";
                            }
                            
                        }
                        $table.="</tr>";
                        
                    }
                }
                $table.="</table>";
            }else{
                $table.="<table style=\"margin-left: auto;margin-right: auto\"><tr><td><table class=\"tg\"><tr><td class=\"tg-0pky\" style=\"color:white\">N/A</td></tr>";
                foreach ($this->jours as $key => $jour) {
                    $table.="<tr><td class=\"tg-0pky-jour\">$key</td></tr>";
                    foreach ($this->creneaux[$key] as  $creneau) {
                        if($creneau[1]>9){
                            $table.="<tr>
                                    <td class=\"tg-0pky-cren\">$creneau[0]h$creneau[1]</td></tr>";
                        }else{
                            $table.="<tr>
                                    <td class=\"tg-0pky-cren\">$creneau[0]h0$creneau[1]</td></tr>";
                        }
                    }
                }


                $table.="</table></td><td><div style=\"overflow: auto;width: 60vw;margin:16px 0 0 -4px;\"><table class=\"tg\"><tr>";
                if($this->nSemDeb!=53){
                    for($i=$this->nSemDeb;$i<=$this->nSemFin+($this->aFin-$this->aDeb)*52;$i++){
                            if($this->artungGrenadeSem53===true && $i==1){
                                $this->nSemDeb--;
                            }else if((($i)%52)!==0 || $i==53){
                                $table.="<td class=\"tg-0pky\">S".(($i)%52)."</td>";
                            }else{
                                $table.="<td class=\"tg-0pky\">S52</td>";
                            }
                    }
                }else{
                    for($i=$this->nSemDeb;$i<=1+$this->nSemFin+($this->aFin-$this->aDeb)*52;$i++){
                            if((($i)%52)!==0 && $i!=53){
                                $table.="<td class=\"tg-0pky\">S".(($i-1)%52)."</td>";
                            }else if($i==53){
                                $table.="<td class=\"tg-0pky\">S53</td>";
                            }else{
                                $table.="<td class=\"tg-0pky\">S52</td>";
                            }
                    }
                }
                $table.="</tr>";
                foreach ($this->jours as $key => $jour) {
                    /*$table.="<tr>
                                <td class=\"tg-0pky-jour\">$jour</td>";*/
                    $table.="<tr>";
                    if($this->nSemDeb!=53){
                        for($i=$this->nSemDeb;$i<=$this->nSemFin+($this->aFin-$this->aDeb)*52;$i++){
                            
                            $timeAct=mktime(0,0,0,1,(($i-1)*7)+$jour,$this->aDeb);

                            $moisAct=array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre")[date('n',$timeAct)-1];
                            $jourAct=date('d',$timeAct);

                            $timeDeb=mktime(0,0,0,array_search($this->mDeb, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jDeb,$this->aDeb);

                            $timeFin=mktime(0,0,0,array_search($this->mFin, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jFin,$this->aFin);

                            

                            if($timeAct>=$timeDeb && $timeAct<=$timeFin){
                                $table.="<td class=\"tg-0pky\">$jourAct-$moisAct</td>";
                            }else{
                                $table.="<td class=\"tg-0pky-fill\"></td>";
                            }
                        }
                    }else{
                        for($i=$this->nSemDeb;$i<=1+$this->nSemFin+($this->aFin-$this->aDeb)*52;$i++){
                              
                            $timeAct=mktime(0,0,0,1,(($i-1)*7)+$jour,$this->aDeb);

                            $moisAct=array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre")[date('n',$timeAct)-1];
                            $jourAct=date('d',$timeAct);

                            $timeDeb=mktime(0,0,0,array_search($this->mDeb, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jDeb,$this->aDeb);

                            $timeFin=mktime(0,0,0,array_search($this->mFin, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jFin,$this->aFin);

                            

                            if($timeAct>=$timeDeb && $timeAct<=$timeFin){
                                $table.="<td class=\"tg-0pky\">$jourAct-$moisAct</td>";
                            }else{
                                $table.="<td class=\"tg-0pky-fill\"></td>";
                            }  
                        }
                    }
                    $table.="</tr>";
                    foreach ($this->creneaux[$key] as  $creneau) {
                        /*if($creneau[1]>9){
                            $table.="<tr>
                                    <td class=\"tg-0pky-cren\">$creneau[0]h$creneau[1]</td>";
                        }else{
                            $table.="<tr>
                                    <td class=\"tg-0pky-cren\">$creneau[0]h0$creneau[1]</td>";
                        }*/
                        $table.="<tr>";
                        for($i=$this->nSemDeb;$i<=$this->nSemFin+($this->aFin-$this->aDeb)*52;$i++){
                            $timeAct=mktime($creneau[0],$creneau[1],0,1,(($i-1)*7)+$jour,$this->aDeb);

                            $timeDeb=mktime($creneau[0],$creneau[1],0,array_search($this->mDeb, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jDeb,$this->aDeb);

                            $timeFin=mktime($creneau[0],$creneau[1],0,array_search($this->mFin, array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre"))+1,$this->jFin,$this->aFin);


                            if($timeAct>=$timeDeb && $timeAct<=$timeFin){
                                if($creneau[1]>9){
                                    $cren="$creneau[0]h$creneau[1]";
                                }else{
                                    $cren="$creneau[0]h0$creneau[1]";
                                }
                                if(isset($this->valeurs[intval(date('Y',$timeAct))][array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre")[date('n',$timeAct)-1] ] [intval(date('j',$timeAct))][$cren])){
                                    $table.="<td class=\"tg-0pky\"><a href=\"%s/$timeAct\" style=\"width: 100%;height: 100%;text-decoration:none;color:black;\"><div style=\"width: 100%;height: 100%;\">".$this->valeurs[intval(date('Y',$timeAct))][array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","decembre")[date('n',$timeAct)-1] ] [intval(date('j',$timeAct))][$cren]."</div></a></td>";
                                }else{
                                    $table.="<td class=\"tg-0pky\"><a href=\"%s/$timeAct\" style=\"width: 100%;height: 100%;text-decoration:none;color:black;\"><div style=\"width: 100%;height: 100%;\">"."N/A"."</div></a></td>";
                                }
                                
                            }else{
                                $table.="<td class=\"tg-0pky-fill\"></td>";
                            }
                            
                        }
                        $table.="</tr>";
                        
                    }
                }
                $table.="</table></div></td></tr></table>";
            }
            return $table;
        }

    }
?>