INSTALL INFORMATION:

Nachdem der Block in der Moodle-Instanz installiert wurde sind folgende Schritte innerhalb eines Kurses,
in dem der Block verwendet werden soll, nötig:

1. Teilnehmer Rechte bearbeiten:
	Um Kursteilnehmern den vollen Funktionsumfang zu gewährleisten müssen ein paar Änderungen
	an den Teilnehmerrechten vorgenommen werden. Das dient dazu, dass Teilnehmer Fragen erstellen
	können und nicht nur die Kurs-Lehrer oder Administratoren. Wichtig: Dies muss im Kurs-Kontext geschehen.
	Dazu sind folgende Schritte als Lehrer/Admin notwendig:
		i. 	Einstellungen / Kurs-Administration / NutzerInnen / Rechte
		ii.	Erweiterte Rollenänderung: Teilnehmer/in
		iii.Folgende Rechte auf "Erlauben" ändern:
			* moodle/question:add
			* moodle/question:editmine
			* moodle/question:movemine
			* moodle/question:usemine
			* moodle/question:viewmine
		iv.	Änderungen speichern
		
2. Fragen Kategorie umbennen:
	Teilnehmer sind nach Schritt 1 in der Lage Fragen innerhalb eines Kurses zu erstellen. Standardmäßig
	heißt die Fragen-Kategorie für einen Kurs "Standard für <Kursname>".
	Damit es für einen Benutzer des Fragensystems klarer ist, in welcher Kategorie er seine Fragen erstellen
	kann sollte dies auf "Eigene Fragen" umbenannt werden:
		i.	Einstellungen / Kurs-Administration / Fragensammlung / Kategorien
		ii.	Gewünschte Kategorie wählen und umbennen