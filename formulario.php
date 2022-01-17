<?php
    session_start();
    require_once("conectar.php");

    if (isset($_SESSION['cliente'])) {
        $id = $_SESSION['cliente']->id;
        $documento = $_SESSION['cliente']->documento;
        $nombre = $_SESSION['cliente']->nombre;
        $apellido = $_SESSION['cliente']->apellido;        
    } else {
        $id="";
        $documento = "";
        $nombre = "";
        $apellido = "";
    }

    if (isset($_SESSION['articulos'])) {
        $articulos = $_SESSION['articulos'];
    } else {
        $articulos = array();
    }

    $total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maestro Detalle</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>

</head>
<body>
    <div class="container">
        <h3>Formulario Maestro - Detalle</h3>
        <form action="modelo.php" method="POST">
            <div class="form-group">
                <table class="table table-bordered">
                    <tr>
                        <td><label for="documento">Documento</label></td>
                        <td><input type="text" class="form-control" name="documento"></td>
                        <td><button name="operacion" value="buscarcliente"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        </td>
                    </tr>
                    <tr>
                        <th>Documento</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                    </tr>
                    <tr>
                        <td><?php echo $documento; ?></td>
                        <td><?php echo $nombre; ?></td>
                        <td><?php echo $apellido; ?></td>
                    </tr>
                </table>
            </div>
        </form>
        <form action="modelo.php" method="POST">
            <div class="form-group">
                <table class="table table-bordered">
                    <tr>
                        <td><label for="codigo">Còdigo</label></td>
                        <td><label for="cantidad">Cantidad</label></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="codigo"></td>
                        <td><input type="text" class="form-control" name="cantidad"></td>                        
                        <td><button name="operacion" value="buscararticulo"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></button></td>
                    </tr>
                    <tr>
                        <th>Codigo</th>                        
                        <th>Descripcion</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php
                        foreach ($articulos as $a):
                    ?>
                    <tr>
                        <td><?php echo $a->codigo; ?></td>
                        <td><?php echo $a->descripcion; ?></td>
                        <td><?php echo $a->cantidad; ?></td>
                        <td><?php echo $a->precio; ?></td>
                        <td><?php echo $a->precio * $a->cantidad; ?></td>
                    </tr>
                    <?php
                        $total += ($a->precio * $a->cantidad);
                        endforeach;
                    ?>
                    <tr>
                        <th><h3>Total:</h3></th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><h3><?php echo $total; ?></h3></td>
                    </tr>
                </table>
            </div>
            <td><input type="submit" name="operacion" value="cancelar"></td>
            <td><input type="submit" name="operacion" value="facturar"></td>
        </form>        
    </div>
</body>
</html>