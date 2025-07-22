<?php
$servicios = [
    [
        "titulo" => "Diseño de Páginas Web",
        "descripcion" => "Landing pages modernas, responsivas y optimizadas. HTML, CSS, JS y Bootstrap.",
        "categoria" => "Diseño Web",
        "contacto" => "webmaster@esenciabinaria.com"
    ],
    [
        "titulo" => "Soporte Técnico a Domicilio",
        "descripcion" => "Instalación de sistemas, limpieza de virus, formateo, recuperación de datos.",
        "categoria" => "Soporte Técnico",
        "contacto" => "soporte@esenciabinaria.com"
    ],
    [
        "titulo" => "Mantenimiento de PCs",
        "descripcion" => "Limpieza interna, cambio de pasta térmica, mejora de rendimiento general.",
        "categoria" => "Mantenimiento",
        "contacto" => "mantenimiento@esenciabinaria.com"
    ],
    [
        "titulo" => "Edición de Video y Audio",
        "descripcion" => "Ediciones para redes sociales, YouTube, eventos y más. Premiere, Audacity, etc.",
        "categoria" => "Multimedia",
        "contacto" => "edicion@esenciabinaria.com"
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Esencia Binaria - Servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://img.pikbest.com/wp/202345/computer-technology-abstract-background-with-green-lights-and-shaped-lines_9590611.jpg!w700wp');
            background-size: cover;
            background-position: center;
            color: white;
        }
        .service-card {
            background-color: rgba(0, 0, 0, 0.75);
            border: 1px solid #4caf50;
            border-radius: 15px;
            transition: transform 0.2s;
        }
        .service-card:hover {
            transform: scale(1.03);
        }
        .navbar {
            background-color: #111;
        }
        .logo {
            width: 50px;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="logo.png" alt="Logo" class="logo">
            <span>Esencia Binaria</span>
        </a>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center mb-4">Servicios y trabajos publicados</h1>
    
    <div class="row g-4">
        <?php foreach ($servicios as $s): ?>
            <div class="col-md-6 col-lg-4">
                <div class="p-3 service-card">
                    <h4><?= htmlspecialchars($s["titulo"]) ?></h4>
                    <p><?= htmlspecialchars($s["descripcion"]) ?></p>
                    <p><strong>Categoría:</strong> <?= htmlspecialchars($s["categoria"]) ?></p>
                    <p><strong>Contacto:</strong> <?= htmlspecialchars($s["contacto"]) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
