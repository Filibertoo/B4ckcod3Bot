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

$char_seq = '[mod]'; //Special sequence for call back plugins
$disabled_plugin = json_decode(file_get_contents('DATA/_plugin.json'), true);
function plugin_all_info($plugin_start)
{
    $o = 1;
    foreach ($plugin_start as $nome) {
        if (empty($nome['name'])) {
            continue;
        }
        if (empty($nome['description'])) {
            continue;
        }
        $return .= $o . '. <b>' . $nome['name'] . '</b> - ' . $nome['description'] . "\n";
        $o++;
    }
    if (!empty($return)) {
        return '📃 Lista Plugins Attivi 📃' . "\n\n<i>Lista completa dei soli plugin abilitati/attivi.</i>\n\n" . $return;
    } else {
        return false;
    }
}
$plugin_start = array();
foreach (scandir('PLUGIN') as $file) {
    if (in_array($file, $disabled_plugin)) { //Se ci sono plugin contenuti nel fil json
        continue; //Li salto e non li conto
    }
    include_once 'PLUGIN/' . $file;
    if (empty($plugin)) {
        continue;
    }
    if (function_exists(replaceUL($plugin) . '_info')) {
        $l                = array();
        $l['name']        = $plugin;
        $i                = replaceUL($plugin) . '_info';
        $l['description'] = $i();
        $l['file']        = $file;
    } else {
        continue;
    }
    $plugin_start[$file] = $l;
    unset($plugin);
}

// ====== [ Management Plugins ] =======
if((($cbdata == "Plugins" or strpos($msg, "/plugins")===0)) and $userID == $isadminbot and $chatID > 0) {
    $menu[] = array(
        array(
            'text' => '➕ Aggiungi',
            'callback_data' => 'AggiungiPlugins'
        ),
        array(
            'text' => 'Rimuovi ➖',
            'callback_data' => 'RimuoviPlugins'
        ),
    );
	$menu[] = array(
        array(
            'text' => '✳️ Abilita/Disabilita 🚫',
            'callback_data' => 'AbilitaDisabilita'
        ),
    );
    $menu[] = array(
        array(
            'text' => '🛠 Configurazione Plugins 🛠',
            'callback_data' => 'ConfigurazionePlugins'
        ),
    );
	$menu[] = array(
        array(
            'text' => '📃 Lista Plugins Attivi 📃',
            'callback_data' => 'ListaPlugins'
        ),
    );
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'admin'
        ),
    );
	$txt = "<b>🗳 Gestione Plugins 🗳</b>\n\n<i>Gestisci da questo menù i plugins del bot.</i>\n\n➕ <b>Aggiungi Plugins</b>: Aggiungi plugins al bot.\n➖ <b>Rimuovi Plugins</b>: Rimuovi plugins al bot.\n🛠 <b>Configurazione Plugins</b>: Configura i plugins da questo menù.\n🚸 <b>Abilita/Disabilita</b>: Abilita/Disabilita plugins senza eliminarli.\n📃 <b>Lista Plugins</b>: Visualizza la lista dei plugins attivi.";
    if($cbdata){
        cb_reply($cbid, 'Gestione Plugins', false, $cbmid, $txt, $menu, 'HTML');
    } else {
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
    }
    setPage($userID, 'Gestione Plugins');
}

