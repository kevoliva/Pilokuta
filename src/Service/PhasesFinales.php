<?php
namespace App\Service;

    /************************************************
    *                                               *
    *     Calendrier v3 : affichage par tournoi     *
    *         et meilleure gestion du code          *
    *                                               *
    ************************************************/

class PhasesFinales
{
	public $etatActuel;
	/*
			$etatActuel = array(
								0 => array(										//Finale
											"equipes" => array(null,15),
											"scores" => array(null,null)
										  ),
								1 => array(										//1ère 1/2 Finale
											"equipes" => array(21,23),
											"scores" => array(null,null)
										  ),
								2 => array(										//2nde 1/2 Finale
											"equipes" => array(10,15),
											"scores" => array(26,30)
										  ),
								3 => array(										//1er 1/4 Finale
											"equipes" => array(21,2),
											"scores" => array(30,5)
										  )
								[...]
							   );
	*/

	public $equipesPossibles;
	/*
			$equipesPossibles = array(
										1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26
							   		);
	*/

	public $profondeur;
	public $expressions;

	public function __construct($etatActuel,$equipesPossibles)
	{

		$this->etatActuel=$etatActuel;
		$this->equipesPossibles=$equipesPossibles;
		$this->triEquipeCroissant();
		$this->profondeur=$this->determinerProfondeur();
		$this->expressions=array(
								"1",
								"2-1-3",
								"4-2-5-1-6-3-7",
								"8-4-9-2-10-5-11-1-12-6-13-3-14-7-15",
								"16-8-17-4-18-9-19-2-20-10-21-5-22-11-23-1-24-12-25-6-26-13-27-3-28-14-29-7-30-15-31",
								"32-16-33-8-34-17-35-4-36-18-37-9-38-19-39-2-40-20-41-10-42-21-43-5-44-22-45-11-46-23-47-1-48-24-49-12-50-25-51-6-52-26-53-13-54-27-55-3-56-28-57-14-58-29-59-7-60-30-61-15-62-31-63"
							);
	}

	public function determinerProfondeur()
	{
		$maxiIndice = $this->getNbParties();
		$profondeur = $this->getPuissanceDeDeux($maxiIndice);

		return $profondeur;
	}

	public function getNbParties(){
		$maxi=0;
		foreach ($this->etatActuel as $indice => $partie) {
			if($indice>$maxi){
				$maxi=$indice;
			}
		}
		return $maxi;
	}

	public function triEquipeCroissant()
	{
		$tabOrdonne = array();
		$dernierTrouve=0;
		foreach ($this->equipesPossibles as $equipe) {
			$dernierTrouve=$this->minimumTab($dernierTrouve);
			$tabOrdonne[]=$dernierTrouve;
		}
	}

	public function minimumTab($minimum)
	{
		$minimumTrouve=9999;
		foreach ($this->equipesPossibles as $equipe) {
			if($equipe<$minimumTrouve && $equipe>$minimum){
				$minimumTrouve=$equipe;
			}
		}
		return $minimumTrouve;
	}

	/*
	 * renvoie le numéro de la ligne où doit se situer le match 
	 * ou null si le match ne peut pas entrer dans le tableau
	 */
	public function getLigneByNumMatch($numMatch)
	{
		$numMatch++; //Finale = 0, nous avons besoin de démarrer à 1
		
		//profondeur est à 1 au minimum pour la finale (une colonne)
		//les indices du tableau commencent à 0
		$expression = $this->expressions[$this->profondeur-1];

		if(($ligne=$this->searchMatchInExpression($numMatch,$expression))!=-1){
			return $ligne;
		}
		return null;
	}

	/*
	 * renvoie le numéro de la colonne où doit se situer le match 
	 * ou null si le match ne peut pas entrer dans le tableau
	 */
	public function getColByNumMatch($numMatch)
	{
		$numMatch++; //Finale = 0, nous avons besoin de démarrer à 1
		
		//profondeur est à 1 au minimum pour la finale (une colonne)
		$colonne = $this->profondeur - $this ->getPuissanceDeDeux($numMatch);

		return $colonne;
	}

	public function getPuissanceDeDeux($nb)
	{
		$puiss=0;
		while (pow(2,++$puiss)<$nb);
		return $puiss;
	}

	/*
	Fonction qui renvoie la localisation du numéro du match souhaité dans l'expression
	passée en paramètre ou -1 en cas d'échec
	*/
	public function searchMatchInExpression($numMatch,$expression)
	{
		$numeros=explode('-', $expression);
		for ($i=0; $i < count($numeros); $i++) { 
			$numero = intval($numeros[$i]);
			if($numero==$numMatch){
				return $i;
			}
		}
		return -1;
	}

	public function getNbLignesMax()
	{
		return pow(2, $this->profondeur)-1;
	}

	public function getNumMatchByNumLigne($numLigne)
	{
		$expression = $this->expressions[$this->profondeur-1];
		$numeros=explode('-', $expression);
		$numMatch=$numeros[$numLigne];
		return $numMatch;
	}

	public function getPhasesFinales()
	{
		$table="<table style=\"width: 100%;height: 600px;\">";
		$table.="<tr id=\"entete\" style=\"border-style: none\">";
		for ($i=$this->profondeur-1; $i > 0; $i--) { 
			$table.="<td style=\"text-align: center;\">
						 1/".pow(2,$i)." de finale
					 </td>";
		}

		$table.="<td style=\"text-align: center;\">
					 Finale
				 </td>
			 </tr>";

		$table.="<tr style=\"\">";
		for ($i=$this->profondeur-1; $i >= 0; $i--) { 
			$table.="<td></td>";
		}
		$table.="</tr>";
		for ($j=0; $j < $this->getNbLignesMax(); $j++) {
			$table.="<tr style=\"\">";
			for ($i=$this->profondeur-1; $i >= 0; $i--) {
				$colonneARemplir = $this->profondeur - 1 - $this->getColByNumMatch($this->getNumMatchByNumLigne($j)) ;
				$table.="<td>";
				if($colonneARemplir==$i){
					$select="<select>";
					foreach ($this->equipesPossibles as $key => $equipe) {
						$select.="<option>eq".$equipe."</option>";
					}
					$select.="</select>";

					$table.="<div>
								<table style=\"width: 90%;margin:0 auto;\">
									<tr>
										<td>".
											$select
										."</td>
										<td><input type=\"text\" name=\"sc1\"></td>
									</tr>
									<tr>
										<td>".
											$select
										."</td>
										<td><input type=\"text\" name=\"sc2\"></td>
									</tr>
								</table>
							</div>";
				}
				$table.="</td>";
			}
			$table.="</tr>";
		}
		$table.="</table>";


		return $table;
	}
	public function getChoix()
	{
		return "<h4>Créez votre tableau de phases finales</h4>
		<br>
		 <div style=\"width: 25%\">
			<select style=\"width: 100%\">
				<option>Série 1</option>
				<option>Série 2</option>
				<option>Série 3</option>
			</select>
			<br>
			<br>
			<a class=\"button blue\" style=\"width: 100%\" href=\"aaa3bbb\">1/4 de finale</a><br><br>
			<a class=\"button blue\" style=\"width: 100%\" href=\"aaa4bbb\">1/8 de finale</a><br><br>
			<a class=\"button blue\" style=\"width: 100%\" href=\"aaa5bbb\">1/16 de finale</a><br><br>
			<a class=\"button blue\" style=\"width: 100%\" href=\"aaa6bbb\">1/32 de finale</a><br><br>
		</div>";
	}
	
}

?>