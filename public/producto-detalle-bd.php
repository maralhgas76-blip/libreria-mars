<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/datos/conectar.php';
require_once __DIR__ . '/../src/modelos/Producto.php';
require_once __DIR__ . '/../src/utilidades/vistas.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

try {
    if ($id === false || $id === null || $id <= 0) {
        throw new OutOfBoundsException('El ID del libro no es válido.');
    }

    $consulta = 'SELECT
                    l.id,
                    l.nombre,
                    l.autor,
                    c.nombre AS categoria,
                    l.precio,
                    l.descripcion,
                    l.stock
                 FROM libros l
                 INNER JOIN categorias c
                    ON c.id = l.categoria_id
                 WHERE l.id = :id';

    $conexion = conectar();
    $sentencia = $conexion->prepare($consulta);
    $sentencia->execute(['id' => $id]);

    $registro = $sentencia->fetch();

    if (!$registro) {
        throw new OutOfBoundsException('No se encontró el libro solicitado.');
    }

    $libro = Producto::desdeArreglo($registro);

} catch (OutOfBoundsException $error) {
    http_response_code(404);
    $mensajeError = $error->getMessage();

} catch (Throwable $error) {
    http_response_code(500);
    $mensajeError = 'No fue posible consultar el libro.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librería - Ficha del libro</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <header>
        <h1>Ficha del libro</h1>
    </header>

    <nav aria-label="Principal">
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="productos-bd.php" aria-current="page">Catálogo</a></li>
            <li><a href="contacto.php">Contacto</a></li>
        </ul>
    </nav>

    <main>
        <?php if (isset($mensajeError)): ?>
            <article>
                <h2>Libro no encontrado</h2>
                <p><?= escapar($mensajeError) ?></p>
                <a class="boton" href="productos-bd.php">Regresar al catálogo</a>
            </article>
        <?php else: ?>
            <article>
                <h2><?= escapar($libro->obtenerNombre()) ?></h2>

                <p class="autor">
                    Autor/a: <?= escapar($libro->obtenerAutor()) ?>
                </p>

                <p class="genero">
                    Género:
                    <?= escapar($libro->obtenerCategoria()->obtenerNombre()) ?>
                </p>

                <p class="precio">
                    Precio: <?= escapar($libro->obtenerPrecioFormateado()) ?>
                </p>

                <?= mostrarDisponibilidad($libro) ?>

                <h3>Descripción</h3>

		 <p class="descripcion">
                    <?= escapar($libro->obtenerDescripcion()) ?>
                </p>

                <a class="boton" href="productos-bd.php">
                    ← Regresar al catálogo
                </a>
            </article>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 Librería</p>
    </footer>
</body>
</html>
