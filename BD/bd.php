<?php

//Conexión a BD
function conectarBD()
{
    //Info de BD
    $host = "localhost";
    $dbname = "Inmobiliaria";
    $usuario = "dbmanager";
    $password = "dbpass123*";
    $pdo = null;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $usuario, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        header("Location: ../SECCIONES/error_bd.php");
    }
}