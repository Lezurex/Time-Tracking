<?php

$contents = file_get_contents("json.json");
$data = json_decode($contents, true);

foreach ($data as $item) {
    print "\n";
    var_dump($item);
}
print "\n";

$i = readline("Index: ");
$input = readline("Append to json array: ");
$data[$i][] = $input;

$contents = json_encode($data, JSON_UNESCAPED_UNICODE);
file_put_contents("json.json", $contents);