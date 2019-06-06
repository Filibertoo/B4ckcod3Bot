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

$plugin = 'Admin';
function Admin_info()
{
    return 'Questo plugin permette di configurare/gestire il bot, aggiungere o rimuovere e di creare comandi e risposte.';
}
$PasswordServerInfo = 'PSWinfo'; //Passowrd necessaria per visualizzare le info del server
function crea_menu($risposta)
{
    $i     = explode('|', $risposta);
    $righe = explode('$', $i[1]);
    $menue = array();
    $riga  = 0;
    $nb    = 0;
    foreach ($righe as $contenuto) {
        $menue[$riga] = array();
        $ex           = explode('-', $contenuto);
        foreach ($ex as $bottone) {
            if (strpos($bottone, '&')) {
                $io             = explode('&', $bottone);
                $menue[$riga][] = array(
                    'text' => $io[0],
                    'callback_data' => $io[1]
                );
            } elseif (strpos($bottone, '*')) {
                $io             = explode('*', $bottone);
                $menue[$riga][] = array(
                    'text' => $io[0],
                    'url' => $io[1]
                );
            }
        }
        $riga++;
        $nb = 1;
    }
    if (count($menue) > 0) {
        return $menue;
    } else {
        return false;
    }
}
    $annulla_azione[] = array(
        array(
            'text' => '❌ Annulla',
            'callback_data' => 'annulla_azione'
        )
    );
