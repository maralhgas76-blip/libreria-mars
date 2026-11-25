<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../src/datos/conectar.php';
require_once __DIR__ . '/../src/modelos/Producto.php';
require_once __DIR__ . '/../src/utilidades/vistas.php';

try {
    $consulta = 'SELECT
                    p.id,
                    p.nombre,
                    p.autor,
                    c.nombre AS categoria,
                    p.precio,
                    p.descripcion,
                    p.stock
                 FROM libros p
                 INNER JOIN categorias c
                    ON c.id = p.categoria_id
                 ORDER BY p.id';

    $registros = conectar()->query($consulta)->fetchAll();

    $productos = array_map(
        fn (array $registro) => Producto::desdeArreglo($registro),
        $registros
    );

} catch (Throwable $error) {
    http_response_code(500);
    $mensajeError = 'No fue posible cargar el catálogo desde MariaDB.';
    $productos = [];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo MariaDB - Librería</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <header>
        <h1>Librería</h1>
        <p>Catálogo de libros desde MariaDB</p>
    </header>

    <nav aria-label="Principal">
        <a href="index.php">Inicio</a>
        <a href="productos-bd.php" aria-current="page">Catálogo</a>
        <a href="contacto.php">Contacto</a>
    </nav>

    <main>
        <h2>Nuestros libros</h2>

        <?php if (isset($mensajeError)): ?>
            <div class="error">
                <?= escapar($mensajeError) ?>
            </div>
        <?php endif; ?>

        <section class="catalogo">
            <?php if (!empty($productos)): ?>

                <?php foreach ($productos as $producto): ?>
                    <?php mostrarTarjetaProducto($producto, 'producto-detalle-bd.php'); ?>
                <?php endforeach; ?>

            <?php elseif (!isset($mensajeError)): ?>
                <article>
                    <h3>No hay libros para mostrar</h3>
                    <p>Revisa los registros de la base de datos.</p>
                </article>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Librería</p>
    </footer>
</body>
</html>
