<?php
namespace App\Service;

    /************************************************
    *                                               *
    *     Calendrier v3 : affichage par tournoi     *
    *         et meilleure gestion du code          *
    *                                               *
    ************************************************/

class CalendrierTournoi
{
	public $tabValeurs; 
	/* $tabValeurs => donnees reçues en entrée pas forcément rangées, peut-être en double
	 de la forme
	 array(
			"25/05/2020" => array(
									"13h" => "25-21"
							      ),
			"24/05/2020" => array(
									"10h" => "20-05"
									"11h" => "Fronton indisponible"
							      ),
			"23/05/2020" => array(
									"15h" => "N/A"
									)
		   );

	*/

	public $tabValeursRange=array();
	/* $tabValeursRange => donnees reçues en entrée rangées par ordre chronologique, sans double
	 de la forme
	 array(
			"24/05/2020" => array(
									"10h" => "20-05"
									"11h" => "Fronton indisponible"
							      ),
			"25/05/2020" => array(
									"13h" => "25-21"
							      ),
		   );

	*/

	public $creneaux=array();
	/*
		$creneaux => créneaux déduits de $tabValeurs de la forme
		
		array(
				"lundi" => array ("10h","11h","11h30"),
				"mardi" => array ("13h")
			  )

	*/


	//Récupère un timestamp, et renvoie le jour de la semaine lié
	public function getJourByDate($date){
		return array("lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche")[(date('w',$date)+6)%7];
	}


	//Récupère un libellé de jour et une heure et renvoie true si le créneau existe dans les créneaux existants, 
	//ou false dans le cas contraire ou si le jour demandé n'a pas encore été déclaré

	public function isCreneauInCreneaux($jour,$heure){
		if(!isset($this->creneaux[$jour]) || !is_array($this->creneaux[$jour])){
			return false;
		}
		foreach ($this->creneaux[$jour] as $key => $creneau) {
			if($this->getIntByHeure($creneau) == $this->getIntByHeure($heure)){
				return true;
			}
		}
		return false;
	}

	//Récupère une date, une heure et un match et renvoie true si la date et l'heure sont déjà pris, 
	//ou false dans le cas contraire, si le jour demandé n'a pas encore été déclaré ou 

	public function isHeureMatchInMatches($jour,$heure){
		if(!isset($this->tabValeursRange[$jour]) || !is_array($this->tabValeursRange[$jour])){
			return false;
		}
		foreach ($this->tabValeursRange[$jour] as $cren => $partie) {
			if($this->getIntByHeure($cren) == $this->getIntByHeure($heure)){
				return true;
			}
		}
		return false;
	}

	//Récupère une date, une heure et un match et renvoie true si le match existe dans les matchs prévus
	//ou false dans le cas contraire, si le jour demandé n'a pas encore été déclaré ou 

	public function isMatchPrevu($match){
		$strsMatchs=explode("-", $match);
		if(intval($strsMatchs[0])==0){return false;}
		foreach ($this->tabValeursRange as $jour => $partiesJour) {
			foreach ($partiesJour as $cren => $partie) { // $partie = $this->tabValeursRange[$jour][$cren]
				if(strcmp($partie,$match)==0){
					return true;
				}
			}
		}
		return false;
	}

	public function getIntByHeure($heureStr)
	{
		$separe=explode(":", $heureStr);
		$heureInt=intval($separe[0])+intval($separe[1])/60;
		return $heureInt;
	}

	public function insertHeureInCreneaux($jour,$heure)
	{

		$copieCreneau = array();
		if(!isset($this->creneaux[$jour]) || !is_array($this->creneaux[$jour]) || count($this->creneaux[$jour])==0){
			$copieCreneau = array($heure);
		}else{
			foreach ($this->creneaux[$jour] as $key => $heureC) {
				if($this->getIntByHeure($heure)<$this->getIntByHeure($heureC) && !$this->isCreneauInCreneaux($jour,$heure)){
					$copieCreneau[] = $heure;
				}
				$copieCreneau[] = $heureC;
				$this->creneaux[$jour]=$copieCreneau;
			}
			if(!$this->isCreneauInCreneaux($jour,$heure)){
					$copieCreneau[] = $heure;
			}
		}
		$this->creneaux[$jour]=$copieCreneau;
	}

