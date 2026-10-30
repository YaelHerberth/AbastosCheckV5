<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/icon/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/Bootstrap/bootstrap.min.css">
    <title>Abastos Check</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
      <a class="navbar-brand" href="../index.php"> <img src="../images/LogoH2.svg" alt="AbastosCheck" class="d-inline-block" style="width: 2.5em;"> AbastosCheck</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarScroll">
        <ul class="navbar-nav ms-auto" style="--bs-scroll-height: 100px;">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="../index.php">Inicio</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle active" href="Departamentos.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Departamentos
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="BebésyNiños.php">Bebés y Niños</a></li>
              <li><a class="dropdown-item" href="CarnesyPescados.php">Carnes y Pescados</a></li>
              <li><a class="dropdown-item" href="Despensa.php">Despensa</a></li>
              <li><a class="dropdown-item" href="FrutasyVerduras.php">Frutas y Verduras</a></li>
              <li><a class="dropdown-item" href="JugosyBebidas">Jugos y Bebidas</a></li>
              <li><a class="dropdown-item" href="Limpieza.php">Limpieza</a></li>
              <li><a class="dropdown-item" href="Mascotas.php">Mascotas</a></li>
              <li><a class="dropdown-item" href="PanaderiayTortilleria.php">Panaderia y Tortilleria</a></li>
              <li><a class="dropdown-item" href="Salchichoneria.php">Salchichoneria</a></li> 
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="mostrarCarrito.php">Carrito (<?php 
                echo (empty($_SESSION['CARRITO']))?0:count(($_SESSION['CARRITO']));
            ?>)</a>
          </li>
          <?php
          if(isset($_SESSION['user_id'])){
        $records = $conn->prepare('SELECT id, email, password FROM users WHERE id = :id');
        $records->bindParam(':id', $_SESSION['user_id']);
        $records->execute();
        $results = $records->fetch(PDO::FETCH_ASSOC);

        $user = null;

        if(count($results) > 0){
            $user = $results;
        }
    }
    ?>
    <?php if(!empty($user)):  ?>
         <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle active" href="sesion_iniciada.php" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= $user['email'] ?></a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="Logout.php">Cerrar Sesión</a></li>
            </ul>
         </li> 
    <?php else: ?>
      <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="Login.php">Iniciar Sesion</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="Signup.php">Registrarse</a>
        </li>
    <?php endif ?>
        </ul>
      </div>
    </div>
    </nav>
<div class="container">