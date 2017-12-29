<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte Demo</title>
    <link rel="stylesheet" media="screen" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <style>
        @keyframes progressX {
            from {
                width: 0%;
            }
            to {
                width: 93%;
            }
        }

        .animate {
            animation: progressX 15s 1 linear;
        }

        .animated {
            width: 93%;
        }

        .bigbutton {
            font-size: xx-large;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <nav class="navbar navbar-default navbar-top" role="navigation">
        <a class="navbar-brand" href="#">Reportes Demo</a>
        <ul class="nav navbar-nav">
            <li class="active">
                <a href="#">Reportes</a>
            </li>
            <li class="">
                <a href="dl.php?formato=xls">Descargar xls</a>
            </li>
            <li class="">
                <a href="dl.php?formato=xslx">Descargar xslx</a>
            </li>
            <li class="">
                <a href="dl.php?formato=csv">Descargar csv</a>
            </li>
            <li class="">
                <a class="" data-toggle="modal" href="#import">Importar DBF</a>
            </li>
        </ul>
    </nav>

    <form action="" id="reporte">
    </form>

    <form action="" id="reporte">
        <div class="col-xs-6">
            <div class="input-group">
                <div class="input-group-addon">De:</div>
                <input type="date" name="de" id="de" class="form-control" required="required"
                       value="<?= $_REQUEST['de'] ?>">
            </div>
        </div>
        <div class="col-xs-6">
            <div class="input-group">
                <div class="input-group-addon">Hasta:</div>
                <input type="date" name="hasta" id="hasta" class="form-control" value="<?= $_REQUEST['hasta'] ?>"
                       max="<?= date('Y-m-d') ?>"
                       required="required">
            </div>
        </div>
        <div class="col-xs-12 text-right">
            <input type="submit" value="Generar" class="btn btn-success">
            <a type="submit" class="btn btn-warning" data-toggle="modal" href="#guardarModal" id="guardarcomo"
               style="display: none;">Guardar Como...</a>
        </div>
    </form>
    <div id="res" class="col-xs-12"></div>

    <div class="modal fade" id="import">
        <form action="import.php" enctype="multipart/form-data" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Importar Archivo DBF</h4>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="dbf" id="importarDBF" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-primary" id="submitButton" value="Importar">
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </form>
    </div><!-- /.modal -->
</div>

<div class="modal fade" id="guardarModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Guardar Reporte como...</h4>
            </div>
            <div class="modal-body">
                <div class="col-xs-4">
                    <a href="#" class="btn btn-info bigbutton" id="csv"><span class="glyphicon glyphicon-save"></span>
                        CSV</a>
                </div>
                <div class="col-xs-4">
                    <a href="#" class="btn btn-primary bigbutton" id="xls"><span
                            class="glyphicon glyphicon-save"></span> XLS</a>
                </div>
                <div class="col-xs-4">
                    <a href="#" class="btn btn-default bigbutton" id="xslx"><span
                            class="glyphicon glyphicon-save"></span>
                        XSLX</a>
                </div>
            </div>
            <div class="clearfix">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script>
    $(function () {
        $('#reporte').on('submit', function (e) {
            e.preventDefault();
            var topost = $(this).serialize();
            $('#csv').attr('href', `guardarcomo.php?formato=csv&${topost}`);
            $('#xls').attr('href', `guardarcomo.php?formato=xls&${topost}`);
            $('#xslx').attr('href', `guardarcomo.php?formato=xslx&${topost}`);

            console.log(topost);
            $.post('reporte.php?tipo=json&', topost, function (data) {
                var rows = JSON.parse(data);
                $('#res').html(`<table class="table table-condensed table-hover"><thead>
    <tr>
        <th>Fecha</th>
        <th>Cod Cliente</th>
        <th>Num Control</th>
        <th>Num Factura</th>
        <th>Subtotal</th>
        <th>IVA</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody id="reporteRowsHere"></tbody>
    <tbody><tr>
<td colspan="4"></td>
<td id="subtotaltotal" class="text-right"></td>
<td id="ivatotal" class="text-right"></td>
<td id="totaltotal" class="text-right"></td>
</tr>
</tbody>
</table>`);
                let reshere = document.querySelector('#reporteRowsHere');
                var subtotaltotal = 0;
                var ivatotal = 0;
                var totaltotal = 0;
                for (var row in rows) {
                    if ((row * 1) == row) {
                        reshere.insertAdjacentHTML('beforeEnd', genRow(rows[row]));
                        subtotaltotal += (rows[row][4]) * 1;
                        ivatotal += rows[row][5] * 1;
                        totaltotal += rows[row][6] * 1;
//                        console.log(rows[row][4], rows[row][5], rows[row][6]);
//                        console.log(subtotaltotal, ivatotal, totaltotal);
//                        if (row == 10) {
//                            console.log(subtotaltotal, ivatotal, totaltotal);
//                            break;
//                        }
                    }
                }
                $('#guardarcomo').show();
                document.querySelector('#subtotaltotal').innerText = subtotaltotal.toFixed(2);
                document.querySelector('#ivatotal').innerText = ivatotal.toFixed(2);
                document.querySelector('#totaltotal').innerText = totaltotal.toFixed(2);

            })
        });

        $('importarDBF').on('submit', function () {
            $('#submitButton').prop('disabled', true);
        })
    })

    function genRow(json) {
        json[4] = json[4].toFixed(2);
        json[5] = json[5].toFixed(2);
        json[6] = json[6].toFixed(2);
        return `<tr>
<td>${json[0]}</td>
<td>${json[1]}</td>
<td>${json[2]}</td>
<td>${json[3]}</td>
<td class="text-right">${json[4]}</td>
<td class="text-right">${json[5]}</td>
<td class="text-right">${json[6]}</td>
</tr>
`
    }
</script>
</body>
</html>