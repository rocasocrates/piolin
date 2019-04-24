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
    $user_id = $_SESSION['usuario_id'];
    $where = "usuarios_id in (select seguido_id
                                from relaciones
                               where seguidor_id = $user_id)";

    if (isset($_POST['texto']))
    {
      $texto = trim($_POST['texto']);
      if ($texto != "")
      {
        $res = pg_query_params($con, "insert into tuits (texto, usuarios_id)
                               values(substring($1 from 1 for 140),
                                      $user_id)", array($texto));
        $usuario = $_SESSION['usuario'];
        goto timeline_usuario;
      }
    }
    else if (isset($_GET['usuario']))
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
          $user_id = $fila['id'];

        timeline_usuario:

          $where = "usuarios_id = $user_id"; ?>
          <p><strong>Timeline de <?= $usuario ?></strong><?php

          if ($user_id != $_SESSION['usuario_id'])
          {
            $res = pg_query($con, "select *
                                     from relaciones
                                    where seguidor_id = {$_SESSION['usuario_id']}
                                          and seguido_id = $user_id");
            if (pg_num_rows($res) > 0)
            { ?>
              <a href="../usuarios/dejar_de_seguir.php?usuario=<?= $user_id ?>">
                (dejar de seguirlo)
              </a><?php
            }
            else
            { ?>
              <a href="../usuarios/seguir.php?usuario=<?= $user_id ?>">
                (seguir)
              </a><?php
            }
          }
          else
          {
            echo " (eres tú)";
          } ?>
          </p><?php
        }
      }
    }
    
    if ($user_id == $_SESSION['usuario_id']): ?>
      <div id="piar">
        <p><form action="index.php" method="post">
          <textarea name="texto" rows="5" cols="40"></textarea><br/>
          <input type="submit" value="Piar" />
        </form></p>
      </div><?php
    endif;
    
    $res = pg_query($con, "select tuits.*, usuario,
                                  to_char(instante,
                                          'DD-MM-YYYY\" a las \"HH24:MI:SS')
                                  as instante_format
                             from tuits join usuarios
                               on usuarios_id = usuarios.id
                            where $where
                         order by instante desc"); ?>

    <div class="tuits"><?php
      for ($i = 0; $i < pg_num_rows($res); $i++):
        $fila = pg_fetch_array($res, $i);
        $texto = $fila['texto'];
        $creador = $fila['usuario'];
        $instante = $fila['instante_format']; ?>
        <div class="tuit">
          <div class="creador">
            <em>
              <strong>De:</strong>
                <a href="index.php?usuario=<?= $creador ?>"><?= $creador ?></a>
              el <?= $instante ?>
            </em>
          </div>
          <div class="texto">
            <?= $texto ?>
          </div>
        </div>
        <hr/><?php
      endfor; ?>
    </div>
    <?php pg_close($con); ?>
  </body>
</html>
