<?php

// функция удаляет все файлы в папке и саму директорию
function deleteDir($dir)
{
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? deleteDir("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

// функция создает директорию
function createDir(string $patnNewDir, int $mode = 0777)
{

    if (!mkdir($patnNewDir, $mode, true)) {
        return false;
    } else
        return true;
}


// функция форматирует вывод размера файла
function format_size($size)
{
    $metrics[0] = 'байт';
    $metrics[1] = 'Кбайт';
    $metrics[2] = 'Мбайт';
    $metrics[3] = 'Гбайт';
    $metrics[4] = 'Тбайт';
    $metric = 0;
    while (floor($size / 1024) > 0) {
        $metric++;
        $size /= 1024;
    }
    $result = round($size, 1) . " " .
        (isset($metrics[$metric]) ? $metrics[$metric] : '???');
    return $result;
}

// функция считает размер папки или файла
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


//$path = "D:/Andrey/";
$path = "../";

if (!empty($_GET["path"]) === true) {
    $pathGet = strip_tags($_GET['path']);
    if (is_dir($pathGet)) {
        if ($pathGet[strlen($pathGet) - 1] !== "/") {
            $pathGet = $pathGet . "/";
        }
        $path = $pathGet;
    } else {
        $message = "Неправильный путь.";
    }
}

if ((!empty($_GET["pathDir"]) === true) and (!empty($_GET["nameDir"]))) {
    $nameDir = preg_replace('/[^(\w) | (\x7F-\xFF) | (\s)]/', '', $_GET["nameDir"]);
    if (createDir($_GET["pathDir"] . $nameDir)) {
        $message = "Директория успешно созданна.";
    } else {
        $message = "Ошибка создания директории";
    }
}

// удаляем директорию или папку
/*if (!empty($_GET["delete"]) === true) {
    if (is_file($_GET["delete"])) {
        if (unlink($_GET["delete"])) {
            $message = "Файл " . $_GET["delete"] . " удален.";
        } else {
            $message = "Ошибка удаления файла.";
        }
    } elseif (is_dir($_GET["delete"])) {
        if (deleteDir($_GET["delete"]) !== false) {
            $message = "Директория " . basename($_GET["delete"]) . " удалена.";
        } else {
            $message = "Ошибка удаления директории.";
        }

    }

}*/


// функция фозвращает список файлов в папке
function search(string $path)
{
    $array = scandir($path);
    if ($array[0] == ".") {
        $array = array_slice($array, 1);
    }
    return $array;
}

function infoArray(string $pathFile)
{
    return @stat($pathFile); // у меня не получилось обойти или проверить на ошибку Warning
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search</title>

    <!-- Latest compiled and minified CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <script
            src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
            crossorigin="anonymous"></script>
    <style>
        body {
            background-color: whitesmoke;
        }

        .container {
            margin-top: 20px;
        }

        form {
            color: #039;
            font-size: 16px;
        }

        table {
            width: 100%;
            text-align: left;
        }

        img {
            height: 20px;
            width: 20px;
        }

        th {
            font-weight: normal;
            color: #039;
            padding: 10px 15px;
        }

        td {
            color: #3f3f64;
            border-top: 1px solid #c7ccde;
            padding: 5px 10px;
            font-size: 14px;
        }

        tr:hover td {
            background: #d2d2ff;
        }

        .create .form-control,
        .create .btn {
            height: 28px;
            font-size: 12px;
        }

    </style>
</head>
<body>

<div class="container">
    <?php if (empty($message) === false): ?>
        <div class="alert alert-info" role="alert">
            <p><?php echo $message; ?></p>
        </div>
    <?php endif; ?>
    <form class="form-inline" action="Search.php" method="GET">
        <div class="form-group">
            <label for="path">Путь </label>
            <input type="text" class="form-control" id="path" name="path" placeholder="D:/Andrey/">
        </div>
        <button type="submit" class="btn btn-primary">Открыть</button>
    </form>
    <table>
        <tr>
            <th>Файл</th>
            <th>Тип</th>
            <th>MINE-тип</th>
            <th>Размер</th>
            <th>Владелец</th>
            <th>Создан</th>
            <th>Просмотрен</th>
            <th>Изменен</th>
            <th>Действие с объектом</th>
        </tr>
        <?php if ($array = search($path)): ?>
            <?php foreach ($array as $item): ?>
                <tr>
                    <?php
                    echo "<td>";
                    echo (is_dir($path . $item)) ? "<a href=\"?path=" . realpath($path . $item) . "\">" : "";
                    //                    echo (is_dir($path.$item))? "<a href=\"?path={$path}{$item}\">": "";
                    echo "<img src=\"", is_dir($path . $item) ? "dir.png" : "file.png", "\">";
                    echo ($item === "..") ? "<img src=\"return.png\"> $item" : " $item";
                    echo (is_dir($path . $item)) ? "</a>" : "";
                    echo "</td>";
                    if (($info = infoArray($path . $item)) !== false) {
                        echo "<td>", (is_dir($path . $item)) ? "dir" : "file", "</td>";
                        echo "<td>", (is_file($path . $item)) ? mime_content_type($path . $item) : '', "</td>";
                        echo "<td>", (is_file($path . $item)) ? format_size($info[7]) : '-', "</td>";

//                        echo "<td>".format_size(getSize($path.$item))."</td>";
                        echo "<td>" . $info[4] . "</td>";
                        echo "<td>", date('j-m-Y', $info[10]), "</td>";
                        echo "<td>", date('j-m-Y', $info[8]), "</td>";
                        echo "<td>", date('j-m-Y', $info[9]), "</td>";
                    } else {
                        echo "<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>";
                    }
                    echo "<td>";
                    echo ($item !== "..") ? "<a href=\"?path={$path}&delete=" . realpath($path . $item) . "\">Удалить</a>" : "-";
                    //                    echo (is_file($path.$item))? "<a href=\"{$path}{$item}\"> Открыть</a>":"";
                    echo "</td>";

                    ?>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="9">
                    <form class="form-inline create" action="Search.php" method="GET">
                        <div class="form-group">
                            <input type="hidden" name="path" value="<?php echo $path; ?>">
                            <input type="hidden" name="pathDir" value="<?php echo $path; ?>">
                            <input type="text" class="form-control" name="nameDir" placeholder="Новая папка">
                        </div>
                        <button type="submit" class="btn btn-primary">Создать папку</button>
                    </form>
                </td>
            </tr>
        <?php endif; ?>

    </table>
</div>

</body>
</html>
