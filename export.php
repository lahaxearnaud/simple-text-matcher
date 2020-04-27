<?php

ini_set('memory_limit', -1);
$data = json_decode(file_get_contents(__DIR__.'/datasets/movies/records.json'), true);
$result = [];
foreach ($data as $item) {
    foreach ($item['actors'] as $name) {
        $name = mb_strtolower($name);
        $name = str_replace([' intl', ' airport'], '', $name);
        $result[] = trim($name);
    }

}
$data = json_decode(file_get_contents(__DIR__.'/datasets/movies/actors.json'), true);
foreach ($data as $item) {
    $name = $item['name'];
    $name = mb_strtolower($name);
    $name = str_replace([' intl', ' airport'], '', $name);
    $result[] = trim($name);
}


$result = array_unique($result);
sort($result);

file_put_contents(__DIR__ . '/Resources/dataset/actors.txt', implode("\n", $result), FILE_APPEND);

