<?php 
session_start ();
require_once("conf.php");
require_once("header.php");


 echo "<title>Операции</title>";

 if (isset($_SESSION['access'])==1)
 {

    echo "<div>";
    echo "<h1 align=center>История операций</h1>";
    if (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        $delete_query = "DELETE FROM oper WHERE IDO = $delete_id";
        if (!mysqli_query($id, $delete_query)) {
            echo "<div class='alert alert-danger'>Ошибка при удалении записи: " . mysqli_error($id) . "</div>";
        }
    }

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
    if($_SESSION['access']==1){
        // select * from oper join prod AS P on oper.IDP=P.IDP join bank as b on p.IDB=b.IDB left join prod on oper.IDPP=prod.IDP   
       
if ($_POST['filt']) {
    $idmf1=$_POST['idmanf1'];
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
    if ($idmf1!='---'){
        $conditions[] = "man.IDM = $idmf1";
    }
    if ($idbf!='---'){
        $conditions[] = "bank.IDB = $idbf";
    }
    if ($namp!=''){
        $conditions[] = "prod.NameP LIKE '%$namp%'";
    }
    if ($type!='---'){
        $conditions[] = "TYPE='$type'";
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
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
}
 //echo $query;
$qs="select SUM(SUMO) as sum, count(*) as count from (".$query.") as a";
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
   
echo "<div>Владелец 1<select name=idmanf1>";
 echo "<option value='---'>---</option>";
 while ($row2 = mysqli_fetch_array($result2))
 {if ($row2['IDM']==$idmf1)echo "<option value=".$row2['IDM']." selected>".$row2['NameF']."</option>";
    else echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
 echo "</select></div>";
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
    
   echo "<div>Место <input type=text value='".$plac."' name=plac></div>";
   echo "</div><br><div style='display:flex; justify-content:space-evenly;'>";
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
   

 
 echo "<div>Владелец 2<select name=idmanf2>";
 echo "<option value='---'>---</option>";
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));
 while ($row2 = mysqli_fetch_array($result2))
 {if ($row2['IDM']==$idmf2)echo "<option value=".$row2['IDM']." selected>".$row2['NameF']."</option>";
    else echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
 echo "</select></div>";

 echo "<input class='inpuB small inpuBB' type=submit name=filt value='Отфильтровать'>";
 echo "</div>";

echo "<br><div class=height style='max-height:75vh'><table class='tab1' border='1' align='center'>";
echo "<thead>
        <tr>
            <th></th>
            <th>Имя владельца</th>
            <th>Продукт</th>
            <th>Тип</th>
            <th>Место</th>
            <th>Сумма операции</th>
            <th>Дата</th>
            <th>Имя владельца 2</th>
            <th>Название продукта 2</th>
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
    echo ">
            <td><input type='checkbox' name='chk_prod[]' onclick='calculateTotal()' value='".$row['IDO']."'></td>
            <td>{$row['ProdNameF']}</td>
            <td>{$row['ProdNameB']} {$row['ProdNameP']}</td>
            <td>{$row['TYPE']}</td>
            <td>{$row['Place']}</td>
            <td>".($row['SUMO']/100)."</td>
            <td>{$row['Dat']}</td>

            <td>{$row['Prod2NameF']}</td>
            <td>{$row['Prod2NameB']} {$row['Prod2NameP']}</td>
            <td>
                <a href='tovars.php?#".$row['IDO']."' class='inpuB small inpuBB'>
                    <i class='bi bi-search'></i></a>
                <a href='addopert.php?change=".$row['IDO']."' class='inpuB small inpuBB'>
                    <i class='bi bi-vector-pen'></i></a>
                <a href='?delete_id={$row['IDO']}' class='inpuB small inpuBB'>
                        <i class='bi bi-trash'></i></a>
            </td>
        </tr>";
}
echo "</table></div>";
echo "<table class=tab1>";
echo "<tr><td>В выводе ".$ss['count']."</td></td>
<td>Сумма общая ".($ss['sum']/100)."</td>
<td class=popol>".($si['sum']/100)." (".$si['count'].")</td>
<td class=mez>".($sm['sum']/100)." (".$sm['count'].")</td>
<td class=trat>".($st['sum']/100)." (".$st['count'].")</td>
<td>Выделенные: <span id='total_amount'>0.00</span></td></tr></table>";

        echo "</form></div>";
    
    }
    
    
    
    
    
























    
    
    
    
    else{

        $qu2=$query." WHERE man2.IDM=".$_SESSION['id_user']." AND man.IDM!=".$_SESSION['id_user']." ";
        $query = $query." WHERE prod.IDM=".$_SESSION['id_user']." ";
    

    if ($_POST['filt']) {
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
        
        if ($idbf!='---'){
            $conditions[] = "bank.IDB = $idbf";
        }
        if ($namp!=''){
            $conditions[] = "prod.NameP LIKE '%$namp%'";
        }
        if ($type!='---'){
            $conditions[] = "TYPE='".$type."'";
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
            $query .= " AND " . implode(" AND ", $conditions);
            $qu2.=" AND " . implode(" AND ", $conditions);
        }
    }
    // echo $query;
    $qs="select SUM(SUMO) as sum, count(*) as count from (".$query.") as a";
    $resum=mysqli_query($id,$qs)or die(" Ошибка выполнения запроса".mysqli_error($id));
    $ss= mysqli_fetch_array($resum);


$query=$query." order by Dat desc";
$result = mysqli_query($id, $query) or die("Ошибка выполнения запроса: " . mysqli_error($id));
$qu2.=" order by Dat desc";
$resu2=mysqli_query($id, $qu2) or die("Ошибка выполнения запроса: " . mysqli_error($id));
echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
$result1=mysqli_query($id,"select * from bank ");
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));


