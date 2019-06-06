<?php
/*
    Copyright (C) 2017 Gynx

    OpusBot is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    OpusBot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

if ($config['funziona_nei_canali']) {
    if ($update['channel_post']) {
        $update['message'] = $update['channel_post'];
        $canale            = true;
    }
}
if ($config['funziona_messaggi_modificati']) {
    if ($update['edited_message']) {
        $update['message'] = $update['edited_message'];
        $editato           = true;
    }
}
if ($config['funziona_messaggi_modificati_canali']) {
    if ($update['edited_channel_post']) {
        $update['message'] = $update['edited_channel_post'];
        $editato           = true;
        $canale            = true;
    }
}

$chatID        = $update['message']['chat']['id']; //ID Chat utente
$userID        = $update['message']['from']['id']; //ID Utente
$msg           = $update['message']['text']; //Update di tipo: messaggio inviato al bot
$username      = $update['message']['from']['username']; //Username dell' utente
$nome          = $update['message']['from']['first_name']; //Nome dell' utente
$cognome       = $update['message']['from']['last_name'];  //Cognome dell' utente
$language_code = $update['message']['from']['language_code']; //Lingua dell' utente
$messageID     = $update['message']['message_id']; //ID del messaggio dell' utente

//In reply (risposta ad un messaggio)
$replyID                = $update['message']['reply_to_message']['forward_from']['id']; //ID di un utente da un messaggio inoltrato
$reply_message_ID       = $update['message']['reply_to_message']['message_id']; //ID del messaggio inoltrato a cui si risponde

//Media update
$caption          = $update['message']['caption']; //Didascalia del media
$video            = $update['message']['video']['file_id']; //Update di tipo:  video
$video_note       = $update['message']['video_note']['file_id']; //Update di tipo:  video-nota
$voice            = $update['message']['voice']['file_id']; //Update di tipo:  vocale
$photo            = $update['message']['photo'][0]['file_id']; //Update di tipo:  immagine
$video_caption    = $update['message']['video']['caption'];
$photo_array = array();
foreach ($update['message']['photo'] as $photo_file_id) {
    $photo_array[] = $photo_file_id;
}
$document   = $update['message']['document']['file_id']; //Update di tipo:  documento (con varie estensioni)
$audio      = $update['message']['audio']['file_id']; //Update di tipo:  audio
$sticker    = $update['message']['sticker']['file_id']; //Update di tipo:  stiker
$location   = $update['longitude']['latitude']; //Update di tipo:  posizione
$contact    = $update['phone_number']['first_name']['last_name']['user_id']; //Update di tipo:  contatto
$venue      = $update['location']['title']['address']['foursquare_id']; //Update di tipo:  posizione

//Messaggi inoltrati
if ($chatID < 0) {
    $msgFChatID    = $update['message']['forward_from_chat']['id']; //Id del canale/gruppo
    $msgFID        = $update['message']['forward_from_message_id']; //Id messaggio del canale/gruppo
    $msgF          = $update['message']['forward_from_chat']; //Messaggio Inoltrato da canale/gruppo
    $msgFText      = $update['message']['text']; //Testo del messaggio inoltrato
    $UsernameF     = $update['message']['forward_from_chat']['username']; //Identifica l' username del canale/gruppo da un messaggio inoltrato
    $msgFType      = $update['message']['forward_from_chat']['type']; //Identifica il tipo di chat(canale/gruppo) da cui proviene il messaggio
    $msgFCG        = $update['message']['forward_from_chat']['title']; //Identifica il nome del canale/gruppo da cui proviene il messaggio inoltrato
    $msgFSignature = $update['message']['forward_signature']; //Autore del messaggio inoltrato (se visibile)
    $msgFDate      = $update['message']['forward_date']; //Data del Messaggio inoltrato(in cui è stato postato) codificata in UNIX TIME
    $DataLeggibile = gmdate('Y-m-d\ H:i:s', $msgFDate);//Questa variabile rende leggibile la data di $msgFDate
}

//Bottoni inline
if ($update['callback_query']) { //Update di tipo: Pulsante
    $cbid     = $update['callback_query']['id']; //Da callback_query: ottieni ID utente
    $cbdata   = $update['callback_query']['data']; //Da callback_query: ottieni data (in cui viene premuto il pulsante)
    $cbtexy   = $update['callback_query']['message']['text']; //Da callback_query: ottieni il testo del messaggio
    $cbmid    = $update['callback_query']['message']['message_id']; //Da callback_query: ottieni ID del messaggio
    $chatID   = $update['callback_query']['message']['chat']['id']; //Da callback_query: ottieni ID del messaggio
    $userID   = $update['callback_query']['from']['id']; //Da callback_query: ottieni ottieni ID utente
    $nome     = $update['callback_query']['from']['first_name']; //Da callback_query: ottieni nome dell' utente
    $cognome  = $update['callback_query']['from']['last_name']; //Da callback_query: ottieni cognome dell' utente
    $username = $update['callback_query']['from']['username']; //Da callback_query: ottieni username dell' utente
}

//Update nei canali
if ($update['channel_post']){
    $message_id_channel =  $update['channel_post']['message_id']; //Id del messaggio inviato nel canale
    $chat_id_channel    = $update['channel_post']['chat']['id']; //Id della chat dove è stato mandato un messaggio
    $chat_id_title      = $update['channel_post']['chat']['title']; //Nome della chat
    $type_chat          = $update['message']['chat']['type']; //Tipo della chat ---> supergroup o channel
    $date_msg_channel   = $update['message']['date']; //Data dell'invio del messaggio
    $text_msg_channel   = $update['message']['text']; //Testo del messaggio inviato nel canale
}

//Amministrazione
if (in_array($userID, $adminbot)) { //Se nell' array ci sono gli admin del bot
    $isadminbot = true;             //Allora $isadminbot è vero
}
