# Changelog
The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).
Exclamation symbols (:exclamation:) note something of importance e.g. breaking changes. Click them to learn more.

## [Unreleased]
### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security

## [Unreleased]
- Plugin LOG Semplice [PAGAMENTO] € 2,50
- Plugin LOG Avanzato [PAGAMENTO] € 4,99
- Plugin Antiflood Semplice [PAGAMENTO] € 5,99
- Plugin Antiflood Avanzato [PAGAMENTO] € 9,99
- Plugin Forward [PAGAMENTO] € 1,99
- Plugin BlackList [PAGAMENTO] € 1,99
- Plugin Forward [PAGAMENTO] € 6,99
- Plugin Board [PAGAMENTO] € 14,99
_Puoi comprare uno di questi plugin dalla sezione apposita del bot per le docs (@B4ckCod3DocsBot)_

## [1.2.3.1] - 12.18.2018
### Fixed
- Risolto un piccolo problema nella connessione con il database
- Modificata la funzione sm e cb_Reply per permettere di impostare correttamente la visualizzazione dell' anteprima dei link
### Changed
- Migliorato e velocizzato il codice del plugin chat.php
- Migliorato plugin chat.php, ora l'utente vede a che messaggio si sta rispondendo

## [1.2.3] - 10.09.2018
### Fixed
- Risolto bug di sicurezza nel file plugin.php
- Fixato il file databse.php
- Fixato il plugin chat.php
### Changed
- Creazione della tabella in un blocco try separato
- Migliorato il file functions.php, sono state miglirare le funzioni sm e cb_reply

## [1.2.2] - 26.08.2018
### Fixed
- Risolto bug nell' abilitazione/disabilitazione dei plugins
- Risolto un piccolo bug in 6 funzioni che non ne permetteva il corretto utilizzo
- Aggiornate le funzioni per mandare i vari tipi di file, è stata aggiunta la formattazione della captation
- Risolto piccolo bug nel sistema di ban, con i comandi /ban [id] e /unban [id]
### Changed
- Migliorato il file database.php, ora eseguirà meno richieste

## [1.2.1] - 06.07.2018
### Changed
- Migliorato il file variables.php aggiungendo gli update per i post nel canale

## [1.2] - 15.06.2018
### Added
- Modificato il file plugin.php, ora è presente un nuovo pulsante: "Configurazione Plugins" dal quale è possibile gestire tutti i plugin che hanno una determianta caratteristicha. Sarà possibile gestirli direttamente dal menù plugin senza fare callback data appositi
### Changed
- Creazione della tabella modificata, aggiungendo la possibilità di creare la tabella con un nome personalizzato
- Codice del file index.php migliorato e aggiornato, ora è tutto commentato
- Creazione della tabella migliorata, e LOG degli errori relativi alla connessione arrichiti di informazioni utili (Messaggio di errore, File dove è presente l'errore, riga del file in cui si trova l'errore)
- La tabella ora contiene una colonna in meno
### Fixed
- Bug nel plugin admin.php che impediva la creazione di comandi se non c'era presente almeno un comando già creato
- Piccolo bug che impediva di mettere il bot in una sottocartella della vps, questo causava diversi problemi

## [1.1] - 19.03.2018
### Added
- Statistiche invio del post
- Nuovo plugin Conversazione [BETA]
- Nuova voce aggiunta nella sezione server info per visualizzare la memoria utilizzata dal bot
### Changed
- Modificata funzione cb_reply, e aggiunta la possibilità di scegliere se far vedere o meno l' anteprima dei link
- Migliorata l' identazione del codice (Ancora incompleta) per una migliore lettura del codice
- La variabile per la password delle informazioni server del bot è stata spostata in cima al file admin.php (Password di Default: PSWinfo)
### Fixed
- Sistemato bug che sbloccava gli utente bannati quando si inviava un post
- Sistemato e migliorato il database
### Security
- Per bloccare il settaggio di webhook esterni sui propri file del bot, sostituire nella variabile $api (file index.php) il token del bot nel formato [botTOKEN]

## [1.0] - 07.02.2018
### Added
- Blocco richieste IP non proveniente da Telegra (Potrebbe non funzionare su altervista.org, nel caso eliminare il file ip.php)
- Aggiunte nuove funzioni
- Nuovi plugin
### Changed
- Variabili spostate del file functions.php a variables.php
- Ricreata la tabella in modo da permettere l' esecuzione dei nuovi plugin
### Fixed
- Sistemato il bug che non permetteva di abilitare/disabilitare i plugin dal menù admin
- Errore frequenta riportato nell' uso della base su VPS