echo "<div style='display:flex; justify-content:space-evenly;'>";
   
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
   echo "<div>Место <input type=text value='".$plac."' name=plac></div>";
   echo "</div><br><div style='display:flex; justify-content:space-evenly;'>";
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
 echo "<div>Владелец 2<select name=idmanf2>";
 echo "<option value='---'>---</option>";
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));
 while ($row2 = mysqli_fetch_array($result2))
 {if ($row2['IDM']==$idmf2)echo "<option value=".$row2['IDM']." selected>".$row2['NameF']."</option>";
    else echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
 echo "</select></div>";

 echo "<input class='inpuB small inpuBB' type=submit name=filt value='Отфильтровать'>";
 echo "</div>";

echo "<br><div class=height style='max-height:75vh'><table class='tab1' border='1' align='center'>";
echo "<thead>
        <tr>
            <th></th>
            
            <th>Продукт</th>
            <th>Тип</th>
            <th>Место</th>
            <th>Сумма операции</th>
            <th>Дата</th>
            <th>Имя владельца 2</th>
            <th>Название продукта 2</th>
            <th>Дейсвия</th>
        </tr>
    </thead>";
while ($row = mysqli_fetch_array($result)) {
    echo "<tr "; 
    if ($row['TYPE']=='трата'){
        echo "class=trat";
    }elseif($row['TYPE']=='пополнение'){
        echo "class=popol";
    }else{
        echo "class=mez";
    }
    echo ">
            <td>
                <input type='checkbox' name='chk_prod[]' onclick='calculateTotal2()' value='".$row['IDP']."'>
            </td>
            <td>{$row['ProdNameB']} {$row['ProdNameP']}</td>
            <td>{$row['TYPE']}</td>
            <td>{$row['Place']}</td>
            <td>".($row['SUMO']/100)."</td>
            
            
            <td>{$row['Dat']}</td>
            <td>{$row['Prod2NameF']}</td>
            <td>{$row['Prod2NameB']} {$row['Prod2NameP']}</td>
            
            <td>
                <a href='tovars.php?#".$row['IDO']."' class='inpuB small inpuBB'>
                    <i class='bi bi-search'></i>
                </a>
                <a href='addopert.php?change=".$row['IDO']."' class='inpuB small inpuBB'>
                    <i class='bi bi-vector-pen'></i></a>
                </a>
                <a href='?delete_id={$row['IDO']}' class='inpuB small inpuBB'>
                        <i class='bi bi-trash'></i>
                    </a>
            </td>
        </tr>";
}
echo "</table>";
echo "Переведенное мне от семьи
<table class='tab1' border='1' align='center'>";
echo "
        <tr>
            <th></th>
            
            <th>Продукт</th>
            <th>Тип</th>
            <th>Место</th>
            <th>Сумма операции</th>
            <th>Дата</th>
            <th>Имя владельца 2</th>
            <th>Название продукта 2</th>
        </tr>
    ";
while ($row = mysqli_fetch_array($resu2)) {
    echo "<tr "; 
    if ($row['TYPE']=='трата'){
        echo "class=trat";
    }elseif($row['TYPE']=='пополнение'){
        echo "class=popol";
    }else{
        echo "class=mez";
    }
    echo ">
            <td>
                <input type='checkbox' name='chk_prod[]' onclick='calculateTotal2()' value='".$row['IDP']."'>
                <input type=radio name='rad' value='".$row['IDP']."'>
            </td>
            <td>{$row['Prod2NameB']} {$row['Prod2NameP']}</td>
            <td>{$row['TYPE']}</td>
            <td>{$row['Place']}</td>
            <td>".($row['SUMO']/100)."</td>
            <td>{$row['Dat']}</td>
            <td>{$row['ProdNameF']}</td>
            <td>{$row['ProdNameB']} {$row['ProdNameP']}</td>
            
            
        </tr>";
}
echo "</table>";
echo "</div>";
echo "<table class=tab1>";
    echo "<tr><td>В выводе ".$ss['count']."</td></td><td></td><td>Сумма общая ".($ss['sum']/100)."</td><td><td>Выделенные: <span id='total_amount'>0.00</span></td></tr></table>";


    }

    echo "</form></div>";
 }else{
    echo "<h1>Доступ на эту страницу закрыт.</h1>";
}


?>

<script>
    function calculateTotal() {
        var total = 0;
        var checkboxes = document.getElementsByName("chk_prod[]");
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                var row = checkboxes[i].parentNode.parentNode;
                var sum = parseFloat(row.cells[5].innerHTML); // Измените индекс на нужный вам
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
                var sum = parseFloat(row.cells[4].innerHTML); // Измените индекс на нужный вам
                total += sum;
            }
        }
        document.getElementById("total_amount").innerHTML = total; // Отображаем сумму с двумя знаками после запятой
    }
</script>