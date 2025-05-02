<?php
ob_start();
error_reporting(0);
date_Default_timezone_set('Asia/Tashkent');
define('API_KEY',"8199560732:AAG-aqtXEo4SvA9eYsfxnvi5-siSnYHyGQA");
$cardnumber="9860606745194716";
$cardholder="RO'ZIBOYEV ABDULAZIZ";
$owner = "7530833627"; //2
$admins_file = file_get_contents("tools/admins.txt") ?: $owner;
$admins = array_filter(explode("\n", $admins_file));
$ownerusername = "Unknown";
// Initialize directories and files
foreach (["step", "kino", "pro"] as $dir) {
if (!is_dir($dir)) mkdir($dir, 0755, true);}
foreach (["tools/users", "tools/block", "tools/channel.txt", "tools/kino_ch.txt", "tools/holat.txt", "tools/forward_holat.txt", "kino/id.txt", "tools/admins.txt"] as $file) {
if (!file_exists($file)) {
file_put_contents($file, $file === "tools/holat.txt" ? "Yoqilgan" : ($file === "tools/forward_holat.txt" ? "ruxsat" : ($file === "tools/admins.txt" ? $owner : "")));}}
$bot = bot('getme',['bot'])->result->username;
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}}
function addstat($id){
$stat=file_get_contents("tools/users");
$check=explode("\n",$stat);
if(!in_array($id,$check)){
file_put_contents("tools/users","\n".$id,8);
}}
function addblock($id){
$stat=file_get_contents("tools/block");
$check=explode("\n",$stat);
if(!in_array($id,$check)){
file_put_contents("tools/block","\n".$id,8);
}}
function joinchat($id) {
global $bot;
static $lastMessageId = null; // Oldingi xabar ID sini saqlash uchun
$kanallar = file_get_contents("tools/channel.txt");
if ($kanallar == null) {
return true;}
$ex = array_filter(explode("\n", $kanallar));
$array = ['inline_keyboard' => []];
$uns = false;
foreach ($ex as $i => $first_line) {
$first_ex = explode("@", $first_line);
$url = trim($first_ex[1]);
$chatInfo = bot('getChat', ['chat_id' => "@$url"]);
$ism = $chatInfo->result->title ?? "Noma'lum kanal";
$ret = bot("getChatMember", [
"chat_id" => "@$url",
"user_id" => $id,]);
$stat = $ret->result->status ?? null;
if ($stat == "creator" || $stat == "administrator" || $stat == "member") {
$array['inline_keyboard'][] = [['text' => "✅ $ism", 'url' => "https://t.me/$url"]];
} else {
$array['inline_keyboard'][] = [['text' => "❌ $ism", 'url' => "https://t.me/$url"]];
$uns = true;}}
$array['inline_keyboard'][] = [['text' => "🔄 Tekshirish", 'callback_data' => "checksuv"]];
if ($uns) {
if ($lastMessageId) {
bot('deleteMessage', [
'chat_id' => $id,
'message_id' => $lastMessageId]);}
$msg = bot('sendMessage', [
'chat_id' => $id,
'text' => "<b>⚠️ Kanallarga obuna bo'ling!</b>",
'parse_mode' => 'html',
'reply_markup' => json_encode($array),]);
if (isset($msg->result->message_id)) {
$lastMessageId = $msg->result->message_id;}
return false;
} else {
return true;}}
// Agar xabar muvaffaqiyatli yuborilgan bo‘lsa, eski xabarni o‘chirishga harakat qiladi
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$cid = $message->chat->id;
$name = $message->chat->first_name;
$tx = $message->text;
$step = file_get_contents("step/$cid.step");
$mid = $message->message_id;
$type = $message->chat->type;
$text = $message->text;
$uid= $message->from->id;
$name = $message->from->first_name;
$familya = $message->from->last_name;
$bio = $message->from->about;
$username = $message->from->username;
$chat_id = $message->chat->id;
$message_id = $message->message_id;
$reply = $message->reply_to_message->text;
$nameru = "<a href='tg://user?id=$uid'>$name $familya</a>";
$botdel = $update->my_chat_member->new_chat_member; 
$botdelid = $update->my_chat_member->from->id; 
$userstatus= $botdel->status; 
$contact = $message->contact;
$contact_id = $contact->user_id;
$contact_user = $contact->username;
$contact_name = $contact->first_name;
$phone = $contact->phone_number;
//inline uchun metodlar
$data = $update->callback_query->data;
$qid = $update->callback_query->id;
$id = $update->inline_query->id;
$query = $update->inline_query->query;
$query_id = $update->inline_query->from->id;
$cid2 = $update->callback_query->message->chat->id;
$mid2 = $update->callback_query->message->message_id;
$callfrid = $update->callback_query->from->id;
$callname = $update->callback_query->from->first_name;
$calluser = $update->callback_query->from->username;
$surname = $update->callback_query->from->last_name;
$about = $update->callback_query->from->about;
$nameuz = "<a href='tg://user?id=$callfrid'>$callname $surname</a>";
$photo = $message->photo;
$file = $photo[count($photo)-1]->file_id;
mkdir("step");
mkdir("kino");
mkdir("pro");
mkdir("tools");
if(file_get_contents("kino/id.txt")==null){
file_put_contents("kino/id.txt",0);
}
$last_kino = file_get_contents("kino/id.txt");
if(file_get_contents("tools/holat.txt")){
}else{
if(file_put_contents("tools/holat.txt","Yoqilgan"));}
if($botdel){ 
if($userstatus=="kicked"){ 
addblock($cid);}}
if(isset($message)){
$block=file_get_contents("tools/block");
$block=str_replace("\n".$cid,"",$block);
file_put_contents("tools/block",$block);}
$umum_d = date("d.m.Y H:i");
if(isset($message)){
addstat($cid);}
$kanal_uz = file_get_contents("step/kanal.txt");
$kanalcha = file_get_contents("tools/kino_ch.txt");
$holat = file_get_contents("tools/holat.txt");
function isPro($user_id) {
$pro_file = "pro/$user_id.txt";
if (file_exists($pro_file)) {
list($status, $expiry_time) = explode("|", file_get_contents($pro_file));
return ($status == "active" && time() < $expiry_time);
}
return false;
}


