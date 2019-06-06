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

$plugin = 'Gestione di utenti';
function Gestione_di_utenti_info()
{
    return "Questo plugin permette l'invio di post globali, di bannare/unbannare utenti e di sapere il numero di iscritti al bot.";
}
function iscritti()
{
    global $db;
    global $table;
    return array(
        'chat_private' => $db->query('SELECT * FROM ' . $table . " WHERE NOT Page = 'disable' AND Chat_ID>0 GROUP BY Chat_ID")->rowCount(),
        'chat_gruppi' => $db->query('SELECT * FROM ' . $table . " WHERE NOT Page = 'disable' AND Chat_ID<0 GROUP BY Chat_ID")->rowCount(),
        'morti_chat_private' => $db->query('SELECT * FROM ' . $table . " WHERE Page = 'disable' AND Chat_ID>0 GROUP BY Chat_ID")->rowCount(),
        'morti_chat_gruppi' => $db->query('SELECT * FROM ' . $table . " WHERE Page = 'disable' AND Chat_ID<0 GROUP BY Chat_ID")->rowCount(),
        'Bannati' => $db->query('SELECT * FROM ' . $table . " WHERE Page = 'ban' AND Chat_ID>0 ORDER BY ID")->rowCount(),
        'Admin' => $db->query('SELECT * FROM ' . $table . " WHERE Type = 'Admin' AND NOT Page = 'ban' AND Chat_ID>0 ORDER BY ID")->rowCount(),
        'Conversazioni_Attive' => $db->query('SELECT * FROM ' . $table . " WHERE Chat = 'On' AND NOT Page = 'ban' AND Chat_ID>0 ORDER BY ID")->rowCount()
    );
}
function invia_post($msg, $q)
{
    global $db;
    global $table;
    global $userID;
    global $config;
    global $q;
    global $u;
    file_put_contents('lastpost.json', $msg);
    $s = $db->query('SELECT * FROM ' . $table . ' ' . $q . ' GROUP BY Chat_ID');
    $db->exec('UPDATE ' . $table . " SET Page = 'inviapost' " . $q);
    while ($b = $s->fetch(PDO::FETCH_ASSOC)) {
        if (sm($b['Chat_ID'], $msg, false, $config['formattazione_messaggi_globali'], false, false, false)) {
            $db->exec('UPDATE ' . $table . " SET Page = '' WHERE Chat_ID = " . $b['Chat_ID']);
        } else {
            $db->exec('UPDATE ' . $table . " SET Page = 'disable' WHERE Chat_ID = " . $b['Chat_ID']);
        }
    }
}
if(($cbdata == "Iscritti" or strpos($msg, "/iscritti")===0) and $userID == $isadminbot and $chatID > 0) {
    $isc      = iscritti();
    $iscritti = "<b>📊 Statistiche Utenti 📊</b>\n\n<i>Visualizza le statistiche di tutti gli utenti che hanno usato/usano il bot. (Gli utenti attivi/inattivi non sono del tutto in tempo reale)</i>\n\n👤 <b>Statistiche Chat Privata</b>";
    $iscritti .= "\n" . '  🔈 <b>Utenti Attivi</b>: ' . $isc['chat_private'];
    $iscritti .= "\n" . '  🔇 <b>Utenti Inattivi</b>: ' . $isc['morti_chat_private'];
    $iscritti .= "\n\n" . '👥 <b>Statistiche Chat Gruppi</b> ';
    $iscritti .= "\n" . '  🔈 <b>Utenti Attivi</b>: ' . $isc['chat_gruppi'];
    $iscritti .= "\n" . '  🔇 <b>Utenti Inattivi</b>: ' . $isc['morti_chat_gruppi'];
    $iscritti .= "\n\n" . '🚫‍ <b>Utenti Bannati</b>: ' . $isc['Bannati'];
    $iscritti .= "\n" . '👨🏻‍ <b>Numero Admin Bot</b>: ' . $isc['Admin'];
    $iscritti .= "\n" . '🗣‍ <b>Conversazioni Attive Bot</b>: ' . $isc['Conversazioni_Attive']; //ConversazioniAttive
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Utenti'
        ),
    );
	if($cbdata){
		cb_reply($cbid, 'Statistiche Utenti', false, $cbmid, $iscritti, $menu, 'HTML');
	}else{
		sm($chatID, $iscritti, $menu, 'HTML', false, false, true);
    exit;
	}
}
if(($cbdata == "Post" or strpos($msg, "/post")===0) and $userID == $isadminbot and $chatID > 0) {
    $menu = array(
        array(
            array(
                'text' => '👤 Utenti',
                'callback_data' => '2post user'
            ),
            array(
                'text' => 'Gruppi 👥',
                'callback_data' => '2post group'
            )
        ),
        array(
            array(
                'text' => '👤 Utenti e Gruppi 👥',
                'callback_data' => '2post all'
            )
        )
    );
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Utenti'
        ),
    );
	$txt = '<b>📣 Post Globale 📣</b>

