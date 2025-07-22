<?php
session_start();

// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

// Datos de conexión MySQL
$host = "localhost";
$user = "root";
$pass = "scorpion"; // Cambia según tu configuración
$db   = "proyecto";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

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

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Esencia Binaria - Cliente</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
/* Igual CSS que en servicios.php para mantener la misma apariencia */
body {
  background-color: #f0f0f0;
  font-family: Arial, Helvetica, sans-serif;
  padding-top: 70px;
}
.navbar {
  background-color: #212529;
  box-shadow: 0 2px 5px rgba(0,0,0,0.5);
}
.navbar a {
  color: #fff;
  font-weight: 700;
  text-decoration: none;
}
.container-main {
  max-width: 1100px;
  margin: auto;
  background: #fff;
  padding: 20px 30px 40px;
  border-radius: 10px;
  box-shadow: 0 0 12px rgb(0 0 0 / 0.15);
}
h1 {
  font-family: 'Orbitron', monospace;
  font-weight: 700;
  color: #212529;
  text-align: center;
  margin-bottom: 20px;
}
.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.filters select, .filters input {
  flex: 1 1 200px;
  padding: 6px 10px;
  font-size: 0.9rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}
.card-servicio {
  border-radius: 12px;
  box-shadow: 0 0 8px rgb(0 0 0 / 0.1);
  margin-bottom: 25px;
  background: #fff;
  overflow: hidden;
  transition: box-shadow 0.3s ease;
  display: flex;
  gap: 20px;
}
.card-servicio:hover {
  box-shadow: 0 0 16px #007bffaa;
}
.card-img {
  width: 140px;
  height: 140px;
  object-fit: cover;
  border-radius: 12px;
  flex-shrink: 0;
}
.card-body {
  padding: 15px 0;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}
.card-title {
  font-family: 'Orbitron', monospace;
  font-weight: 700;
  font-size: 1.3rem;
  margin-bottom: 10px;
  color: black;
  text-align: center;
}
.card-desc {
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 12px;
  color: #333;
  flex-grow: 1;
}
.card-info {
  font-size: 0.9rem;
  color: #555;
  margin-bottom: 6px;
}
.btn-group {
  display: flex;
  gap: 10px;
  margin-top: auto;
}
.btn-contactar, .btn-resena, .btn-postular {
  flex: 1;
  font-family: 'Orbitron', monospace;
  font-weight: 700;
  font-size: 0.9rem;
  padding: 8px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.25s ease;
  color: white;
}
.btn-contactar {
  background-color: #007bff;
}
.btn-contactar:hover {
  background-color: #0056b3;
}
.btn-resena {
  background-color: #28a745;
}
.btn-resena:hover {
  background-color: #1e7e34;
}
.btn-postular {
  background-color: #6c757d;
}
.btn-postular:hover {
  background-color: #495057;
}
.reseñas {
  margin-top: 10px;
  border-top: 1px solid #ddd;
  padding-top: 12px;
}
.reseña-item {
  font-size: 0.9rem;
  border-bottom: 1px solid #eee;
  padding: 6px 0;
  color: #444;
}
.estrellas {
  margin-left: 6px;
  font-size: 1.1rem;
}
.estrellas span {
  color: #f0c419;
}
.estrellas .vacía {
  color: #ccc;
}
.no-reseñas {
  font-style: italic;
  color: #888;
  font-size: 0.9rem;
  margin-top: 8px;
}
.hidden {
  display: none;
}
form .stars input {
  display: none;
}
form .stars label {
  font-size: 1.6rem;
  color: #aaa;
  cursor: pointer;
  transition: color 0.25s ease;
}
form .stars input:checked ~ label,
form .stars label:hover,
form .stars label:hover ~ label {
  color: #f0c419;
}
form textarea {
  width: 100%;
  margin-top: 8px;
  border: 1px solid #000;
  border-radius: 6px;
  padding: 8px;
  resize: vertical;
  font-family: Arial, sans-serif;
}
form button[type="submit"] {
  margin-top: 8px;
  padding: 8px 12px;
  font-weight: 700;
  font-family: 'Orbitron', monospace;
  background: #000;
  color: #fff;
  border: 2px solid #000;
  border-radius: 6px;
  cursor: pointer;
}
form button[type="submit"]:hover {
  background: #fff;
  color: #000;
}
</style>
<script>
function filtrarServicios() {
  const cat = document.getElementById('filtro-categoria').value.toLowerCase();
  const ubi = document.getElementById('filtro-ubicacion').value.toLowerCase();
  const palabra = document.getElementById('filtro-palabra').value.toLowerCase();

  const cards = document.querySelectorAll('.card-servicio');
  cards.forEach(card => {
    const categoria = card.getAttribute('data-categoria').toLowerCase();
    const ubicacion = card.getAttribute('data-ubicacion').toLowerCase();
    const texto = card.innerText.toLowerCase();

    let mostrar = true;

    if (cat && cat !== 'todas' && categoria !== cat) mostrar = false;
    if (ubi && ubi !== 'todas' && ubicacion !== ubi) mostrar = false;
    if (palabra && !texto.includes(palabra)) mostrar = false;

    card.style.display = mostrar ? '' : 'none';
  });
}