/* TUGMALAR /START */
$menu = json_encode([
'inline_keyboard'=>[
[['text'=>"🗂️ Barcha kinolari",'url'=>"https://t.me/".str_ireplace("@",null,$kanalcha)]],]]);
$usermenu = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔍 Qidirish"],['text'=>"💎 Pro"]],
[['text'=>"✉️ Yordam"],['text'=>"🎟 Buyurtma"]],
[['text'=>"💰 Donat"]]]]);
$admenu = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔍 Qidirish"],['text'=>"💎 Pro"]],
[['text'=>"✉️ Yordam"],['text'=>"🎟 Buyurtma"]],
[['text'=>"💰 Donat"],['text' => "🔒 Admin Panel"]]]]);
$panel = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"📢 Kanallarni sozlash"],['text'=>"📊 Statistika"]],
[['text'=>"✉ Xabar Yuborish"],['text'=>"🤖 Bot holati"]],
[['text'=>"📤 Kino Yuklash"],['text'=>'🗑 Kino O\'chirish']],
[['text'=>"👤 Boshqarish"],['text'=>"◀️Bosh sahifa"]]]]);
$usercontrolpanel = json_encode([
'resize_keyboard'=>true,
'keyboard' => [
[['text' => "Admin +"],['text' => "Admin -"]],
[['text' => "Pro +"],['text' => "Pro -"]],
[['text'=>"🗄 Boshqarish"]]]]);
$searchmenu = json_encode([
'inline_keyboard' => [
[['text' => "#️⃣ Kodi bilan", 'callback_data' => "bycode"]],
[['text' => "🔠 Nomi bilan", 'callback_data' => "byname"]],
[['text' => "🎲 Tasodifiy (janr)", 'callback_data' => "bygenre"]],
[['text' => "🎲 Tasodifiy", 'callback_data' => "byrandom"]],]]);
$genremenu = json_encode([
'inline_keyboard' => [
[['text' => "🎬 Fantastika", 'callback_data' => "bygenre_fantastika"], ['text' => "🧭 Sarguzasht", 'callback_data' => "bygenre_sarguzasht"]],
[['text' => "🎭 Drama", 'callback_data' => "bygenre_drama"],['text' => "⚔️ Urush", 'callback_data' => "bygenre_urush"]],
[['text' => "👻 Ujas", 'callback_data' => "bygenre_ujas"], ['text' => "💘 Romantik", 'callback_data' => "bygenre_romantik"]],
[['text' => "🎨 Animatsion", 'callback_data' => "bygenre_animatsion"]],
[['text' => "🔙 Orqaga", 'callback_data' => "backmenu"]]]]);
$mbu = json_encode([
'inline_keyboard' => [
[['text' => "🗂️ Barcha kinolari", 'url' => "https://t.me/".str_ireplace("@", "", $kanalcha)]],
[['text' => "🔙 Orqaga", 'callback_data' => "backmenu"]],]]);
$back = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"◀️ Orqaga"]],]]);
$backz = json_encode([
'inline_keyboard' => [
[['text' => "Bekor qilish 🚫", 'callback_data' => "baz"]],]]);
$boshqarish = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🗄 Boshqarish"]],]]);
$holat = file_get_contents("tools/holat.txt");
if($text){
if($holat == "O'chirilgan"){
if(in_array($cid,$admins)){
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"⛔️ <b>Bot vaqtinchalik o'chirilgan!</b>
<i>Botda ta'mirlash ishlari olib borilayotgan bo'lishi mumkin!</i>",
'parse_mode'=>'html',]);
exit();
}}}   
if($text == "🗄 Boshqarish" or $text=="🔒 Admin Panel"){
if(in_array($cid,$admins)){
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Admin paneliga xush kelibsiz!</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel,]);
unlink("step/$cid.step");
unlink("step/test.txt");
unlink("step/$cid.txt");
exit();}}
/* TUGMALAR /END */
/* FUNCTIONS /START*/
if ($text == "/start" or $text=="◀️Bosh sahifa") {
if (in_array($cid,$admins)) { // Agar foydalanuvchi obuna bo‘lgan bo‘lsa
bot('sendPhoto', [
'photo' => 'https://67e96a318e592.myxvest1.ru/uzbekcinema/tools/image.png',
'chat_id' => $cid,
'caption' => "- 👋 Assalomu alaykum <a href='tg://user?id=$cid'>$name</a>, botimizga xush kelibsiz!",
'parse_mode' => 'html',
'reply_markup' => $admenu
]);
}
elseif (joinchat($cid)) { // Agar foydalanuvchi obuna bo‘lgan bo‘lsa
bot('sendPhoto', [
'photo' => 'https://67e96a318e592.myxvest1.ru/uzbekcinema/tools/image.png',
'chat_id' => $cid,
'caption' => "- 👋 Assalomu alaykum <a href='tg://user?id=$cid'>$name</a>, botimizga xush kelibsiz!",
'parse_mode' => 'html',
'reply_markup' => $usermenu]);}}
if ($text == "🔍 Qidirish" or $text == "/search") {
if(joinchat($cid)){bot('sendMessage', [
'chat_id' => $cid,
'text' => "🔍 Qidirish menyusi:",
'parse_mode' => 'html',
'reply_markup' => $searchmenu]);
file_put_contents("step/$cid.step", "search"); // Foydalanuvchi bosqichi saqlanadi
exit();}}
/* PRO step*/
if ($text == "💎 Pro" or $text == "/pro" or $data == "get_pro") {
    if(isPro($cid)){
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>💎 Sizda PRO obuna faol!</b>",
            'parse_mode' => 'html']);
    }
    else{    
        bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>💎 PRO AFZALLIKLARI!</b><blockquote>1. Saqlab olish imkoniyati\n2. Yuklab olish imkoniyati\n3. Ulashish imkoniyati\n4. Buyurtma qilish</blockquote>
💰 Narxi - 10 000 so'm (1 oy)
💳 - <code>$cardnumber</code>
👤 - <i>$cardholder</i>
<b>To‘lov chekini yuboring:</b>",
        'parse_mode' => 'html',
        'reply_markup' => $backz
    ]);
    file_put_contents("step/$cid.step", "pro_chek"); // Chek kutish bosqichi
    exit();
}}

// Agar foydalanuvchi chek yuborsa (rasm yoki skrinshot)
if ($step == "pro_chek" and (isset($message->photo) or isset($message->document))) {
    unlink("step/$cid.step"); // bosqichni o‘chiramiz
    foreach ($admins as $admin_id) {
        $caption = "📥 <b>Yangi PRO to‘lov cheki!</b>\n\n👤 <a href='tg://user?id=$cid'>$name</a>\n🆔 ID: <code>$cid</code>";
        $reply_markup = json_encode([
            'inline_keyboard' => [
                [['text' => "✅ PRO ni faollashtirish", 'callback_data' => "pro_on_$cid"]],
                [['text' => "🚫 PRO ni qabul qilmaslik", 'callback_data' => "pro_off_$cid"]]
            ]
        ]);
        if (isset($message->photo)) {
            $photo = $message->photo[count($message->photo)-1]->file_id;
            bot('sendPhoto', [
                'chat_id' => $admin_id,
                'photo' => $photo,
                'caption' => $caption,
                'parse_mode' => 'html',
                'reply_markup' => $reply_markup
            ]);
        } elseif (isset($message->document)) {
            $doc = $message->document->file_id;
            bot('sendDocument', [
                'chat_id' => $admin_id,
                'document' => $doc,
                'caption' => $caption,
                'parse_mode' => 'html',
                'reply_markup' => $reply_markup
            ]);
        }
    }
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "📨 <b>To‘lov cheki adminlarga yuborildi. Tez orada tekshirilib PRO faollashtiriladi.</b>",
        'parse_mode' => 'html'
    ]);
    exit();
}

