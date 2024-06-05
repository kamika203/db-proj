<?php 
session_start ();
require_once("conf.php");
require_once("header.php");


echo "<title>Банковские продукты</title>";
echo "<div>";
echo "<h1 align=center>Банковские продукты</h1>";


if ($_POST['sav'])                            //сохранение изменений
{
    $idp1=$_POST['IDP1'];
    $namP1=get_post($id,'NameP1');
    $idbank1=get_post($id,'idbank1');
    $idman1=get_post($id,'idman1');
    $pro1=get_post($id,'PROC1');
    $su1=$_POST['SUMP1']*100;
    $upd1=date('Y-m-d');
    $q1="update prod set NameP='".$namP1."', IDB='".$idbank1."',
     IDM='".$idman1."', SUMP='".$su1."', PROC='".$pro1."', 
     upd='".$upd1."' where IDP='".$idp1."'";
    //echo $q1;
    mysqli_query($id, $q1);
}

if (($_POST['add'])){
    $namp=get_post($id,'NameP');
    $sum=$_POST['SUMP']*100;
    $PRO=get_post($id,'PROC');
    $idm=get_post($id,'idman');
    $idb=get_post($id,'idbank');
    $upd=date('Y-m-d');

   $q="insert into prod value(null,'".$namp."',".$sum.",".$PRO.",".$idm.",".$idb.",'".$upd."')";
   // echo $q;
   mysqli_query($id,$q);
   echo "<script> window.location.replace('".$_SERVER['PHP_SELF']."'); </script>";
} 

if (($_POST['rad']) && ($_POST['del']))        //удаление выбранной информации
{
 $q="delete from prod where IDP='".$_POST['rad']."'";
//echo $q;
 mysqli_query($id,$q) or die('Error:'.mysqli_error($id));
}