function toggleForm(id) {
  const form = document.getElementById('resena-' + id);
  if (form.classList.contains('hidden')) {
    form.classList.remove('hidden');
  } else {
    form.classList.add('hidden');
  }
}

function enviarReseña(id) {
  const estrellas = document.querySelector('input[name="estrella-'+id+'"]:checked');
  const comentario = document.getElementById('comentario-'+id).value.trim();
  if(!estrellas || !comentario){
    alert('Por favor, selecciona una calificación y escribe tu reseña.');
    return false;
  }
  alert('Gracias por tu reseña para el servicio #' + id + '.');
  // Aquí agregarías la lógica para guardar la reseña en base de datos
  document.getElementById('resena-'+id).classList.add('hidden');
  document.getElementById('comentario-'+id).value = '';
  const estrellasInput = document.querySelectorAll('input[name="estrella-'+id+'"]');
  estrellasInput.forEach(e => e.checked = false);
  return false;
}

function abrirChat(titulo) {
  alert('Se abriría chat o contacto directo con ' + titulo);
}
</script>
</head>
<body>
<nav class="navbar fixed-top d-flex justify-content-between px-4 py-2 align-items-center">
  <div style="font-weight:bold; font-family: 'Orbitron', monospace; color:#fff; font-size:1.2rem;">
    Esencia Binaria - Cliente
  </div>
  <a href="logout.php" style="color:#fff; font-weight:700; text-decoration:none;">Cerrar sesión</a>
</nav>