	public function insertMatchInMatches($jour,$heure,$match)
	{

		$copieMatch = array();
		if(!isset($this->tabValeursRange[$jour]) || !is_array($this->tabValeursRange[$jour]) || count($this->tabValeursRange[$jour])==0){
			$copieMatch = array($heure=>$match);
		}else{
			foreach ($this->tabValeursRange[$jour] as $cren => $partie) {
				if($this->getIntByHeure($heure)<$this->getIntByHeure($cren) && !$this->isMatchPrevu($match)){
					$copieMatch[$heure] = $match;
				}
				$copieMatch[$cren] = $partie;
				$this->tabValeursRange[$jour]=$copieMatch;
			}
			if(!$this->isMatchPrevu($match)){
					$copieMatch[$heure] = $match;
			}
		}
		$this->tabValeursRange[$jour]=$copieMatch;
	}

	public function initCreneaux($arrJour)
	{
		$this->creneaux=array();
		foreach ($arrJour as $id => $jour) {
			$this->creneaux[$jour]=array();
		}
	}

	public function initTabValeurRangees($premierJour,$dernierJour)
	{
		//echo $premierJour." : ".$dernierJour;

		$premJourStrs = explode("/",$premierJour);
		$datePrem = mktime(0,0,0,intval($premJourStrs[1]),intval($premJourStrs[0]),intval($premJourStrs[2]));
		$dernJourStrs = explode("/",$dernierJour);
		$dateDern = mktime(0,0,0,intval($dernJourStrs[1]),intval($dernJourStrs[0]),intval($dernJourStrs[2]));

		$currDate=$datePrem;
		while($currDate <= $dateDern){
			$this->tabValeursRange[date("d/m/Y",$currDate)]=array();
			$currDate+=86400;
		}
		return;
	}

	public function minDate($tab)
	{
		$minDate="31/12/2099";
		foreach ($tab as $date => $parties) {
			if($this->datetoInt($date)<$this->datetoInt($minDate)){
				$minDate=$date;
			}
		}
		return $minDate;
	}

	public function datetoInt($date)
	{
		$strDate=explode("/", $date);
		$intDate=0;
		try {
			$intDate=intval($strDate[0])+365.25*intval($strDate[1])/12+365.25*intval($strDate[2]);
		} catch (NumberFormatException $e) {
			
		} finally {
			return $intDate;
		}
	}

	public function maxDate($tab)
	{
		$maxDate="01/01/1900";
		foreach ($tab as $date => $parties) {
			if($this->datetoInt($date)>$this->datetoInt($maxDate)){
				$maxDate=$date;
			}
		}
		return $maxDate;
	}


	public function __construct($tabValeurs)
	{
		$this->tabValeurs = $tabValeurs;
		$this->traiterEntrees();
	}
	public function traiterEntrees(){
		$this->initTabValeurRangees($this->minDate($this->tabValeurs),$this->maxDate($this->tabValeurs));

		$this->initCreneaux(array("debug","lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche"));

		foreach ($this->tabValeurs as $date => $creneaux) {
			//$date est au format jj/mm/aaaa
			$dates = explode("/", $date);

			//on récupère les informations souhaitées
			$numJour = intval($dates[0]);
			$mois = intval($dates[1]);
			$annee = intval($dates[2]);

			//on convertit au format date
			$timeJour=mktime(0, 0, 0, $mois, $numJour ,$annee);

			//et on récupère le libellé du jour
			$libelJour = $this->getJourByDate($timeJour);

			foreach ($creneaux as $heure => $match) {
				if(!$this->isCreneauInCreneaux($libelJour,$heure)){
					$this->insertHeureInCreneaux($libelJour,$heure);
				}
				if(!$this->isHeureMatchInMatches($date,$heure) && !$this->isMatchPrevu($match)){
					$this->insertMatchInMatches($date,$heure,$match);
				}
			}

		}
	}

	public function nbCreneau()
	{
		return count($this->creneaux,1);
	}

	public function getDayBefore($date,$jour)
	{
		if($this->getJourByDate($date)!=$jour){
			return $this->getDayBefore($date-86400,$jour);
		}else{
			return $date;
		}
	}

