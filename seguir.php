<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf8"/>
    <title>Timeline</title>
  </head>
  <body><?php
    require '../comunes/comunes.php';
    
    encabezado();
    
    $con = conectar();
    
    if (isset($_GET['usuario']))
    {
      $usuario = trim($_GET['usuario']);
      if ($usuario != "")
      {
        $res = pg_query($con, "select *
                                 from usuarios
                                where id::text = '$usuario'");

        if (pg_num_rows($res) > 0)
        {
          $fila = pg_fetch_array($res, 0);
          $user_id = $fila['id'];
          $seguir_a = $fila['usuario'];

          if ($user_id != $_SESSION['usuario_id'])
          {
            $res = pg_query($con, "select *
                                     from relaciones
                                    where seguidor_id = {$_SESSION['usuario_id']}
                                          and seguido_id = $user_id");
            if (pg_num_rows($res) > 0): ?>
              <h2>Ya seguías a <?= $seguir_a ?></h2><?php
            else:
              $res = pg_query($con, "insert into relaciones (seguidor_id,
                                                             seguido_id)
                                     values ({$_SESSION['usuario_id']},
                                             $user_id)");
                                             
              if (pg_affected_rows($res) > 0): ?>
                <h2>A partir de ahora, ya sigues a <?= $seguir_a ?></h2><?php
              endif;
            endif;
          }
        }
      }
    }
    pg_close($con); ?>
  </body>
</html>