if ($cbdata == 'annulla_azione' and $userID == $isadminbot and $chatID > 0) {
    setPage($userID);
    cb_reply($cbid, 'Annullato!', true, $cbmid, 'Azione annullata!');
    exit;
}
if ($isadminbot) {
    $markhtml = array(
        'formattazione_predefinita',
        'formattazione_messaggi_globali'
    );
    $commands = json_decode(file_get_contents('DATA/_comandi.json'), true);
    if (!$commands) {
        $commands = array();
    }
    $risposte_json = json_decode(file_get_contents('DATA/_risposte.json'), true);
    if (!$risposte_json) {
        $risposte_json = array();
    }
	if(($cbdata == "Configurazione" or strpos($msg, "/configurazione")===0) and $userID == $isadminbot and $chatID > 0) {
        $tastiera    = array();
        $r           = 0;
        $valori      = $languages;
        $n           = 1;
        $k           = 0;
        $limite_riga = 1;
        foreach ($config as $valore => $bool) {
            if ($n > $limite_riga) {
                $n = 1;
                $k++;
            }
            ;
            if ($bool == true) {
                $cal = "config_false " . $valore;
                if (!in_array($valore, $markhtml) and $valore != 'tastiera_predefinita') {
                    $vari = ucwords(str_replace('_', ' ', $valore)) . ' ✔️';
                }
            } else {
                $cal = "config_true " . $valore;
                if (!in_array($valore, $markhtml) and $valore != 'tastiera_predefinita') {
                    $vari = ucwords(str_replace('_', ' ', $valore)) . ' ❌';
                }
            }
            if ($valore == 'tastiera_predefinita' and $bool != 'Normale') {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': Inline';
                $cal  = "config_false " . $valore;
            } elseif ($bool == 'Normale' and $valore == 'tastiera_predefinita') {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': Normale';
                $cal  = "config_true " . $valore;
            }
            if (in_array($valore, $markhtml) and $bool == 'Markdown') {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': Markdown';
                $cal  = "config_false " . $valore;
            } elseif ($bool == 'HTML' and in_array($valore, $markhtml)) {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': HTML';
                $cal  = "config_true " . $valore;
            }
            $tastiera[$k][] = array(
                'text' => $vari,
                'callback_data' => $cal
            );
            $n++;
			}
        $tastiera[] = array(
            array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Impostazioni'
            )
        );
        $txt = "<b>🛠 Configurazione 🛠 </b>\n\n<i>Gestisci da questa sezione la configurazione del bot.</i>";
        setPage($userID, 'Configurazione');
		if($cbdata){
            cb_reply($cbid, 'Configurazione', false, $cbmid, $txt, $tastiera, 'HTML');
		} else {
			sm($chatID, $txt, $tastiera, 'HTML', false, false, true);
        }
    }
    if (strpos($cbdata, 'config_true') === 0 and $userID == $isadminbot and $chatID > 0) {
        $lang = str_replace('config_true ', '', $cbdata);
        if (in_array($lang, $markhtml)) {
            $config[$lang] = 'Markdown';
        } elseif ($lang == 'tastiera_predefinita') {
            $config[$lang] = 'inline';
        } else {
            $config[$lang] = true;
        }
        $tastiera    = array();
        $r           = 0;
        $valori      = $languages;
        $n           = 1;
        $k           = 0;
        $limite_riga = 1;
        foreach ($config as $valore => $bool) {
            if ($n > $limite_riga) {
                $n = 1;
                $k++;
            }
            ;
            if ($bool == true) {
                $cal = "config_false " . $valore;
                if (!in_array($valore, $markhtml) and $valore != 'tastiera_predefinita') {
                    $vari = ucwords(str_replace('_', ' ', $valore)) . ' ✔️';
                }
            } else {
                $cal = "config_true " . $valore;
                if (!in_array($valore, $markhtml) and $valore != 'tastiera_predefinita') {
                    $vari = ucwords(str_replace('_', ' ', $valore)) . ' ❌';
                }
            }
            if ($valore == 'tastiera_predefinita' and $bool != 'Normale') {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': Inline';
                $cal  = "config_false " . $valore;
            } elseif ($bool == 'Normale' and $valore == 'tastiera_predefinita') {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': Normale';
                $cal  = "config_true " . $valore;
            }
            if (in_array($valore, $markhtml) and $bool == 'Markdown') {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': Markdown';
                $cal  = "config_false " . $valore;
            } elseif ($bool == 'HTML' and in_array($valore, $markhtml)) {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': HTML';
                $cal  = "config_true " . $valore;
            }
            $tastiera[$k][] = array(
                'text' => $vari,
                'callback_data' => $cal
            );
            $n++;
        }
		$tastiera[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Impostazioni'
            )
        );
        $txt = "<b>🛠 Configurazione 🛠</b>\n\n<i>Gestisci da questa sezione la configurazione del bot.</i>";
	    cb_reply($cbid, 'Configurazione', false, $cbmid, $txt, $tastiera, 'HTML');
		setPage($userID, 'Configurazione');
    }
    if (strpos($cbdata, 'config_false') === 0 and $userID == $isadminbot and $chatID > 0) {
        $lang = str_replace('config_false ', '', $cbdata);
        if (in_array($lang, $markhtml)) {
            $config[$lang] = 'HTML';
        } elseif ($lang == 'tastiera_predefinita') {
            $config[$lang] = 'Normale';
        } else {
            $config[$lang] = false;
        }
        $tastiera    = array();
        $r           = 0;
        $valori      = $languages;
        $n           = 1;
        $k           = 0;
        $limite_riga = 1;
        foreach ($config as $valore => $bool) {
            if ($n > $limite_riga) {
                $n = 1;
                $k++;
            }
            ;
            if ($bool == true) {
                $cal = "config_false " . $valore;
                if (!in_array($valore, $markhtml) and $valore != 'tastiera_predefinita') {
                    $vari = ucwords(str_replace('_', ' ', $valore)) . ' ✔️';
                }
            } else {
                $cal = "config_true " . $valore;
                if (!in_array($valore, $markhtml) and $valore != 'tastiera_predefinita') {
                    $vari = ucwords(str_replace('_', ' ', $valore)) . ' ❌';
                }
            }
            if ($valore == 'tastiera_predefinita' and $bool != 'Normale') {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': Inline';
                $cal  = "config_false " . $valore;
            } elseif ($bool == 'Normale' and $valore == 'tastiera_predefinita') {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': Normale';
                $cal  = "config_true " . $valore;
            }
            if (in_array($valore, $markhtml) and $bool == 'Markdown') {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': Markdown';
                $cal  = "config_false " . $valore;
            } elseif ($bool == 'HTML' and in_array($valore, $markhtml)) {
                $vari = ucwords(str_replace('_', ' ', $valore)) . ': HTML';
                $cal  = "config_true " . $valore;
            }
            $tastiera[$k][] = array(
                'text' => $vari,
                'callback_data' => $cal
            );
            $n++;
        }
		$tastiera[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Impostazioni'
            )
        );
        $txt = "<b>🛠 Configurazione 🛠</b>\n\n<i>Gestisci da questa sezione la configurazione del bot.</i>";
	    cb_reply($cbid, 'Configurazione', false, $cbmid, $txt, $tastiera, 'HTML');
		setPage($userID, 'Configurazione');
    }
    file_put_contents('DATA/_config.json', json_encode($config));
    if ($cbdata == 'Comandi' and $userID == $isadminbot and $chatID > 0) {
        $tastiera    = array();
        $r           = 0;
        $valori      = $commands;
        $conta       = count($valori);
        $n           = 1;
        $k           = 0;
        $limite_riga = 3;
        if ($conta > 0) {
            foreach ($valori as $valore => $risposta) {
                if ($n > $limite_riga) {
                    $n = 1;
                    $k++;
                }
                ;
                $vari           = '/' . $valore;
                $cal            = 'editcommand ' . $valore;
                $tastiera[$k][] = array(
                    'text' => $vari,
                    'callback_data' => $cal
                );
                $n++;
            }
            $k++;
	       }
        $tastiera[$k][] = array(
            'text' => 'Aggiungi...',
            'callback_data' => 'addcommand'
        );
        $tastiera[] = array(
            array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Richieste'
            )
        );
        $txt = "<b>📱 Comandi 📱</b>\n\n<i>Gestisci da questo menù i comandi del bot. Puoi creare comandi con tastiere inline e link.</i>\n\n➕ <b>Aggiungi Comandi</b>: Aggiungi comandi al bot.\n➖ <b>Rimuovi Comandi</b>: Rimuovi comandi al bot.\n🖌 <b>Modifica Comandi</b>: Modifica comandi del bot.";
        cb_reply($cbid, 'Gestione Comandi', false, $cbmid, $txt, $tastiera, 'HTML');
        setPage($userID, 'Gestione Comandi');
       }
    if ($cbdata == 'addcommand' and $userID == $isadminbot and $chatID > 0) {
		$tastiera[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Comandi'
				)
			);
		$txt = "<b>📱 Comandi - Crea 📱</b>\n\nCome vorresti chiamare il <b>comando</b>?";
		cb_reply($cbid, 'Nome Comando', false, $cbmid, $txt, $tastiera, 'HTML');
		setPage($userID, 'addcommand1');
        exit;
    }
    if ($u['Page'] == 'addcommand1' and $msg and $userID == $isadminbot and $chatID > 0) {
        $comando = clean($msg);
		$tastiera[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'addcommand'
				)
			);
		$txt = "<b>📱 Comandi - Crea 📱</b>\n\nCosa vuoi che il bot invii con il comando /" . $comando . '?';
		sm($chatID, $txt, $tastiera, 'HTML', false, false, true);
		setPage($userID, 'addcommand2:' . $comando);
        exit;
    }
    if (strpos($u['Page'], 'addcommand2') === 0 and !empty(clean($msg)) and $msg and $userID == $isadminbot and $chatID > 0) {
        $comando            = str_replace('addcommand2:', '', $u['Page']);
        $risposta           = $msg;
        $commands[$comando] = $risposta;
        setPage($userID, '');
		$tastiera[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Comandi'
				)
			);
        sm($userID, "<b>📱 Comandi - Crea 📱</b>\n\nIl comando /" . $comando . ' è stato <b>creato</b>.', $tastiera, 'HTML', false, false, true);
        file_put_contents('DATA/_comandi.json', json_encode($commands));
        exit;
    }
    if (strpos($cbdata, 'editcommand') === 0 and $userID == $isadminbot and $chatID > 0 and $cbdata != 'addcommand') {
        $comando    = str_replace('editcommand ', '', $cbdata);
		setPage($userID, 'Edita Comando');
		$tastiera[] = array(
			array(
				'text' => '✍🏻 Modifica',
				'callback_data' => 'modcommand ' . $comando
			),
			array(
				'text' => 'Elimina ❌',
				'callback_data' => 'delcommand ' . $comando
			),
		);
		$tastiera[] = array(
			array(
				'text' => '🔙 Indietro',
				'callback_data' => 'Comandi'
			)
		);
        cb_reply($cbid, '️Edita Comando', false, $cbmid, "<b>📱 Comandi - Edita 📱</b>\n\n<i>Con questa funzione puoi modificare il comando che hai appena selezionato.</i>\n\nCosa vuoi fare con il <b>comando</b> \"/" . $comando . '" ?', $tastiera, 'HTML');
        exit;
    }
    if (strpos($cbdata, 'delcommand ') === 0 and $userID == $isadminbot and $chatID > 0) {
        $comando = str_replace('delcommand ', '', $cbdata);
        unset($commands[$comando]);
        file_put_contents('DATA/_comandi.json', json_encode($commands));
		setPage($userID, 'Comando Eliminato');
		$menu[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Comandi'
			)
		);
		$txt = "<b>📱 Comandi - Edita 📱</b>\n\nIl comando è stato <b>eliminato</b>.";
		cb_reply($cbid, 'Comando Eliminato', false, $cbmid, $txt, $menu, 'HTML');
        exit;
    }
    if (strpos($cbdata, 'modcommand ') === 0 and $userID == $isadminbot and $chatID > 0 and $cbdata != 'addcommand') {
        $comando = str_replace('modcommand ', '', $cbdata);
        setPage($userID, 'modcommand ' . $comando);
		$menu[] = array(
			array(
                'text' => '🚫 Annulla Operazione 🚫',
                'callback_data' => 'annulla_azione2'
				)
			);
		$txt = "<b>📱 Comandi - Edita 📱</b>\n\nInvia la nuova risposta al <b>comando</b>.";
		cb_reply($cbid, 'Edita Comando', false, $cbmid, $txt, $menu, 'HTML');
        exit;
    }
    if (strpos($u['Page'], 'modcommand ') === 0 and !empty($msg) and $userID == $isadminbot and $chatID > 0) {
        setPage($userID);
        $comando            = str_replace('modcommand ', '', $u['Page']);
        $risposta           = $msg;
        $commands[$comando] = $risposta;
        file_put_contents('DATA/_comandi.json', json_encode($commands));
		$menu[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Comandi'
				)
			);
		$txt = "<b>📱 Comandi - Edita 📱</b>\n\n<b>Comando modificato</b> con successo.";
		sm($userID, $txt, $menu, 'HTML', false, false, true);
        exit;
    }
    if ($cbdata == 'Risposte' and $userID == $isadminbot and $chatID > 0) {
        $tastiera    = array();
        $r           = 0;
        $valori      = $risposte_json;
        $conta       = count($valori);
        $n           = 1;
        $k           = 0;
        $limite_riga = 3;
        if ($conta > 0) {
            foreach ($valori as $valore => $risposta) {
                if ($n > $limite_riga) {
                    $n = 1;
                    $k++;
                }
                ;
                $vari           = $valore;
                $cal            = 'editrisposta ' . $valore;
                $tastiera[$k][] = array(
                    'text' => $vari,
                    'callback_data' => $cal
                );
                $n++;
            }
            $k++;
            $tastiera[$k][] = array(
                'text' => 'Aggiungi...',
                'callback_data' => 'addrisposta'
            );
		$tastiera[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Richieste'
				)
			);
		$txt = "<b>🖌 Risposte 🖌</b>\n\n<i>Gestisci da questo menù le risposte del bot. Puoi creare risposte automatiche a cui il bot risponderà, data una specifica combinazione di caratteri.</i>\n\n➕ <b>Aggiungi Risposte</b>: Aggiungi risposte al bot.\n➖ <b>Rimuovi Risposte</b>: Rimuovi risposte al bot.\n🖌 <b>Modifica Risposte</b>: Modifica risposte del bot.";
		cb_reply($cbid, 'Gestione Risposte', false, $cbmid, $txt, $tastiera, 'HTML');
		setPage($userID, 'Gestione Risposte');
        }
        $tastiera[][] = array(
            'text' => 'Aggiungi...',
            'callback_data' => 'addrisposta'
        );
        if ($conta == 0 and $userID == $isadminbot and $chatID > 0) {
            $tastiera[] = array(
                array(
                    'text' => '🔙 Indietro',
                    'callback_data' => 'Richieste'
                    )
                );
            cb_reply($cbid, 'Prima Risposta', false, $cbmid, "<b>🖌 Risposte 🖌</b>\n\n<b>Crea la tua prima risposta</b> (testuale)!\nPremi su Aggiungi per iniziare.", $tastiera, 'HTML');
        }
        exit;
    }
    if ($cbdata == 'addrisposta' and $userID == $isadminbot and $chatID > 0) {
		$tastiera[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Risposte'
				)
			);
		$txt = "<b>🖌 Risposte - Crea 🖌</b>\n\nCome vorresti chiamare la <b>risposta</b>?";
		cb_reply($cbid, 'Nome Risposta', false, $cbmid, $txt, $tastiera, 'HTML');
		setPage($userID, 'addrisposta1');
        exit;
    }
    if ($u['Page'] == 'addrisposta1' and $msg and $userID == $isadminbot and $chatID > 0) {
		$tastiera[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'addrisposta'
				)
			);
		$txt = "<b>🖌 Risposte - Crea 🖌</b>\n\nCosa vuoi che il bot invii se un <b>utente</b> scrivesse: $msg ?";
		sm($chatID, $txt, $tastiera, 'HTML', false, false, true);
		setPage($userID, 'addrisposta2:' . $msg);
        exit;
    }
    if (strpos($u['Page'], 'addrisposta2') === 0 and !empty($msg) and $msg) {
        $comando            = str_replace('addrisposta2:', '', $u['Page']);
        $risposta           = $msg;
        $risposte_json[$comando] = $risposta;
		$tastiera[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Risposte'
				)
			);
        setPage($userID, '');
		$txt = "<b>🖌 Risposte - Crea 🖌</b>\n\nLa risposta <b>$comando</b> è stata creata.";
		sm($userID, $txt, $tastiera, 'HTML', false, false, true);
        file_put_contents('DATA/_risposte.json', json_encode($risposte_json));
        exit;
    }
    if (strpos($cbdata, 'editrisposta') === 0 and $userID == $isadminbot and $chatID > 0) {
        $comando    = str_replace('editrisposta ', '', $cbdata);
		$tastiera[] = array(
			array(
				'text' => '✍🏻 Modifica',
				'callback_data' => 'modrisposta ' . $comando
			),
			array(
				'text' => 'Elimina ❌',
				'callback_data' => 'delrisposta ' . $comando
			),
		);
		$tastiera[] = array(
			array(
				'text' => '🔙 Indietro',
				'callback_data' => 'Risposte'
			)
		);
        cb_reply($cbid, '️Edita Risposta', false, $cbmid, "<b>🖌 Risposte - Edita 🖌</b>\n\n<i>Con questa funzione puoi modificare la risposta che hai appena selezionato.</i>\n\nCosa vuoi fare con la <b>risposta</b> $comando ?", $tastiera, 'HTML');
        exit;
    }
    if (strpos($cbdata, 'delrisposta ') === 0 and $userID == $isadminbot and $chatID > 0) {
        $comando = str_replace('delrisposta ', '', $cbdata);
        unset($risposte_json[$comando]);
        file_put_contents('DATA/_risposte.json', json_encode($risposte_json));
		setPage($userID, 'Comando Eliminato');
		$menu[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Risposte'
			)
		);
		$txt = "<b>🖌 Risposte - Edita 🖌</b>\n\nLa risposta è stata <b>eliminata</b>.";
		cb_reply($cbid, 'Risposta Eliminata', false, $cbmid, $txt, $menu, 'HTML');
        exit;
    }
