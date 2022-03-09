<?php

$group = 2;
$x = <<<EOL

EOL;

$data = [];

foreach (explode(PHP_EOL, $x) as $l)
{
    $l = trim($l);
    $l = preg_replace('/\d.\. /', '', $l);
    
    if (strpos($l, ' – '))
        [$author, $name] = explode(' – ', $l);
    else if (strpos($l, ' - '))
        [$author, $name] = explode(' - ', $l);
    else
        continue;
    
    
    
    $data[] = [
        'author' => $author,
        'name' => $name,
        'group' => $group,
        'type' => null,
    ];
}

echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);