// PRO aktivlashtirish admin tomonidan
if (strpos($data, "pro_on_") !== false) {
    $user_id = explode("_", $data)[2];
    $expiry_time = time() + (30 * 24 * 60 * 60); // 1 oy (30 kun) qo'shish
    file_put_contents("pro/$user_id.txt", "active|$expiry_time");
    bot('sendMessage', [
        'chat_id' => $user_id,
        'text' => "🎉 <b>Tabriklaymiz! Siz endi PRO foydalanuvchisiz!</b>\nPRO holati 1 oy davomida amal qiladi.",
        'parse_mode' => 'html'
    ]);
    bot('editMessageText', [
        'chat_id' => $cid2,
        'message_id' => $mid,
        'text' => "✅ <b>PRO foydalanuvchi faollashtirildi:</b> <code>$user_id</code>",
        'parse_mode' => 'html'
    ]);
    exit();
}

// PRO holatini tugatish
if (strpos($data, "pro_off_") !== false) {
    $user_id = explode("_", $data)[2];
    file_put_contents("pro/$user_id.txt", "deactive");
    bot('sendMessage', [
        'chat_id' => $user_id,
        'text' => "☹️ <b>Afsus! Sizning to'lovingiz amalga oshmagan yoki chekingizda xatolik mavjud.</b>",
        'parse_mode' => 'html'
    ]);
    bot('editMessageText', [
        'chat_id' => $cid2,
        'message_id' => $mid,
        'text' => "❌ <b>PRO foydalanuvchi faollashtirilmadi:</b> <code>$user_id</code>",
        'parse_mode' => 'html'
    ]);
    exit();
}

