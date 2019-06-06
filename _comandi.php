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

require_once 'plugin.php';
if ($msg == '/tastiera') {
    $menu[] = array(
        'Voce 1'
    );
    $menu[] = array(
        'Voce 2',
        'Voce 3'
    );
    $menu[] = array(
        'Voce 5'
    );
    sm($chatID, 'Tastiera normale, /nascondi per nasconderla!', $menu, '', false, false, false);
}
if ($msg == '/plugins') {
    sm($userID, plugin_all_info($plugin_start), false, 'HTML');
}
if ($msg == '/nascondi') {
    $text = 'Tastiera Nascosta.';
    sm($chatID, $text, 'nascondi');
}
if ($msg == '/itastiera') {
    $menu[] = array(
        array(
            'text' => 'Notifica tipo 1',
            'callback_data' => '/test1'
        ),
        array(
            'text' => 'Notifica tipo 2',
            'callback_data' => '/test2'
        )
    );
    $menu[] = array(
        array(
            'text' => 'Notifica tipo 3',
            'callback_data' => '/test3'
        ),
        array(
            'text' => 'Notifica tipo 4',
            'callback_data' => '/test4'
        )
    );
    $menu[] = array(
        array(
            'text' => 'Sottomenù',
            'callback_data' => '/sottomenu'
        )
    );
    sm($chatID, 'Tastiera inline.', $menu, 'Markdown', false, false, true);
}
if ($cbdata == '/test1') {
    cb_reply($cbid, 'Primo tipo di notifica', false);
}
if ($cbdata == '/test2') {
    cb_reply($cbid, 'Secondo tipo di notifica', true);
}
if ($cbdata == '/test3') {
    cb_reply($cbid, 'Primo tipo di notifica', false, $cbmid, 'Messaggio Modificato');
}
if ($cbdata == '/test4') {
    cb_reply($cbid, 'Secondo tipo di notifica', true, $cbmid, 'Messaggio Modificato');
}
if ($cbdata == '/sottomenu') {
    $menu[] = array(
        array(
            'text' => 'Notifica tipo 1',
            'callback_data' => '/test1'
        ),
        array(
            'text' => 'Notifica tipo 2',
            'callback_data' => '/test2'
        )
    );
    cb_reply($cbid, 'Ecco il sottomenù!', true, $cbmid, 'Ecco un <b>sottomenù</b>!', $menu, 'HTML');
}
if ($msg == '/foto') {
    si($chatID, 'DATA/foto.jpg', false, 'Questa è la didascalia');
}