<i>Con questa funzione puoi inviare un messaggio che riceveranno tutti gli utenti del bot. Se selezioni gruppo, il messaggio verrà inviato anche nei gruppi collegati.</i>

Dove vuoi inviare il <b>messaggio globale</b>?';
	if($cbdata){
		cb_reply($cbid, 'Gestisci Utenti', false, $cbmid, $txt, $menu, 'HTML');
	}else{
		sm($chatID, $txt, $menu, 'HTML', false, false, true);
    exit;
	}
}
if (strpos($cbdata, '2post') === 0 and $isadminbot) {
    setPage($userID, 'post ' . str_replace('2post ', '', $cbdata));
    $menu[] = array(
        array(
            'text' => '🚫 Annulla Operazione 🚫',
            'callback_data' => 'annulla_azione3'
        )
    );
    cb_reply($cbid, 'Ok!', false, $cbmid, '<b>📣 Post Globale 📣</b>

<b>Formattazione</b>: ' . $config['formattazione_messaggi_globali'] .'
Invia il <b>post globale</b> che vuoi inviare.', $menu);
    exit;
}
if (strpos($u['Page'], 'post') === 0 and $msg) {
    setPage($userID);
    $achi = str_replace('post ', '', $u['Page']);
    if ($achi == 'user') {
        $q = "WHERE `Chat_ID` > 0 AND NOT PAGE = 'ban'";
    }
    if ($achi == 'group') {
        $q = 'WHERE `Chat_ID` < 0';
    }
    if ($achi == 'all') {
        $q = "WHERE 1 AND NOT PAGE = 'ban'";
    }
	$txt = '<b>📣 Post Globale 📣</b>

Post in <b>viaggio</b> verso gli utenti.';
    $start = time();
	sm($userID, $txt, $menu, 'HTML', false, false, true);
    file_put_contents('lastpost.json', $msg);
    try {
        invia_post($msg, $q);
    } catch (Exception $e) {
        sm($userID, $e->getMessage(), false, 'HTML', false, false, false);
    }
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Utenti'
        ),
    );
    $end = time();
    $tempo = $end-$start;
    $isc      = iscritti();
    $PostRicevuto = $isc['chat_private'];
    $BotDisabilitato = $isc['morti_chat_private'];
    sm($userID, "<b>📣 Post Globale 📣</b>\n\nInvio del post <b>completato</b>, resoconto:\n\n🔈 <b>Utenti che hanno ricevuto il post</b>: $PostRicevuto\n🔇 <b>Utenti che hanno disabilitato il bot</b>: $BotDisabilitato\n⏱ <b>Tempo Impiegato</b>: <i>$tempo s</i>", $menu, 'HTML', false, false, true);
    exit;
}
if ($chatID > 0){
    if (strpos($msg, '/ban ') === 0 and $isadminbot) {
        $campo = explode(' ', $msg);
        $db->exec('UPDATE ' . $table . " SET Page = 'ban' WHERE Chat_ID = '" . $campo[1] . "' OR Username = '" . str_replace('@', '', $campo[1]) . "'");
        sm($chatID, 'Ho bannato <b>' . str_replace('@', '', $campo[1]) . '</b> dal bot');
    }
    if (strpos($msg, '/unban ') === 0 and $isadminbot) {
        $campo = explode(' ', $msg);
        $db->exec('UPDATE ' . $table . " SET Page = '' WHERE Chat_ID = '" . $campo[1] . "' OR Username = '" . str_replace('@', '', $campo[1]) . "'");
        sm($chatID, 'Ho sbannato <b>' . str_replace('@', '', $campo[1]) . '</b> dal bot');
    }
}
//Gestione Utenti
if(($cbdata == "Utenti" or strpos($msg, "/utenti")===0) and $userID == $isadminbot and $chatID > 0) {
    $menu[] = array(
        array(
            'text' => '📊 Statistiche Utenti 📊',
            'callback_data' => 'Iscritti'
        ),
    );
    $menu[] = array(
        array(
            'text' => '🚯 Ban',
            'callback_data' => 'BanUtenti'
        ),
        array(
            'text' => 'Unban ✅',
            'callback_data' => 'UnbanUtenti'
        )
    );
    $menu[] = array(
        array(
            'text' => '📣 Post Globale 📣',
            'callback_data' => 'Post'
        ),
    );
    $menu[] = array(
        array(
            'text' => '📨 Messaggio Istantaneo 📨',
            'callback_data' => 'InviaMessaggio'
        ),
    );
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'admin'
        ),
    );
	$txt = '<b>👥 Gestione Utenti 👥</b>

