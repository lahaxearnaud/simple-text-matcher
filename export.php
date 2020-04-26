<?php



$handle = fopen(__DIR__.'/model.csv', "r");
$result = [];
fgets($handle);
while (($line = fgets($handle)) !== false) {
    $line = utf8_encode($line);
    $line = explode(',', $line);
    $line = mb_strtolower(trim($line[2]));
    $line = str_replace('"', '', $line);
    if (in_array($line, $result, true)) {
        continue;
    }
    $result[] = $line;
}

$result = array_unique($result);

fclose($handle);

file_put_contents(__DIR__ . '/Resources/dataset/car_models.txt', implode("\n", $result));

