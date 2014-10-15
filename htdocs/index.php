<?php

/*

BLOCKCHAINS WEBSITE

POWERED BY BLOCKSTRAP

http://blockstrap.com

*/

error_reporting(-1);

$base = dirname(__FILE__);
include_once($base.'/_libs/php/bs.php');
$bs = new blockstrap_core($base);

$slug = $bs->slug($_SERVER);
$currency = $bs->currency($_SERVER);
$directory = $bs->directory($_SERVER, $base);

$data = $bs->data($base, $slug, $directory, $currency);
$html = $bs->html($base, $slug, $directory);
$content = $bs->content($base, $slug, $directory);

if(isset($_GET['debug']) && $_GET['debug'] == true)
{
    var_dumped($data);
    var_dumped($html);
    var_dumped($content);
}

// ADD CONTENT TO DATA
$data['content'] = $content;

// MERGE DATA AND HTML
$bs->display($html, $data);

exit;
