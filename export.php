<?php



$handle = fopen(__DIR__.'/Resources/dataset/fr/villes_france.csv', "r");
$result = [];
fgets($handle);
while (($line = fgets($handle)) !== false) {
    $line = utf8_encode($line);
    $line = explode(',', $line);
    $line = mb_strtolower(trim($line[3]));
    $line = str_replace('"', '', $line);
    if (in_array($line, $result, true)) {
        continue;
    }
    $result[] = $line;
}

$result = array_unique($result);

fclose($handle);

file_put_contents(__DIR__ . '/Resources/dataset/cities.txt', implode("\n", $result));