// ====== [ Add Plugins ] =======
if(($cbdata == "AggiungiPlugins" or strpos($msg, "/aggiungiplugins")===0) and $userID == $isadminbot and $chatID > 0) {
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Plugins'
        ),
    );
	$txt = "<b>➕ Aggiungi Plugins ➕</b>\n\n<i>Con questa funzione puoi aggiungere dei plugin direttamente dal bot, senza dover collegarti al tuo host/VPS.</i>\n\nInvia il <b>plugin</b> che desideri aggiungere/aggiornare al bot.";
    if($cbdata){
		cb_reply($cbid, 'Aggiungi Plugins', false, $cbmid, $txt, $menu, 'HTML');
    } else {
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
	}
	setPage($userID, 'Aggiungi Plugins');
}
if ($u['Page'] == 'Aggiungi Plugins' and $document and $userID == $isadminbot and $chatID > 0) {
    $nodoc = $update['message']['document']['file_name'];
    $e = explode('.', $nodoc);
    if (strtolower($e[1]) == 'php') {
        downloadFile($document, 'PLUGIN/' . $nodoc);
    	$menu[] = array(
            array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Plugins'
            ),
        );
		$txt = "<b>➕ Aggiungi Plugins ➕</b>\n\nIl plugin <b>$nodoc</b> è stato scaricato e caricato con successo al tuo host/VPS.";
		sm($chatID, $txt, $menu, 'HTML', false, false, true);
        setPage($userID, 'Plugin Caricato');
        exit;
    } else {
    	$menu[] = array(
            array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Plugins'
            ),
        );
        $txt = "<b>➕ Aggiungi Plugins ➕</b>\n\n❌ <b>Errore:</b> Il file plugin deve avere come estensione .php";
		sm($chatID, $txt, $menu, 'HTML', false, false, true);
    }
}

// ====== [ Remove Plugins ] =======
if(($cbdata == "RimuoviPlugins" or strpos($msg, "/RimuoviPlugins")===0) and $userID == $isadminbot and $chatID > 0) {
    $tastiera    = array();
    $r           = 0;
    $valori      = scandir('PLUGIN');
    $valori      = array_diff($valori, array('.', '..'));
    if (!empty($valori)) {
        $n           = 1;
        $k           = 0;
        $limite_riga = 4;
        foreach ($valori as $valore) {
            if ($valore == '.' or $valore == '..') {
                continue;
            }
            if ($n > $limite_riga) {
                $n = 1;
                $k++;
            }
            $tastiera[$k][] = array(
                'text' => $valore,
                'callback_data' => 'unlink|' . $valore
            );
            $n++;
        }
		$tastiera[] = array(
            array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Plugins'
			)
		);
		$txt = "<b>➕ Rimuovi Plugins ➕</b>\n\n<i>Con questa funzione puoi rimuovere dei plugin direttamente dal bot, senza dover collegarti al tuo host/VPS. Questa operazione cancellerà definitivamente il file dal tuo host/VPS.</i>\n\nSeleziona il <b>plugin</b> che desideri cancellare dal bot.";
		cb_reply($cbid, 'Rimuovi Plugins', false, $cbmid, $txt, $tastiera, 'HTML');
		setPage($userID, 'Rimuovi Plugins');
    } else {
    	$menu[] = array(
            array(
                'text' => '🔙 Indietro',
                'callback_data' => 'Plugins'
            ),
        );
		$txt = "<b>➕ Rimuovi Plugins ➕</b>\n\n❌ <b>Errore:</b> La cartella dei file dei plugins, è vuota.";
		cb_reply($cbid, 'Rimuovi Plugins', false, $cbmid, $txt, $menu, 'HTML');
    }
}
if (strpos($cbdata, 'unlink') === 0 and $userID == $isadminbot and $chatID > 0 and $u['Page'] == 'Rimuovi Plugins') {
    $i       = explode('|', $cbdata);
    $file    = $i[1];
    $menu1[] = array(
        array(
            'text' => '❌ Non Eliminare',
            'callback_data' => 'dont'
        ),
		array(
            'text' => 'Elimina ✅',
            'callback_data' => 'delete|' . $file
        )
    );
    shuffle($menu1);
	$menu1[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'RimuoviPlugins'
        ),
    );
	$txt = "<b>➕ Rimuovi Plugins ➕</b>\n\nVuoi davvero <b>eliminare</b> il file?";
    cb_reply($cbid, 'Rimuovi Plugin Selezionato', false, $cbmid, $txt, $menu1, 'HTML');
	setPage($userID, 'Rimuovi Plugin Selezionato');
    exit;
}
if (strpos($cbdata, 'delete') === 0 and $userID == $isadminbot and $chatID > 0) {
    $i    = explode('|', $cbdata);
    $file = $i[1];
    unlink('PLUGIN/' . $file);
		$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Plugins'
        ),
    );
	$txt = "<b>➕ Rimuovi Plugins ➕</b>\n\nIl plugin <b>$file</b> è stato <b>eliminato</b>.";
    cb_reply($cbid, 'Plugin Eliminato', false, $cbmid, $txt, $menu, 'HTML');
	setPage($userID, 'Plugin Eliminato');
    exit;
}
if ($cbdata == 'dont' and $userID == $isadminbot and $chatID > 0) {
	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Plugins'
        ),
    );
	$txt = "<b>➕ Rimuovi Plugins ➕</b>\n\nIl plugin selezionato, <b>non</b> è stato <b>eliminato</b>.";
    cb_reply($cbid, 'Plugin Non Eliminato', false, $cbmid, $txt, $menu, 'HTML');
	setPage($userID, 'Plugin Non Eliminato');
}