if (strpos($cbdata, 'modrisposta ') === 0 and $userID == $isadminbot and $chatID > 0) {
        $comando = str_replace('modrisposta ', '', $cbdata);
        setPage($userID, 'modrisposta ' . $comando);
		$menu[] = array(
			array(
                'text' => '🚫 Annulla Operazione 🚫',
                'callback_data' => 'annulla_azione4'
				)
			);
		$txt = "<b>🖌 Risposte - Edita 🖌</b>\n\nInvia la nuova risposta alla <b>risposta</b>.";
		cb_reply($cbid, 'Edita Risposta', false, $cbmid, $txt, $menu, 'HTML');
        exit;
    }
if (strpos($u['Page'], 'modrisposta ') === 0 and !empty($msg) and $userID == $isadminbot and $chatID > 0) {
        setPage($userID);
        $comando            = str_replace('modrisposta ', '', $u['Page']);
        $risposta           = $msg;
        $risposte_json[$comando] = $risposta;
        file_put_contents('DATA/_risposte.json', json_encode($risposte_json));
		$menu[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Risposte'
				)
			);
		$txt = "<b>🖌 Risposte - Edita 🖌</b>\n\n<b>Risposta modificata</b> con successo.";
		sm($userID, $txt, $menu, 'HTML', false, false, true);
        exit;
    }
}
//Aggiungi/Rimuovi Amministratori Bot
function seType($chatID, $Type = '') //Funziona per cambiare il Type in Admin/User
{
    global $table;
    global $db;
    $sth = $db->prepare('UPDATE ' . $table . ' SET Type = :type WHERE chat_id = ' . $chatID);
    $sth->bindParam(':type', $Type, PDO::PARAM_STR, 7);
    $sth->execute();
}
if(($cbdata == "Amministratori+" or strpos($msg, "/amministratori+")===0) and $userID == $isadminbot and $chatID > 0) {
    setPage($userID, 'aggiungiadmin');
		$menu[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Amministratori'
				)
			);
	$txt = "<b>➕ Aggiungi Amministratori 👮🏼</b>\n\nPer aggiungere un amministratore al bot <b>inoltra un messaggio</b> del nuovo admin, oppure invia il suo <b>ID</b>.";
    if($cbdata){
		cb_reply($cbid, 'Aggiungi Admin', false, $cbmid, $txt, $menu, 'HTML');
    } else{
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
	}
}
if ($msg and $userID == $isadminbot and $u['Page'] == 'aggiungiadmin' and $chatID > 0) {
    if ($update['message']['forward_from']['id']) {
        seType($update['message']['forward_from']['id'], 'Admin');
        setPage($chatID, 'Ha aggiunto un admin');
        $adminbot[] = $update['message']['forward_from']['id'];
		$menu[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Amministratori'
				)
			);
			$txt = "<b>➕ Aggiungi Amministratori 👮🏼</b>\n\nL'utente <b>" . $update['message']['forward_from']['first_name'] . '</b> è diventato un <b>nuovo admin</b> del bot.';
			sm($userID, $txt, $menu, 'HTML', false, false, true);
    } else {
        if (is_numeric($msg)) {
            seType($msg, 'Admin');
            setPage($chatID, 'Ha aggiunto un admin');
		$menu[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Amministratori'
				)
			);
			$txt = "<b>➕ Aggiungi Amministratori 👮🏼</b>\n\nL' utente " . $msg . ' è diventato un <b>nuovo admin</b> del bot.';
			sm($userID, $txt, $menu, 'HTML', false, false, true);
            $adminbot[] = $msg;
        }
    }
}
//Rimuovi Admin
if(($cbdata == "Amministratori-" or strpos($msg, "/amministratori-")===0) and $userID == $isadminbot and $chatID > 0) {
    setPage($userID, 'rimuoviadmin');
	$menu[] = array(
		array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Amministratori'
        )
	);
	$txt = "<b>➖ Rimuovi Amministratori 👮🏼</b>\n\nPer rimuovere un amministratore al bot <b>inoltra un messaggio</b> del nuovo admin, oppure invia il suo <b>ID</b>.";
    if($cbdata){
		cb_reply($cbid, 'Rimuovi Admin', false, $cbmid, $txt, $menu, 'HTML');
    } else{
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
	}
}
if ($msg and $userID == $isadminbot and $u['Page'] == 'rimuoviadmin') {
    if ($update['message']['forward_from']['id']) {
        seType($update['message']['forward_from']['id'], 'User');
        setPage($chatID, 'Ha rimosso un admin');
        $type = "User";
        if (($key = array_search($update['message']['forward_from']['id'], $adminbot)) !== false) {
            unset($adminbot[$key]);
        }
    	$menu[] = array(
    		array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Amministratori'
            )
    	);
		$txt = "<b>➖ Rimuovi Amministratori 👮🏼</b>\n\nL'utente <b>" . $update['message']['forward_from']['first_name'] . '</b> <b>non</b> è più <b>admin</b> del bot.';
		sm($userID, $txt, $menu, 'HTML', false, false, true);
    } else {
        if (is_numeric($msg)) {
            seType($msg, 'User');
            setPage($chatID, 'Ha rimosso un admin');
            if (($key = array_search($msg, $adminbot)) !== false) {
                unset($adminbot[$key]);
            }
		$menu[] = array(
			array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Amministratori'
				)
			);
			$txt = "<b>➖ Rimuovi Amministratori 👮🏼</b>\n\nL'utente <b>" . $msg . ' non</b> è più <b>admin</b> del bot.';
			sm($userID, $txt, $menu, 'HTML', false, false, true);
			$adminbot[] = $msg;
        }
    }
}
$commands = json_decode(file_get_contents('DATA/_comandi.json'), true);
foreach ($commands as $comando => $risposta) {
    if ($msg == '/' . $comando) {
        $l = crea_menu($risposta);
        if ($l) {
            $r = explode('|', $risposta);
            $primo_array = array('[NOME]', '[COGNOME]', '[ID]', '[USERNAME]');
            $secondo_array = array($nome, $cognome, $userID, $username);
            if (!isset($username)) {
                $secondo_array[3] = 'Nessun username';
            }
            sm($userID, str_replace($primo_array, $secondo_array, str_replace('|' . $r[1], '', $risposta)), $l);
        } else {
            sm($userID, $risposta);
        }
    }
}
$risposte_json = json_decode(file_get_contents('DATA/_risposte.json'), true);
foreach ($risposte_json as $comando => $risposta) {
    if (strtolower($msg) == strtolower($comando)) {
        $l = crea_menu($risposta);
        if ($l) {
            $r = explode('|', $risposta);
            $primo_array = array('[NOME]', '[COGNOME]', '[ID]', '[USERNAME]');
            $secondo_array = array($nome, $cognome, $userID, $username);
            if (!isset($username)) {
                $secondo_array[3] = 'Nessun username';
            }
            sm($userID, str_replace($primo_array, $secondo_array, str_replace('|' . $r[1], '', $risposta)), $l);
        } else {
            sm($userID, $risposta);
        }
    }
}
//Pannello Admin
if(($cbdata == "admin" or strpos($msg, "/admin")===0) and $userID == $isadminbot and $chatID > 0) {
    $menu[] = array(
        array(
            'text' => '❓Informazioni & Guida 📖',
            'callback_data' => 'Informazioni'
        ),
    );
	$menu[] = array(
        array(
            'text' => '🗳 Plugin',
            'callback_data' => 'Plugins'
        ),
        array(
            'text' => 'Richieste 📦',
            'callback_data' => 'Richieste'
        )
    );
	$menu[] = array(
        array(
            'text' => '📣 Gestione Utenti 👤',
            'callback_data' => 'Utenti'
        ),
    );
	$menu[] = array(
        array(
            'text' => '⚙️ Impostazioni ⚙️',
            'callback_data' => 'Impostazioni'
        ),
    );
	$txt = "<b>⛑ Pannello Admin - $userbot ⛑</b>\n\n<i>Questo pannello serve per amministrare il bot e per varie funzioni.</i>\n\n<b>❓ Informazioni & Guida </b>: Breve documentazione del bot.\n<b>🗳 Plugin</b>: Gestisci i plugins del bot.\n<b>📦 Richieste</b>: Gestisci i comandi e le risposte del bot.\n<b>👤 Gestione Utenti</b>: Gestisci gli utenti del bot.\n<b>⚙️ Impostazioni</b>: Gestisci le impostazioni e la configurazione del bot.";
    if($cbdata){
		cb_reply($cbid, 'Pannello Admin', false, $cbmid, $txt, $menu, 'HTML');
    } else{
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
	}
	setPage($userID, 'Pannello Admin');
	}
