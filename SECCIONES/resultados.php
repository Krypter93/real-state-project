<?php
include("../BD/bd.php");

//Conexión a base de datos
$pdo = conectarBD();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Conversión de datos recibidos por post antes de enviarlos en las consultas a la Base de Datos
    $precio = isset($_POST["precio"]) ? floatval($_POST["precio"]) : "";
    $habitaciones = isset($_POST["habitaciones"]) ? intval($_POST["habitaciones"]) : "";
    $fecha = isset($_POST["fecha_construccion"]) ? intval($_POST["fecha_construccion"]) : "";

    //En caso que no se seleccione nada en el formulario
    if ($precio == null && $habitaciones == null && $fecha == null) {
        header("Location: ./sindatos.php");
    }

    try {
        //Consulta a BD
        //Obtener todos los datos que cumplan al menos una de las opciones seleccionadas en el formulario enviado a este archivo
        $sql = "SELECT * FROM `Viviendas` JOIN `Propietarios` ON `Propietarios`.id = `Viviendas`.propietario_id WHERE precio = :pr OR habitaciones = :hab OR fecha_construccion = :fc";
        $query = $pdo->prepare($sql);
        $query->bindParam(":pr", $precio, PDO::PARAM_STR);
        $query->bindParam(":hab", $habitaciones, PDO::PARAM_INT);
        $query->bindParam(":fc", $fecha, PDO::PARAM_INT);
        $query->execute();
        $resultados = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        header("Location:./error_resultados.php");
    } finally {
        $pdo = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Resultados</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Favicon-->
    <link rel="shortcut icon" href="../Img/house.png" type="image/x-icon">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <!-- CSS Local-->
    <link rel="stylesheet" href="../CSS/resultados.css">

    <!--Enlace a Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header>
        <h1>Resultados de búsqueda <i class="fa-solid fa-square-poll-horizontal"></i></h1>
    </header>

    <!-- Mostrando datos de la BD -->
    <div class="info">
        <?php foreach ($resultados as $valor) { ?>
            <div class="info-card">

                <img src="<?php echo $valor["foto"]; ?>" alt="imagen bd">

                <p>Precio: <strong>$ <?php echo number_format($valor["precio"], 0); ?></strong> </p>

                <p>Habitaciones: <strong> <?php echo $valor["habitaciones"]; ?> </strong> </p>

                <p>Metros cuadrados: <strong> <?php echo $valor["metros_cuadrados"]; ?> m² </strong> </p>

                <p>Amueblado: <strong> <?php echo $valor["amueblado"]; ?> </strong> </p>

                <p>Fecha de construcción: <strong> <?php echo $valor["fecha_construccion"]; ?> </strong> </p>

                <p>Propietario: <strong> <?php echo $valor["nombre"] . " " . $valor["apellidos"]; ?> </strong> </p>

                <p>Email: <strong> <?php echo $valor["email"]; ?> </strong> </p>

                <p>Teléfono: <strong> <?php echo $valor["telefono"]; ?> </strong> </p>

            </div>
        <?php } ?>
    </div>

    <!-- Botón de regreso al formulario -->
    <div class="btn">
        <a href="./filtro.php">
            <button>Volver al formulario</button>
        </a>

    </div>

    <footer>
        <div class="copy">
            Todos los derechos reservados <i class="fa-solid fa-copyright"></i>
        </div>

        <!-- Enlaces a página de bienvenida y redes sociales -->
        <div class="social">

            <i class="fa-solid fa-house index"></i>

            <i class="fa-brands fa-facebook face"></i></i>

            <i class="fa-brands fa-square-x-twitter xt"></i>

            <i class="fa-brands fa-instagram inst"></i>

            <i class="fa-brands fa-tiktok tk"></i>

            <i class="fa-brands fa-youtube yt"></i>

        </div>
    </footer>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

    <!-- Enlace Javascript Local index js -->
    <script src="../JS/index.js"></script>
</body>

</html>