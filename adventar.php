<?php
function ShowErrorMessage($message)
{
  print <<< EOD
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">

<title>Error</title>
<link rel="stylesheet" href="http://tatt.ch/style.css">
</head>
<body>
<div id="container">
$message
</div>
</body>
</html>
EOD;
}

// URLを解析します。
$current_dir = "adventar";
$base_name = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . "/" . $current_dir;
$url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

$REQUEST_URI = $_SERVER["REQUEST_URI"];  // 例:/adventar/2016/kuin/1/
preg_match("/^\/$current_dir\/(\d+)\/([^\/]+)\/(\d+)\/((?:preview)?)$/", $REQUEST_URI, $match);
if(count($match) != 5){
  ShowErrorMessage("不正なURLです。");
  return;
}
$year = $match[1];
$dir = $match[2];
$day = $match[3];
$additional = $match[4];
$isPreview = (bool)($additional == "preview");
$fileurl = "$year/$dir/day$day/index.html";
$fileurl_prev = "$year/$dir/day" . strval($day - 1) . "/index.html";
$fileurl_post = "$year/$dir/day" . strval($day + 1) . "/index.html";
$theme_dat = "$year/$dir/theme.dat";
$adventarId_dat = "$year/$dir/adventar_id.dat";

// 日付をチェックします。
$utc = time();
$now = new DateTime();
$now->setTimestamp($utc)->setTimezone(new DateTimeZone('Asia/Tokyo'));
$release_time = DateTime::createFromFormat('j-M-Y', "$day-Dec-$year");
$daycheck = (bool)($now->getTimestamp() >= $release_time->getTimestamp());

// 記事の存在をチェックします。
$utc = time();

