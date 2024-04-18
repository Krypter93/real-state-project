<?php
include("../BD/bd.php");

//Conexión a base de datos
$pdo = conectarBD();
$query = null;

try {
    if ($pdo) {
        //Solicitar precios
        $queryP = $pdo->prepare("SELECT DISTINCT `precio` FROM `Viviendas` ORDER BY `precio` ");
        $queryP->execute();
        $datosP = $queryP->fetchAll(PDO::FETCH_ASSOC);

        //Solicitar cantidad de habitaciones
        $queryH = $pdo->prepare("SELECT DISTINCT `habitaciones` FROM `Viviendas` ORDER BY `habitaciones` ");
        $queryH->execute();
        $datosH = $queryH->fetchAll(PDO::FETCH_ASSOC);

        //Solicitar fecha de construcción
        $queryF = $pdo->prepare("SELECT DISTINCT `fecha_construccion` FROM `Viviendas` ORDER BY `fecha_construccion` ");
        $queryF->execute();
        $datosF = $queryF->fetchAll(PDO::FETCH_ASSOC);
    } else {
        throw new PDOException("Error");
    }
} catch (PDOException $e) {
    header("Location: ./error_bd.php");
} finally {
    $pdo = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Formulario de Búsqueda</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Favicon-->
    <link rel="shortcut icon" href="../Img/house.png" type="image/x-icon">

    <!--CSS Local-->
    <link rel="stylesheet" href="../CSS/filtro.css">

    <!--Enlace a Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body class="filtro">
    <div class="container text-center mt-5 bg-light rounded pref" style="width:85%;">
        <div class="row justify-content-center">
            <h1 class="titulo mt-2">Seleccione sus preferencias <i class="fa-solid fa-filter"></i></h1>
            <form action="resultados.php" method="post" autocomplete="off">
                <div class="row justify-content-center">
                    <div class="col-3">
                        <!-- Insertar todos los datos de precio, habitaciones y fecha de construcción, disponibles en la inmobiliaria, para que el usuario pueda seleccionar sus preferencias, en estos tres parámetros. -->

                        <!-- Precio -->
                        <div class="input-group input-group-lg m-4 selects">
                            <select class="form-select precio" name="precio">
                                <option selected value="">Precio del inmueble</option>
                                <?php foreach ($datosP as $valor) { ?>
                                    <option value="<?php echo $valor["precio"]; ?>"> <?php echo number_format($valor["precio"], 0); ?> </option>
                                <?php } ?>
                            </select>

                        </div>
                    </div>

                    <!-- Cantidad de habitaciones -->
                    <div class="col-3">
                        <div class="input-group input-group-lg m-4">
                            <select class="form-select" name="habitaciones">
                                <option selected value="">Cantidad de habitaciones</option>
                                <?php foreach ($datosH as $valor) { ?>
                                    <option value="<?php echo $valor["habitaciones"]; ?>"><?php echo $valor["habitaciones"]; ?></option>
                                <?php  } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Fecha de construcción -->
                    <div class="col-3">
                        <div class="input-group input-group-lg m-4">
                            <select class="form-select" name="fecha_construccion">
                                <option selected value="">Fecha de construcción</option>
                                <?php foreach ($datosF as $valor) { ?>
                                    <option value="<?php echo $valor["fecha_construccion"]; ?>"><?php echo $valor["fecha_construccion"]; ?></option>
                                <?php  } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <button class="mt-3 p-2 rounded bt fs-5 fw-semibold" id="btn" type="submit"> Buscar <i class="fa-solid fa-magnifying-glass"></i></button>

        </div>
        </form>
    </div>

    <!-- Contenedor para mensajes en bucle -->
    <div class="texto"></div>

    <footer>
        <div class="copy">
            Todos los derechos reservados <i class="fa-solid fa-copyright"></i>
        </div>

        <!-- Enlaces a página de bienvenida y a redes sociales -->
        <div>
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

    <!-- Enlace a script javascript local filtro js -->
    <script src="../JS/filtro.js"></script>

    <!-- Enlace script javascript local index -->
    <script src="../JS/index.js"></script>
</body>

</html>