#!/bin/bash

#FONCTIONNALITES DU SCRIPT :
#Génèrer des requêtes sql pour création d'un jeu de données :
	#Update sur Militaires pour date recrutement et naissance
	#Insert sur Affectation : 1 seul ; date Recrutement ; idCaserne généré aléatoirement (entre la 1 et 10)
	#Insert sur DetientGrades : 1 seul pour le moment selon un pourcentage prédéfini ; datePromotion (dateRecrut pour soldat) entre dateRecru et 2015
	#Insert sur PossedeDiplome : 1 seul pour le moment selon le grade, date dans l'intervalle dateRecrutement et datePromotion
	#Insert sur AppartientRegiment : 1 seul pour le moment, tous Forces Armées : même date que recrutement
#S'assure et génére des dates cohérentes pour chaque requête. 
#Fonctionne en l'état pour 100 Militaires, il suffit de changé le seq du for si dessous pour augmenter (et de changer la répartition des grades).

# for i in `seq 241 621` #pour rajouter 500entrées
for i in `seq 1 100` #fonctionne pour 100 entrées, pour plus il faut modifier la répartition des grades en conséquence
do
	#calcul de la date de naissance
	dayBirth=$(shuf -i 1-27 -n 1)
	monthBirth=$(shuf -i 1-12 -n 1)
	yearBirth=$(shuf -i 1950-1990 -n 1)

	#calcul des variables de recrutement min et max selon l'âge
	yearRecrutMin=$(($yearBirth+19))
	if [ $yearBirth -le 1960 ]; then yearRecrutMax=$(($yearBirth+40)); fi
	if [ $yearBirth -le 1970 ]; then yearRecrutMax=$(($yearBirth+40)); fi
	if [ $yearBirth -le 1980 ]; then yearRecrutMax=$(($yearBirth+35)); fi
	if [ $yearBirth -le 1990 ]; then yearRecrutMax=$(($yearBirth+25)); fi

	#calcul des variables aléatoires secondaires (dépendants de la date de naissance)
	dayRecrut=$(shuf -i 1-27 -n 1)
	dayProm=$(shuf -i 1-27 -n 1)
	dayDip=$(shuf -i 1-27 -n 1)

	monthRecrut=$(shuf -i 1-12 -n 1)
	monthProm=$(shuf -i 1-12 -n 1)
	monthDip=$(shuf -i 1-$monthProm -n 1)

	yearRecrut=$(shuf -i $yearRecrutMin-$yearRecrutMax -n 1)
	yearProm=$(shuf -i $yearRecrut-2015 -n 1)
	yearDip=$(shuf -i $yearRecrut-$yearProm -n 1)

	#augmenter l'intervalle si plus de 10 casernes
	idCaserne=$(shuf -i 1-10 -n 1)

	#On ajoute des 0 devant les mois/jours si moins de 10.
	if [ $monthBirth -le 9 ]; then monthBirth='0'$monthBirth; fi
	if [ $monthRecrut -le 9 ]; then	monthRecrut='0'$monthRecrut; fi
	if [ $monthProm -le 9 ]; then monthProm='0'$monthProm; fi
	if [ $monthDip -le 9 ]; then monthDip='0'$monthDip;	fi
	if [ $dayBirth -le 9 ]; then dayBirth='0'$dayBirth;	fi
	if [ $dayRecrut -le 9 ]; then dayRecrut='0'$dayRecrut; fi
	if [ $dayProm -le 9 ]; then dayProm='0'$dayProm; fi
	if [ $dayDip -le 9 ]; then dayDip='0'$dayDip; fi

	#on stock dans un tableau de variable chacune des dates calculés et on met en forme pour SQL
	dateNaissance[$i]='"'$yearBirth'-'$monthBirth'-'$dayBirth'"'
	dateRecrut[$i]='"'$yearRecrut'-'$monthRecrut'-'$dayRecrut'"'
	dateProm[$i]='"'$yearProm'-'$monthProm'-'$dayProm'"'
	dateObt[$i]='"'$yearDip'-'$monthDip'-'$dayDip'"'

	#selection du grade
	# random=$(shuf -i 1-380 -n 1) #pour 380 ligne supp
	# 	#grade selon % -> Tableau de répartition des grades (condition gauche - droite = %)
	# 	if [ $random -le 11 ] && [ $random -gt 0 ]; then id='"2"' && idD='"DEMS"'; fi
	# 	if [ $random -le 27 ] && [ $random -gt 11 ]; then id='"3"' && idD='"DEMS"'; fi
	# 	if [ $random -le 40 ] && [ $random -gt 27 ]; then id='"4"' && idD='"CPOS"'; fi
	# 	if [ $random -le 42 ] && [ $random -gt 40 ]; then id='"5"' && idD='"DCS"'; fi
	# 	if [ $random -le 50 ] && [ $random -gt 42 ]; then id='"6"' && idD='"DCS"'; fi
	# 	if [ $random -le 70 ] && [ $random -gt 50 ]; then id='"7"' && idD='"DQSG2"'; fi
	# 	if [ $random -le 90 ] && [ $random -gt 70 ]; then id='"8"' && idD='"DQSG2"'; fi
	# 	if [ $random -le 120 ] && [ $random -gt 90 ]; then id='"9"' && idD='"BT1"'; fi
	# 	if [ $random -le 150 ] && [ $random -gt 120 ]; then id='"10"' && idD='"BE2"'; fi
	# 	if [ $random -le 160 ] && [ $random -gt 150 ]; then id='"11"' && idD='"BS"'; fi
	# 	if [ $random -le 170 ] && [ $random -gt 160 ]; then id='"12"' && idD='"CAT1"'; fi
	# 	if [ $random -le 230 ] && [ $random -gt 170 ]; then id='"13"' && idD='"CAT1"'; fi
	# 	if [ $random -le 380 ] && [ $random -gt 230 ]; then id='"14"'; fi
	if [ $i -eq 1 ]; then id='"1"' && idD='"DEMS"' && random=101; else #grade d'amiral : un seul
		variable aléatoire 
		random=$(shuf -i 2-100 -n 1)
		#grade selon % -> Tableau de répartition des grades (condition gauche - droite = %)
		if [ $random -le 5 ] && [ $random -gt 0 ]; then id='"2"' && idD='"DEMS"'; fi
		if [ $random -le 8 ] && [ $random -gt 5 ]; then id='"3"' && idD='"DEMS"'; fi
		if [ $random -le 12 ] && [ $random -gt 8 ]; then id='"4"' && idD='"CPOS"'; fi
		if [ $random -le 15 ] && [ $random -gt 12 ]; then id='"5"' && idD='"DCS"'; fi
		if [ $random -le 20 ] && [ $random -gt 15 ]; then id='"6"' && idD='"DCS"'; fi
		if [ $random -le 27 ] && [ $random -gt 20 ]; then id='"7"' && idD='"DQSG2"'; fi
		if [ $random -le 38 ] && [ $random -gt 27 ]; then id='"8"' && idD='"DQSG2"'; fi
		if [ $random -le 48 ] && [ $random -gt 38 ]; then id='"9"' && idD='"BT1"'; fi
		if [ $random -le 58 ] && [ $random -gt 48 ]; then id='"10"' && idD='"BE2"'; fi
		if [ $random -le 68 ] && [ $random -gt 58 ]; then id='"11"' && idD='"BS"'; fi
		if [ $random -le 78 ] && [ $random -gt 68 ]; then id='"12"' && idD='"CAT1"'; fi
		if [ $random -le 88 ] && [ $random -gt 78 ]; then id='"13"' && idD='"CAT1"'; fi
		if [ $random -le 100 ] && [ $random -gt 88 ]; then id='"14"'; fi #gradeSoldat
	fi

	#mse en forme matricule
	ii='"'$i'"'
	
	#Enfin, on génère les requêtes SQL !
	echo 'UPDATE `Militaires` SET `date_naissance`='${dateNaissance[$i]}',`date_recrutement`='${dateRecrut[$i]}' WHERE `matricule`='$ii';'
	echo 'INSERT INTO `Actifs` (`matricule`) VALUES ('$ii');'
	echo 'INSERT INTO `AppartientRegiment` (`matricule`,`id`,`date_appartenance`) VALUES ('$ii',"Forces Armées",'${dateRecrut[$i]}');'
	echo 'INSERT INTO `Affectation` (`matricule`,`id`,`date_affectation`) VALUES ('$ii',"'$idCaserne'",'${dateRecrut[$i]}');'

	#condition supplémentaire dans le cas du rang soldat, on ne met pas de diplômes dans ce jeu de donnée pour ce grade.
	if [ $random -gt 88 ] && [ $random -le 100 ]
	then
		echo 'INSERT INTO `DetientGrades` (`matricule`,`id`,`date_promotion`) VALUES ('$ii','$id','${dateRecrut[$i]}');'
	else
		echo 'INSERT INTO `DetientGrades` (`matricule`,`id`,`date_promotion`) VALUES ('$ii','$id','${dateProm[$i]}');'
		echo 'INSERT INTO `PossedeDiplomes` (`matricule`,`id`,`date_obtention`,`pays_obtention`,`organisme_formateur`) VALUES ('$ii','$idD','${dateObt[$i]}',"CONGO","centre de formation militaire");'
	fi
done