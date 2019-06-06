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

if ($chatID > 0) {
    if (!$nome) $nome ='';
    if (!$cognome) $cognome ='';
    if (!$username) $username ='';
    $q  = $db->query("SELECT * FROM $table WHERE Chat_ID = $chatID");
    $q5 = $db->query("SELECT count(*) FROM $table WHERE Chat_ID = $chatID");
    if ($q5->fetchColumn() == 0) {
        if (in_array($chatID, $adminbot)) $type = 'Admin'; else $type = 'User';
        $sth = $db->prepare("INSERT INTO $table (Type, Name, Lastname, Username, Chat_ID, Chat, Antiflood, Page) VALUES (:type, :nome, :cognome, :username, :chatID, :chat, :antiflood, '')");
        $sth->bindParam(':type', $type, PDO::PARAM_STR, 7);
        $sth->bindParam(':nome', $nome, PDO::PARAM_STR, 7);
        $sth->bindParam(':cognome', $cognome, PDO::PARAM_STR, 7);
        $sth->bindParam(':username', $username, PDO::PARAM_STR, 7);
        $sth->bindParam(':chatID', $chatID, PDO::PARAM_INT);
        $sth->bindParam(':chat', $chat='Off', PDO::PARAM_STR, 7);
        $sth->bindParam(':antiflood', $SetAntiflood = time().'_0', PDO::PARAM_STR, 7);
        $sth->execute();
    } else {
        $u = $q->fetch(PDO::FETCH_ASSOC);
        if ($u['Page'] == 'disable') {
            $sth = $db->prepare("UPDATE $table SET Page = '' WHERE Chat_ID = :chatID");
            $sth->bindParam(':chatID', $chatID, PDO::PARAM_INT);
            $sth->execute();
        }
        if ($u['Page'] == 'ban') {
            sm($chatID, 'Sei stato <b>bannato</b> dal Bot.');
            exit;
        }
    }
}