if($_SESSION['access']==1){
    $query="select bank.IDB, NameB, man.IDM, NameP, NameF, SUMP, PROC, IDP from bank join prod on bank.IDB=prod.IDB join man on prod.IDM=man.IDM ";
//НАЗВАНИЕ БАНКА,продукт, сумма процент
if ($_POST['filt']) {
    $idmf = $_POST['idmanf'];
    $idbf = $_POST['idbankf'];
    $namp = $_POST['pname'];
    $conditions =array();

    if ($idbf != '---') {
        $conditions[] = "bank.IDB = $idbf";
    }
    if ($idmf != '---') {
        $conditions[] = "man.IDM = $idmf";
    }
    if ($namp != "") {
        $conditions[] = "NameP LIKE '%$namp%'";
    }

    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    if ($_POST['order'] != '---') {
        $query .= " ORDER BY " . $_POST['order'];
    }
}
$qs="select SUM(SUMP) as sum, count(*) as count from (".$query.") as a";
$resum=mysqli_query($id,$qs)or die(" Ошибка выполнения запроса".mysqli_error($id));
$ss= mysqli_fetch_array($resum);

$result=mysqli_query($id,$query)or die(" Ошибка выполнения запроса".mysqli_error($id));
echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
$result1=mysqli_query($id,"select * from bank ");
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));

   echo "<div style='display:flex; justify-content:space-evenly;'>";
   echo "<div>Название <input type=text value='".$namp."' name=pname></div>";
   echo "<div>Банк <select class='small' name=idbankf>";
   echo "<option value='---'>---</option>";
   while ($row1 = mysqli_fetch_array($result1)) 
   { if ($row1['IDB']==$idbf) echo "<option value=".$row1['IDB']." selected>".$row1['NameB']."</option>";
    else echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
   echo "</select></div>";
 echo "<div>Владелец <select name=idmanf>";
 echo "<option value='---'>---</option>";
 while ($row2 = mysqli_fetch_array($result2))
 {if ($row2['IDM']==$idmf)echo "<option value=".$row2['IDM']." selected>".$row2['NameF']."</option>";
    else echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
 echo "</select></div>";
 echo "<div>Сортировка 
     <select name=order>
     <option value='---'>---</option>
     <option value='NameB'>Банк</option>
     <option value='NameP'>Продукт</option>
     <option value='SUMP'>Сумма</option>
     <option value='PROC'>Процент</option>
     <option value='NameF'>Владелец</option>
     </select>
     </div>";
 echo "<input class='inpuB small inpuBB' type=submit name=filt value='Отфильтровать'>";
 echo "</div>";

echo "<br><div class=height style='max-height: 40vh'>";
echo "<table class=tab1 border=0 align=center>";
echo "<thead><tr><th ></th><th>Банк</th><th>Продукт</th><th>Владелец</th><th>Сумма</th><th>%</th><th></th></tr></thead>";
while($row=mysqli_fetch_array($result)){
    echo "<tr>";
    echo "<td align=right><input type=radio name='rad' value='".$row['IDP']."'>";
    echo "<td>".$row["NameB"]."</td>
     <td>".$row["NameP"]."</td>
     <td>".$row["NameF"]."</td>
      <td>  ".($row["SUMP"]/100)." </td> <td>".$row["PROC"]."</td>
      <td><input type='checkbox' name='chk_prod[]' onclick='calculateTotal()' value='".$row['IDP']."'></td>";
    echo "</tr>";
}
echo "</table></div>";
echo "<table class=tab1>";
echo "<tr><td>В выводе ".$ss['count']."</td></td><td></td><td>Сумма общая ".($ss['sum']/100)."</td><td><td>Выделенные: <span id='total_amount'>0.00</span></td></tr></table>";


$result1=mysqli_query($id,"select * from bank ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id)); 
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));
echo "<br><div><div style='display:flex; justify-content:space-evenly;'>";
echo "<div>Банк <select name=idbank>";   
while ($row1 = mysqli_fetch_array($result1)) 
{ echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
echo "</select></div>";
echo "<div>Название <input type=text name=NameP value=''></div>";
echo "<div>Владелец ";
echo "<select name=idman>";    
while ($row2 = mysqli_fetch_array($result2)) 
{ echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
echo "</select></div>";
echo "<div>Сумма <input type=number name=SUMP value='' step='0.01'></div>";
echo "<div>Процент<input type=number name=PROC value='' step='0.1'></div>";
echo "<input type=submit name=add value='Добавить' class='inpuB small inpuBB'></div><br>";
echo "<div style='display:flex; justify-content:space-evenly;'>";
echo "<div><input type=submit name=del value='Удалить' class='inpuB small inpuBB'>  выбранный продукт</div>";
echo "<div><input type=submit name=upd value='Изменить' class='inpuB small inpuBB'> выбранный продукт</div>";		 
echo "</div></div>";


if (($_POST['rad']) && ($_POST['upd']))			//изменение выбранной информации
{
   echo "<br><hr><b>Введите новые</b><div class=ins>";
   echo "";			  
 $q="select * from prod where IDP=".$_POST['rad'];
 $res=mysqli_query($id,$q);
 $result1=mysqli_query($id,"select * from bank ");
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));
   $row = mysqli_fetch_array($res);
   echo "<div>Банк <select name=idbank1>";
   while ($row1 = mysqli_fetch_array($result1)) 
   {  if ($row1['IDB']==$row['IDB']) echo "<option value=".$row1['IDB']." selected>".$row1['NameB']."</option>";
    else echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
   echo "</select></div>";
 echo "<div>название <input type=text name=NameP1 value='".$row['NameP']."' size=15></div>";
 echo "<div>Владелец <select name=idman1>";
 while ($row2 = mysqli_fetch_array($result2))
 {if ($row2['IDM']==$row['IDM'])echo "<option value=".$row2['IDM']." selected>".$row2['NameF']."</option>";
    else echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
    echo "</select></div>";
    echo "<div>Сумма <input type=number name=SUMP1 value='".($row['SUMP']/100)."' step='0.01'></div>";
    echo "<div>Процент <input type=number name=PROC1 value='".$row['PROC']."' step='0.1'></div>";


 echo "<input type=submit name=sav value='Сохранить' class='inpuB small inpuBB'>";
 echo "<input type=hidden name=IDP1 value='".$row['IDP']."'>";

echo "</div>";
}


echo "</form>";




}elseif($_SESSION['access']==2){
    
    $query="select bank.IDB, NameB, NameP, SUMP, PROC, IDP from bank join prod on bank.IDB=prod.IDB  where IDM=".$_SESSION['id_user'];
    //НАЗВАНИЕ БАНКА,продукт, сумма процент
    if($_POST['filt'] ){
        $idbf=$_POST['idbankf'];
        $namp=$_POST['pname'];
        if($idbf!='---' and $namp!=""){
            $query=$query." and bank.IDB=".$idbf." and prod.NameP like '%".$namp."%'";
        }elseif($namp!=""){
            $query=$query." and prod.NameP like '%".$namp."%'";
        }elseif($idbf!='---'){
            $query=$query." and bank.IDB=".$idbf."";
        }

        if($_POST['order']!="---"){
            $query=$query." order by ".$_POST['order'];
        }
        
    }


    $qs="select SUM(SUMP) as sum, count(*) as count from (".$query.") as a";
    $resum=mysqli_query($id,$qs)or die(" Ошибка выполнения запроса".mysqli_error($id));
    $ss= mysqli_fetch_array($resum);
    
    
    $result=mysqli_query($id,$query)or die(" Ошибка выполнения запроса".mysqli_error($id));
    echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
    $result1=mysqli_query($id,"select * from bank ");
       echo "<div style='display:flex; justify-content:space-evenly;'>";
       echo "<div>Название <input type=text value='".$namp."' name=pname></div>";
       echo "<div>Банк <select class='small' name=idbankf>";
       echo "<option value='---'>---</option>";
       while ($row1 = mysqli_fetch_array($result1)) 
       { if ($row1['IDB']==$idbf) echo "<option value=".$row1['IDB']." selected>".$row1['NameB']."</option>";
        else echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
       echo "</select></div>";
       echo "<div>Сортировка 
     <select name=order>
     <option value='---'>---</option>
     <option value='NameB'>Банк</option>
     <option value='NameP'>Продукт</option>
     <option value='SUMP'>Сумма</option>
     <option value='PROC'>Процент</option>
     </select>
     </div>";
     echo "<input class='inpuB small inpuBB' type=submit name=filt value='Отфильтровать'>";
     
     
     echo "</div>";
    
    
    echo "<br><div class=height style='max-height: 40vh'>";
    echo "<table class=tab1 border=0 align=center>";
    echo "<thead><tr><th ></th><th>Банк</th><th>Продукт</th><th>Сумма</th><th>%</th><th></th></tr></thead>";
    while($row=mysqli_fetch_array($result)){
        echo "<tr>";
        echo "<td align=right><input type=radio name='rad' value='".$row['IDP']."'>";
        echo "<td>".$row["NameB"]."</td> 
        <td>".$row["NameP"]."</td>
        <td>".($row["SUMP"]/100)."</td> <td>".$row["PROC"]."</td>
        <td><input type='checkbox' name='chk_prod[]' onclick='calculateTotal2()' value='".$row['IDP']."'></td>";
        echo "</tr>";
    }
    echo "</table></div>";
    echo "<table class=tab1>";
    echo "<tr><td>В выводе ".$ss['count']."</td></td><td></td><td>Сумма общая ".($ss['sum']/100)."</td><td><td>Выделенные: <span id='total_amount'>0.00</span></td></tr></table>";

    
    $result1=mysqli_query($id,"select * from bank ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id)); 
    echo "<br><div><div style='display:flex; justify-content:space-evenly;'>";
    echo "<div>Банк <select name=idbank>";   
    while ($row1 = mysqli_fetch_array($result1)) 
    {  if ($row1['IDB']==$_GET["IDBtoAdd"]) echo "<option value=".$row1['IDB']." selected>".$row1['NameB']."</option>";
        else echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
    echo "</select></div>";
    echo "<div>Название <input type=text name=NameP value=''></div>";
    echo "<input type=hidden name=idman value='".$_SESSION['id_user']."'>";
    echo "<div>Сумма <input type=number name=SUMP value='' step='0.01'></div>";
    echo "<div>Процент<input type=number name=PROC value='' step='0.1' ></div>";
    echo "<input type=submit name=add value='Добавить' class='inpuB small inpuBB'></div><br>";
    echo "<div style='display:flex; justify-content:space-evenly;'>";
    echo "<div><input type=submit name=del value='Удалить' class='inpuB small inpuBB'>  выбранный продукт</div>";
    echo "<div><input type=submit name=upd value='Изменить' class='inpuB small inpuBB'> выбранный продукт</div>";		 
    echo "</div></div>";
    
    
    if (($_POST['rad']) && ($_POST['upd']))			//изменение выбранной информации
    {
       echo "<br><hr><b>Введите новые</b><div class=ins>";
       echo "";			  
     $q="select * from prod where IDP=".$_POST['rad'];
     $res=mysqli_query($id,$q);
     $result1=mysqli_query($id,"select * from bank ");
       $row = mysqli_fetch_array($res);
       echo "<div>Банк <select name=idbank1>";
       while ($row1 = mysqli_fetch_array($result1)) 
       {  if ($row1['IDB']==$row['IDB']) echo "<option value=".$row1['IDB']." selected>".$row1['NameB']."</option>";
        else echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
       echo "</select></div>";
     echo "<div>название <input type=text name=NameP1 value='".$row['NameP']."' size=15></div>";
     echo "<input type=hidden name=idman1 value='".$row['IDM']."'>";
        echo "<div>Сумма <input type=number name=SUMP1 value='".($row['SUMP']/100)."' step='0.01'></div>";
        echo "<div>Процент <input type=number name=PROC1 value='".$row['PROC']."' step='0.1'></div>";
    
     echo "<input type=submit name=sav value='Сохранить' class='inpuB small inpuBB'>";
     echo "<input type=hidden name=IDP1 value='".$row['IDP']."'>";
    
    echo "</div>";
    }
    
    echo "</form>";
    
}
else{
    echo "<h1>Доступ на эту страницу закрыт.</h1>";
}

echo "</div>";


function get_post($i,$var)
{
return mysqli_real_escape_string($i,$_POST[$var]);
}
?>

<script>
    function calculateTotal() {
        var total = 0;
        var checkboxes = document.getElementsByName("chk_prod[]");
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                var row = checkboxes[i].parentNode.parentNode;
                var sum = parseFloat(row.cells[4].innerHTML); // Измените индекс на нужный вам
                total += sum;
            }
        }
        document.getElementById("total_amount").innerHTML = total; // Отображаем сумму с двумя знаками после запятой
    }

    function calculateTotal2() {
        var total = 0;
        var checkboxes = document.getElementsByName("chk_prod[]");
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                var row = checkboxes[i].parentNode.parentNode;
                var sum = parseFloat(row.cells[3].innerHTML); // Измените индекс на нужный вам
                total += sum;
            }
        }
        document.getElementById("total_amount").innerHTML = total; // Отображаем сумму с двумя знаками после запятой
    }
</script>
