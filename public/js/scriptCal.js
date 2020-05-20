function traitModal(date,heure,partie="N/A",juge="") {
	//date est de la forme JJ/MM/AAA
	//heure est de la forme 00h00
	//partie est de la forme eq1-eq2 ou une indisponibilité
	//juge est de la forme Joueur1
	dateStrs = date.split("/");
	jour = parseInt(dateStrs[0]);
	mois = ["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"][parseInt(dateStrs[1])-1];
	annee = parseInt(dateStrs[2]);
	document.getElementById('dateP').innerHTML="Partie du "+jour+" "+mois+" "+annee+" à "+heure;

	equipes=partie.split("-");

	if((equipes.length!=2 || parseInt(equipes[0])=="NaN" || parseInt(equipes[1])=="NaN") && partie != "N/A"){
		document.getElementById('descrIndispo').innerHTML=partie;
	}else{
		document.getElementById('descrIndispo').innerHTML="";
	}


	$('#exampleModal').modal();
}