<?php 
session_start ();
require_once("conf.php");
require_once("header.php");
echo "<title>Товары</title>";

if (isset($_SESSION['access'])==1)
 {
   if (isset($_GET['delete_id'])) {
      $delete_id = intval($_GET['delete_id']);
      $delete_query = "DELETE FROM oper WHERE IDO = $delete_id";
      if (!mysqli_query($id, $delete_query)) {
          echo "<div class='alert alert-danger'>Ошибка при удалении записи: " . mysqli_error($id) . "</div>";
      }
  }

  if (isset($_GET['delete_idT'])) {
    $delete_id = intval($_GET['delete_idT']);
    $delete_query = "DELETE FROM tovar WHERE IDT = $delete_id";
    if (!mysqli_query($id, $delete_query)) {
        echo "<div class='alert alert-danger'>Ошибка при удалении записи: " . mysqli_error($id) . "</div>";
    }
}

    echo "<div>";
    echo "<h1 align=center>Товары</h1>";
    $query = "
    SELECT
        oper.IDO,
        oper.Dat,
        oper.SUMO,
        oper.TYPE,
        oper.Place,
        prod.IDP AS ProdIDP,
        prod.NameP AS ProdNameP,
        bank.IDB AS ProdIDB,
        bank.NameB AS ProdNameB,
        man.IDM AS ProdIDM,
        man.NameF AS ProdNameF,
        prod2.IDP AS Prod2IDP,
        prod2.NameP AS Prod2NameP,
        bank2.IDB AS Prod2IDB,
        bank2.NameB AS Prod2NameB,
        man2.IDM AS Prod2IDM,
        man2.NameF AS Prod2NameF
    FROM
        oper
    JOIN
        prod ON oper.IDP = prod.IDP
    JOIN
        bank ON prod.IDB = bank.IDB
    JOIN
        man ON prod.IDM = man.IDM
    LEFT JOIN
        prod AS prod2 ON oper.IDPP = prod2.IDP
    LEFT JOIN
        bank AS bank2 ON prod2.IDB = bank2.IDB
    LEFT JOIN
        man AS man2 ON prod2.IDM = man2.IDM
    ";

    $queryt="select IDT, IDO, NameT,Cost from tovar ";
    if($_SESSION['access']==2){
      $query = $query." WHERE prod.IDM=".$_SESSION['id_user']." ";
    }


if ($_POST['filt']) {
    $conditions2=array();
    $namt=$_POST['tname'];
    $eqt=$_POST['eqt'];
    $co=$_POST['cost']*100;
    if($_SESSION['access']==1){
        $idmf1=$_POST['idmanf1'];
         }
        $idbf=$_POST['idbankf'];
        $namp=$_POST['pname'];
        $type=$_POST['type'];
        $plac=$_POST['plac'];
        $eq=$_POST['eq'];
        $su=$_POST['sum']*100;
        $beg=$_POST['beg'];
        $end=$_POST['end'];
        $idmf2=$_POST['idmanf2'];
        $conditions =array();


    if($namt!=''){
      $conditions2[] = "NameT LIKE '%$namt%'";
    }
    if($co!=''){
      $conditions2[] = "Cost".$eqt." $co";
    }
    if (count($conditions2) > 0) {
      $queryt .= " WHERE " . implode(" AND ", $conditions2);
    }
    $resut=mysqli_query($id,$queryt)or die(" Ошибка выполнения запроса".mysqli_error($id));
    $arr=array();
    $num=array();
    while($st= mysqli_fetch_array($resut)){
        $num[]=$st['IDO'];
        $numt[]=$st['IDT'];
    }

    if (count($conditions2) > 0 and count($num) > 0) {
        $conditions[]="IDO=".implode(" OR IDO=",$num);
    }
   

  
  if($_SESSION['access']==1){
   if ($idmf1!='---'){
         $conditions[] = "man.IDM = $idmf1";
   }
}
  if ($idbf!='---'){
      $conditions[] = "bank.IDB = $idbf";
  }
  if ($namp!=''){
      $conditions[] = "prod.NameP LIKE '%$namp%'";
  }
  if ($type!='---'){
      $conditions[] = "TYPE=$type";
  }
  if ($plac!=''){
      $conditions[] = "Place LIKE '%$plac%'";
  }
  if ($su!=''){
      $conditions[] = "SUMO".$eq." $su";
  }
  if ($beg!=''){
      $conditions[] = "Dat>= '$beg'";
  }
  if ($end!=''){
      $conditions[] = "Dat<= '$end'";
  }
  if ($idmf2!='---'){
      $conditions[] = "man2.IDM = $idmf2";
  }

  if (count($conditions) > 0) {
        if($_SESSION['access']==1){
                $query .= " WHERE " . implode(" AND ", $conditions);
        }elseif($_SESSION['access']==2){
            $query .= " AND " . implode(" AND ", $conditions);
        }
  }
  

}
//echo $query;
$qs="select SUM(SUMO) as sum, count(*) as count from (".$query.") as a";
// echo $qs;
$resum=mysqli_query($id,$qs)or die(" Ошибка выполнения запроса".mysqli_error($id));
$ss= mysqli_fetch_array($resum);

$query=$query." order by Dat desc";
$result = mysqli_query($id, $query) or die("Ошибка выполнения запроса: " . mysqli_error($id));

$qi="select SUM(SUMO) as sum, count(*) as count from (".$query.") as a where a.TYPE='пополнение'";
$resui=mysqli_query($id,$qi)or die(" Ошибка выполнения запроса".mysqli_error($id));
$si= mysqli_fetch_array($resui);

$qm="select SUM(SUMO) as sum, count(*) as count from (".$query.") as a where a.TYPE='между'";
$resm=mysqli_query($id,$qm)or die(" Ошибка выполнения запроса".mysqli_error($id));
$sm= mysqli_fetch_array($resm);

$qt="select SUM(SUMO) as sum, count(*) as count from (".$query.") as a where a.TYPE='трата'";
$resut=mysqli_query($id,$qt)or die(" Ошибка выполнения запроса".mysqli_error($id));
$st= mysqli_fetch_array($resut);

echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
$result1=mysqli_query($id,"select * from bank ");
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));