// ====== [ Enable-Disable Plugins ] =======
if (strpos($msg, '/disabilita ') === 0 and $userID == $isadminbot and $chatID > 0) {
    $msg1                   = str_replace('/disabilita ', '', $msg);
    $disabled_plugin[$msg1] = $msg1;
    sm($userID, 'Il plugin <b>' . $msg1 . '</b> è stato disabilitato!', false, 'HTML');
}
if (strpos($msg, '/abilita ') === 0 and $userID == $isadminbot) {
    $msg1 = str_replace('/abilita ', '', $msg);
    unset($disabled_plugin[$msg1]);
    sm($userID, 'Il plugin <b>' . $msg1 . '</b> è stato riabilitato!', false, 'HTML');
}
if(($cbdata == "AbilitaDisabilita" or strpos($msg, "/abilitadisabilita")===0) and $userID == $isadminbot and $chatID > 0) {

    $tastiera2    = array();
    $r2           = 0;
    $valori2      = scandir('PLUGIN');
    $valori2      = array_diff($valori2, array('.', '..'));
    if (!empty($valori2)) {
        $n2           = 1;
        $k2           = 0;
        $limite_riga2 = 4;
        foreach ($valori2 as $valore2) {
            if ($valore2 == '.' or $valore2 == '..') {
                continue;
            }
            if ($n2 > $limite_riga2) {
                $n2 = 1;
                $k2++;
            }
        ;
            $tastiera2[$k2][] = array(
                'text' => $valore2,
                'callback_data' => 'unlink2|' . $valore2
            );
            $n2++;
        }
			$tastiera2[] = array(
                array(
                    'text' => '🔙 Indietro',
                    'callback_data' => 'Plugins'
				)
			);
		$txt2 = "<b>✳️ Abilita/Disabilita 🚫</b>\n\n<i>Con questa funzione puoi abilitare/disabilitare uno o più plugin del bot senza dover cancellare nessun file.</i>\n\nSeleziona il <b>plugin</b> che desideri abilitare/disabilitare al bot.";
		cb_reply($cbid, 'Abilita/Disabilita Plugins', false, $cbmid, $txt2, $tastiera2, 'HTML');
		setPage($userID, 'AbilitaDisabilita');
    }
}
if (strpos($cbdata, 'unlink2') === 0 and $userID == $isadminbot and $chatID > 0 and $u['Page'] == 'AbilitaDisabilita') {
    $i2       = explode('|', $cbdata);
    $file2    = $i2[1];
    $menu2[] = array(
        array(
            'text' => '🚫 Disabilita',
            'callback_data' => "Disabilita|$file2"
        ),
		array(
            'text' => 'Abilita ✳️',
            'callback_data' => "Abilita|$file2"
        )
    );
    shuffle($menu2);
	$menu2[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'AbilitaDisabilita'
        ),
    );
	$txt2 = "<b>✳️ Abilita/Disabilita 🚫</b>\n\nVuoi abilitare o disabilitare questo plugin?";
    cb_reply($cbid, 'Abilita/Disabilita Plugin Selezionato', false, $cbmid, $txt2, $menu2, 'HTML');
	setPage($userID, 'Abilita/Disabilita Plugin Selezionato');
    exit;
}
if (strpos($cbdata, 'Disabilita') === 0 and $userID == $isadminbot and $chatID > 0 and $u['Page'] == 'Abilita/Disabilita Plugin Selezionato') {
    $i2       = explode('|', $cbdata);
    $file2    = $i2[1];
	$disabled_plugin[$file2] = $file2;

	$menu2[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Plugins'
        ),
    );
	$txt2 = "<b>✳️ Abilita/Disabilita 🚫</b>\n\nIl Plugin selezionato è stato <b>disabilitato</b>.";
    cb_reply($cbid, 'Plugin Disabilitato', false, $cbmid, $txt2, $menu2, 'HTML');
	setPage($userID, 'Plugin disabilitato');
}
if (strpos($cbdata, 'Abilita') === 0  and !(strpos($cbdata, 'AbilitaDisabilita') === 0)  and $userID == $isadminbot and $chatID > 0 and $u['Page'] == 'Abilita/Disabilita Plugin Selezionato') {
    $i2       = explode('|', $cbdata);
    $file2    = $i2[1];
    unset($disabled_plugin[$file2]);

	$menu2[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Plugins'
        ),
    );
	$txt2 = "<b>✳️ Abilita/Disabilita 🚫</b>\n\nIl Plugin selezionato è stato <b>abilitato</b>.";
    cb_reply($cbid, 'Plugin Abilitato', false, $cbmid, $txt2, $menu2, 'HTML');
	setPage($userID, 'Plugin Abilitato');
}

