<?php
// conexión que configuramos para InfinityFree
include("conexion.php");

// Variable para mostrar mensajes de éxito o error al usuario
$mensaje = "";

// 2. Procesamos el formulario cuando el usuario hace clic en enviar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lugar = mysqli_real_escape_string($conexion, $_POST['lugar']);
    $titulo = mysqli_real_escape_string($conexion, $_POST['titulo']);
    $reporte = mysqli_real_escape_string($conexion, $_POST['reporte']);
    
    // Manejo de la foto subida
    $nombre_foto = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $carpeta_destino = "imagenes_reportes/";
        
        // Si la carpeta no existe en tu hosting, la crea automáticamente
        if (!file_exists($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }
        
        // Creamos un nombre único para la foto para que no se sobrescriban
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_foto = time() . "_" . uniqid() . "." . $extension;
        $ruta_final = $carpeta_destino . $nombre_foto;
        
        // Movemos el archivo temporal a la carpeta definitiva en el servidor
        move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_final);
    }

    // Insertamos los datos en la tabla (guardamos la ruta/nombre de la foto)
    $consulta = "INSERT INTO comunidad_reportes (lugar, titulo, reporte, foto) VALUES ('$lugar', '$titulo', '$reporte', '$nombre_foto')";
    
    if (mysqli_query($conexion, $consulta)) {
        $mensaje = "<p style='color: #4dfd4d; font-weight: bold;'>¡Reporte enviado con éxito a la comunidad! ❄️</p>";
    } else {
        $mensaje = "<p style='color: #ff4d4d; font-weight: bold;'>Error al enviar el reporte: " . mysqli_error($conexion) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Adáptate al invierno</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <nav>
        <img src="Logo/Logo invi sin fondo con orilla blanca.png" alt="Logo Adáptate al invierno" class="nav-logo-superior">
        
        <div class="nav-links">
            <a href="index.html">Inicio</a> 
            <a href="Salud.html">Salud</a>
            <a href="Hogar.html">Hogar</a>
            <a href="Curiosidades.html">Curiosidades</a>
            <a href="Medio ambiente.html">Medio ambiente</a>
            <a href="Reportes Comunidad.php" style="background: var(--primary); color: white;">Reportes de la Comunidad</a>

            <button class="theme-toggle" id="theme-toggle" title="Cambiar tema">🌙</button>
        </div>
    </nav>

    <header>
        <h1>Reportes de la Comunidad</h1>
        <p>Información sobre eventos y actividades relacionadas con el invierno</p>
    </header>

    <div class="contenedor-reportes">
        
        <div class="card-glass">
            <h2>Crear Reporte Comunitario</h2>
            <?php echo $mensaje; ?>
            
            <form action="reportes.php" method="POST" enctype="multipart/form-data">
                <div class="form-grupo">
                    <label for="lugar">¿Dónde sucede?</label>
                    <input type="text" id="lugar" name="lugar" class="form-control" placeholder="Ej: Calle Principal, Zona Norte" required>
                </div>

                <div class="form-grupo">
                    <label for="titulo">Título del Reporte</label>
                    <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Ej: Acumulación de nieve / Avería de luz" required>
                </div>

                <div class="form-grupo">
                    <label for="reporte">Detalle del Reporte</label>
                    <textarea id="reporte" name="reporte" class="form-control" rows="5" placeholder="Describe la situación con detalle..." required></textarea>
                </div>

                <div class="form-grupo">
                    <label for="foto">Evidencia Fotográfica (Opcional)</label>
                    <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn-enviar">Publicar Reporte</button>
            </form>
        </div>

        <div class="card-glass">
            <h2>Reportes Recientes de la Comunidad</h2>
            
            <?php
            // Traemos los reportes ordenados del más nuevo al más antiguo
            $resultado = mysqli_query($conexion, "SELECT * FROM comunidad_reportes ORDER BY id DESC");
            
            if (mysqli_num_rows($resultado) > 0) {
                while ($fila = mysqli_fetch_assoc($resultado)) {
                    echo "<div class='reporte-item'>";
                    echo "<h3>" . htmlspecialchars($fila['titulo']) . "</h3>";
                    echo "<div class='reporte-meta'>📍 <strong>Lugar:</strong> " . htmlspecialchars($fila['lugar']) . " | 🗓️ " . $fila['fecha'] . "</div>";
                    echo "<p>" . nl2br(htmlspecialchars($fila['reporte'])) . "</p>";
                    
                    // Si el reporte incluye foto, la mostramos en pantalla
                    if (!empty($fila['foto'])) {
                        echo "<img src='imagenes_reportes/" . $fila['foto'] . "' class='reporte-img' alt='Evidencia'>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p>No hay reportes comunitarios registrados en este momento. ¡Sé el primero!</p>";
            }
            ?>
        </div>
    </div>

    <footer>
        <h3>Información adicional</h3>
        <p>© 2026 Adaptate al invierno - Todos los derechos reservados</p>
        <p>Contactos: masinfoadaptartealinvierno@gmail.com</p>
    </footer>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', currentTheme);
        themeToggle.textContent = currentTheme === 'dark' ? '☀️' : '🌙';

        themeToggle.addEventListener('click', () => {
            const theme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            themeToggle.textContent = theme === 'dark' ? '☀️' : '🌙';
        });
    </script>
</body>
</html>