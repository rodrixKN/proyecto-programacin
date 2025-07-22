<?php
session_start();

// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php'; // conexión a la base de datos

// Obtener servicios
$sql_servicios = "SELECT * FROM servicios ORDER BY titulo";
$result_servicios = $conn->query($sql_servicios);

// Obtener reseñas
$sql_resenas = "SELECT * FROM resenas";
$result_resenas = $conn->query($sql_resenas);

// Organizar reseñas por servicio
$reseñas = [];
if ($result_resenas && $result_resenas->num_rows > 0) {
    while ($row = $result_resenas->fetch_assoc()) {
        $id_serv = $row['servicio_id'];
        if (!isset($reseñas[$id_serv])) {
            $reseñas[$id_serv] = [];
        }
        $reseñas[$id_serv][] = $row;
    }
}

// Extraer categorías y ubicaciones para filtros
$categorias = [];
$ubicaciones = [];
if ($result_servicios && $result_servicios->num_rows > 0) {
    $result_servicios->data_seek(0);
    while ($s = $result_servicios->fetch_assoc()) {
        if (!in_array($s['categoria'], $categorias)) {
            $categorias[] = $s['categoria'];
        }
        if (!in_array($s['ubicacion'], $ubicaciones)) {
            $ubicaciones[] = $s['ubicacion'];
        }
    }
    sort($categorias);
    sort($ubicaciones);
}
?>