<div class="container-main">

  <h1>Servicios y Oficios Disponibles</h1>

  <p style="font-weight:600; font-size:0.95rem; text-align:center; margin-bottom: 25px;">
    Filtra los servicios para encontrar el que mejor se adapte a tus necesidades.
  </p>

  <div class="filters">
    <select id="filtro-categoria" onchange="filtrarServicios()">
      <option value="todas">Todas las categorías</option>
      <?php foreach ($categorias as $c): ?>
        <option value="<?= strtolower($c) ?>"><?= htmlspecialchars($c) ?></option>
      <?php endforeach; ?>
    </select>

    <select id="filtro-ubicacion" onchange="filtrarServicios()">
      <option value="todas">Todas las ubicaciones</option>
      <?php foreach ($ubicaciones as $u): ?>
        <option value="<?= strtolower($u) ?>"><?= htmlspecialchars($u) ?></option>
      <?php endforeach; ?>
    </select>

    <input type="text" id="filtro-palabra" placeholder="Buscar por palabra clave..." oninput="filtrarServicios()" />
  </div>

  <?php
  if ($result_servicios && $result_servicios->num_rows > 0):
      $result_servicios->data_seek(0);
      while ($servicio = $result_servicios->fetch_assoc()):
          $id = $servicio['id'];
          $resenias_serv = $reseñas[$id] ?? [];
          $total = count($resenias_serv);
          $sum = 0;
          foreach ($resenias_serv as $r) {
              $sum += $r['estrellas'];
          }
          $promedio = $total > 0 ? round($sum / $total, 1) : 0;
  ?>
    <div
      class="card-servicio"
      data-categoria="<?= strtolower($servicio['categoria']) ?>"
      data-ubicacion="<?= strtolower($servicio['ubicacion']) ?>"
    >
      <img src="<?= htmlspecialchars($servicio['imagen']) ?>" alt="Imagen <?= htmlspecialchars($servicio['titulo']) ?>" class="card-img" />
      <div class="card-body">
        <div class="card-title"><?= htmlspecialchars($servicio['titulo']) ?></div>
        <div class="card-desc"><?= htmlspecialchars($servicio['descripcion']) ?></div>
        <div class="card-info"><strong>Categoría:</strong> <?= htmlspecialchars($servicio['categoria']) ?></div>
        <div class="card-info"><strong>Ubicación:</strong> <?= htmlspecialchars($servicio['ubicacion']) ?></div>
        <div class="card-info"><strong>Contacto:</strong> <a href="mailto:<?= htmlspecialchars($servicio['contacto']) ?>"><?= htmlspecialchars($servicio['contacto']) ?></a></div>
        <div class="card-info"><strong>Calificación:</strong>
          <?= $promedio ?> / 5
          <span class="estrellas">
            <?php
              $estrellas_llenas = intval($promedio);
              $estrellas_vacias = 5 - $estrellas_llenas;
              echo str_repeat('<span>★</span>', $estrellas_llenas);
              echo str_repeat('<span class="vacía">★</span>', $estrellas_vacias);
            ?>
          </span>
          (<?= $total ?> reseñas)
        </div>

        <div class="btn-group">
          <button class="btn-postular" onclick="alert('Te postularías para el servicio <?= addslashes(htmlspecialchars($servicio['titulo'])) ?>')">Postularse</button>
          <button class="btn-resena" onclick="toggleForm(<?= $id ?>)">Dejar Reseña</button>
          <button class="btn-contactar" onclick="abrirChat('<?= addslashes(htmlspecialchars($servicio['titulo'])) ?>')">Abrir Chat</button>
        </div>

        <form id="resena-<?= $id ?>" class="hidden" onsubmit="return enviarReseña(<?= $id ?>);">
          <div style="margin-top: 10px;">
            <strong>Tu calificación:</strong><br/>
            <div class="stars" style="direction: rtl; display: inline-block;">
              <?php
              for ($i=5; $i>=1; $i--) {
                echo '<input type="radio" id="star'.$i.'-'.$id.'" name="estrella-'.$id.'" value="'.$i.'">';
                echo '<label for="star'.$i.'-'.$id.'">★</label>';
              }
              ?>
            </div>
          </div>
          <textarea id="comentario-<?= $id ?>" rows="3" placeholder="Escribe tu reseña aquí..."></textarea>
          <button type="submit">Enviar Reseña</button>
        </form>

        <div class="reseñas">
          <strong>Reseñas:</strong>
          <?php if ($total > 0): ?>
            <?php foreach ($resenias_serv as $r): ?>
              <div class="reseña-item">
                <strong><?= htmlspecialchars($r['usuario']) ?>:</strong> <?= htmlspecialchars($r['comentario']) ?>
                <span class="estrellas">
                  <?php
                    echo str_repeat('<span>★</span>', $r['estrellas']);
                    echo str_repeat('<span class="vacía">★</span>', 5 - $r['estrellas']);
                  ?>
                </span>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="no-reseñas">No hay reseñas para este servicio aún.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php
      endwhile;
  else:
  ?>
  <p>No hay servicios disponibles.</p>
  <?php endif; ?>

</div>
</body>
</html>

<?php
$conn->close();
?>