if(0){
}else if($day <= 0 || 25 < $day){
  ShowErrorMessage("無効なURLです。");
}else if(!file_exists($theme_dat)){
  ShowErrorMessage("テーマが準備できていません。");
}else if(!file_exists($fileurl)){
  ShowErrorMessage("記事が準備できていません。");
}else{
  $theme = trim(file_get_contents($theme_dat));
  $adventar_id = 0;
  if(file_exists($adventarId_dat)){
    $adventar_id = trim(file_get_contents($adventarId_dat));
  }else{
    $adventar_id = -1;
  }
  $title = "$theme Advent Calendar ${year} - ${day}日目";

  $header = "";
  $footer = "";
  $main_html = "";
  $is_include_kuin_code = false;
  {
    $prev_exist = file_exists($fileurl_prev);
    $post_exist = file_exists($fileurl_post);
    $disp_div = ($prev_exist || $post_exist) ? "inline" : "none";
    $prev_visivility = $prev_exist ? "visible" : "hidden";
    $post_visivility = $post_exist ? "visible" : "hidden";
    $day_m1 = $day - 1;
    $day_p1 = $day + 1;

    $header = <<< EOD
<div style="display:$disp_div">
  <a href="../$day_m1/$additional" style="visibility:$prev_visivility">←前の日の記事</a>　　
  <a href="../$day_p1/$additional" style="visibility:$post_visivility">→次の日の記事</a>
</div>
<hr>
EOD;

    $footer = <<< EOD
<div style="display:$disp_div">
  <hr>
  <a href="../$day_m1/$additional" style="visibility:$prev_visivility">←前の日の記事</a>　　
  <a href="../$day_p1/$additional" style="visibility:$post_visivility">→次の日の記事</a>
</div>
EOD;
  }

  $ogp_flag = false;
  $ogp_data = "";
  if($daycheck || $isPreview){
    $main_html =  file_get_contents($fileurl);

    while(preg_match("/###(\w*.(kn|kn.txt|kn.png))###/", $main_html, $match)){
      $expand_filename = $match[1];
      $ext = $match[2];

      if($ext == 'kn'){
        $expand_url = "$year/$dir/src/$expand_filename";
        if(file_exists($expand_url)){
          $kn_src =  htmlspecialchars(file_get_contents($expand_url));

          $kn = <<< EOD
<div>
  <span style="background-color:#f0f8ff; padding:0 10px 0 10px"><a href="../src/$expand_filename">$expand_filename</a></span>
  <pre id="code" style="border-radius:0; margin: 0px -10px 0px -10px;">$kn_src</pre>
</div>
EOD;

          $kn2 = "";
          for($i = 0; $i < strlen($kn); $i++){
            if($kn[$i] == '\\'){
              $kn2 .= "\\\\";
            }else{
              $kn2 .= $kn[$i];
            }
          }
          $testtest = "[hogehoge]";
          $main_html = preg_replace("/^([\d\D]*)###(\w*.kn)###([\d\D]*)$/", "\${1}" . $kn2 . "<hr>\${3}", $main_html);
          #$main_html = preg_split("/^([\d\D]*)###(\w*.kn)###([\d\D]*)$/", $main_html).join( "\${1}" . $kn2 . "<hr>test4<hr>\${3}");
          $is_include_kuin_code = true;
        }else{
          $main_html = preg_replace("/^([\d\D]*)###(\w*.kn)###([\d\D]*)$/", "\${1}##$expand_filename##\${3}", $main_html);
        }
      }else if($ext == 'kn.png'){
        $expand_url = "$year/$dir/output_images/$expand_filename";
        if(file_exists($expand_url)){
          $png = <<< EOD
<div style="text-align:center; background-color:#ffffff">
<img src="../output_images/$expand_filename" alt="[Image:$expand_filename]" style="box-sizing: border-box; max-width:100%; vertical-align: middle; border:3px solid; border-color:#888888"/>
</div>
EOD;
          $main_html = preg_replace("/^([\d\D]*)###(\w*.kn.png)###([\d\D]*)$/", "\${1}$png\${3}", $main_html);
          if(!$ogp_flag){
            $ogp_flag = true;
            $ogp_data = <<<EOD
<meta property="og:title" content="$title" />
<meta property="og:type" content="article" />
<meta property="og:url" content="$url" />
<meta property="og:image" content="$base_name/$expand_url" />
<meta property="og:site_name" content="Tatt.ch" />
<meta property="og:description" content="$title" />
<meta name="twitter:card" content="summary" />
EOD;
          }
        }else{
          $main_html = preg_replace("/^([\d\D]*)###(\w*.kn.png)###([\d\D]*)$/", "\${1}##$expand_filename##\${3}", $main_html);
        }
      }else if($ext == 'kn.txt'){
        $expand_url = "$year/$dir/output_txt/$expand_filename";
        if(file_exists($expand_url)){
          $txt_src =  htmlspecialchars(file_get_contents($expand_url));
          $txt = <<< EOD
<span style="background-color:#fff8f0; padding:0 10px 0 10px"><a href="../output_txt/$expand_filename">出力結果</a></span>
<pre style="background-color:#fff8f0; margin: 0px -10px 0px -10px; padding:0px 20px 0px 20px; overflow:auto;">$txt_src</pre>
EOD;
          $main_html = preg_replace("/^([\d\D]*)###(\w*.kn.txt)###([\d\D]*)$/", "\${1}$txt\${3}", $main_html);
        }else{
          $main_html = preg_replace("/^([\d\D]*)###(\w*.kn.txt)###([\d\D]*)$/", "\${1}##$expand_filename##\${3}", $main_html);
        }
      }
    }
  }else{
    $main_html = "※未公開です。当日の午前0時0分に公開されます。";
  }

  print <<< EOD
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
EOD;
  if ($ogp_flag) {
    print $ogp_data;
  }
  print <<< EOD

<title>$title</title>
<link rel="stylesheet" href="http://tatt.ch/style.css">
<style>
    hr {border-top: 2px solid #B0D4E5;}
</style>

EOD;

  if($is_include_kuin_code){
    print <<< EOD
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-AMS_HTML" defer="defer"></script>
<script src="https://tatt61880.github.io/kuin-web/src-noconflict/ace.js?2020-07-17"></script>
<script src="https://tatt61880.github.io/kuin-web/src-noconflict/ext-language_tools.js?2020-07-17"></script>
<script>
window.onload = function() {
  let editor = ace.edit('code');
  editor.setTheme('ace/theme/kuin');
  editor.session.setMode('ace/mode/kuin');
  editor.setReadOnly(true);
  editor.setOptions({
    maxLines: 10000,
    autoScrollEditorIntoView: true,
    fontSize: '16px',
  });
  editor.resize();
}
</script>

EOD;
  }
  print <<< EOD
</head>
<body>
<div id="container">
<h1>$title</h1>
EOD;
  if($adventar_id != -1){
    print <<< EOD
<p>この記事は【<a href="http://www.adventar.org/calendars/$adventar_id">$theme Advent Calendar ${year}</a>】の${day}日目の記事です。</p>
EOD;
  }else{
    print <<< EOD
<p>この記事は【$theme Advent Calendar ${year}】の${day}日目の記事です。</p>
EOD;
  }
  print $header;
  if($theme == "Kuin"){
    print <<< EOD
<p style="color:#008888; font-size:0.8em">【記事中で紹介しているコードについて】<br>
コンパイルが通らない場合など、不具合があれば、<a href="https://twitter.com/tatt61880">@tatt61880</a>まで連絡いただけると助かります。よろしくお願いいたします。</p><hr>
EOD;

  }
  print $main_html;
  print $footer;

  print <<< EOD
</div>
</body>
</html>
EOD;
    }
?>
