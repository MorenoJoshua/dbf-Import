<?php
require_once 'Lang.php';
$strings = new Lang();

if ($_FILES) {
    $filename = $_REQUEST['filename'] . '_' . time() . '.' . strtolower($_REQUEST['conv']);
    date_default_timezone_set('America/Tijuana');
    require 'xbase.php';
    $table = new \XBase\Table($_FILES['dbf']['tmp_name']);
    $arrayfile = [];
    $cols = [];
    foreach ($table->getColumns() as $key => $val) {
        $cols[] = $key;
    }
    $arrayfile[] = $cols;

    $joined = join('", "', $cols);
    $toecho = <<<CSV
"$joined"

CSV;

    while ($row = $table->nextRecord()) {
        $thisrow = [];
        foreach ($cols as $col) {
            $thisrow[] = $row->$col;
        }
        $arrayfile[] = $thisrow;
        $joinedrow = join('", "', $thisrow);
        $toecho .= <<<CSV
"$joinedrow"

CSV;
    }

    switch (strtolower($_POST['conv'])) {
        case 'csv':
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            echo $toecho;
            break;

        case 'xls':
            require_once 'excell/PHPExcel.php';
            $x = new PHPExcel();
            $sh1 = $x->createSheet();
            $sh1->fromArray($arrayfile);
            $writer = PHPExcel_IOFactory::createWriter($x, 'Excel5');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            break;

        case 'xslx':
            require_once 'excell/PHPExcel.php';
            $x = new PHPExcel();
            $sh1 = $x->createSheet();
            $sh1->fromArray($arrayfile);
            $writer = PHPExcel_IOFactory::createWriter($x, 'Excel2007');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            break;
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="j">
        <title>Convertidor DBF</title>
        <link rel="stylesheet" media="screen"
              href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <style>
            .convButton {
                font-size: xx-large;
                padding: 35px;
            }

            #cosa {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 100%;
                max-width: 600px;
                padding: 5px;
                background: white;
                box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3);
                opacity: 1;
                pointer-events: auto;
                transition: 400ms cubic-bezier(0, 0, 0.32, 1);
            }

            #cosa.noshow {
                transform: translate(-50%, 0%);
                opacity: 0;
                pointer-events: none;
                transition: 400ms cubic-bezier(0, 0, 0.32, 1);
            }

            #cosa:hover {
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }

            body {
                background: #4f9fb2;
            }

            .white {
                color: #fff;
            }

            .huge {
                font-size: 5em;
            }
        </style>
    </head>
    <body>
    <div class="panel noshow" id="cosa">
        <form action="" method="post" role="form" enctype="multipart/form-data">
            <div class="input-group">
                <div class="input-group-addon">
                    Nombre de archivo
                </div>
                <input type="text" class="form-control" name="filename" placeholder="..." required autofocus>
            </div>
            <div class="input-group">
                <div class="input-group-addon">Archivo</div>
                <input type="file" name="dbf" id="dbf" class="form-control" required>
            </div>
            <input type="submit" value="CSV" name="conv" class="convButton col-xs-12 col-sm-4 btn btn-primary"
                   id="acsv">
            <input type="submit" value="XLS" name="conv" class="convButton col-xs-12 col-sm-4 btn btn-info" id="axls">
            <input type="submit" value="XSLX" name="conv" class="convButton col-xs-12 col-sm-4 btn btn-default"
                   id="axsls">
        </form>

    </div>
    <div class="container">
        <h1 class="white huge text-center">DBF -> CSV/XLS</h1>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script>
        $(function () {

            $('form').on('submit', function (e) {
                $('.convButton').attr(disabled, true);
                setTimeout(function () {
                    $('.convButton').attr(disabled, false);
                }, 4e3);
            });
            $('#cosa').delay(400).removeClass('noshow');
        })
    </script>
    </body>
    </html>
    <?php
} ?>