if(($cbdata == "Impostazioni" or strpos($msg, "/impostazioni")===0) and $userID == $isadminbot and $chatID > 0) {
    $menu[] = array(
        array(
            'text' => '👮🏼 Admin',
            'callback_data' => 'Amministratori'
        ),
        array(
            'text' => 'Config 🛠',
            'callback_data' => 'Configurazione'
        ),
    );
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'admin'
        ),
    );
	$txt = "<b>⚙️ Impostazioni ⚙️</b>\n\n<i>Gestisci le impostazioni del bot da questo menù.</i>\n\n<b>👮🏼 Admin</b>: Gestisci gli amministratori del bot.\n<b>🛠 Configurazione</b>: Gestisci la configurazione del bot.";
    if($cbdata){
		cb_reply($cbid, 'Impostazioni', false, $cbmid, $txt, $menu, 'HTML');
    } else {
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
	}
		setPage($userID, 'Impostazioni');
	}
if(($cbdata == "Amministratori" or strpos($msg, "/amministratori")===0) and $userID == $isadminbot and $chatID > 0) {
    setPage($userID, 'Amministratori');
    $menu[] = array(
        array(
            'text' => '➕ Aggiungi',
            'callback_data' => 'Amministratori+'
        ),
        array(
            'text' => 'Rimuovi ➖',
            'callback_data' => 'Amministratori-'
        ),
    );
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Impostazioni'
        ),
    );
	$txt = "<b>👮🏼 Amministratori 👮🏼</b>\n\n<i>Gestisci da questo menù gli amministratori del bot.</i>\n\n➕ <b>Aggiungi</b>: Aggiungi admin al bot.\n➖ <b>Rimuovi</b>: Rimuovi admin al bot.";
    if($cbdata){
		cb_reply($cbid, 'Amministratori', false, $cbmid, $txt, $menu, 'HTML');
    } else{
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
	}
}
//Informazioni
if(($cbdata == "Informazioni" or strpos($msg, "/informazioni")===0) and $userID == $isadminbot and $chatID > 0) {
    $menu[] = array(
        array(
            'text' => '📖 Docs Bot',
            'callback_data' => 'GuidaBot'
        ),
        array(
            'text' => 'Server Info 📡',
            'callback_data' => 'ServerInfo'
        )
    );
    $menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'admin'
        ),
    );
    $txt = "❓ <b>Informazioni & Guida</b> 📖\n\n<i>Breve documentazione del bot e informazioni sul server.</i>\n\n📖 <b>Docs Bot</b>: Documentazione bot, spiegazione comandi, menù, utilizzo.\n📡 <b>Server Info</b>: Informazioni utili sul server in cui è hostato il bot.";
    if($cbdata){
        cb_reply($cbid, 'Informazioni & Guida', false, $cbmid, $txt, $menu, 'HTML');
    }else{
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
    }
    setPage($userID, 'Informazioni & Guida');
}
//Password per accedere alle informazioni del server
if(($cbdata == "ServerInfo" or strpos($msg, "/ServerInfo")===0) and $userID == $isadminbot and $chatID > 0) {
    $menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Informazioni'
        ),
    );
    $txt = "📡 <b>Informazioni Server</b> 📡\n\nInserisci la password per accedere alle informazioni del server.";
    if($cbdata){
        cb_reply($cbid, 'ServerInfo Inserimento Password', false, $cbmid, $txt, $menu, 'HTML');
    }else{
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
    }
    setPage($userID, 'ServerInfo-Password');
}
if(((($u['Page'] == 'ServerInfo-Password') and $msg == $PasswordServerInfo) and $userID == $isadminbot and $chatID > 0)and $cbdata != 'Informazioni'){
    $menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Informazioni'
        ),
    );
    //Info SERVER
    $ServerAddres = $_SERVER["SERVER_ADDR"];
    $ServerName = $_SERVER["SERVER_NAME"];
    $ServerSoftware = $_SERVER["SERVER_SOFTWARE"];
    $ServerProtocol = $_SERVER["SERVER_PROTOCOL"];
    $RequestMethod = $_SERVER["REQUEST_METHOD"];
    $ServerSoftware = $_SERVER["SERVER_SOFTWARE"];
    $ScriptFileName = $_SERVER["SCRIPT_FILENAME"];
    $HTTPConnection = $_SERVER["HTTP_CONNECTION"];
    $PortaRemota = $_SERVER["REMOTE_PORT"];
    //$result = shell_exec('ping '.$ip.' -c 2');

    function get_memory_usage() {
    $size = memory_get_usage(true);
    $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
    return @round($size/pow(1024, ($i=floor(log($size, 1024)))), 2).' '.$unit[$i];
    }
    $MemoriaUtilizzata = get_memory_usage();

    $txt = "📡 <b>Informazioni Server</b> 📡\n\n<i>Informazioni utili sul server in cui è hostato il bot.</i>\n\n<b>Nome Server</b>: $ServerName\n<b>Indirizzo Server</b>: $ServerAddres\n<b>Protocollo</b>: $ServerProtocol\n<b>Connessione HTTP</b>: $HTTPConnection\n<b>Porta Remota</b>: $PortaRemota\n<b>Software</b>: $ServerSoftware\n<b>Metodo Richieste</b>: $RequestMethod\n<b>Percorso Bot</b>: $ScriptFileName\n<b>Memoria Utilizzata</b>: $MemoriaUtilizzata";
    sm($chatID, $txt, $menu, 'HTML', false, false, true);
    setPage($userID, 'Server Info');
} else if(((($u['Page'] == 'ServerInfo-Password') and $msg != $PasswordServerInfo) and $userID == $isadminbot and $chatID > 0)and $cbdata != 'Informazioni') { //Se la password è sbagliata
    $menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Informazioni'
        ),
    );
    $txt = "📡 <b>Informazioni Server</b> 📡\n\nPassword <b>errata</b>! Riprova, oppure torna al menù informazioni.";
    sm($chatID, $txt, $menu, 'HTML', false, false, true);
    setPage($userID, 'ServerInfo-Password');
}
//Annulla Operazione 2
if(strpos($cbdata, "annulla_azione2")===0 and $isadminbot == $chatID)
{
    $menu[] = array(
        array(
            "text" => "🔙 Indietro",
            "callback_data" => "Comandi"
        ),
    );
    cb_reply($cbid, 'Operazione Annullata', false, $cbmid, "🚫 <b>Operazione Annullata</b> 🚫\n\nOperazione annullata con successo.", $menu, 'HTML');
    setPage($userID);
    exit;
}
//Annulla Operazione 4
if(strpos($cbdata, "annulla_azione4")===0 and $isadminbot == $chatID)
{
    $menu[] = array(
        array(
            "text" => "🔙 Indietro",
            "callback_data" => "Risposte"
        ),
    );
    cb_reply($cbid, 'Operazione Annullata', false, $cbmid, "🚫 <b>Operazione Annullata</b> 🚫\n\nOperazione annullata con successo.", $menu, 'HTML');
    setPage($userID);
    exit;
}
//GuidaBot
if(($cbdata == "GuidaBot" or strpos($msg, "/guidabot")=== 0) and $userID == $isadminbot and $chatID > 0) {
    $menu[] = array(
        array(
            'text' => 'Funzioni del Bot',
            'callback_data' => 'FunzioniBot'
        ),
    );
    $menu[] = array(
        array(
            'text' => 'Creare Comandi dal Bot',
            'callback_data' => 'CreaComandiBot'
        ),
    );
    $menu[] = array(
        array(
            'text' => 'Creare Plugin',
            'callback_data' => 'CreaPlugin'
        ),
    );
    $menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Informazioni'
        ),
    );
    $txt = "📖 <b>Docs Bot</b> 📖\n\n<i>Documentazione base delle funzionalità e potenzialità del bot.</i>";
    if($cbdata){
        cb_reply($cbid, 'Documentazione Bot', false, $cbmid, $txt, $menu, 'HTML');
    }else{
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
    }
    setPage($userID, 'Documentazione');
}
//Funzioni del Bot (Documentazione)
if(strpos($cbdata, "FunzioniBot")=== 0 and $isadminbot == $chatID and $chatID > 0)
{
    $menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'GuidaBot'
        ),
    );
    $txt = "<b>Funzioni del Bot</b>\n\n<i>Lista delle funzioni del bot</i>

  ● Documentazione del bot;
  ● Informazioni del server in cui è hostato il bot;
  ● Aggiungi/Rimuovi - Abilita/Disabilita Plugin con lista dei Plugin Attivi direttamente dal bot;
  ● Aggiungi/Modifica/Rimuovi comandi direttamente dal bot;
  ● Aggiungi/Modifica/Rimuovi risposte automatiche direttamente dal bot;
  ● Statistiche Utenti direttamente sul bot;
  ● Ban/Unban utenti dal bot;
  ● Invia post globali dal bot verso utenti (Chat Private)/gruppi(in cui il bot è presente).
  ● Aggiungi/Rimuovi admin direttamente dal bot;
  ● Modifica la configurazione del bot;";
    cb_reply($cbid, 'Funzioni del Bot', false, $cbmid, $txt, $menu, 'HTML');
    setPage($userID, 'FunzioniBot');
    exit;
}
//Crea comandi del Bot (Documentazione)
if(strpos($cbdata, "CreaComandiBot")=== 0 and $isadminbot == $chatID and $chatID > 0)
{
    $menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'GuidaBot'
        ),
    );
    $txt = "<b>Crea Comandi dal Bot</b>\n\n<i>Guida per creare direttamente comandi dal bot con i tasti.</i>

