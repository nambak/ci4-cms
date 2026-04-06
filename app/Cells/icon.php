<?php
$path = __DIR__ . '/icons/' . $name . '.svg';

if (file_exists($path)) {
    $svg = file_get_contents($path);
    // SVG에 class 속성 주입
    echo str_replace('<svg ', '<svg class="' . esc($class) . '" ', $svg);
}
?>