	public function getDayAfter($date,$jour)
	{
		if($this->getJourByDate($date)!=$jour){
			return $this->getDayAfter($date+86400,$jour);
		}else{
			return $date;
		}
	}

	public function getCalendrier()
	{	
		//Variable qui accueille toute la table
		$table="<table class=\"tg\">";

		/*
			Variable qui contient les lignes, qui elles-mêmes contiennent les cases
			de la forme :
			trs = array (
							"lundi"=>array("<td></td>","<td>30/03</td>","<td>06/04</td>",...), 
							"mardi"=>array(...),
							...
						);
		*/
		$trs=array();
		
		foreach ($this->creneaux as $libelJour => $listeCreneaux) {
			if($libelJour!="debug"){
				$trs[$libelJour]=array("<td class=\"lblJour\">$libelJour</td>");
			}
			
			foreach ($listeCreneaux as $key => $heure) {
				$trs[$libelJour."-".$heure]=array("<td>$heure</td>");
			}
			
		}
		
		$premJourStrs = explode("/",$this->minDate($this->tabValeursRange));
		$datePrem = mktime(0,0,0,intval($premJourStrs[1]),intval($premJourStrs[0]),intval($premJourStrs[2]));
		$currDate = $datePrem;


		$dernJourStrs = explode("/",$this->maxDate($this->tabValeursRange));
		$dateDern = mktime(0,0,0,intval($dernJourStrs[1]),intval($dernJourStrs[0]),intval($dernJourStrs[2]));
		

		//Éviter le décalage de jours : on comble les jours du lundi précédent au jour du début par des cases vides
		$lundiBefore = $this->getDayBefore($datePrem,"lundi");
		
		while ($lundiBefore < $datePrem) {
			$libelJour = $this->getJourByDate($lundiBefore);
			$trs[$libelJour][]="<td class=\"vide\"></td>";
			foreach ($this->creneaux[$libelJour] as $key => $heure) {
				$trs[$libelJour."-".$heure][]="<td class=\"vide\"></td>";
			}
			$lundiBefore+=86400;
		}

		//Chaque jour on insère une valeur ou N/A

		while($currDate<=$dateDern){
			$libelJour = $this->getJourByDate($currDate);
			$trs[$libelJour][]="<td class=\"lblJour\">".date("j ",$currDate).array("janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre")[intval(date("n",$currDate))-1]."</td>";
			foreach ($this->creneaux[$libelJour] as $key => $heure) {
				if(!isset($this->tabValeursRange[date("d/m/Y",$currDate)][$heure]) || $this->tabValeursRange[date("d/m/Y",$currDate)][$heure]=="N\A" || $this->tabValeursRange[date("d/m/Y",$currDate)][$heure]=="N/A"){
					$trs[$libelJour."-".$heure][]="<td onclick=\"traitModal('".date("d/m/Y",$currDate)."','$heure','N/A')\"></td>";
				}else{
					$trs[$libelJour."-".$heure][]="<td  onclick=\"traitModal('".date("d/m/Y",$currDate)."','$heure','".$this->tabValeursRange[date("d/m/Y",$currDate)][$heure]."')\">".$this->tabValeursRange[date("d/m/Y",$currDate)][$heure]."</td>";
				}
			}
			$currDate+=86400;
		}
		//dd($trs);

		//Éviter la fin abrupte : on comble les jours du jour de fin au dimanche qui suit par des cases vides
		$dimancheAfter = $this->getDayAfter($dateDern,"dimanche");
		
		while ($dateDern < $dimancheAfter) {
			$dateDern+=86400;
			$libelJour = $this->getJourByDate($dateDern);
			foreach ($this->creneaux[$libelJour] as $key => $heure) {
				$trs[$libelJour."-".$heure][]="<td class=\"vide\"></td>";
			}
			$trs[$libelJour][]="<td class=\"vide\"></td>";
		}

		foreach ($trs as $ind1 => $tr) {
			$table.="<tr>";
			foreach ($tr as $ind2 => $td) {
				$table.=$td;
			}
			$table.="</tr>";
		}
		$table.="</table><br><br>";
		return $table;
	}
}

?>