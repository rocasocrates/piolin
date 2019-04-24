<?php
  function conectar()
  {
    $con = pg_connect("host=localhost user=piolin password=piolin
                       dbname=piolin");
    return $con;
  }

  function encabezado($top = false)
  {
    $pre = ($top) ? '' : '../';
    
    if (!isset($_SESSION['usuario'])): ?>
      <p>No te dejo entrar</p><?php
      header("Refresh: 2;url={$pre}usuarios/login.php");
      die();
    endif;
    
    $usuario = $_SESSION['usuario'];
    $usuario_id = $_SESSION['usuario_id']; ?>
    
    <h2 style="float:left"><a href="../timeline/index.php">Piolín</a></h2>
    
    <div style="float:right position:relative">
      <form action="<?= $pre ?>usuarios/logout.php" method="post">
        <p align="right">Usuario:
        <a href="../timeline/index.php?usuario=<?= $usuario ?>">
          <?= $usuario ?></a>
        <input type="submit" value="Cerrar" />
        </p>
      </form><?php
      
      $con = conectar();
      $res = pg_query($con, "select max(sigues_a) as sigues_a,
                                    max(seguidores) as seguidores
                               from (select count(*) as sigues_a,
                                            null as seguidores
                                       from relaciones
                                      where seguidor_id = $usuario_id
                                      union
                                     select null, count(*)
                                       from relaciones
                                      where seguido_id = $usuario_id) t");
      $fila = pg_fetch_array($res, 0);
      $sigues_a = $fila['sigues_a'];
      $seguidores = $fila['seguidores']; ?>
      <p align="right">
        Sigues a: <a href="../timeline/sigues_a.php"><?= $sigues_a ?></a> |
        Seguidores: <a href="../timeline/seguidores.php"><?= $seguidores ?></a>
      </p>
    </div>
    <hr/><?php
    pg_close($con);
  }
?>
