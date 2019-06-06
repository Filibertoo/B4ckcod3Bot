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

$plugin = 'Inline';
function Inline_info()
{
    return 'Un plugin che permette la creazione di messaggi <code>inline!</code>';
}
function inline($inline, $json, $time = 5)
{
    global $API;
    global $api;
    $ris  = json_encode($json);
    $args = array(
        'inline_query_id' => $inline,
        'results' => $ris,
        'cache_time' => $time
    );
    $rr   = post('post', $API . 'answerInlineQuery', $args);
}
if ($update['inline_query']) {
    $inline   = $update['inline_query']['id'];
    $msg      = $update['inline_query']['query'];
    $userID   = $update['inline_query']['from']['id'];
    $username = $update['inline_query']['from']['username'];
    $name     = $update['inline_query']['from']['first_name'];
    $json     = array(
        array(
            'type' => 'article',
            'id' => 'kakfieokakfieofo',
            'title' => 'Invia messaggio...',
            'description' => 'Premi qui 1',
            'message_text' => 'Questo appare: testo 1',
            'parse_mode' => 'Markdown'
        ),
        array(
            'type' => 'article',
            'id' => 'alalalalalalala',
            'title' => 'Invia messaggio...',
            'description' => 'Premi qui 2',
            'message_text' => 'Questo appare: testo 2',
            'parse_mode' => 'Markdown'
        )
    );
    inline($inline, $json, 5);
}
