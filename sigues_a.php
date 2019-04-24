<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf8"/>
    <title>Sigues a</title>
  </head>
  <body><?php
    require '../comunes/comunes.php';
    encabezado();
    $con = conectar();
    $usuario_id = $_SESSION['usuario_id'];

    if (isset($_GET['usuario']))
    {
      $usuario = trim($_GET['usuario']);
      if ($usuario != "")
      {
        $res = pg_query($con, "select *
                                 from usuarios
                                where usuario = '$usuario'");
        if (pg_num_rows($res) > 0)
        {
          $fila = pg_fetch_array($res, 0);
          $usuario_id = $fila['id'];
        }
      }
    }

    $res = pg_query($con, "select *, usuarios.usuario
                             from relaciones join usuarios
                               on seguido_id = usuarios.id
                            where seguidor_id = $usuario_id");
    
    if (pg_num_rows($res) > 0): ?>
      <h2>Sigues a:</h2>
      <ul><?php
      for ($i = 0; $i < pg_num_rows($res); $i++):
        $fila = pg_fetch_array($res, $i);
        $seguido = $fila['usuario']; ?>
        <li>
          <a href="index.php?usuario=<?= $seguido ?>"><?= $seguido ?></a>
        </li><?php
      endfor; ?>
      </ul><?php
    else: ?>
      <h2>No sigues a ningún contacto</h2><?php
    endif;

    pg_close($con); ?>
  </body>
</html>
