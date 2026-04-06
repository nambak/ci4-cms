<?php
$safeName = basename($name);
$path = __DIR__ . '/icons/' . $safeName . '.svg';

if (file_exists($path)) {
    $svg = file_get_contents($path);

    if (preg_match('/(<svg[^>]*class=(["\'])[^"\']*)\2/', $svg)) {
        // class="existing" → class="existing w-6 h-6"
        $svg = preg_replace('/(<svg[^>]*class=(["\'])[^"\']*)\2/', '$1 ' . esc($class) . '$2', $svg);
    } else {
        // class 속성이 없으면 새로 추가
        $svg = str_replace('<svg ', '<svg class="' . esc($class) . '" ', $svg);
    }
    echo $svg;
}
?>