<a href=\"https://s18.postimg.org/s9erbl7d5/Esempio.png\">1️⃣ </a> Dal pannello admin, spostarsi sul menù \"Comandi\".
2️⃣  Aggiungere un nuovo comando (Nome casuale).
3️⃣  Quando verrà chiesto con che cosa dvorò rispondere il bot al comando stabilito seguire la sequente legenda:

● | => Per segnalare l\'esistenza di un menù\n● $ => Per la divisione in righe\n● - => Per la divisione in bottoni\n● * per gli URL e & per i <i>callback_data</i>
\n\n<b>Esempio:</b>\nCiao! <code>|Testo per l'url*http://url.dominio-Testo Callback data&callback che vuoi$\" . 'Altro link*http://url.dominio-</code>

<i>In foto puoi vedere un esempio del risultato finale.</i>

";
    cb_reply($cbid, 'Crea Comandi dal Bot', false, $cbmid, $txt, $menu, 'HTML');
    setPage($userID, 'CreaComandiBot');
    exit;
}
//Crea Plugin
if(strpos($cbdata, "CreaPlugin")=== 0 and $isadminbot == $chatID and $chatID > 0)
{
    $menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'GuidaBot'
        ),
    );
    $txt = "<b>CreaPlugin</b>\n\n<i>Guida per creare plugin per il bot.</i>

