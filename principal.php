<?php
// Variables iniciales
$perfil = null;
$mensaje = "";
$fotoError = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = htmlspecialchars(trim($_POST["nombre"] ?? ""));
    $email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $telefono = htmlspecialchars(trim($_POST["telefono"] ?? ""));
    $descripcion = htmlspecialchars(trim($_POST["descripcion"] ?? ""));
    $intereses = htmlspecialchars(trim($_POST["intereses"] ?? ""));
    $estudios = htmlspecialchars(trim($_POST["estudios"] ?? ""));
    $trabajos_anteriores = htmlspecialchars(trim($_POST["trabajos_anteriores"] ?? ""));
    $trabajos_buscados = htmlspecialchars(trim($_POST["trabajos_buscados"] ?? ""));

    if ($nombre === "" || $email === "") {
        $mensaje = "Por favor completa todos los campos obligatorios (*).";
    } else {
        $fotoUrl = null;
        if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES["foto"]["type"], $allowedTypes)) {
                $uploadDir = __DIR__ . "/uploads/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
                $nombreArchivo = "foto_" . time() . "." . $ext;
                $destino = $uploadDir . $nombreArchivo;

                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $destino)) {
                    $fotoUrl = "uploads/" . $nombreArchivo;
                } else {
                    $fotoError = "Error al subir la foto.";
                }
            } else {
                $fotoError = "Formato de imagen no permitido. Solo JPG, PNG o GIF.";
            }
        }
        $perfil = [
            "nombre" => $nombre,
            "email" => $email,
            "telefono" => $telefono,
            "descripcion" => $descripcion,
            "intereses" => $intereses,
            "estudios" => $estudios,
            "trabajos_anteriores" => $trabajos_anteriores,
            "trabajos_buscados" => $trabajos_buscados,
            "foto" => $fotoUrl
        ];
        $mensaje = "Perfil creado correctamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Principal - Esencia Binaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #121212;
            color: #fff;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        /* Barra simple */
        nav {
            background:#222;
            padding:10px;
            text-align:center;
            border-radius:10px;
            margin-bottom: 25px;
            width: 100%;
            box-sizing: border-box;
        }
        nav a {
            color:#4caf50;
            margin: 0 15px;
            font-weight:700;
            text-decoration:none;
            font-size:1.1rem;
        }
        nav a:hover {
            text-decoration: underline;
        }

        .container-left {
            background-color: rgba(0,0,0,0.85);
            border-radius: 15px;
            padding: 30px;
            width: 600px;
            max-width: 100%;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
        }
        .container-right {
            background-color: rgba(0,0,0,0.85);
            border-radius: 15px;
            padding: 30px;
            width: 350px;
            max-width: 100%;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
        }
        .form-label.required::after {
            content:" *";
            color: #dc3545;
        }
        .foto-preview {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #4caf50;
            margin-bottom: 15px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        h2, h3, h4 {
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 25px;
        }
        .section-title {
            border-bottom: 2px solid #4caf50;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: 600;
            color: #4caf50;
        }
        p {
            white-space: pre-wrap;
            font-size: 0.95rem;
        }
        .btn-secondary, .btn-primary {
            width: 100%;
            margin-top: 20px;
        }
        textarea {
            resize: vertical;
            min-height: 60px;
        }
        /* Bosquejo proveedores */
        .bosquejo-proveedores {
            background-color: rgba(76, 175, 80, 0.1);
            padding: 15px;
            border-radius: 10px;
            color: #204020;
            font-size: 0.95rem;
        }
        /* Sección info Esencia Binaria */
        .info-esencia {
            background-color: rgba(58, 58, 58, 0.9);
            border-radius: 15px;
            padding: 20px;
            width: 100%;
            max-width: 980px;
            box-shadow: 0 0 15px rgba(0,0,0,0.7);
            margin-bottom: 30px;
            text-align: center;
        }
        .info-esencia h1 {
            color: #4caf50;
            margin-bottom: 15px;
        }
        .info-esencia p {
            font-size: 1.1rem;
            color: #ddd;
        }
    </style>
</head>
<body>

<nav>
    <a href="registro.php">Crear cuenta</a>
    <a href="login.php">Iniciar sesión</a>
</nav>

<div class="info-esencia">
    <h1>Esencia Binaria</h1>
    <p>Conectamos talentos y oportunidades en informática, soporte técnico y desarrollo web.<br>
    Contáctanos en: <a href="mailto:contacto@esenciabinaria.com" style="color:#4caf50;">contacto@esenciabinaria.com</a></p>
</div>

<div class="container-left">
    <h2>Crear / Editar Perfil</h2>

    <?php if ($mensaje): ?>
        <div class="alert <?= $perfil ? 'alert-success' : 'alert-danger' ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if ($fotoError): ?>
        <div class="alert alert-warning"><?= $fotoError ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="text-white">
        <div class="mb-3">
            <label for="nombre" class="form-label required">Nombre completo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required value="<?= $_POST['nombre'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label required">Email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?= $_POST['email'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= $_POST['telefono'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción personal</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= $_POST['descripcion'] ?? '' ?></textarea>
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Foto de perfil (JPG, PNG, GIF)</label>
            <input class="form-control" type="file" id="foto" name="foto" accept="image/*">
        </div>

        <div class="section-title">Intereses</div>
        <div class="mb-3">
            <textarea class="form-control" id="intereses" name="intereses" placeholder="Ejemplo: Desarrollo web, redes, seguridad informática"><?= $_POST['intereses'] ?? '' ?></textarea>
        </div>

        <div class="section-title">Estudios realizados</div>
        <div class="mb-3">
            <textarea class="form-control" id="estudios" name="estudios" placeholder="Ejemplo: Técnico en informática, ingeniería de sistemas"><?= $_POST['estudios'] ?? '' ?></textarea>
        </div>

        <div class="section-title">Trabajos anteriores</div>
        <div class="mb-3">
            <textarea class="form-control" id="trabajos_anteriores" name="trabajos_anteriores" placeholder="Ejemplo: Soporte técnico, desarrollador freelance"><?= $_POST['trabajos_anteriores'] ?? '' ?></textarea>
        </div>

        <div class="section-title">Trabajos o servicios que buscas</div>
        <div class="mb-3">
            <textarea class="form-control" id="trabajos_buscados" name="trabajos_buscados" placeholder="Ejemplo: Soporte remoto, desarrollo de apps, asesorías"><?= $_POST['trabajos_buscados'] ?? '' ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">Guardar Perfil</button>
    </form>
</div>

<div class="container-right">
    <h3>Perfil guardado</h3>

    <?php if ($perfil): ?>
        <div class="text-center">
            <?php if ($perfil['foto']): ?>
                <img src="<?= htmlspecialchars($perfil['foto']) ?>" alt="Foto perfil" class="foto-preview mb-3" />
            <?php else: ?>
                <div class="foto-preview bg-secondary d-flex justify-content-center align-items-center mx-auto mb-3">Sin foto</div>
            <?php endif; ?>
        </div>

        <h4 class="text-center mb-2"><?= $perfil['nombre'] ?></h4>

        <hr class="bg-success">

        <p><strong>Email:</strong> <?= $perfil['email'] ?></p>
        <?php if ($perfil['telefono']): ?>
            <p><strong>Teléfono:</strong> <?= $perfil['telefono'] ?></p>
        <?php endif; ?>
        <?php if ($perfil['descripcion']): ?>
            <p><strong>Descripción:</strong><br> <?= nl2br($perfil['descripcion']) ?></p>
        <?php endif; ?>

        <?php if ($perfil['intereses']): ?>
            <p><strong>Intereses:</strong><br> <?= nl2br($perfil['intereses']) ?></p>
        <?php endif; ?>

        <?php if ($perfil['estudios']): ?>
            <p><strong>Estudios realizados:</strong><br> <?= nl2br($perfil['estudios']) ?></p>
        <?php endif; ?>

        <?php if ($perfil['trabajos_anteriores']): ?>
            <p><strong>Trabajos anteriores:</strong><br> <?= nl2br($perfil['trabajos_anteriores']) ?></p>
        <?php endif; ?>

        <?php if ($perfil['trabajos_buscados']): ?>
            <p><strong>Trabajos o servicios que buscas:</strong><br> <?= nl2br($perfil['trabajos_buscados']) ?></p>
        <?php endif; ?>

        <!-- BOSQUEJO PARA PROVEEDORES -->
        <hr style="border-color:#4caf50; margin-top:30px;">
        <h4 class="text-center text-success">Vista para Proveedores</h4>
        <div class="bosquejo-proveedores">
            <div class="text-center mb-3">
                <?php if ($perfil['foto']): ?>
                    <img src="<?= htmlspecialchars($perfil['foto']) ?>" alt="Foto perfil" class="foto-preview" style="border-color:#204020;" />
                <?php else: ?>
                    <div class="foto-preview bg-secondary d-flex justify-content-center align-items-center mx-auto mb-3" style="color:#204020;">Sin foto</div>
                <?php endif; ?>
            </div>
            <p><strong>Nombre:</strong> <?= $perfil['nombre'] ?></p>
            <p><strong>Intereses:</strong><br> <?= nl2br($perfil['intereses']) ?></p>
            <p><strong>Servicios que busca:</strong><br> <?= nl2br($perfil['trabajos_buscados']) ?></p>
            <p><strong>Contacto:</strong> <?= $perfil['email'] ?></p>
        </div>

    <?php else: ?>
        <p>No hay perfil creado aún.</p>
    <?php endif; ?>
</div>

</body>
</html>