// ===== [ Config Plugins ] ======
if(($cbdata == "ConfigurazionePlugins" or strpos($msg, "/configurazioneplugins")===0) and $userID == $isadminbot) {

        $menu    = array();
        $r2           = 0;
        $valori2      = scandir('PLUGIN');
        $valori2      = array_diff($valori2, array('.', '..'));
        if (!empty($valori2)) {
            $n2           = 1;
            $k2           = 0;
            $limite_riga2 = 1;
            foreach ($valori2 as $valore2) {
                if ($valore2 == '.' or $valore2 == '..') {
                    continue;
                }
                if ($n2 > $limite_riga2) {
                    $n2 = 1;
                    $k2++;
                }
                $check = strpos(strtolower($valore2), $char_seq);
                if ($check !== false){

                    $FileNameExploded = explode($char_seq, $valore2)[1]; //return the name of file without special sequence
                    $menu[$k2][] = array(
                        'text' => $FileNameExploded,
                        'callback_data' => $valore2
                    );
                } else {
                    continue;
                }
                $n2++;
            }
        }

    $menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Plugins'
        ),
    );
    $txt = "🛠 <b>Configurazione Plugins</b> 🛠\n\n<i>Configura e gestisci da questo menù le varie impostazioni dei plugin, che permettono la configurazione direttamente dal bot.</i>";
    if($cbdata){
        cb_reply($cbid, 'Configurazione Plugins', false, $cbmid, $txt, $menu, 'HTML');
    } else{
        sm($chatID, $txt, $menu, 'HTML', false, false, true);
    }
    setPage($userID, 'ConfigurazionePlugins');
}

// ====== [ Plugins List ] =======
if(($cbdata == "ListaPlugins" or strpos($msg, "/listaplugins")===0) and $userID == $isadminbot) {

	$menu[] = array(
        array(
            'text' => '🔙 Indietro',
            'callback_data' => 'Plugins'
        ),
    );
	$plugins = plugin_all_info($plugin_start);
    if($cbdata){
		cb_reply($cbid, 'Lista Plugins', false, $cbmid, plugin_all_info($plugin_start), $menu, 'HTML');
    } else {
        sm($chatID, plugin_all_info($plugin_start), $menu, 'HTML', false, false, true);
	}
	setPage($userID, 'Lista Plugins');
}

//Questa riga va sempre ultima in questo file
file_put_contents('DATA/_plugin.json', json_encode($disabled_plugin));
