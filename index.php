<?php
/**
 * Created by @OnyxTM.
 * User: Morteza Bagher Telegram id : @mench
 * Date: 11/12/2016
 * Time: 09:19 PM
 */



include "config.php";


define('API_KEY','توکن ربات');
$admin = "آیدی عددی ادمین";

$update = json_decode(file_get_contents('php://input'));
$txt = $update->message->text;
$chat_id = $update->message->chat->id;
$message_id = $update->message->message_id;
$channel_forward = $update->channel_post->forward_from;
$channel_text = $update->channel_post->text;
$from = $update->message->from->id;
$chatid = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;
$msgid = $update->callback_query->message->message_id;



function bridge($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}

function apiRequest($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}


$user = file_get_contents('pmembers.txt');
$members = explode("\n", $user);
if (!in_array($chat_id, $members)) {
    $add_user = file_get_contents('pmembers.txt');
    $add_user .= $chat_id . "\n";
    file_put_contents('pmembers.txt', $add_user);
}

if (preg_match('/^\/([Ss]tate)/', $txt) && $from == $admin) {
    $user = file_get_contents('pmembers.txt');
    $member_id = explode("\n", $user);
    $member_count = count($member_id) - 1;
    bridge('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "👥 تعداد کاربران جدید ربات شما : $member_count",
        'parse_mode' => 'HTML'
    ]);
} else if (preg_match('/^\/([Uu]ploadtext)/', $txt)) {
    $tttext = str_replace("/uploadtext","",$txt);
    $nt22 = utf8_encode($tttext);
    function rp($Number){
        $Rand = substr(str_shuffle("123456789"), 0, $Number);
        return $Rand;
    }
    $ids = rp(8);

//        $mysqli->query("INSERT INTO uploadtext (id, text) VALUES ($id, '$tttext')");

    $mysqli->query("INSERT INTO uploadtext (id, text) VALUES ($ids,'$nt22')");
    bridge("sendMessage",[
        'chat_id'=>$chat_id,
        'text'=>"@UploadText_PHPBot $ids",
        'parse_mode'=>'HTML',
        'reply_markup'=>json_encode(['inline_keyboard'=>[
            [['text'=>'ارسال برای دیگران',"switch_inline_query"=>"$ids"]]
        ]])
    ]);
} else if (preg_match('/^\/([Ss]endtoall)/', $txt) && $from == $admin) {
    $strh = str_replace("/sendtoall", "", $txt);
    $ttxtt = file_get_contents('pmembers.txt');
    $membersidd = explode("\n", $ttxtt);
    for ($y = 0; $y < count($membersidd); $y++) {
        bridge("sendMessage", [
            'chat_id' => $membersidd[$y],
            "text" => $strh,
            "parse_mode" => "HTML"
        ]);
    }
    $memcout = count($membersidd) - 1;
    bridge("sendMessage", [
        'chat_id' => $admin,
        "text" => "پیام شما به $memcout نفر ارسال شد.",
        "parse_mode" => "HTML"
    ]);
} else if (preg_match('/^\/([Ff]ortoall)/', $txt) && $from == $admin) {
    $ttxtt = file_get_contents('pmembers.txt');
    $membersidd = explode("\n", $ttxtt);

    for ($y = 0; $y < count($membersidd); $y++) {
        bridge("forwardmessage", [
            'chat_id' => $membersidd[$y],
            'from_chat_id' => $chat_id,
            'message_id' => $update->message->reply_to_message->message_id
        ]);
    }

    $memcout = count($membersidd) - 1;
    bridge("sendMessage", [
        'chat_id' => $admin,
        "text" => "پیام شما به $memcout نفر ارسال شد.",
        "parse_mode" => "HTML"
    ]);
} elseif ($txt == "/start") {
    apiRequest("sendMessage", [
        'chat_id' => $chat_id,
        'text' => "ســلام 😉
به ربات آپلود متن خوش اومدی 👉
متن خودت رو بعد از دستور /uploadtext وارد کنید تا من اون رو آپلود کنم روی دیتابیس",
        'reply_markup' => json_encode(['inline_keyboard' => [
            [['text' => 'طراحی و ساخته شده توسط منچ', 'url' => "https://t.me/joinchat/AAAAAD7GSGnI_QyAB3RtwQ"]]
        ]])
    ]);
} else {
    apiRequest("sendMessage", [
        'chat_id' => $chat_id,
        'text' => "متاسفانه چیزی پیدا نکردم☹️",
        'reply_markup' => json_encode(['inline_keyboard' => [
            [['text' => 'طراحی و ساخته شده توسط منچ', 'url' => "https://t.me/joinchat/AAAAAD7GSGnI_QyAB3RtwQ"]]
        ]])
    ]);
}


$res = $mysqli->query("SELECT * FROM uploadtext");

while ($row = $res->fetch_assoc()){
    $idq = $row["id"];
    $textq = $row["text"];


    $idqq = $update->inline_query->id;
    $textqq = $update->inline_query->query;

    $nt2 = utf8_decode($textq);
    if ($textqq == $idq) {
        bridge('answerInlineQuery', [
            'inline_query_id' => $update->inline_query->id,
            'results' => json_encode([[
                'type' => 'article',
                'id' => base64_encode(rand(5,555)),
                'title' => 'دارای دکمه اشتراک',
                'input_message_content' => ['parse_mode' => 'HTML', 'message_text' => "$nt2"],
                'reply_markup' => [
                    'inline_keyboard' => [
                        [
                            ['text' => "اشتراک", 'switch_inline_query' => "$idq"]
                        ]
                    ]
                ]

            ],[
                'type' => 'article',
                'id' => base64_encode(rand(5,555)),
                'title' => 'بدون دکمه اشتراک',
                'input_message_content' => ['parse_mode' => 'HTML', 'message_text' => "$nt2"],
            ],[
                'type' => 'article',
                'id' => base64_encode(rand(5,555)),
                'title' => 'اشتراک کد',
                'input_message_content' => ['parse_mode' => 'HTML', 'message_text' => "@UploadText_PHPBot $idq"],
                'reply_markup' => [
                    'inline_keyboard' => [
                        [
                            ['text' => "اشتراک", 'switch_inline_query' => "$idq"]
                        ]
                    ]
                ]
            ]])
        ]);
    } else {
        bridge('answerInlineQuery', [
            'inline_query_id' => $update->inline_query->id,
            'results' => json_encode([[
                'type' => 'article',
                'switch_pm_text'=>"شروع و ساخت",
                'id' => base64_encode(rand(5,555)),
                'title' => 'چیزی یافت نشد.',
                'input_message_content' => ['parse_mode' => 'HTML', 'message_text' => "چیزی یافت نشد.
                @UploadText_PHPBot
                @CH_PM_BOT
                @CH_PM
                @ch_jockdoni"]
            ]])
        ]);
    }
}
