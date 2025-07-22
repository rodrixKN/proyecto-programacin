<?php
session_start();

// Simulamos usuarios para ejemplo (en la vida real usarías base de datos)
$usuarios = [
    "cliente1" => ["password" => "pass123", "rol" => "cliente"],
    "proveedor1" => ["password" => "pass123", "rol" => "proveedor"]
];

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST["username"] ?? "";
    $pass = $_POST["password"] ?? "";

    if (isset($usuarios[$user]) && $usuarios[$user]["password"] === $pass) {
        // Login OK
        $_SESSION["usuario"] = $user;
        $_SESSION["rol"] = $usuarios[$user]["rol"];
        header("Location: servicios.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Login - Esencia Binaria</title>
<style>
body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 50px; }
form { background: white; padding: 20px; border-radius: 8px; max-width: 320px; margin: auto; box-shadow: 0 0 8px #ccc; }
input { width: 100%; padding: 8px; margin: 10px 0; border-radius: 5px; border: 1px solid #aaa; }
button { width: 100%; padding: 10px; background: #000; color: white; font-weight: bold; border: none; border-radius: 5px; cursor: pointer; }
.error { color: red; font-weight: bold; }
</style>
</head>
<body>

<h2 style="text-align:center;">Login a Esencia Binaria</h2>

<form method="POST" action="">
    <label>Usuario:</label>
    <input type="text" name="username" required autofocus />
    <label>Contraseña:</label>
    <input type="password" name="password" required />
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <button type="submit">Ingresar</button>
</form>

</body>
</html>