// PRO holatini tekshirish
if($text == "/prostatus"){
// PRO holatini va tugash vaqtini tekshirish
$pro_file = "pro/$cid.txt";
if(file_exists($pro_file)){
list($status, $expiry_time) = explode("|", file_get_contents($pro_file));
if ($status == "active" && time() < $expiry_time) {
$remaining_time = $expiry_time - time();
$days_left = ceil($remaining_time / (60 * 60 * 24));
$msg = "✅ Siz PRO foydalanuvchisiz! Muddat: $days_left kun qoldi.";
}else {
$msg = "❌ Sizning PRO statusingiz tugagan yoki faollashtirilmagan.";}
}else {
$msg = "❌ Siz PRO foydalanuvchi emassiz.";}
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>$msg</b>",
'parse_mode'=>'html']);
exit();}
if($text == "✉️ Yordam" or $text == "/help"){
if(joinchat($cid)){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>✉️ Muammo haqida ma'lumot yuboring</b>",
'parse_mode'=>'html',
'reply_markup'=>$backz,]);
file_put_contents("step/$cid.step", "help");
exit();}}
if ($text == "🎟 Buyurtma" or $text == "/order") {
if (joinchat($cid)) {  // x ni olib tashladim
if (isPro($cid)) {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>🎬 Kino haqida ma'lumot yuboring (nomi, tavsifi, rasm yoki video)</b>",
'parse_mode' => 'html',
'reply_markup' => $backz,]);
file_put_contents("step/$cid.step", "order");
exit();
} elseif(joinchat($cid)) {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "🔒 <b>Bu bo'lim faqat PRO foydalanuvchilar uchun!</b>\n\n🔓 PRO obunani faollashtirish uchun pastdagi tugmani bosing:",
'parse_mode' => 'html',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "🔓 PRO obuna bo'lish", 'callback_data' => "get_pro"]],
[['text' => "🔙 Orqaga", 'callback_data' => "back"]]
]])]);
exit();
}}}
if ($text == "💰 Donat" or $text == "/donate") {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>💰 Donat qilish ixtiyoriy!</b><blockquote>Sizning donatingiz bizning 
kanalimiz va botimiz 
rivoji uchun allbata!</blockquote>
💳 - <code>$cardnumber</code>
👤 - <i>$cardholder</i>",
'parse_mode' => 'html',
'reply_markup' => $backz]);
file_put_contents("step/$cid.step", "pro_chek"); // Chek kutish bosqichi
exit();}
/* ADMIN PANEL FUNCs*/
if($text == "📢 Kanallarni sozlash"){
if(in_array($cid,$admins)){
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🔐 Majburiy obuna",'callback_data'=>"kqosh"]],
[['text'=>"*️⃣ Qo'shimcha kanallar",'callback_data'=>"qoshimchakanal"]],
]])]);
exit();}}
if($text == "📊 Statistika"){
if(in_array($cid,$admins)){
$ping=sys_getloadavg();
$stat=substr_count(file_get_contents("tools/users"),"\n");
$nostat=substr_count(file_get_contents("tools/block"),"\n")??0;
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"💡 <b>O'rtacha yuklanish:</b> <code>$ping[0]</code>
👥 <b>Foydalanuvchilar:</b> $stat ta 
⛔️ <b>Nofaol:</b> $nostat ta ",
'parse_mode'=>'html']);
exit();}}
if($text == "✉ Xabar Yuborish"){
if(in_array($cid,$admins)){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Yuboriladigan xabar turini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"👤 Userga",'callback_data'=>"user"]],
[['text'=>"🗣️ Oddiy",'callback_data'=>"send"]],
[['text'=>"Orqaga",'callback_data'=>"boshqarish"]],	
]])]);
exit();}}
if($text == "🤖 Bot holati"){
if(in_array($cid,$admins)){
if($holat == "Yoqilgan"){
$xolat = "O'chirish";}
if($holat == "O'chirilgan"){
$xolat = "Yoqish";}
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Hozirgi holat:</b> $holat",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$xolat",'callback_data'=>"bot"]],
[['text'=>"Orqaga",'callback_data'=>"boshqarish"]]
]])]);
exit();}}
if($text == "📤 Kino Yuklash"){
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"🔢 Iltimos, kino uchun kod kiriting:",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid.step", 'kinostep1');
exit();}
if($text == "🗑 Kino O'chirish"){
bot('SendMessage',[
'chat_id' => $cid,
'text' => "📂 O'chirish uchun kino kodini kiriting:",
'parse_mode' => 'html',
'reply_markup' => $boshqarish,]);
file_put_contents("step/$cid.step", 'kinostep_del');
exit();}
if ($text == "👤 Boshqarish") {
if (in_array($cid,$admins)) { // Agar foydalanuvchi obuna bo‘lgan bo‘lsa
bot('sendMessage', [
'chat_id' => $cid,
'text' => "Userni boshqarish:",
'parse_mode' => 'html',
'reply_markup' => $usercontrolpanel
]);}}
/* IF and STEPs*/
//Search step
if ($data == "backmenu") {
bot('editMessageText', [
'chat_id' => $cid2,
'message_id' => $mid2,
'text' => "🔍 Qidirish menyusi:",
'parse_mode' => 'html',
'reply_markup' => $searchmenu
]);
if (file_exists("step/$cid2.step")) {
unlink("step/$cid2.step");}
exit();}
if ($data == "bycode") {
file_put_contents("step/$cid2.step", "bycode");
bot('editMessageText', [
'chat_id' => $cid2,
'message_id'=>$mid2,
'text' => "#️⃣ Kodni kiriting:",
'parse_mode' => 'html',
'reply_markup'=>$mbu
]);}
if ($data == "byname") {
file_put_contents("step/$cid2.step", "byname");
bot('editMessageText', [
'chat_id' => $cid2,
'message_id'=>$mid2,
'text' => "🔠 Kino nomini kiriting:",
'parse_mode' => 'html',
'reply_markup'=>$mbu
]);}
if ($data == "bygenre") {
file_put_contents("step/$cid2.step", "byname");
bot('editMessageText', [
'chat_id' => $cid2,
'message_id'=>$mid2,
'text' => "Janr orqali qidiring (tasodifiy)",
'parse_mode' => 'html',
'reply_markup'=>$genremenu
]);}
if ($data == "byrandom") {
$folders = array_filter(glob("kino/*"), 'is_dir');
if (count($folders) > 0) {
$random = $folders[array_rand($folders)];
bot('deleteMessage',[
'chat_id'=>$cid,
'message_id'=>$mid]);
send_kino($cid2, $random);
}else{
bot('editMessageText', [
'chat_id' => $cid2,
'message_id'=>$mid2,
'text' => "Hech qanday kino topilmadi.",
'reply_markup'=>$menu
]);}}
if (file_exists("step/$cid.step")) {
$step = file_get_contents("step/$cid.step");
unlink("step/$cid.step");
if ($step == "bycode") {
$path = "kino/$text";
if (file_exists("$path/film.txt")) {

send_kino($cid, $path);
} else {
bot('editMessageText', [
'chat_id' => $cid,
'message_id'=>$mid,
'text' => "❌ Bunday koddagi kino topilmadi."
]);}}
if ($step == "byname") {
$text = mb_strtolower($text);
$found = false;
foreach (glob("kino/*/nomi.txt") as $file) {
$nomi = file_get_contents($file);
if (mb_stripos(mb_strtolower($nomi), $text) !== false) {
send_kino($cid, dirname($file));
$found = true;
break;}}
if (!$found) {
bot('editMessageText', [
'chat_id' => $cid,
'message_id'=> $mid,
'text' => "❌ Bunday nomli kino topilmadi."
]);}}}
if (strpos($data, "bygenre_") === 0) {
$genre = str_replace("bygenre_", "", $data);
$genre = mb_strtolower($genre);
$films = [];
foreach (glob("kino/*/janri.txt") as $file) {
$janr = mb_strtolower(trim(file_get_contents($file)));
if (strpos($janr, $genre) !== false) {
$films[] = dirname($file);}}
if (count($films) > 0) {
bot('deleteMessage',[
'chat_id'=>$cid,
'message_id'=>$mid]);
$random = $films[array_rand($films)];
send_kino($cid2, $random);
} else {
bot('editMessageText', [
'chat_id' => $cid2,
'message_id' => $mid2,
'text' => "❌ Ushbu janrda kino topilmadi.",
'reply_markup' => $genremenu
]);}}
function send_kino($chat_id, $path) {
global $bot, $kanalcha, $admins,$mid,$cid;
// Kino haqida ma'lumotlarni o'qish
$code = basename($path);
$nomi = @file_get_contents("$path/nomi.txt");
$tili = @file_get_contents("$path/tili.txt");
$formati = @file_get_contents("$path/formati.txt");
$janri = @file_get_contents("$path/janri.txt");
$yosh = @file_get_contents("$path/yosh.txt");
$video_id = @file_get_contents("$path/film.txt");
$downcount = file_exists("$path/downcount.txt") ? file_get_contents("$path/downcount.txt") : 0;
$downcount++;
file_put_contents("$path/downcount.txt", $downcount);
if(joinchat($chat_id) == true){
if(isPro($chat_id)){
bot('deleteMessage',[
'chat_id'=>$chat_id,
'message_id'=>$mid]);
bot('sendVideo', [
'chat_id' => $chat_id,
'video' => $video_id,
'caption' => "<b>🎬| Kino Nomi: $nomi
➖➖➖➖➖➖➖➖➖➖➖➖
🌐| Tili: $tili
💾| Sifati: $formati
🎭| Janri:  $janri
⛔️| Yosh chegarasi: $yosh
➖➖➖➖➖➖➖➖➖➖➖➖
🔢| Kino kodi: 《<code>$code</code>》
🔗| Kanal: $kanalcha
📁| Yuklash: $downcount
🤖| Bizning bot: @$bot</b>",
'parse_mode' => 'html',
'protect_content' => false,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "📋 Ulashish", 'url' => "https://t.me/share/url?url=https://t.me/$bot?start=$code"]],]])]);}
else{
bot('deleteMessage',[
'chat_id'=>$chat_id,
'message_id'=>$video_id]);
bot('sendVideo', [
'chat_id' => $chat_id,
'video' => $video_id,
'caption' => "<b>🎬| Kino Nomi: $nomi
➖➖➖➖➖➖➖➖➖➖➖➖
🌐| Tili: $tili
💾| Sifati: $formati
🎭| Janri:  $janri
⛔️| Yosh chegarasi: $yosh
➖➖➖➖➖➖➖➖➖➖➖➖
🔢| Kino kodi: 《<code>$code</code>》
🔗| Kanal: $kanalcha
📁| Yuklash: $downcount
🤖| Bizning bot: @$bot</b>",
'parse_mode' => 'html',
'protect_content' => true,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "📋 Ulashish", 'url' => "https://t.me/share/url?url=https://t.me/$bot?start=$code"]]]])]);    
}}
}

