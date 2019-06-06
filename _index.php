<?php
/*
    Copyright (C) 2018 B4ckCod3Bot

    B4ckCod3Bot is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    B4ckCod3Bot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

define('B4ckCod3Bot', 'B4ckCod3Bot 1.1 Stabile',  true);
$url     = explode('.', $_SERVER['HTTP_HOST']); //Identifico il nome del dominio
# ========= [ About ] =========
$api     = $_GET['api']; //Ricavo token del bot (Tramite il webhook)
if ($_GET['api'] != $api)  die('Blocked'); //Security test for block the clonation of bot
$userbot = $_GET['userbot'] ? $_GET['userbot'] : false; //Ricavo username del bot (Tramite il webhook), altrimenti lo setto su false
if (!$userbot) {
    $userbot = $_POST['userbot'];
}
# ========= [ Table DB ] =========
$costum_table = false;
if ($costum_table !== false) {$table = 'CostumName';}
else {$table = $userbot;}
# ========= [ Connection To DB ] =========
try {
    if ($url[1] == 'altervista') { //Accedo al database MySQL (Altervista)
        $db = new PDO('mysql:host=localhost;dbname=my_' . $url[0] . ';charset=utf8', $url[0], ''); //Acces to altervista db
    } else {
        require_once 'mysql.php';
        $db = new PDO('mysql:host=localhost;dbname=' . $credenziali_accesso['database'] . ';charset=utf8', $credenziali_accesso['user'], $credenziali_accesso['password']); //Accedo al database MySQL (Virtual Private Server)
    }
} catch (PDOException $e) { //Se la connesione al database non va a buon fine
    file_put_contents('error.log', 'Error PDO: ' . $e->getMessage() . ' - file ---> ' . $e->getFile() . ' line ' . $e->getLine() . "\n\n"); //Se ci sono errori li scrivo In error.log
    throw new Exception('PDO: ' . $e->getMessage() . ' file ---> ' . $e->getFile() . ' line ' . $e->getLine() . "\n\n");
    exit;
}
# ========= [ Create Table on DB ] =========
try {
    #$db->set_charset('utf8mb4'); #for changin the charset
    # ========= [ Creating Table DB ] =========
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //Gestisco la modalità di errori con la modalità di lanciare eccezioni
    $db->exec("CREATE TABLE IF NOT EXISTS $table (
        `ID` int(0) NOT NULL AUTO_INCREMENT,
        `Type` varchar(20),
        `Name` varchar(256) NOT NULL,
        `Lastname` varchar(256) NOT NULL,
        `Username` varchar(32) NOT NULL,
        `Chat_ID` bigint(0),
        `Chat` varchar(50),
        `Antiflood` varchar(25),
        `Page` varchar(200),
        PRIMARY KEY (`ID`));");
} catch (PDOException $e) {
    file_put_contents('error.log', 'Error PDO: ' . $e->getMessage() . ' - file ---> ' . $e->getFile() . ' line ' . $e->getLine() . "\n\n"); //Se ci sono errori li scrivo In error.log
    throw new Exception('PDO: ' . $e->getMessage() . ' file ---> ' . $e->getFile() . ' line ' . $e->getLine() . "\n\n");
    exit;
}
# ========= [ Block IP ] =========
if (!empty($_SERVER['REMOTE_ADDR'])) {
    include_once 'ip.php';
}
# ========= [ Admin ] =========
$idadmin = $_GET['admin']; //Ricavo ID admin del bot (Tramite il webhook)
$adminID = $idadmin;
$file_admin = false; //false se vuoi che la lista admin non venga messa nel file _admin.json
if ($file_admin == true){
    $adminbot = json_decode(file_get_contents('DATA/_admin.json'), true);
    if (!in_array($adminID, $adminbot)) { //Lista admin del bot nel file _admin.jso
        $adminbot[] = $adminID;
    }
    file_put_contents('DATA/_admin.json', json_encode(array_unique($adminbot))); //Scrivo in _admin.json gli admin del bot
} else {
    $adminbot = array(
    	$adminID, //263932373, Aggiugi ID di altri admin
    );
}
# ========= [ Other ] =========
$content = file_get_contents('php://input'); //Ottengo gli upsates di PHP
$update  = json_decode($content, true); //Decodifico gli updates di PHP
require_once 'variables.php'; //File che contiene le variabili utilizzabili
require_once '_config.php'; //File NECESSARIO per la configurazione del bot
require_once '_comandi.php'; //File in cui andranno inseriti tutti i comandi
$informazioni            = json_decode(getMe(), true);
$informazioni_importanti = array(
    'UserBot' => $informazioni['result']['username']
);
file_put_contents('DATA/_data.json', json_encode($informazioni_importanti)); //Scrivo in _data.json l'username del bot
