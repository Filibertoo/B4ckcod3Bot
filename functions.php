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

$API = 'https://api.telegram.org/' . $api . '/'; //Token del bot
require_once 'database.php'; //Richiedi file
function post($type, $url, $data = []) //Funzione per richieste POST
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if (strtoupper($type) == 'GET') {
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if (strtoupper($type) == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}
function multipart($url, $data)
{
    $ch               = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Content-Type:multipart/form-data'
    ));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output    = curl_exec($ch);
    curl_close($ch);
    return $output;
}
function sendMessage($chat_id, $text, $parse_mode = 'default', $disable_web_page_preview = 'default', $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default', $resize_keyboard = false) //Funzione per mandare messaggi
{
    global $API;
    global $config;
    global $update;
    if ($config['stato_automatico']) {
        sendChatAction($chat_id, 'typing');
    }
    if ($parse_mode == 'default') {
        $parse_mode = $config['formattazione_predefinita'];
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm   = json_encode($rm);
    $args = array(
        'chat_id' => $chat_id,
        'text' => $text,
        'disable_notification' => $disable_notification,
        'parse_mode' => $parse_mode
    );
    if ($disable_web_page_preview) {
        $args['disable_web_page_preview'] = $disable_web_page_preview;
    }
    if ($reply_to_message_id) {
        $args['reply_to_message_id'] = $reply_to_message_id;
    }
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    if ($text) {
        $rr   = post('post', $API . __FUNCTION__, $args);
        $ar   = json_decode($rr, true);
        $ok   = $ar['ok'];
        $e403 = $ar['error_code'];
        if ($e403 == '403') {
            return false;
        } elseif ($e403) {
            return false;
        } else {
            return $rr;
        }
    }
}
function sendPhoto($chat_id, $photo, $caption = '', $parse_mode, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default', $resize_keyboard = false)
{
    global $API;
    global $config;
    global $update;
    if ($config['stato_automatico']) {
        sendChatAction($chat_id, 'upload_photo');
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($parse_mode == 'default') {
        $parse_mode = $config['formattazione_predefinita'];
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm = json_encode($rm);
    if (file_exists($photo)) {
        $photo = new CURLFile($photo);
    }
    $args = array(
        'chat_id' => $chat_id,
        'photo' => $photo,
        'caption' => $caption,
        'parse_mode' => $parse_mode,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $req       = json_decode(multipart($API . __FUNCTION__, $args), true);
    if ($req['ok']) {
        return json_encode($req);
    } else {
        return false;
    }
}
function cb_reply($id, $text, $alert = false, $cbmid = false, $ntext = false, $nmenu = false, $npm = 'pred', $dal = 'default')
{
    global $api;
    global $API;
    global $chatID;
    global $config;
    global $update;
    if ($npm === 'pred') {
        $npm = $config['formattazione_predefinita'];
    }
    if ($dal === 'default'){
        $dal = $config['nascondi_anteprima_link'];
    }
    $args = array(
        'callback_query_id' => $id,
        'text' => $text,
        'show_alert' => $alert
    );
    $r    = post('post', $API . 'answerCallbackQuery', $args);
    if ($cbmid) {
        if ($nmenu) {
            $rm = array(
                'inline_keyboard' => $nmenu
            );
            $rm = json_encode($rm);
        }
        $args = array(
            'chat_id' => $chatID,
            'message_id' => $cbmid,
            'text' => $ntext,
            'parse_mode' => $npm,
            'disable_web_page_preview' => $dal
        );
        if ($nmenu) {
            $args['reply_markup'] = $rm;
        }
        $r = post('post', $API . 'editMessageText', $args);
    }
}
function kickChatMember($chat_id, $user_id, $until_date)
{
    global $API;
    $args      = array(
        'chat_id' => $chat_id,
        'user_id' => $user_id,
        'until_date' => $until_date
    );
    $r         = post('post', $API . 'kickChatMember', $args);
    $rr        = json_decode($r, true);
    $risultato = $rr['ok'];
    if ($risultato == false) {
        return false;
    } else {
        return $richiesta;
    }
}
function unbanChatMember($chat_id, $user_id)
{
    global $API;
    $args      = array(
        'chat_id' => $chat_id,
        'user_id' => $user_id
    );
    $r         = post('post', $API . 'unbanChatMember', $args);
    $rr        = json_decode($r, true);
    $risultato = $rr['ok'];
    if ($risultato == false) {
        return false;
    } else {
        return $richiesta;
    }
}
function getMe()
{
    global $API;
    $richiesta = post('post', $API . 'getMe');
    $rr        = json_decode($richiesta, true);
    $risultato = $rr['ok'];
    if ($risultato == false) {
        return false;
    } else {
        return $richiesta;
    }
}
function getWebhookInfo()
{
    global $API;
    $richiesta = post('post', $API . __FUNCTION__, $args);
    $rr        = json_decode($richiesta, true);
    $risultato = $rr['ok'];
    if ($risultato == false) {
        return false;
    } else {
        return $richiesta;
    }
}
function setWebhook($url, $certificate = false, $max_connections = 40, $allowed_updates = false)
{
    global $API;
    $args      = array(
        'url' => $url,
        'certificate' => $certificate,
        'max_connections' => $max_connections,
        'allowed_updates' => $allowed_updates
    );
    $richiesta = post('post', $API . __FUNCTION__, $args);
    $rr        = json_decode($richiesta, true);
    $risultato = $rr['ok'];
    if ($risultato == false) {
        return false;
    } else {
        return $richiesta;
    }
}
function deleteWebhook()
{
    global $API;
    $richiesta = post('post', $API . __FUNCTION__, $args);
    $rr        = json_decode($richiesta, true);
    $risultato = $rr['ok'];
    if ($risultato == false) {
        return false;
    } else {
        return $richiesta;
    }
}
function getUpdates($offset = false, $limit = false, $timeout = false, $allowed_updates = false)
{
    global $API;
    if ($offset) {
        $data['offset'] = $offset;
    }
    if ($limit) {
        $data['limit'] = $limit;
    }
    if ($timeout) {
        $data['timeout'] = $timeout;
    }
    if ($allowed_updates) {
        $data['allowed_updates'] = $allowed_updates;
    }
    $richiesta = post('post', $API . __FUNCTION__, $args);
    $rr        = json_decode($richiesta, true);
    $risultato = $rr['ok'];
    if ($risultato == false) {
        return false;
    } else {
        return $richiesta;
    }
}
function editMessageText($chat_id, $message_id, $inline_message_id = false, $text, $parse_mode = 'default', $disable_web_page_preview = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($reply_markup) {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
        $rm = json_encode($rm);
    }
    if ($parse_mode == 'default') {
        $parse_mode = $config['formattazione_predefinita'];
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    $args = array(
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => $parse_mode,
        'disable_web_page_preview' => $disable_web_page_preview
    );
    if ($message_id) {
        $args['message_id'] = $message_id;
    } else {
        $args['inline_message_id'] = $inline_message_id;
    }
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function forwardMessage($chat_id, $from_chat_id, $message_id, $disable_notification = false)
{
    global $API;
    global $config;
    global $update;
    $args      = array(
        'chat_id' => $chat_id,
        'from_chat_id' => $from_chat_id,
        'message_id' => $message_id,
        'disable_notification' => $disable_notification
    );
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function sendChatAction($chat_id, $action = 'typing')
{
    global $API;
    global $config;
    global $update;
    $args      = array(
        'chat_id' => $chat_id,
        'action' => $action
    );
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function sendSticker($chat_id, $sticker, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($parse_mode == 'default') {
        $parse_mode = $config['formattazione_predefinita'];
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    if (file_exists($sticker)) {
        $sticker = new CURLFile($sticker);
    }
    $rm   = json_encode($rm);
    $args = array(
        'chat_id' => $chat_id,
        'sticker' => $sticker,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function sendVideo($chat_id, $video, $duration = false, $width = false, $height = false, $caption = '', $parse_mode, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($config['stato_automatico']) {
        sendChatAction($chat_id, 'upload_video');
    }
    if ($parse_mode == 'default') {
        $parse_mode = $config['formattazione_predefinita'];
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    if (file_exists($video)) {
        $video = new CURLFile($video);
    }
    $rm   = json_encode($rm);
    $args = array(
        'chat_id' => $chat_id,
        'video' => $video,
        'caption' => $caption,
        'parse_mode' => $parse_mode,
        'duration' => $duration,
        'width' => $width,
        'height' => $height,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $req       = json_decode(multipart($API . __FUNCTION__, $args), true);
    if ($req['ok']) {
        return json_encode($req);
    } else {
        return false;
    }
}
function sendAudio($chat_id, $audio, $caption = '', $parse_mode, $duration = false, $performer = false, $title = false, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($config['stato_automatico']) {
        sendChatAction($chat_id, 'upload_audio');
    }
    if ($parse_mode == 'default') {
        $parse_mode = $config['formattazione_predefinita'];
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm   = json_encode($rm);
    if (file_exists($audio)) {
        $audio = new CURLFile($audio);
    }
    $args = array(
        'chat_id' => $chat_id,
        'audio' => $audio,
        'caption' => $caption,
        'parse_mode' => $parse_mode,
        'duration' => $duration,
        'performer' => $performer,
        'title' => $title,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $req       = json_decode(multipart($API . __FUNCTION__, $args), true);
    if ($req['ok']) {
        return json_encode($req);
    } else {
        return false;
    }
}
function sendVideoNote($chat_id, $video_note, $duration = false, $length = false, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($config['stato_automatico']) {
        sendChatAction($chat_id, 'upload_document');
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm   = json_encode($rm);
    if (file_exists($video_note)) {
        $video_note = new CURLFile($video_note);
    }
    $args = array(
        'chat_id' => $chat_id,
        'video_note' => $video_note,
        'duration' => $duration,
        'length' => $length,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $req       = json_decode(multipart($API . __FUNCTION__, $args), true);
    if ($req['ok']) {
        return json_encode($req);
    } else {
        return false;
    }
}
function sendDocument($chat_id, $document, $caption = '', $parse_mode, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($config['stato_automatico']) {
        sendChatAction($chat_id, 'upload_document');
        sleep(0.3);
        sendChatAction($chat_id, 'upload_audio');
    }
    if ($parse_mode == 'default') {
        $parse_mode = $config['formattazione_predefinita'];
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm   = json_encode($rm);
    if (file_exists($document)) {
        $document = new CURLFile($document);
    }
    $args = array(
        'chat_id' => $chat_id,
        'caption' => $caption,
        'parse_mode' => $parse_mode,
        'disable_notifiction' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id,
        'document' => $document
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $req       = json_decode(multipart($API . __FUNCTION__, $args), true);
    if ($req['ok']) {
        return json_encode($req);
    } else {
        return false;
    }
}
function sendVoice($chat_id, $voice, $caption = '', $parse_mode, $duration = false, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($config['stato_automatico']) {
        sendChatAction($chat_id, 'record_audio');
        sleep(0.3);
        sendChatAction($chat_id, 'upload_audio');
    }
    if ($parse_mode == 'default') {
        $parse_mode = $config['formattazione_predefinita'];
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm   = json_encode($rm);
    if (file_exists($voice)) {
        $voice = new CURLFile($voice);
    }
    $args = array(
        'chat_id' => $chat_id,
        'voice' => $voice,
        'caption' => $caption,
        'parse_mode' => $parse_mode,
        'duration' => $duration,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $req       = json_decode(multipart($API . __FUNCTION__, $args), true);
    if ($req['ok']) {
        return json_encode($req);
    } else {
        return false;
    }
}
function sendLocation($chat_id, $latitude, $longitude, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($config['stato_automatico']) {
        sendChatAction($chat_id, 'find_location');
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm   = json_encode($rm);
    $args = array(
        'chat_id' => $chat_id,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function sendVenue($chatID, $latitude, $longitude, $title, $address, $foursquare_id = false, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($config['stato_automatico']) {
        sendChatAction($chat_id, 'find_location');
    }
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm   = json_encode($rm);
    $args = array(
        'chat_id' => $chatID,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'title' => $title,
        'address' => $address,
        'foursquare_id' => $foursquare_id,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function sendContact($chat_id, $phone_number, $first_name, $last_name = false, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false, $inline = 'default')
{
    global $API;
    global $config;
    global $update;
    if ($inline == 'default') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    if ($resize_keyboard == true) {
        $inline = false;
    }
    if ($disable_web_page_preview == 'default') {
        $disable_web_page_preview = $config['nascondi_anteprima_link'];
    }
    if (!$inline) {
        if ($resize_keyboard == true) {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $reply_markup,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm   = json_encode($rm);
    $args = array(
        'chat_id' => $chat_id,
        'phone_number' => $phone_number,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    if ($last) {
        $args['last_name'] = $last;
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function leaveChat($chat_id)
{
    global $API;
    $args = array(
        'chat_id' => $chat_id
    );
    $rr   = post('post', $API . __FUNCTION__, $args);
    $rrr  = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function getChat($chat_id)
{
    global $API;
    $args = array(
        'chat_id' => $chat_id
    );
    $rr   = post('post', $API . __FUNCTION__, $args);
    $rrr  = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function getChatAdministrators($chat_id)
{
    global $API;
    $args = array(
        'chat_id' => $chat_id
    );
    $rr   = post('post', $API . __FUNCTION__, $args);
    $rrr  = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function getChatMembersCount($chat_id)
{
    global $API;
    $args = array(
        'chat_id' => $chat_id
    );
    $rr   = post('post', $API . __FUNCTION__, $args);
    $rrr  = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function getChatMember($chat_id, $user_id)
{
    global $API;
    $args = array(
        'chat_id' => $chat_id,
        'user_id' => $user_id
    );
    $rr   = post('post', $API . __FUNCTION__, $args);
    $rrr  = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function getFile($file_id)
{
    global $API;
    $args = array(
        'file_id' => $file_id
    );
    $rr   = post('post', $API . __FUNCTION__, $args);
    $rrr  = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function getUserProfilePhotos($user_id, $offset = false, $limit = false)
{
    global $API;
    $data = array(
        'user_id' => $user_id
    );
    if ($offset) {
        $data['offset'] = $offset;
    }
    if ($limit) {
        $data['limit'] = $limit;
    }
    $richiesta = post('post', $API . __FUNCTION__, $args);
    $rr        = json_decode($richiesta, true);
    $risultato = $rr['ok'];
    if ($risultato == false) {
        return false;
    } else {
        return $richiesta;
    }
}
function editMessageCaption($chat_id, $message_id, $inline_message_id = false, $caption = '', $reply_markup = false)
{
    global $API;
    global $config;
    global $update;
    if ($message_id and $chatID) {
        if ($reply_markup) {
            $rm = array(
                'inline_keyboard' => $reply_markup
            );
            $rm = json_encode($rm);
        }
        $args = array(
            'chat_id' => $chat_id,
            'caption' => $caption,
            'message_id' => $message_id
        );
        if ($reply_markup) {
            $args['reply_markup'] = $rm;
        }
    } else {
        if ($reply_markup) {
            $rm = array(
                'inline_keyboard' => $reply_markup
            );
            $rm = json_encode($rm);
        }
        $args = array(
            'caption' => $caption,
            'inline_message_id' => $inline_message_id
        );
        if ($reply_markup) {
            $args['reply_markup'] = $rm;
        }
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function editMessageReplyMarkup($chat_id, $message_id, $inline_message_id = false, $reply_markup = false)
{
    global $API;
    global $config;
    global $update;
    if ($message_id and $chat_id) {
        if ($reply_markup) {
            $rm = array(
                'inline_keyboard' => $reply_markup
            );
            $rm = json_encode($rm);
        }
        $args = array(
            'chat_id' => $chatID,
            'message_id' => $message_id
        );
        if ($reply_markup) {
            $args['reply_markup'] = $rm;
        }
    } else {
        if ($reply_markup) {
            $rm = array(
                'inline_keyboard' => $reply_markup
            );
            $rm = json_encode($rm);
        }
        $args = array(
            'inline_message_id' => $inline_message_id
        );
        if ($reply_markup) {
            $args['reply_markup'] = $rm;
        }
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function answerCallbackQuery($callback_query_id, $text, $show_alert = false, $url = false, $cache_time = 0)
{
    global $API;
    global $config;
    global $update;
    $args = array(
        'callback_query_id' => $callback_query_id,
        'cache_time' => $cache_time,
        'text' => $text,
        'url' => $url
    );
    if ($show_alert) {
        $args['show_alert'] = true;
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function answerInlineQuery($inline_query_id, $results, $cache_time = 300, $is_personal = false, $next_offset = false, $switch_pm_text = false, $switch_pm_parameter = false)
{
    global $API;
    global $config;
    global $update;
    $args = array(
        'inline_query_id' => $inline_query_id,
        'results' => $results,
        'cache_time' => $cache_time
    );
    if ($switch_pm_text and $switch_pm_parameter) {
        $args['switch_pm_text']      = $switch_pm_text;
        $args['switch_pm_parameter'] = $switch_pm_parameter;
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function sendGame($chat_id, $game_short_name, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false)
{
    global $API;
    global $config;
    global $update;
    if ($reply_markup) {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    $rm   = json_encode($rm);
    $args = array(
        'chat_id' => $chat_id,
        'game_short_name' => $game_short_name,
        'disable_notification' => $disable_notification
    );
    if ($reply_to_message_id) {
        $args['reply_to_message_id'] = $reply_to_message_id;
    }
    if ($reply_markup) {
        $args['reply_markup'] = $reply_markup;
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function setGameScore($user_id, $score, $force = false, $disable_edit_message, $chat_id, $message_id, $inline_message_id = false)
{
    global $API;
    global $config;
    global $update;
    if ($message_id and $chat_id) {
        $args = array(
            'user_id' => $user_id,
            'score' => $score,
            'force' => $force,
            'disable_edit_message' => $disable_edit_message,
            'chat_id' => $chat_id,
            'message_id' => $message_id
        );
    } else {
        $args = array(
            'user_id' => $user_id,
            'score' => $score,
            'force' => $force,
            'disable_edit_message' => $disable_edit_message,
            'inline_message_id' => $inline_message_id
        );
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function getGameHighScores($user_id, $chat_id, $message_id, $inline_message_id = false)
{
    global $API;
    global $config;
    global $update;
    if ($message_id and $chat_id) {
        $args = array(
            'user_id' => $user_id,
            'chat_id' => $chat_id,
            'message_id' => $message_id
        );
    } else {
        $args = array(
            'user_id' => $user_id,
            'inline_message_id' => $inline_message_id
        );
    }
    $rr        = post('post', $API . __FUNCTION__, $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function parseMessage($type = 'message', $markup = 'HTML')
{
    global $content;
    global $update;
    if (!empty($type)) {
        $msg       = $update[$type]['text'];
        $entities  = $update[$type]['entities'];
        $lengthmsg = strlen($msg);
        $msgi      = $msg;
        $s         = str_split($msgi);
        $i         = false;
        foreach ($entities as $format) {
            $typeformat = $format['type'];
            $offset     = $format['offset'];
            $length     = $format['length'];
            if ($typeformat == 'code') {
                if ($markup == 'HTML') {
                    $s[$offset]           = substr_replace($s[$offset], '<code>', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '</code>' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                } else {
                    $s[$offset]           = substr_replace($s[$offset], '`', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '`' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                }
            } elseif ($typeformat == 'bold') {
                if ($markup == 'HTML') {
                    $s[$offset]           = substr_replace($s[$offset], '<b>', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '</b>' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                } else {
                    $s[$offset]           = substr_replace($s[$offset], '*', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '*' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                }
            } elseif ($typeformat == 'italic') {
                if ($markup == 'HTML') {
                    $s[$offset]           = substr_replace($s[$offset], '<i>', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '</i>' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                } else {
                    $s[$offset]           = substr_replace($s[$offset], '_', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '_' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                }
            } elseif ($typeformat == 'pre') {
                if ($markup == 'HTML') {
                    $s[$offset]           = substr_replace($s[$offset], '<pre>', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '</pre>' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                } else {
                    $s[$offset]           = substr_replace($s[$offset], '```', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '```' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                }
            } elseif ($typeformat == 'text_link') {
                $url = $format['url'];
                if ($markup == 'HTML') {
                    $s[$offset]           = substr_replace($s[$offset], '<a href="' . $url . '">', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '</a>' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                } else {
                    $s[$offset]           = substr_replace($s[$offset], '[', strlen($s[$offset]) - 1, 0);
                    $s[$offset + $length] = '](' . $url . ')' . (isset($s[$offset + $length]) ? $s[$offset + $length] : '');
                }
            }
        }
        $msgi = implode('', $s);
        return $msgi;
    } else {
        return false;
    }
}
function deleteMessage($chat_id, $message_id)
{
    global $API;
    global $api;
    $args = array(
        'chat_id' => $chat_id,
        'message_id' => $message_id
    );
    $rr   = post('post', $API . __FUNCTION__, $args);
    $rrr  = json_decode($rr, true);
    if ($rrr['ok']) {
        return $rr;
    } else {
        return false;
    }
}
function sendInvoice($chat_id, $title, $description, $payload, $provider_token, $start_parameter, $currency, $prices, $photo_url = false, $photo_size = false, $photo_width = false, $photo_height = false, $need_name = false, $need_phone_number = false, $need_email = false, $need_shipping_address = false, $is_flexible = false, $disable_notification = false, $reply_to_message_id = 'default', $reply_markup = false)
{
    global $API;
    if ($reply_to_message_id == 'default' and $config['risposta_automatica'] === true) {
        $reply_to_message_id = $update['message']['message_id'];
    } elseif ($reply_to_message_id == 'default') {
        $reply_to_message_id = false;
    }
    $args = array(
        'chat_id' => $chat_id,
        'title' => $title,
        'description' => $description,
        'payload' => $payload,
        'provider_token' => $provider_token,
        'start_parameter' => $start_parameter,
        'currency' => $currency,
        'prices' => $prices,
        'photo_url' => $photo_url,
        'photo_size' => $photo_size,
        'photo_width' => $photo_width,
        'need_name' => $need_name,
        'need_phone_number' => $need_phone_number,
        'need_email' => $need_email,
        'need_shipping_address' => $need_shipping_address,
        'is_flexible' => $is_flexible,
        'disable_notification' => $disable_notification,
        'reply_to_message_id' => $reply_to_message_id
    );
    if ($reply_markup) {
        $rm = array(
            'inline_keyboard' => $reply_markup
        );
    }
    $rm = json_encode($rm);
    if ($reply_markup) {
        $args['reply_markup'] = $rm;
    }
    $rr  = post('post', $API . __FUNCTION__, $args);
    $rrr = json_decode($rr, true);
    if ($rrr['ok']) {
        return $rr;
    } else {
        return false;
    }
}
function answerShippingQuery($shipping_query_id, $ok = true, $shipping_options = false, $error_message = false)
{
    global $API;
    $args = array(
        'shipping_query_id' => $shipping_query_id,
        'ok' => $ok,
        'shipping_option' => $shipping_options,
        'error_message' => $error_message
    );
    $rr   = post('post', $API . __FUNCTION__, $args);
    $rrr  = json_decode($rr, true);
    if ($rrr['ok']) {
        return $rr;
    } else {
        return false;
    }
}
function answerPreCheckoutQuery($pre_checkout_query_id, $ok = true, $error_message = false)
{
    global $API;
    $args = array(
        'pre_checkout_query_id' => $pre_checkout,
        'ok' => $ok,
        'error_message' => $error_message
    );
    $rr   = post('post', $API . __FUNCTION__, $args);
    $rrr  = json_decode($rr, true);
    if ($rrr['ok']) {
        return $rr;
    } else {
        return false;
    }
}
function sm($chatID, $text, $rmf = false, $pm = 'pred', $dis = false, $replyto = 'default', $inline = 'pred', $dal = 'default')
{
    global $API;
    global $api;
    global $userID;
    global $update;
    global $config;
    if ($config['stato_automatico']) {
        sendChatAction($chatID, 'typing');
    }
    if ($pm === 'pred') {
        $pm = $config['formattazione_predefinita'];
    }
    if ($dal === 'default'){
        $dal = $config['nascondi_anteprima_link'];
    }
    if ($replyto === 'default' and $config['risposta_automatica'] === true) {
        $replyto = $update['message']['message_id'];
    } elseif ($replyto === 'default') {
        $replyto = false;
    }
    if ($inline === 'pred') {
        if ($config['tastiera_predefinita'] == 'inline') {
            $inline = true;
        } elseif ($config['tastiera_predefinita'] == 'normale') {
            $inline = false;
        }
    }
    if ($rmf == 'nascondi') {
        $inline = false;
    }
    if (!$inline) {
        if ($rmf == 'nascondi') {
            $rm = array(
                'hide_keyboard' => true
            );
        } else {
            $rm = array(
                'keyboard' => $rmf,
                'resize_keyboard' => true
            );
        }
    } else {
        $rm = array(
            'inline_keyboard' => $rmf
        );
    }
    $rm   = json_encode($rm);
    $args = array(
        'chat_id' => $chatID,
        'text' => $text,
        'disable_notification' => $dis,
        'parse_mode' => $pm
    );
    if ($dal) {
        $args['disable_web_page_preview'] = $dal;
    }
    if ($replyto) {
        $args['reply_to_message_id'] = $replyto;
    }
    if ($rmf) {
        $args['reply_markup'] = $rm;
    }
    if ($text) {
        $rr   = post('post', $API . 'sendMessage', $args);
        $ar   = json_decode($rr, true);
        $ok   = $ar['ok'];
        $e403 = $ar['error_code'];
        if ($e403 == '403') {
            return false;
        } elseif ($e403) {
            return false;
        } else {
            return $rr;
        }
    }
}
function si($chatID, $img, $rmf = false, $cap = '')
{
    global $API;
    global $api;
    global $userID;
    global $update;
    global $config;
    if ($config['stato_automatico']) {
        sendChatAction($chatID, 'upload_photo');
    }
    $rm = array(
        'inline_keyboard' => $rmf
    );
    $rm = json_encode($rm);
    if (strpos($img, '.')) {
        $img = str_replace('_index.php', '', $_SERVER['SCRIPT_URI']) . $img;
    }
    $args = array(
        'chat_id' => $chatID,
        'photo' => $img,
        'caption' => $cap
    );
    if ($rmf) {
        $args['reply_markup'] = $rm;
    }
    $rr   = post('post', $API . 'sendPhoto', $args);
    $ar   = json_decode($rr, true);
    $ok   = $ar['ok'];
    $e403 = $ar['error_code'];
    if ($e403 == '403') {
        return false;
    } elseif ($e403) {
        return false;
    } else {
        return $rr;
    }
}
function setPage($chatID, $page = '')
{
    global $table;
    global $db;
    $sth = $db->prepare('UPDATE ' . $table . ' SET page = :page WHERE chat_id = ' . $chatID);
    $sth->bindParam(':page', $page, PDO::PARAM_STR, 7);
    $sth->execute();
}
function sv($chatID, $vid, $rmf = false, $cap = '', $inline = false)
{
    global $API;
    global $api;
    global $config;
    if ($config['stato_automatico']) {
        sendChatAction($chatID, 'upload_video');
    }
    if ($rmf) {
        if ($inline) {
            $rm = array(
                'inline_keyboard' => $rmf
            );
            $rm = json_encode($rm);
        } else {
            $rm = array(
                'keyboard' => $rmf,
                'resize_keyboard' => true
            );
            $rm = json_encode($rm);
        }
    }
    $args = array(
        'chat_id' => $chatID,
        'video' => $vid,
        'caption' => $cap
    );
    if ($rmf) {
        $args['reply_markup'] = $rm;
    }
    $rr        = post('post', $API . 'sendVideo', $args);
    $rrr       = json_decode($rr, true);
    $risultato = $rrr['ok'];
    if ($risultato == true) {
        return $rr;
    } else {
        return false;
    }
}
function downloadFile($fileID, $path)
{
    global $API;
    global $api;
    $i        = json_decode(post('post', $API . 'getFile', array(
        'file_id' => $fileID
    )), true);
    $url      = 'https://api.telegram.org/file/' . $api . '/' . $i['result']['file_path'];
    $newfname = $path;
    $file     = fopen($url, 'rb');
    if ($file) {
        $newf = fopen($newfname, 'wb');
        if ($newf) {
            while (!feof($file)) {
                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }
    if ($file) {
        fclose($file);
    }
    if ($newf) {
        fclose($newf);
    }
}
function restrictChatMember($chat_id, $user_id, $until_date = false, $can_send_messages = true, $can_send_media_messages = true, $can_send_other_messages = true, $can_add_web_page_previews = true)
{
    global $API;
    global $api;
    if ($chat_id and $user_id) {
        $args = array(
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'until_date' => $until_date,
            'can_send_messages' => $can_send_messages,
            'can_send_media_messages' => $can_send_media_messages,
            'can_send_other_messages' => $can_send_other_messages,
            'can_add_web_page_previews' => $can_add_web_page_previews
        );
        $i    = json_decode(post('post', $API . __FUNCTION__, $args), true);
        if ($i['ok']) {
            return $i;
        } else {
            return false;
        }
    }
}
function promoteChatMember($chat_id, $user_id, $can_change_info = true, $can_post_messages = true, $can_edit_messages = true, $can_delete_messages = true, $can_invite_users = true, $can_restrict_members = true, $can_pin_messages = true, $can_promote_members = true)
{
    global $API;
    global $api;
    if ($chat_id and $user_id) {
        $args = array(
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'can_change_info' => $can_change_info,
            'can_post_messages' => $can_post_messages,
            'can_edit_messages' => $can_edit_messages,
            'can_delete_messages' => $can_delete_messages,
            'can_invite_users' => $can_invite_users,
            'can_restrict_members' => $can_restrict_members,
            'can_pin_messages' => $can_pin_messages,
            'can_promote_members' => $can_promote_members
        );
        $i    = json_decode(post('post', $API . __FUNCTION__, $args), true);
        if ($i['ok']) {
            return $i;
        } else {
            return false;
        }
    }
}
function degradeChatMember($chat_id, $user_id)
{
    global $API;
    global $api;
    if ($chat_id and $user_id) {
        $args = array(
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'can_change_info' => false,
            'can_post_messages' => false,
            'can_edit_messages' => false,
            'can_delete_messages' => false,
            'can_invite_users' => false,
            'can_restrict_members' => false,
            'can_pin_messages' => false,
            'can_promote_members' => false
        );
        $i    = json_decode(post('post', $API . __FUNCTION__, $args), true);
        if ($i['ok']) {
            return $i;
        } else {
            return false;
        }
    }
}
function exportChatInviteLink($chat_id)
{
    global $API;
    global $api;
    if ($chat_id) {
        $i = json_decode(post('post', $API . __FUNCTION__, array('chat_id' => $chat_id)), true);
        if ($i['ok']) {
            return $i;
        } else {
            return false;
        }
    }
}
function setChatPhoto($chat_id, $photo)
{
    global $api;
    global $API;
    if ($chat_id and file_exists($photo)) {
        $args          = array(
            'chat_id' => $chat_id
        );
        $url           = $API . 'setChatPhoto';
        $args['photo'] = new CURLFile($photo);
        $ch            = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:multipart/form-data'
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        $output = curl_exec($ch);
        $rrr    = json_decode($output, true);
        if ($r['ok']) {
            return $output;
        } else {
            return false;
        }
    }
}
function deleteChatPhoto($chat_id)
{
    global $api;
    global $API;
    if ($chat_id) {
        $args = array(
            'chat_id' => $chat_id
        );
        $i    = json_decode(post('post', $API . __FUNCTION__, $args), true);
        if ($i['ok']) {
            return $i;
        } else {
            return false;
        }
    }
}
function setChatTitle($chat_id, $title)
{
    global $api;
    global $API;
    if ($chat_id and $title) {
        $args = array(
            'chat_id' => $chat_id,
            'title' => $title
        );
        $i    = json_decode(post('post', $API . __FUNCTION__, $args), true);
        if ($i['ok']) {
            return $i;
        } else {
            return false;
        }
    }
}
function setChatDescription($chat_id, $description)
{
    global $api;
    global $API;
    if ($chat_id and $description) {
        $args = array(
            'chat_id' => $chat_id,
            'description' => $description
        );
        $i    = json_decode(post('post', $API . __FUNCTION__, $args), true);
        if ($i['ok']) {
            return $i;
        } else {
            return false;
        }
    }
}
function pinChatMessage($chat_id, $message_id, $disable_notification = false)
{
    global $api;
    global $API;
    if ($chat_id and $message_id) {
        $args = array(
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'disable_notification' => $disable_notification
        );
        $i    = json_decode(post('post', $API . __FUNCTION__, $args), true);
        if ($i['ok']) {
            return $i;
        } else {
            return false;
        }
    }
}
function unpinChatMessage($chat_id)
{
    global $api;
    global $API;
    if ($chat_id) {
        $args = array(
            'chat_id' => $chat_id
        );
        $i    = json_decode(post('post', $API . __FUNCTION__, $args), true);
        if ($i['ok']) {
            return $i;
        } else {
            return false;
        }
    }
}
function clean($string)
{
    $string = str_replace(' ', '-', $string);
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
}
function replaceUL($string)
{
    $string = str_replace(' ', '_', $string);
    return $string;
}
