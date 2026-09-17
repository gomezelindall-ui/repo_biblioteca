<?php
require_once 'config.php';

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'crear_usuario') {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, cedula, telefono) VALUES (?, ?, ?)");
            $stmt->execute([trim($_POST['nombre']), trim($_POST['cedula']), trim($_POST['telefono'])]);
            $mensaje = 'Usuario registrado correctamente.';
        } elseif ($accion === 'crear_libro') {
            $stmt = $pdo->prepare("INSERT INTO libros (codigo, titulo, autor) VALUES (?, ?, ?)");
            $stmt->execute([trim($_POST['codigo']), trim($_POST['titulo']), trim($_POST['autor'])]);
            $mensaje = 'Libro registrado correctamente.';
        } elseif ($accion === 'crear_prestamo') {
            $cedula = trim($_POST['cedula_usuario']);
            $codigo = trim($_POST['codigo_libro']);

            $u = $pdo->prepare("SELECT nombre FROM usuarios WHERE cedula = ?");
            $u->execute([$cedula]);
            $usuario = $u->fetch();

            $l = $pdo->prepare("SELECT titulo FROM libros WHERE codigo = ?");
            $l->execute([$codigo]);
            $libro = $l->fetch();

            if (!$usuario) {
                throw new Exception('La cédula no corresponde a un usuario registrado.');
            }
            if (!$libro) {
                throw new Exception('El código no corresponde a un libro registrado.');
            }

            $fecha = $_POST['fecha_prestamo'] ?: date('Y-m-d');
            $limite = date('Y-m-d', strtotime($fecha . ' +7 days'));

            $stmt = $pdo->prepare("INSERT INTO prestamos
                (cedula_usuario, nombre_usuario, codigo_libro, fecha_prestamo, fecha_limite)
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$cedula, $usuario['nombre'], $codigo, $fecha, $limite]);
            $mensaje = "Préstamo registrado. Fecha límite: $limite.";
        } elseif ($accion === 'devolver') {
            $stmt = $pdo->prepare("UPDATE prestamos SET estado = 'Devuelto' WHERE id = ?");
            $stmt->execute([(int)$_POST['id']]);
            $mensaje = 'Préstamo marcado como devuelto.';
        } elseif ($accion === 'eliminar_usuario') {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([(int)$_POST['id']]);
            $mensaje = 'Usuario eliminado.';
        } elseif ($accion === 'eliminar_libro') {
            $stmt = $pdo->prepare("DELETE FROM libros WHERE id = ?");
            $stmt->execute([(int)$_POST['id']]);
            $mensaje = 'Libro eliminado.';
        } elseif ($accion === 'eliminar_prestamo') {
            $stmt = $pdo->prepare("DELETE FROM prestamos WHERE id = ?");
            $stmt->execute([(int)$_POST['id']]);
            $mensaje = 'Préstamo eliminado.';
        }
    } catch (Throwable $ex) {
        $error = $ex->getMessage();
    }
}

$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC")->fetchAll();
$libros = $pdo->query("SELECT * FROM libros ORDER BY id DESC")->fetchAll();
$prestamos = $pdo->query("SELECT * FROM prestamos ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VividReading - Biblioteca</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div>
        <h1>VividReading</h1>
        <p>Gestión de usuarios, libros y préstamos</p>
    </div>
</header>

<main>
<?php if ($mensaje): ?><div class="alert success"><?= e($mensaje) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

<section class="cards">
<article class="card"><h3>Usuarios</h3><strong><?= count($usuarios) ?></strong></article>
<article class="card"><h3>Libros</h3><strong><?= count($libros) ?></strong></article>
<article class="card"><h3>Préstamos</h3><strong><?= count($prestamos) ?></strong></article>
</section>

<section class="panel">
<h2>Registrar usuario</h2>
<form method="post" class="form-grid">
<input type="hidden" name="accion" value="crear_usuario">
<label>Nombre<input required name="nombre"></label>
<label>Cédula<input required name="cedula"></label>
<label>Teléfono<input required name="telefono"></label>
<button>Guardar usuario</button>
</form>
</section>

<section class="panel">
<h2>Registrar libro</h2>
<form method="post" class="form-grid">
<input type="hidden" name="accion" value="crear_libro">
<label>Código<input required name="codigo"></label>
<label>Título<input required name="titulo"></label>
<label>Autor<input required name="autor"></label>
<button>Guardar libro</button>
</form>
</section>

<section class="panel">
<h2>Registrar préstamo</h2>
<form method="post" class="form-grid">
<input type="hidden" name="accion" value="crear_prestamo">
<label>Cédula del usuario<input required name="cedula_usuario"></label>
<label>Código del libro<input required name="codigo_libro"></label>
<label>Fecha del préstamo<input type="date" name="fecha_prestamo" value="<?= date('Y-m-d') ?>"></label>
<div class="hint">La fecha límite se calcula automáticamente a 7 días.</div>
<button>Registrar préstamo</button>
</form>
</section>

<section class="panel">
<h2>Usuarios registrados</h2>
<table><tr><th>Nombre</th><th>Cédula</th><th>Teléfono</th><th>Acción</th></tr>
<?php foreach ($usuarios as $u): ?><tr><td><?= e($u['nombre']) ?></td><td><?= e($u['cedula']) ?></td><td><?= e($u['telefono']) ?></td><td><form method="post"><input type="hidden" name="accion" value="eliminar_usuario"><input type="hidden" name="id" value="<?= $u['id'] ?>"><button class="danger">Eliminar</button></form></td></tr><?php endforeach; ?>
</table>
</section>

<section class="panel">
<h2>Libros registrados</h2>
<table><tr><th>Código</th><th>Título</th><th>Autor</th><th>Acción</th></tr>
<?php foreach ($libros as $l): ?><tr><td><?= e($l['codigo']) ?></td><td><?= e($l['titulo']) ?></td><td><?= e($l['autor']) ?></td><td><form method="post"><input type="hidden" name="accion" value="eliminar_libro"><input type="hidden" name="id" value="<?= $l['id'] ?>"><button class="danger">Eliminar</button></form></td></tr><?php endforeach; ?>
</table>
</section>

<section class="panel">
<h2>Préstamos</h2>
<table><tr><th>Usuario</th><th>Cédula</th><th>Libro</th><th>Fecha préstamo</th><th>Fecha límite</th><th>Estado</th><th>Acciones</th></tr>
<?php foreach ($prestamos as $p): ?>
<tr>
<td><?= e($p['nombre_usuario']) ?></td><td><?= e($p['cedula_usuario']) ?></td><td><?= e($p['codigo_libro']) ?></td>
<td><?= e($p['fecha_prestamo']) ?></td><td><?= e($p['fecha_limite']) ?></td><td><?= e($p['estado']) ?></td>
<td>
<?php if ($p['estado'] === 'Prestado'): ?><form method="post" class="inline"><input type="hidden" name="accion" value="devolver"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button>Devolver</button></form><?php endif; ?>
<form method="post" class="inline"><input type="hidden" name="accion" value="eliminar_prestamo"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="danger">Eliminar</button></form>
</td>
</tr>
<?php endforeach; ?>
</table>
</section>
</main>
<footer>VividReading · Biblioteca</footer>
</body>
</html>
