<?php

function dirsize($d)
{
    $count1 = 0;
    $dh = opendir($d);
    while (($files = readdir($dh)) !== false) {
        if ($files != "." && $files != "..") {
            $path = $d . "/" . $files;
            if (is_dir($path)) {
                $count1 += dirsize($path, $count1);
            } elseif (is_file($path)) {
                $count1 = filesize($path);
            }
        }
    }
    closedir($dh);
    return $count1;
}

//echo dirsize("test.php");
echo "<hr>";

function getSize(string $path)
{
    $size = 0;
    if (is_file($path)) {
        $size += filesize($path);
    } elseif (is_dir($path)) {
        $array = scandir($path);
        foreach (array_slice($array, 2) as $item) {
            $size += getSize($path . "/" . $item);
        }
    }
    return $size;
}

echo getSize("C:/xampp/htdocs/form/Search/");