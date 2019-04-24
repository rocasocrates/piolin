<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf8"/>
    <title>Iniciar sesión</title>
  </head>
  <body><?php
    require '../comunes/comunes.php';
    
    $usuario = (isset($_POST['usuario'])) ? trim($_POST['usuario']) : '';
    $password = (isset($_POST['password'])) ? trim($_POST['password']) : '';
    
    if ($usuario != '' && $password != ''):
      $con = conectar();
      $res = pg_query_params($con, "select *
                                    from usuarios
                                    where usuario = $1 and
                                          password = md5($2)",
                             array($usuario, $password));
      $num_rows = pg_num_rows($res);
      pg_close($con);

      if ($num_rows > 0):
        $_SESSION['usuario'] = $usuario;
        $fila = pg_fetch_array($res, 0);
        $_SESSION['usuario_id'] = $fila['id'];
        header("Location: ../timeline/index.php");
      else: ?>
        <p>Usuario incorrecto</p><?php
      endif;
    endif; ?>

    <form action="login.php" method="post">
      <label for="usuario">Usuario:</label>
      <input type="text" name="usuario" /><br/>
      <label for="password">Contraseña:</label>
      <input type="password" name="password" /><br/>
      <input type="submit" value="Iniciar sesión" />
    </form>
  </body>
</html>