<i>Gestisci da questo menù gli utenti del bot.</i>

👤 <b>Utenti</b>: Visualizza gli utenti attivi/inattivi nel bot/gruppi.
🚯 <b>Ban Utenti</b>: Banna utenti dal bot.
✅ <b>Unban Utenti</b>: Sbanna utenti dal bot.
📣 <b>Post Globale</b>: Invia un messaggio che riceveranno tutti gli utenti.
📨 <b>Messaggio Istantaneo</b>: Invia un messaggio ad un determinato utente.';
        if($cbdata){
			cb_reply($cbid, 'Gestisci Utenti', false, $cbmid, $txt, $menu, 'HTML');
        } else{
            sm($chatID, $txt, $menu, 'HTML', false, false, true);
		}
			setPage($userID, 'Utenti');
	}
//Annulla Operazione
if(strpos($cbdata, "annulla_azione3")===0 and $isadminbot == $chatID)
{
    $menu[] = array(
        array(
            "text" => "🔙 Indietro",
            "callback_data" => "Utenti"
        ),
    );
    cb_reply($cbid, 'Operazione Annullata', false, $cbmid, "🚫 <b>Operazione Annullata</b> 🚫

Operazione annullata con successo.", $menu, 'HTML');
    setPage($userID);
    exit;
}
//Ban
if(strpos($cbdata, "BanUtenti")=== 0 and $isadminbot == $chatID and $cbdata != 'Utenti' and $u['Page'] = 'BanUnban' and $chatID > 0)
{
    $menu[] = array(
        array(
            "text" => "🔙 Indietro",
            "callback_data" => "Utenti"
        ),
    );
    cb_reply($cbid, 'Ban Utenti', false, $cbmid, "🚯 <b>Ban Utenti</b> 🚯

<b>Inoltra un messaggio</b> della persona che vuoi bannare, oppure invia il suo <b>ID</b>.", $menu, 'HTML');
    setPage($userID, 'BanUtenti');
    exit;
}
if ($msg and $userID == $isadminbot and $u['Page'] == 'BanUtenti' and $chatID > 0) {
    if ($update['message']['forward_from']['id']) {
        setPage($update['message']['forward_from']['id'], 'ban');
        setPage($chatID, 'Ha bannato un utente');
        $menu[] = array(
            array(
                "text" => "🔙 Indietro",
                "callback_data" => "Utenti"
            ),
        );
			$txt = "🚯 <b>Ban Utenti</b> 🚯

L'utente <b>" . $update['message']['forward_from']['first_name'] . '</b> è stato <b>bannato</b> dal bot.';
		sm($userID, $txt, $menu, 'HTML', false, false, true);
    } else {
        if (is_numeric($msg)) {
            $menu[] = array(
                array(
                    "text" => "🔙 Indietro",
                    "callback_data" => "Utenti"
                ),
            );
			$txt = "🚯 <b>Ban Utenti</b> 🚯

L' utente " . $msg . ' è stato <b>bannato</b> dal bot.';
			sm($userID, $txt, $menu, 'HTML', false, false, true);
            setPage($chatID, 'Ha bannato un utente');
            setPage($msg, 'ban');
        }
    }
}
//Unban
if(strpos($cbdata, "UnbanUtenti")=== 0 and $isadminbot == $chatID and $cbdata != 'Utenti' and $u['Page'] = 'BanUnban' and $chatID > 0)
{
    $menu[] = array(
        array(
            "text" => "🔙 Indietro",
            "callback_data" => "Utenti"
        ),
    );
    cb_reply($cbid, 'Unban Utenti', false, $cbmid, "✅ <b>Unban Utenti</b> ✅

<b>Inoltra un messaggio</b> della persona che vuoi sbannare, oppure invia il suo <b>ID</b>.", $menu, 'HTML');
    setPage($userID, 'UnbanUtenti');
    exit;
}
if ($msg and $userID == $isadminbot and $u['Page'] == 'UnbanUtenti' and $chatID > 0) {
    if ($update['message']['forward_from']['id']) {
        setPage($update['message']['forward_from']['id'], '');
        setPage($chatID, 'Ha sbannato un utente');
        $menu[] = array(
            array(
                "text" => "🔙 Indietro",
                "callback_data" => "Utenti"
            ),
        );
			$txt = "✅ <b>Unban Utenti</b> ✅

L'utente <b>" . $update['message']['forward_from']['first_name'] . '</b> è stato <b>sbannato</b> dal bot.';
		sm($userID, $txt, $menu, 'HTML', false, false, true);
    } else {
        if (is_numeric($msg)) {
            $menu[] = array(
                array(
                    "text" => "🔙 Indietro",
                    "callback_data" => "Utenti"
                ),
            );
			$txt = "✅ <b>Unban Utenti</b> ✅

L' utente " . $msg . ' è stato <b>sbannato</b> dal bot.';
			sm($userID, $txt, $menu, 'HTML', false, false, true);
            setPage($chatID, 'Ha sbannato un utente');
            setPage($msg, '');
        }
    }
}

//Scomponi Page
$PageScomposta = explode("_", $u['Page']);
//InviaMessaggio
if(strpos($cbdata, "InviaMessaggio")=== 0 and $isadminbot == $chatID and $chatID > 0)
{
    $menu[] = array(
        array(
            "text" => "🔙 Indietro",
            "callback_data" => "Utenti"
        ),
    );
    cb_reply($cbid, 'Invia Messaggio', false, $cbmid, "📨 <b>Messaggio Istantaneo</b> 📨\n\n<i>Con questa funzione, inviando un ID di un utente o inoltrando un suo messaggio testuale puoi mandargli un messaggio istantaneo a cui potrò rispondere.</i>

<b>Inoltra un messaggio</b> della persona a cui vuoi inviare il messaggio, oppure invia il suo <b>ID</b>.", $menu, 'HTML');
    setPage($userID, 'InviaMessaggio');
    exit;
}

//Ricezione ID utente a cui invaire il messaggio
if ($msg and $userID == $isadminbot and $u['Page'] == 'InviaMessaggio' and $chatID > 0) {
    if ($update['message']['forward_from']['id']) {
        $menu[] = array(
            array(
                "text" => "🔙 Indietro",
                "callback_data" => "InviaMessaggio"
            ),
        );
		$txt = "📨 <b>Messaggio Istantaneo</b> 📨\n\nInvia ora il messaggio istandaneo da inviare a <b>" . $update['message']['forward_from']['first_name'] . '</b>.';
		sm($userID, $txt, $menu, 'HTML', false, false, true);
        setPage($chatID, 'IDRicevuto_'.$update['message']['forward_from']['id']);
    } else {
        if (is_numeric($msg)) {
            $menu[] = array(
                array(
                    "text" => "🔙 Indietro",
                    "callback_data" => "InviaMessaggio"
                ),
            );
			$txt = "📨 <b>Messaggio Istantaneo</b> 📨\n\nInvia ora il messaggio istandaneo da inviare a <b>" . $msg . '</b>.';
			sm($userID, $txt, $menu, 'HTML', false, false, true);
            setPage($chatID, 'IDRicevuto_'.$msg);
        }
    }
}
//Ricezione ID via link
$textS = explode(" ",$msg);
$explode    = explode("_", $textS[1]);
if($explode[0] == "MessaggiIstantaneo" and $userID == $isadminbot and $chatID > 0) {
    $menu[] = array(
        array(
            "text" => "🔙 Menù Utenti",
            "callback_data" => "InviaMessaggio"
        ),
    );
    $txt = "📨 <b>Messaggio Istantaneo</b> 📨\n\nInvia ora il messaggio istandaneo da inviare a <b>" . $explode[1] . '</b>.';
    sm($userID, $txt, $menu, 'HTML', false, false, true);
    setPage($chatID, 'IDRicevuto_'.$explode[1]);
}
//Riscrivi Messaggio
if ($cbdata == 'RiscriviMessaggio' and $isadminbot == $chatID and $chatID > 0){
    $menu[] = array(
        array(
            "text" => "🔙 Indietro",
            "callback_data" => "InviaMessaggio"
        ),
    );
    $txt = "📨 <b>Messaggio Istantaneo</b> 📨\n\nInvia ora il messaggio istandaneo da inviare a <b>" . $PageScomposta[1] . '</b>.';
    if($cbdata){
        cb_reply($cbid, 'Invia Messaggio', false, $cbmid, $txt, $menu, 'HTML');
    } else {
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
    }
    setPage($chatID, 'IDRicevuto_'.$PageScomposta[1]);
}
//Anteprima
if (($msg and $PageScomposta[0] == 'IDRicevuto' and !$cbdata) and $isadminbot == $chatID and $chatID > 0){
    $menu[] = array(
        array(
            "text" => "Invia Messaggio",
            "callback_data" => "ConfermaInviaMessaggio"
        ),
    );
    $menu[] = array(
        array(
            "text" => "🔙 Indietro",
            "callback_data" => "RiscriviMessaggio"
        ),
    );
    file_put_contents('lastpost.json', $msg);
    $txt = "📨 <b>Messaggio Istantaneo</b> 📨\n\nEcco l' <b>anteprima</b> del messaggio:\n<i>$msg</i>\n\nPremi \"<b>Invia Messaggio</b>\" per inviare il messaggio. Premi indietro per annullare.";
    sm($userID, $txt, $menu, 'HTML', false, false, true);
    setPage($chatID, 'MessaggioRicevuto_'.$PageScomposta[1].'_');
}
//Invio Del messaggio
if ($cbdata == 'ConfermaInviaMessaggio' and $PageScomposta[0] == 'MessaggioRicevuto' and $isadminbot == $chatID and $chatID > 0){

    $menu[] = array(
        array(
            "text" => "🔙 Indietro",
            "callback_data" => "Utenti"
        ),
    );
    $Messaggio = file_get_contents('lastpost.json');
    cb_reply($cbid, 'Messaggio Inviato', false, $cbmid, "📨 <b>Messaggio Istantaneo</b> 📨\n\nMessaggio <b>inviato</b>, presto l' utente <b>" . $PageScomposta[1] . "</b> lo riceverà!", $menu, 'HTML');

    //Invio del messaggio all' utente
    #$PulsanteRispondiUtente[] = array(
    #    array(
    #        "text" => "🖍 Rispondi 🖍",
    #        "callback_data" => "RispondiInviaMessaggioAdmin"
    #    ),
    #);
    sm($PageScomposta[1], "#Admin 📌 <b>Messaggio dagli Admin:\n\n</b>".$Messaggio."\n\n<i>Puoi rispondere a questo messaggio rispondendo via reply (Premi prolungatamente sul messaggio e schiaccia \"Rispondi\").</i>", $PulsanteRispondiUtente, 'HTML', false, false, true);
    setPage($chatID, 'ConfermaInviaMessaggio_'.$PageScomposta[1]);
}

//RispondiInviaMessaggio
$replyText = $update['message']['reply_to_message']['text'];
if (strpos($replyText, '#Admin') === 0 and $msg) {
    preg_match_all('#\[(.*?)\]#', $replyText, $nomea);
    $replyToID = $nomea[1][0];
    //Invia risposta a tutti gl iadmin
    if (!empty($username)) {
        $nome = $nome . ' (@' . $username . ')';
    }
    foreach ($adminbot as $ad) {
        if ($ad != $chatID){
            $menu[] = array(
                array(
                    "text" => "📨 Invia Nuovo Messaggio 📩",
                    "url" => "https://t.me/".$userbot."?start=User_".$userID
                ),
            );
            sm($ad, "📨 <b>Risposta al Messaggio Istantaneo</b>\n\n<b>Utente:</b> $nome [<code>$userID</code>]\n<b>Risposta:</b> $msg", $menu, 'HTML', false, false, true);
        }
    }
    sm($chatID, 'Risposta Inviata.');
    exit;
}