echo "<div style='display:flex; justify-content:space-evenly;'>";
if ($_SESSION['access']==1){
    echo "<div>Владелец <select name=idmanf1>";
    echo "<option value='---'>---</option>";
    while ($row2 = mysqli_fetch_array($result2))
    {if ($row2['IDM']==$idmf1)echo "<option value=".$row2['IDM']." selected>".$row2['NameF']."</option>";
      else echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
    echo "</select></div>";
}
echo "<div>Банк <select class='small' name=idbankf>";
echo "<option value='---'>---</option>";
while ($row1 = mysqli_fetch_array($result1)) 
{ if ($row1['IDB']==$idbf) echo "<option value=".$row1['IDB']." selected>".$row1['NameB']."</option>";
else echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
echo "</select></div>";
 echo "<div>Название <input type=text value='".$namp."' name=pname></div>";
  echo "<div>Тип <select name=type>
  <option value='---'>---</option>
  <option value='трата'>трата</option>
  <option value='пополнение'>пополнение</option>
  <option value='между'>между</option>
  </select>
  </div>";
  
 
 echo "</div><br><div style='display:flex; justify-content:space-evenly;'>";
 echo "<div>Место <input type=text value='".$plac."' name=plac></div>";
 echo "<div>Сумма<select name=eq>
 <option value='='>=</option>
 <option value='<'><</option>
 <option value='>'>></option>
 <option value='!='>!=</option>
 <option value='<='><=</option>
 <option value='>='>>=</option>
 </select>
 <input type=numder value='".($su/100)."' name=sum>
 </div>";
 echo "<div>Период от <input type=date value='".$beg."' name=beg> до <input type=date value='".$end."' name=end></div>";
 
 echo "</div><br><div style='display:flex; justify-content:space-evenly;'>";
 echo "<div>Название <input type=text value='".$namt."' name=tname></div>";
 echo "<div>Цена <select name=eqt>
 <option value='='>=</option>
 <option value='<'><</option>
 <option value='>'>></option>
 <option value='!='>!=</option>
 <option value='<='><=</option>
 <option value='>='>>=</option>
 </select>
 <input type=numder value='".($co/100)."' name=cost>
 </div>";
echo "<div>Получатель<select name=idmanf2>";
echo "<option value='---'>---</option>";
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));
while ($row2 = mysqli_fetch_array($result2))
{if ($row2['IDM']==$idmf2)echo "<option value=".$row2['IDM']." selected>".$row2['NameF']."</option>";
  else echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
echo "</select></div>";

echo "<input class='inpuB small inpuBB' type=submit name=filt value='Отфильтровать'>";
echo "</div>";

echo "<br><div class=height style='max-height:70vh'><table class='tab1' border='1' align='center'>";
echo "<thead>
      <tr>
          
          <th>".($_SESSION['access']==1?'Имя владельца':'')."</th>
          <th>Продукт</th>
          <th>Тип</th>
          <th>Место</th>
          <th>Сумма операции</th>
          <th>Дата</th>
          
          <th>Действия</th>
      </tr>
  </thead>";
  // <th>детализация</th>
while ($row = mysqli_fetch_array($result)) {
  echo "<tr "; 
  if ($row['TYPE']=='трата'){
      echo "class=trat";
  }elseif($row['TYPE']=='пополнение'){
      echo "class=popol";
  }else{
      echo "class=mez";
  }
  echo " id='".$row['IDO']."'>
          ";
        //   <td><input type='checkbox' name='chk_prod[]' onclick='calculateTotal()' value='".$row['IDO']."'></td>
          echo "
          <td>".($_SESSION['access']==1?$row['ProdNameF']:'')."</td>
          <td>{$row['ProdNameB']} {$row['ProdNameP']}</td>
          <td>{$row['TYPE']}</td>
          <td>{$row['Place']}</td>
          <td>".($row['SUMO']/100)."</td>
          <td>{$row['Dat']}</td>

          <td>
          <a href='addopert.php?change=".$row['IDO']."' class='inpuB small inpuBB'>
          <i class='bi bi-vector-pen'></i></a>
              <a href='?delete_id={$row['IDO']}' class='inpuB small inpuBB'>
                      <i class='bi bi-trash'></i></a>
          </td>
      </tr>";

    if($row['Prod2NameF']){
        echo "<tr class=mez2>
            
            <td></td>
            <td>=> {$row['Prod2NameB']} {$row['Prod2NameP']}</td>
            <td>=> {$row['Prod2NameF']}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>";
    }

    $quee="select IDT,NameT,Cost from tovar where IDO=".$row['IDO'];
    // if (count($conditions2) > 0) {
    //         $quee .= " AND " . implode(" AND ", $conditions2);
    // }
    $rese=mysqli_query($id,$quee)or die(" Ошибка выполнения запроса".mysqli_error($id));
    while ($row2 = mysqli_fetch_array($rese)) {
        echo "<tr "; 
            if ($row['TYPE']=='трата'){
                echo "class='trat2 ";
            }elseif($row['TYPE']=='пополнение'){
                echo "class='popol2 ";
            }else{
                echo "class='mez2 ";
            }
            if (isset($numt) && is_array($numt) && (count($conditions2) > 0)) {
                if (in_array($row2['IDT'], $numt)) {
                    echo " ist' ";
                } else {
                    echo " ' ";
                }
            }
            echo " id='{$row2['IDT']}t'>
            <td></td>
            ";
            // <td><input type='checkbox' name='chk_prod2[]' onclick='calculateTotal()' value=".$row['IDO']."></td>
            echo "
            <td>{$row2['NameT']}</td>
          <td></td>
          <td></td>
          <td>".($row2['Cost']/100)."</td>
          <td></td>
          <td>
              
              <a href='?delete_idT={$row['IDT']}' class='inpuB small inpuBB'>
                      <i class='bi bi-trash'></i></a>
          </td>
        
        </tr>";
    }

}
echo "</table></div>";
echo "<table class=tab1>";
  echo "<tr><td>В выводе ".$ss['count']."</td></td>
  <td>Сумма общая ".($ss['sum']/100)."</td>
  <td class=popol>".($si['sum']/100)." (".$si['count'].")</td>
  <td class=mez>".($sm['sum']/100)." (".$sm['count'].")</td>
  <td class=trat>".($st['sum']/100)." (".$st['count'].")</td>
  <td></td></tr></table>"; 
//   <td>Выделенные: <span id='total_amount'>0.00</span></td></tr></table>";

      echo "</form></div>";
  


    echo "</div>";
 }else{
   echo "<h1>Доступ на эту страницу закрыт.</h1>";
}
?>

<!-- <script>
    function calculateTotal() {
        var total = 0;
        var checkboxes = document.getElementsByName("chk_prod[]");
        var child=document.getElementsByName("chk_prod2[]");
        var acc=<?= $_SESSION['access']?>;
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                var row = checkboxes[i].parentNode.parentNode;
                console.log(checkboxes[i].checked, checkboxes[i].value);
                var sum = parseFloat(row.cells[5].innerHTML); // Измените индекс на нужный вам
                total += sum;
            }
        }
        document.getElementById("total_amount").innerHTML = total; // Отображаем сумму с двумя знаками после запятой
    }

    

    
</script> -->