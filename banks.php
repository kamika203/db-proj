<?php 
session_start ();
require_once("conf.php");
require_once("header.php");

if ($_POST['sav'])                            //сохранение изменений
{
    $idb1=$_POST['IDB1'];
    $namB1=get_post($id,'NameP1');
    $q1="update bank set NameB='".$namB1."' where IDB='".$idb1."'";
    //echo $q1;
    mysqli_query($id, $q1);
}

if (($_POST['add'])){
    $namp=get_post($id,'NameB');

   $q="insert into bank value(null,'".$namp."')";
   // echo $q;
   mysqli_query($id,$q)  or die('Error:'.mysqli_error($id));
   echo "<script> window.location.replace('".$_SERVER['PHP_SELF']."'); </script>";
} 

if (($_POST['rad']) && ($_POST['del']))        //удаление выбранной информации
{
 $q="delete from bank where IDB='".$_POST['rad']."'";
//echo $q;
 mysqli_query($id,$q) or die('Error:'.mysqli_error($id));
}

$acc=$_SESSION['access'];
$idm=$_SESSION['id_user'];
$query="select IDB, NameB from bank order by NameB";
$result=mysqli_query($id,$query)or die(" Ошибка выполнения запроса".mysqli_error($id));
echo "<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Банки</title>
</head>";
echo "<div><h1 align=center>Банки</h1>";
echo "<br><div >";
echo "<form class=formtwo method='post' action='".$_SERVER['PHP_SELF']."'>";
echo "<div class=height>";

echo "<table class=tab1 border=2 width=40% align=center>";

echo "<thead><tr><th colspan=2>Банк</th>";
if($acc==2){
  echo "<th>Добавить<br> к себе</th><th>Число<br>моих</th>";
}elseif($acc==1){
  echo "<th>Число счетов</th>";
}
echo "</tr></thead>";
while($row=mysqli_fetch_array($result)){
    echo "<tr>";
    if ($acc==1){
      echo "<td align=right><input type=radio name='rad' value='".$row['IDB']."'>";
    }

    if ($acc==1){
      echo "<td >";
    }else{echo "<td colspan=2>";}

    
    echo $row["NameB"]."</td>";
    
    if($acc==2){
      echo "<td>
            <a href='prods.php?IDBtoAdd=".$row['IDB']."' class='inpuB small inpuBB'>Добавить</a>
          </td>";

      $quer="select count(*) as count from prod where IDM=".$idm." and IDB=".$row['IDB'].";";
      $res=mysqli_query($id,$quer)or die(" Ошибка выполнения запроса".mysqli_error($id));
      $querres=mysqli_fetch_array($res);
      echo "<td>".$querres['count']."</td>";
    }
    elseif($acc==1){
      $quer="select count(*) as count from prod where IDB=".$row['IDB'].";";
      $res=mysqli_query($id,$quer)or die(" Ошибка выполнения запроса".mysqli_error($id));
      $querres=mysqli_fetch_array($res);
      echo "<td>".$querres['count']."</td>";
    }
    echo "</tr>";
}
echo "</table></div>";

if ($acc==1){

echo "<div class=>";
echo "Название:<br><input class='inpu small' type=text name=NameB value='' size=15><br>";
echo "<input class=inpuB type=submit name=add value='Добавить'><br>";

echo '<hr><div>Выбранный Банк</div>' ;
echo "<input class=inpuB type=submit name=del value='Удалить'> <br> ";
echo "<input class=inpuB type=submit name=upd value='Изменить'> <br>";		



if (($_POST['rad']) && ($_POST['upd']))			//изменение выбранной информации
{
   echo "<hr><b>Изменить:</b><br>";			  
 $q="select * from bank where IDB=".$_POST['rad'];
 $res=mysqli_query($id,$q);
   $row = mysqli_fetch_array($res);
 echo "Название: <input class='inpu small' type=text name=NameP1 value='".$row['NameB']."' size=15><br>";


 echo "<input class=inpuB type=submit name=sav value='Сохранить'>";
 echo "<input  type=hidden name=IDB1 value='".$row['IDB']."'>";

}
echo "</div>";
}
echo "</form></div></div>";


mysqli_close($id); 

function get_post($i,$var)
{
return mysqli_real_escape_string($i,$_POST[$var]);
}
?> 