if(joinchat($cid2) == true){
$text=file_get_contents("step/$cid2.kino_ids");
if($text!==null){
$code = basename($path);
$nomi=file_get_contents("kino/$text/nomi.txt");
$tili=file_get_contents("kino/$text/tili.txt");
$formati=file_get_contents("kino/$text/formati.txt");
$janri=file_get_contents("kino/$text/janri.txt");
$yosh=file_get_contents("kino/$text/yosh.txt");
$downcount=file_get_contents("kino/$text/downcount.txt");
$downcount=+1;
file_put_contents("kino/$text/downcount.txt",$downcount);
$video_id=file_get_contents("kino/$text/film.txt");
if(joinchat($chat_id) == true){
if(isPro($chat_id)){
bot('sendVideo', [
'chat_id' => $chat_id,
'video' => $video_id,
'caption' => "<b>🎬| Kino Nomi: $nomi
➖➖➖➖➖➖➖➖➖➖➖➖
🌐| Tili: $tili
💾| Sifati: $formati
🎭| Janri:  $janri
⛔️| Yosh chegarasi: $yosh
➖➖➖➖➖➖➖➖➖➖➖➖
🔢| Kino kodi: 《<code>$code</code>》
🔗| Kanal: $kanalcha
📁| Yuklash: $downcount
🤖| Bizning bot: @$bot</b>",
'parse_mode' => 'html',
'protect_content' => false,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "📋 Ulashish", 'url' => "https://t.me/share/url?url=https://t.me/$bot?start=$code"]]]])]);}
else{
bot('sendVideo', [
'chat_id' => $chat_id,
'video' => $video_id,
'caption' => "<b>🎬| Kino Nomi: $nomi
➖➖➖➖➖➖➖➖➖➖➖➖
🌐| Tili: $tili
💾| Sifati: $formati
🎭| Janri:  $janri
⛔️| Yosh chegarasi: $yosh
➖➖➖➖➖➖➖➖➖➖➖➖
🔢| Kino kodi: 《<code>$code</code>》
🔗| Kanal: $kanalcha
📁| Yuklash: $downcount
🤖| Bizning bot: @$bot</b>",
'parse_mode' => 'html',
'protect_content' => true,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "📋 Ulashish", 'url' => "https://t.me/share/url?url=https://t.me/$bot?start=$code"]]]])]);    
}}
unlink("step/$cid2.kino_ids");
exit();
}}
/* Link orqali */
if(mb_stripos($text,"/start")!==false){
$exp=explode(" ",$text);
$text=$exp[1];
if(joinchat($cid)==1){
$code = basename($path);
$nomi=file_get_contents("kino/$text/nomi.txt");
$tili=file_get_contents("kino/$text/tili.txt");
$formati=file_get_contents("kino/$text/formati.txt");
$janri=file_get_contents("kino/$text/janri.txt");
$yosh=file_get_contents("kino/$text/yosh.txt");
$downcount=file_get_contents("kino/$text/downcount.txt")+1;
file_put_contents("kino/$text/downcount.txt",$downcount);
$video_id=file_get_contents("kino/$text/film.txt");
if(isPro($cid)){
bot('sendVideo',[
'chat_id'=>$cid,
'video'=>$video_id,
'caption'=>"<b>🎬| Kino Nomi: $nomi
➖➖➖➖➖➖➖➖➖➖➖➖
🌐| Tili: $tili
💾| Sifati: $formati
🎭| Janri:  $janri
⛔️| Yosh chegarasi: $yosh
➖➖➖➖➖➖➖➖➖➖➖➖
🔢| Kino kodi: 《<code>$text</code>》
🔗| Kanal: $kanalcha
📁| Yuklash: $downcount
🤖| Bizning bot: @$bot</b>",
'parse_mode'=>'html',
'protect_content'=>false,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"📋 Ulashish",'url'=>"https://t.me/share/url?url=https://t.me/$bot?start=$text"]],
]])]);
}else{
bot('sendVideo',[
'chat_id'=>$cid,
'video'=>$video_id,
'caption'=>"<b>🎬| Kino Nomi: $nomi
➖➖➖➖➖➖➖➖➖➖➖➖
🌐| Tili: $tili
💾| Sifati: $formati
🎭| Janri:  $janri
⛔️| Yosh chegarasi: $yosh
➖➖➖➖➖➖➖➖➖➖➖➖
🔢| Kino kodi: 《<code>$text</code>》
🔗| Kanal: $kanalcha
📁| Yuklash: $downcount
🤖| Bizning bot: @$bot</b>",
'parse_mode'=>'html',
'protect_content'=>true,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"📋 Ulashish",'url'=>"https://t.me/share/url?url=https://t.me/$bot?start=$text"]],
]])]);
}
exit();}}
/*Kod Orqali*/
if(is_numeric($text)==true and empty($step)){
if(joinchat($cid)==1){
$code = basename($path);
$nomi=file_get_contents("kino/$text/nomi.txt");
$tili=file_get_contents("kino/$text/tili.txt");
$formati=file_get_contents("kino/$text/formati.txt");
$janri=file_get_contents("kino/$text/janri.txt");
$yosh=file_get_contents("kino/$text/yosh.txt");
$downcount=file_get_contents("kino/$text/downcount.txt")+1;
file_put_contents("kino/$text/downcount.txt",$downcount);
$video_id=file_get_contents("kino/$text/film.txt");
if(isPro($cid)){
bot('sendVideo',[
'chat_id'=>$cid,
'video'=>$video_id,
'caption'=>"<b>🎬| Kino Nomi: $nomi
➖➖➖➖➖➖➖➖➖➖➖➖
🌐| Tili: $tili
💾| Sifati: $formati
🎭| Janri:  $janri
⛔️| Yosh chegarasi: $yosh
➖➖➖➖➖➖➖➖➖➖➖➖
🔢| Kino kodi: 《<code>$text</code>》
🔗| Kanal: $kanalcha
📁| Yuklash: $downcount
🤖| Bizning bot: @$bot</b>",
'parse_mode'=>'html',
'protect_content'=>false,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"📋 Ulashish",'url'=>"https://t.me/share/url?url=https://t.me/$bot?start=$text"]],
]])]);
}else{
bot('sendVideo',[
'chat_id'=>$cid,
'video'=>$video_id,
'caption'=>"<b>🎬| Kino Nomi: $nomi
➖➖➖➖➖➖➖➖➖➖➖➖
🌐| Tili: $tili
💾| Sifati: $formati
🎭| Janri:  $janri
⛔️| Yosh chegarasi: $yosh
➖➖➖➖➖➖➖➖➖➖➖➖
🔢| Kino kodi: 《<code>$text</code>》
🔗| Kanal: $kanalcha
📁| Yuklash: $downcount
🤖| Bizning bot: @$bot</b>",
'parse_mode'=>'html',
'protect_content'=>true,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"📋 Ulashish",'url'=>"https://t.me/share/url?url=https://t.me/$bot?start=$text"]],
]])]);
};}}
/* PRO step*//* PRO step */