<a href=\"https://s18.postimg.org/5eggml67d/esempio_plugin.png\">❗️</a> Un plugin per essere letto ha bisogno di avere <b>due requisiti</b>:
• Una <b>variabile \$plugin</b> con il nome del plugin da volere mostrare;
• Una <b>funzione</b> con il valore dato a <b>\$plugin</b> come prefisso (nel caso ci fossere spazi, sostituirli con _) e <b>_info</b> come suffisso che come <b>return</b> ha la descrizione del plugin.

<b>Esempio:</b>

<code>\$plugin = 'Esempio di plugin';
function Esempio_di_plugin_info(){
return 'Questa è la descrizione!';
}</code>

<i>In foto puoi vedere un esempio del risultato finale.</i>";
    cb_reply($cbid, 'Crea Plugin', false, $cbmid, $txt, $menu, 'HTML');
    setPage($userID, 'CreaPlugin');
    exit;
}
//Richieste
if(($cbdata == "Richieste" or strpos($msg, "/richieste")===0) and $userID == $isadminbot and $chatID > 0) {
	$menu[] = array(
        array(
            'text' => '📱 Comandi',
            'callback_data' => 'Comandi'
        ),
        array(
            'text' => 'Risposte 🖌',
            'callback_data' => 'Risposte'
        )
    );
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'admin'
        ),
    );

	$txt = "📦 <b>Richieste</b> 📦\n\n<i>Gestisci da questo menù i comandi e le risposte automatiche del bot.</i>\n\n📱 <b>Comandi</b>: Gestisci i comandi del bot.\n🖌 <b>Risposte</b>: Gestisci le risposte automatiche per gli utenti.";
        if($cbdata){
			cb_reply($cbid, 'Richieste', false, $cbmid, $txt, $menu, 'HTML');
        } else{
            sm($chatID, $txt, $menu, 'HTML', false, false, true);
		}
		setPage($userID, 'Richieste');
}
