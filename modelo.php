<?php
session_start();

$operacion = $_REQUEST['operacion'];

switch ($operacion) {
    case 'buscarcliente': buscarcliente();
        # code...
        break;

    case 'buscararticulo': buscararticulo();
        # code...
        break;

    case 'facturar': facturar();
        # code...
        break;

    case 'cancelar': cancelar();
        # code...
        break;

    default:
        # code...
        break;
}


function buscarcliente()
{
    include("conectar.php");
    $documento = $_REQUEST['documento'];
    $cliente = $bd->query("SELECT * FROM cliente WHERE documento = $documento")->fetch(PDO::FETCH_OBJ);
    $_SESSION['cliente'] = $cliente;
    header("location:formulario.php");
/*
    echo"<pre>";
    print_r($_SESSION); 
    echo"</pre>";
*/
}

function buscararticulo()
{
    include("conectar.php");
    $codigo = $_REQUEST['codigo'];
    $cantidad = $_REQUEST['cantidad'];
    $articulo = $bd->query("SELECT * FROM producto WHERE codigo = $codigo")->fetch(PDO::FETCH_OBJ);
    $articulo->cantidad = $cantidad;
    $_SESSION['articulos'][] = $articulo;

    header("location:formulario.php");
}

function facturar()
{
    include("conectar.php");
    $cliente = $_SESSION['cliente'];
    $articulos = $_SESSION['articulos'];

    $bd->query("INSERT INTO pedido(fecha, id_cliente, cantidad_productos, importe_total, estado) VALUES('$HOY',$cliente->id,0,'0.00',1)");
    $ultimopedido = $bd->lastInsertId();

    foreach ($articulos as $a)
    {
        $bd->query("INSERT INTO itempedido(id_pedido, id_producto, cantidad, importe) VALUES($ultimopedido, $a->id_producto, $a->cantidad, '$a->precio' )");
    }

    imprimir($ultimopedido);
/*
    session_destroy();
    header("location:formulario.php");
*/
/*
    echo"<pre>";
    print_r($_SESSION); 
    echo"</pre>";
*/
}

function imprimir($ultimopedido)
{
    include("conectar.php");
    include("fpdf/fpdf.php");
    $cliente = $_SESSION['cliente'];
    $articulos = $_SESSION['articulos'];
    $totalimporte = 0;
    $totalproductos = 0;
    /*
    $factura = $bd->query("SELECT pedido.id as idpedido, cliente.documento as documento, cliente.apellido as apellido, cliente.nombre as nombre, itempedido.id_producto as idproducto, producto.descripcion as descripcion, itempedido.cantidad as cantidad, itempedido.importe as importe, pedido.cantidad_productos as cantidad_productos, pedido.importe_total as importe_total
                        FROM pedido, itempedido, cliente, producto
                        WHERE pedido.id = itempedido.id_pedido
                        AND pedido.id_cliente = cliente.id
                        AND itempedido.id_producto = producto.id_producto
                        AND pedido.id = $ultimopedido")->fetchAll(PDO::FETCH_OBJ);
*/
/*
    echo"<pre>";
    print_r($_SESSION);

    print_r($factura);
    echo"</pre>";
*/
    $hoja = new FPDF();
    $hoja->AddPage();
    $hoja->SetFont('Arial', 'B', 28);
    $hoja->SetXY(10,10);
    $hoja->Cell(200, 20, 'VENTA', 0, 1,  'C', false, '');
    $hoja->SetFont('Arial', 'B', 20);
    $hoja->Cell(80, 20, 'Detalle del Pedido: ', 0, 0,  'C', false, '');
    $hoja->Cell(80, 20, $ultimopedido, 0, 1,  'C', false, '');
    $hoja->SetFont('Arial', 'B', 12);

    $hoja->Cell(35, 15, 'Documento', 1, 0,  'C', false, '');
    $hoja->Cell(60, 15, 'Apellido', 1, 0,  'C', false, '');
    $hoja->Cell(60, 15, 'Nombre', 1, 1,  'C', false, '');
    $hoja->Cell(35, 15, $cliente->documento, 1, 0,  'C', false, '');
    $hoja->Cell(60, 15, $cliente->apellido, 1, 0,  'C', false, '');
    $hoja->Cell(60, 15, $cliente->nombre, 1, 1,  'C', false, '');

    $hoja->Ln();

    $hoja->SetFont('Arial', 'B', 10);
    $hoja->Cell(20, 10, 'Codigo', 1, 0,  'C', false, '');
    $hoja->Cell(80, 10, 'Descripcion', 1, 0,  'C', false, '');
    $hoja->Cell(20, 10, 'Cantidad', 1, 0,  'C', false, '');
    $hoja->Cell(30, 10, 'Importe', 1, 0,  'C', false, '');
    $hoja->Cell(30, 10, 'Subtotal', 1, 1,  'C', false, '');

    $hoja->SetFont('Arial', 'I', 10);
    foreach ($articulos as $a) {
        $hoja->Cell(20, 8, $a->codigo, 1, 0,  'C', false, '');
        $hoja->Cell(80, 8, $a->descripcion, 1, 0,  'C', false, '');
        $hoja->Cell(20, 8, $a->cantidad, 1, 0,  'C', false, '');
        $hoja->Cell(30, 8, $a->precio, 1, 0,  'C', false, '');
        $hoja->Cell(30, 8, $a->precio * $a->cantidad, 1, 1,  'C', false, '');
        $totalimporte += ($a->precio * $a->cantidad);
        $totalproductos += $a->cantidad;
    }

    $hoja->Ln();
    $hoja->Cell(20, 8, '', 1, 0,  'C', false, '');
    $hoja->Cell(80, 8, 'Total productos / Importe: ', 1, 0,  'R', false, '');
    $hoja->Cell(20, 8, $totalproductos, 1, 0,  'C', false, '');
    $hoja->Cell(30, 8, '', 1, 0,  'C', false, '');
    $hoja->Cell(30, 8, $totalimporte, 1, 1,  'C', false, '');

    $hoja->Output();
}


function cancelar()
{
    session_destroy();
    header("location:formulario.php");
}

?>