// PRO holatini tekshirish
if($text == "/prostatus"){
// PRO holatini va tugash vaqtini tekshirish
$pro_file = "pro/$cid.txt";
if(file_exists($pro_file)){
list($status, $expiry_time) = explode("|", file_get_contents($pro_file));
if ($status == "active" && time() < $expiry_time) {
$remaining_time = $expiry_time - time();
$days_left = ceil($remaining_time / (60 * 60 * 24));
$msg = "✅ Siz PRO foydalanuvchisiz! Muddat: $days_left kun qoldi.";
}else {
$msg = "❌ Sizning PRO statusingiz tugagan yoki faollashtirilmagan.";}
}else {
$msg = "❌ Siz PRO foydalanuvchi emassiz.";}
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>$msg</b>",
'parse_mode'=>'html']);
exit();}
/* HELP & ORDER STEP */
if($step == "order" or $step == "help"){
unlink("step/$cid.step");
$title = $step == "order" ? "📩 <b>Yangi buyurtma!</b>" : "📩 <b>Yangi xabar!</b>";
foreach ($admins as $admin_id) {
$caption = "$title\n\n👤 <b>Foydalanuvchi:</b> <a href='tg://user?id=$cid'>$name</a>\n🆔 <b>ID:</b> $cid";
$markup = json_encode([
'inline_keyboard'=>[[['text'=>"✍️ Javob berish", 'callback_data'=>"reply_$cid"]],]]);
if(isset($message->photo)){
$photo = $message->photo[count($message->photo)-1]->file_id;
bot('sendPhoto',[
'chat_id'=>$admin_id,
'photo'=>$photo,
'caption'=>$caption,
'parse_mode'=>'html',
'reply_markup'=>$markup]);
} elseif(isset($message->video)){
$video = $message->video->file_id;
bot('sendVideo',[
'chat_id'=>$admin_id,
'video'=>$video,
'caption'=>$caption,
'parse_mode'=>'html',
'reply_markup'=>$markup]);
} else {
bot('sendMessage',[
'chat_id'=>$admin_id,
'text'=>$caption."\n💬 <b>Xabar:</b> $text",
'parse_mode'=>'html',
'reply_markup'=>$markup
]);}}
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"✅ <b>So‘rovingiz adminlarga yuborildi. Tez orada javob berishadi.</b>",
'parse_mode'=>'html',]);
exit();}
if(strpos($data, "reply_") !== false){
$user_id = str_replace("reply_", "", $data);
bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>Foydalanuvchiga yubormoqchi bo‘lgan xabaringizni yozing:</b>",
'parse_mode'=>'html',]);
file_put_contents("step/$cid2.step", "reply-$user_id");
exit();}
if(mb_stripos($step, "reply-") !== false){
$user_id = explode("-", $step)[1];
unlink("step/$cid.step");
bot('sendMessage',[
'chat_id'=>$user_id,
'text'=>"📩 <b>Admin javobi: </b>$admins\n$text",
'parse_mode'=>'html',]);
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"✅ <b>Xabaringiz foydalanuvchiga yuborildi!</b>",
'parse_mode'=>'html',
]);
exit();}
/*ADMIN MENU STEPs*/
if($data == "kanalsozla"){
if(in_array($cid2,$admins)){
bot('editMessageText',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🔐 Majburiy obuna",'callback_data'=>"kqosh"]],
[['text'=>"*️⃣ Qo'shimcha kanallar",'callback_data'=>"qoshimchakanal"]],]])]);
exit();
}}
if($data=="qoshimchakanal"){
bot('editMessageText',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"📝 Kino kanal",'callback_data'=>"kinokanal"]],
[['text'=>"🔙 Orqaga",'callback_data'=>"kanalsozla"]],
]])]);
exit();}
if($data=="kinokanal"){
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2]);
bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>Kinolar yuboriladigan kanalni kiriting:</b>
<i>Namuna: @username</i>",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid2.step",'kinokanal');
exit();}
if($step=="kinokanal" and in_array($cid,$admins)){
if(stripos($text,"@")!==false){
file_put_contents("tools/kino_ch.txt",$text);
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>✅ Saqlandi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel,]);
unlink("step/$cid.step");
exit();
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>⛔️ Faqat kanalning foydalanuvchi nomini yuboring!</b>",
'parse_mode'=>'html']);
exit;
}}
if($data=="kanallar"){
bot('editMessageText',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
'text'=>"<b>Quyidagilardan birini tanlang:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➕ Kanal Qo'shish",'callback_data'=>"kqosh"],['text'=>"🗑 Kanallarni O'chirish",'callback_data'=>"kochir"]],
[['text'=>"📑 Kanallar Ro'yxat",'callback_data'=>"mroyxat"]],
[['text'=>"➡️ Orqafa",'callback_data'=>"kanalsozla"]],]])]);
exit();
}
/*Kanal obuna bo'lish*/
if($data == "kqosh"){
if($text=="/start"){
unlink("step/$cid.step");
}else{
bot('editMessagetext',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
'text'=>"*📢 Kerakli kanalni manzilini yuboring:*",
'parse_mode'=>'MarkDown',
'reply_markup'=>$back1]);
file_put_contents("step/$cid2.step",'qosh');}}
if($step == "qosh"){
if($text=="/start"){
unlink("step/$cid.step");
}else{
if(stripos($text,"@")!==false){
if($kanallar == null){
file_put_contents("tools/channel.txt",$text);
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>$text - kanal qo'shildi</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel,]);
unlink("step/$cid.step");
}else{
file_put_contents("tools/channel.txt","$kanallar\n$text");
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>$text - kanal qo'shildi</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel,]);
unlink("step/$cid.step");
}}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>⚠️ Kanal manzili kiritishda xatolik:</b>
Masalan: @username",
'parse_mode'=>'html',]);}}}
if($data=="kochir"){
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,]);
bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>🗑 Kanallar o'chirildi!</b>",
'parse_mode'=>"html",]);
unlink("tools/channel.txt");}
if($data=="mroyxat"){
if($kanallar==null){
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,]);
bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>Kanallar ulanmagan!</b>",
'parse_mode'=>"html",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🏡 Bosh menyu",'callback_data'=>"profil"],['text'=>"◀️ Orqaga",'callback_data'=>"panel"]],]])]);
}else{
$soni = substr_count($kanallar,"@");
bot('editMessageText',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
'text'=>"<b>Ulangan kanallar ro'yxati ⤵️</b>
➖➖➖➖➖➖➖➖
<i>$kanallar</i>
<b>Ulangan kanallar soni:</b> $soni ta",
'parse_mode'=>"html",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🏡 Bosh menyu",'callback_data'=>"profil"],['text'=>"◀️ Orqaga",'callback_data'=>"panel"]],
]])
]);}}
/* SEND MESSAGE step*/
if($data == "user"){
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,]);
bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>Foydalanuvchi iD raqamini kiriting:</b>",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid2.step",'user');
exit();}
if($step == "user"){
if(in_array($cid,$admins)){
if(is_numeric($text)=="true"){
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Foydalanuvchiga yubormoqchi bo'lgan xabaringizni kiriting:</b>",
'parse_mode'=>'html',]);
file_put_contents("step/$cid.step","xabar-$text");
exit();
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Faqat raqamlardan foydalaning!</b>",
'parse_mode'=>'html',]);
exit();}}}
if(mb_stripos($step, "xabar-") !== false){
if(in_array($cid,$admins)){
$id = explode("-", $step)[1];
$get = bot('getChat',[
'chat_id'=>$id,]);
$first = $get->result->first_name;
$users = $get->result->username;
bot('copyMessage',[
'chat_id'=>$id,
'message_id'=>$mid,
'from_chat_id'=>$cid,]);
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"✅ <b>Foydalanuvchiga xabaringiz yuborildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel,]);
unlink("step/$cid.step");
exit();}}
if($data == "send"){
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,]);
bot('SendMessage',[
'chat_id'=>$cid2,
'text'=>"*Xabar matnini kiriting:*",
'parse_mode'=>'MarkDown',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid2.step","sendpost");
exit();}
if($step == "sendpost"){
if(in_array($cid,$admins)){
unlink("step/$chat_id.step");
$users=file_get_contents("tools/users");
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"*Xabar Yuborish Boshlandi* ✅",
'parse_mode'=>'MarkDown',]);
$a=explode("\n",$users);
$x=0;
$y=0;
foreach($a as $id){
$key=$message->reply_markup;
$keyboard=json_encode($key);
$ok=bot('copyMessage',[
'from_chat_id'=>$cid,
'chat_id'=>$id,
'message_id'=>$mid,
])->ok;
if($ok==true){
}else{
$okk=bot('copyMessage',[
'from_chat_id'=>$cid,
'chat_id'=>$id,
'message_id'=>$mid,
])->ok;}
if($okk==true or $ok==true){
$x=$x+1;
bot('editMessageText',[
'chat_id'=>$cid,
'message_id'=>$mid,
'text'=>"✅ <b>Yuborildi:</b> $x
❌ <b>Yuborilmadi:</b> $y",
'parse_mode'=>'html',
'reply_markup'=>$panel]);
}elseif($okk==false){
$y=$y+1;
bot('editmessagetext',[
'chat_id'=>$cid,
'message_id'=>$mid + 1,
'text'=>"✅ <b>Yuborildi:</b> $x
❌ <b>Yuborilmadi:</b> $y",
'parse_mode'=>'html',
]);}}
bot('editmessagetext',[
'chat_id'=>$cid,
'message_id'=>$mid + 1,
'text'=>"✅ <b>Yuborildi:</b> $x
❌ <b>Yuborilmadi:</b> $y",
'parse_mode'=>'html',]);}}
/* BOT STATUS */
if($data == "xolat"){
if($holat == "Yoqilgan"){
$xolat = "O'chirish";}
if($holat == "O'chirilgan"){
$xolat = "Yoqish";}
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,]);
bot('SendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>Hozirgi holat:</b> $holat",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$xolat",'callback_data'=>"bot"]],
[['text'=>"Orqaga",'callback_data'=>"boshqarish"]]]])]);
exit();}
if($data == "bot"){
if($holat == "Yoqilgan"){
file_put_contents("tools/holat.txt","O'chirilgan");
bot('editMessageText',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
'text'=>"<b>Muvaffaqiyatli o'zgartirildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[[['text'=>"◀️ Orqaga",'callback_data'=>"xolat"]],]])]);
}else{
file_put_contents("tools/holat.txt","Yoqilgan");
bot('editMessageText',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
'text'=>"<b>Muvaffaqiyatli o'zgartirildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode(['inline_keyboard'=>[[['text'=>"◀️ Orqaga",'callback_data'=>"xolat"]],]])]);}}
/* MOVIE UPLOAD step */
if($step == "kinostep1" and isset($text)){
if(in_array($cid, $admins)){
$kod = $text;
mkdir("kino/$kod");
file_put_contents("step/new_kino", $kod);
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"🍿 Kino nomini kiriting:",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid.step", 'kinostep2');
exit();}}
$newkino = file_get_contents("step/new_kino");
if($step == "kinostep2" and isset($text)){
if(in_array($cid, $admins)){
file_put_contents("kino/$newkino/nomi.txt", $text);
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"🏞 Kino uchun banner yuboring:",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid.step", 'kinostep3');
exit();}}
if($step == "kinostep3" and isset($message->photo)){
if(in_array($cid, $admins)){
$photo_id = $message->photo[count($message->photo)-1]->file_id;
file_put_contents("kino/$newkino/rasm.txt", $photo_id);
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"🇺🇿 Kinoni qaysi tilga tarjima qilingan:",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid.step", 'kinostep4');
exit();}}
if($step == "kinostep4" and isset($text)){
if(in_array($cid, $admins)){
file_put_contents("kino/$newkino/tili.txt", $text);
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"📹 Kino formatini kiriting:\n<i>Namuna: 144p,240p,360p,720p,1080p</i>",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid.step", 'kinostep5');
exit();}}
if($step == "kinostep5" and isset($text)){
if(in_array($cid, $admins)){
file_put_contents("kino/$newkino/formati.txt", $text);
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"🎭 Kino janrini kiriting:\n<i>Namuna: Drama, Romantik</i>",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid.step", 'kinostep6');
exit();}}
if($step == "kinostep6" and isset($text)){
if(in_array($cid, $admins)){
file_put_contents("kino/$newkino/janri.txt", $text);
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"🛑 Kino yosh chegarasini kiriting:\n<i>Namuna: 0+, 12+, 16+, 18+</i>",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid.step", 'kinostep7');
exit();}}
if($step == "kinostep7" and isset($text)){
if(in_array($cid, $admins)){
file_put_contents("kino/$newkino/yosh.txt", $text);
file_put_contents("kino/$newkino/downcount.txt", 0);
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"📺 Endi esa film faylini yuboring:",
'parse_mode'=>'html',
'reply_markup'=>$boshqarish,]);
file_put_contents("step/$cid.step", 'kino');
exit();}}
/* POST */
if($step=="kino" and isset($message->video)){
$video = $message->video;
$file_id = $message->video->file_id;
file_put_contents("kino/$newkino/film.txt",$file_id);
bot('sendmessage',[
'chat_id'=>$cid,
'text'=>"✅ Kino kanal va botga joylandi $kanalcha",
'reply_markup'=>$panel,]);
$nomi=file_get_contents("kino/$newkino/nomi.txt");
$tili=file_get_contents("kino/$newkino/tili.txt");
$formati=file_get_contents("kino/$newkino/formati.txt");
$janri=file_get_contents("kino/$newkino/janri.txt");
$yosh=file_get_contents("kino/$newkino/yosh.txt");
$downcount=file_get_contents("kino/$newkino/downcount.txt");
$downcount=+1;
file_put_contents("kino/$newkino/downcount.txt",$downcount);
$rasm=file_get_contents("kino/$newkino/rasm.txt");
bot('sendPhoto',[
'chat_id'=>$kanalcha,
'photo'=>$rasm,
'caption'=>"<b>🎬| Kino nomi: $nomi
➖➖➖➖➖➖➖➖➖➖➖➖
🌐| Tili: $tili
💾| Sifati: $formati
🎭| Janri:  $janri
⛔️| Yosh chegarasi: $yosh
➖➖➖➖➖➖➖➖➖➖➖➖
🔢| Kino kodi: 《<code>$newkino</code>》
🤖| Bizning bot: @$bot</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🎥 Kinoni yuklab olish",'url'=>"https://t.me/$bot?start=$newkino"]],
[['text'=>"📋 Ulashish",'url'=>"https://t.me/share/url?url=https://t.me/$bot?start=$newkino"]],]])]);
unlink("step/$cid.step");
exit();
}else{
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"<b>1</b>",
'parse_mode'=>'html',
'disable_web_page_preview'=>true,
'reply_markup'=>$menu,]);}
/* DELETE MOVIE */
if($step == "kinostep_del" && isset($text)){
if(in_array($cid, $admins)){
$kino_id = $text;
// Kino ID bo'yicha papka mavjudligini tekshirish
if (file_exists("kino/$kino_id")) {
// Papkani va ichidagi fayllarni o'chirish
array_map('unlink', glob("kino/$kino_id/*"));
rmdir("kino/$kino_id");
// Kino muvaffaqiyatli o'chirildi
bot('SendMessage', [
'chat_id' => $cid,
'text' => "🎬 Kino (ID: $kino_id) muvaffaqiyatli o'chirildi!",
'parse_mode' => 'html',
'reply_markup' => $boshqarish,]);
} else {
// Kino topilmadi
bot('SendMessage', [
'chat_id' => $cid,
'text' => "❌ Bunday kino kodiga ega kino topilmadi! Iltimos, to'g'ri kino kodini kiriting.",
'parse_mode' => 'html',
'reply_markup' => $boshqarish,]);}
// Stepni reset qilish
file_put_contents("step/$cid.step", '');
exit();}}
/* USER CONTROL */
if ($text === "Admin +" && in_array($cid, $admins)) {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Yangi adminning ID sini kiriting:</b>",
'parse_mode' => 'html',
'reply_markup' => $boshqarish,]);
file_put_contents("step/$cid.step", "add_admin", LOCK_EX);
exit();}
if ($step === "add_admin" && isset($tx) && in_array($cid, $admins)) {
if (is_numeric($text)) {
$new_admin_id = $text;
$admins_file = file_get_contents("tools/admins.txt");
if (strpos($admins_file, $new_admin_id) === false) {
file_put_contents("tools/admins.txt", "$admins_file\n$new_admin_id", LOCK_EX);
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Foydalanuvchi (ID: $new_admin_id) admin sifatida qo‘shildi!</b>",
'parse_mode' => 'html',
'reply_markup' => $panel,]);
} else {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Bu foydalanuvchi allaqachon admin!</b>",
'parse_mode' => 'html',
'reply_markup' => $panel,]);}
unlink("step/$cid.step");
exit();} }
if ($text === "Admin -" && in_array($cid, $admins)) {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>O‘chirmoqchi bo‘lgan adminning ID sini kiriting:</b>",
'parse_mode' => 'html',
'reply_markup' => $boshqarish,]);
file_put_contents("step/$cid.step", "remove_admin", LOCK_EX);
exit();}
if ($step === "remove_admin" && isset($text) && in_array($cid, $admins)) {
if (is_numeric($text)) {
$remove_id = $text;
$admins_array = file("tools/admins.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (in_array($remove_id, $admins_array)) {
// Admin ro'yxatidan olib tashlash
$new_admins = array_filter($admins_array, fn($id) => $id != $remove_id);
file_put_contents("tools/admins.txt", implode("\n", $new_admins), LOCK_EX);
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Foydalanuvchi (ID: $remove_id) adminlar ro‘yxatidan o‘chirildi!</b>",
'parse_mode' => 'html',
'reply_markup' => $panel,]);
} else {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Bu foydalanuvchi adminlar ro‘yxatida yo‘q!</b>",
'parse_mode' => 'html',
'reply_markup' => $panel,]);}
unlink("step/$cid.step");
exit();}}
//Pro+
if ($text === "Pro +" && in_array($cid, $admins)) {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Yangi PRO foydalanuvchining ID sini kiriting:</b>",
'parse_mode' => 'html',
'reply_markup' => $boshqarish,
]);
file_put_contents("step/$cid.step", "add_pro", LOCK_EX);
exit();
}

