<?php
error_reporting(0);
header('Content-type: application/json;');

$request = $_GET['request'] ?? "arz"; 

$urls = [
    "tala" => "https://akharinkhabar.ir/price/gold",
    "arz" => "https://akharinkhabar.ir/price/money"
];

$patterns = [
    "tala" => '/<div class="item_container__GWrAw">(?:(?!<\/div>).)+<\/div>\s*<\/div>/s',
    "arz" => '/<div class="item_container__IRmY8">(?:(?!<\/div>).)+<\/div>\s*<\/div>/s'
];

$ch = curl_init($urls[$request]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
$html = curl_exec($ch);
curl_close($ch);

preg_match_all($patterns[$request], $html, $matches);

$formattedCurrencies = array();
foreach ($matches[0] as $value) {
    preg_match('/<h4 class="item_title__.*?">(.+?)<\/h4>/', $value, $name_match);
    preg_match('/<span class="item_price__.*?">(.+?)<\/span>/', $value, $price_match);

    $formattedCurrency = array(
        'name' => strip_tags($name_match[1]),
        'price' => strip_tags($price_match[1]),
    );
    $formattedCurrencies[] = $formattedCurrency;
}

echo json_encode($formattedCurrencies, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