if ($step === "add_pro" && isset($tx) && in_array($cid, $admins)) {
if (is_numeric($text)) {
$new_pro_id = $text;
$pro_file = "pro/$new_pro_id.txt";

if (!file_exists($pro_file)) {
$expiry_time = time() + (30 * 24 * 60 * 60); // 1 oy
file_put_contents($pro_file, "active|$expiry_time", LOCK_EX);

bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Foydalanuvchi (ID: $new_pro_id) PRO foydalanuvchi sifatida qo‘shildi! PRO muddat: 1 oy</b>",
'parse_mode' => 'html',
'reply_markup' => $panel,
]);
} else {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Bu foydalanuvchi allaqachon PRO foydalanuvchi!</b>",
'parse_mode' => 'html',
'reply_markup' => $panel,
]);
}

unlink("step/$cid.step");
exit();
}
}
// PRO o'chirish
if ($text === "Pro -" && in_array($cid, $admins)) {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>O‘chirmoqchi bo‘lgan PRO foydalanuvchining ID sini kiriting:</b>",
'parse_mode' => 'html',
'reply_markup' => $boshqarish,
]);
file_put_contents("step/$cid.step", "remove_pro", LOCK_EX);
exit();
}

if ($step === "remove_pro" && isset($text) && in_array($cid, $admins)) {
if (is_numeric($text)) {
$remove_pro_id = $text;
$file_path = "pro/$remove_pro_id.txt";

if (file_exists($file_path)) {
unlink($file_path); // Faylni o‘chiradi
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Foydalanuvchi (ID: $remove_pro_id) PRO ro‘yxatdan o‘chirildi.</b>",
'parse_mode' => 'html',
'reply_markup' => $panel,
]);
} else {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>Bu foydalanuvchi PRO ro‘yxatida topilmadi.</b>",
'parse_mode' => 'html',
'reply_markup' => $panel,
]);
}

unlink("step/$cid.step");
exit();
}
}
//CANCEL MINI
if($data == "baz"){
bot('deleteMessage',[
'chat_id' => $cid2,
'message_id' => $mid2,
'reply_markup' => $usermenu]);
bot('sendMessage', [
'chat_id' => $cid2,
'message_id' => $mid2,
'text'=>"Bekor qilindi ⚠️",
'reply_markup' => $usermenu]);
if (file_exists("step/$cid2.step")) {
unlink("step/$cid2.step"); // Foydalanuvchi bosqichini tozalash
}exit();}
/* STEP PANEL */
if($data == "boshqarish"){
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
]);
bot('SendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>Admin paneliga xush kelibsiz!</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel,
]);
exit();
}
?>