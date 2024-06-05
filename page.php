<?php 
require_once("conf.php");
require_once("header.php");

if ($_POST['sav'])                            //сохранение изменений
{
    $idp1=$_POST['IDP1'];
    $namP1=get_post($id,'NameP1');
    $idbank1=get_post($id,'idbank1');
    $idman1=get_post($id,'idman1');
    $pro1=get_post($id,'PROC1');
    $su1=get_post($id,'SUMP1');
    $upd1=date('Y-m-d');
    $q1="update prod set NameP='".$namP1."', IDB='".$idbank1."', IDM='".$idman1."', SUMP='".$su1."', PROC='".$pro1."', upd='".$upd1."' where IDP='".$idp1."'";
    //echo $q1;
    mysqli_query($id, $q1);
}

if (($_POST['add'])){
    $namp=get_post($id,'NameP');
    $sum=get_post($id,'SUMP');
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


$query="select IDP, NameB,NameP, SUMP, PROC,NameF from bank join prod on bank.IDB=prod.IDB join man on prod.IDM=man.IDM ";
//НАЗВАНИЕ БАНКА,продукт, сумма процент
$result=mysqli_query($id,$query)or die(" Ошибка выполнения запроса".mysqli_error($id));
echo "<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<link rel=stylesheet href='./styles/st.css'>
<title>Страница</title>
</head>";
echo "<div><a href='que.php'>запросы</a>";
echo "<h1 align=center>Информация о Продуктах</h1>";
echo "<br><br>";
echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
echo "<div class=one>";
echo "<table class=tab border=2 width=40% align=center>";
echo "<tr><th ></th><th>Банк</th><th>Продукт</th><th>Владелец</th><th>Сумма</th><th>%</th></tr>";
while($row=mysqli_fetch_array($result)){
    echo "<tr>";
    echo "<td align=right><input type=radio name='rad' value='".$row['IDP']."'>";
    echo "<td>".$row["NameB"]."</td> <td>".$row["NameP"]."</td><td>".$row["NameF"]."</td> <td>".$row["SUMP"]."</td> <td>".$row["PROC"]."</td>";
    echo "</tr>";
}
echo "</table>";

$result1=mysqli_query($id,"select * from bank ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id)); 
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));
echo "<div class=ins>";
echo "<table border=0 width=25% align=center>";
echo "<tr><td>Банк </td><td>";
echo "<select name=idbank>";   
while ($row1 = mysqli_fetch_array($result1)) 
{ echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
echo "</select></td></tr>";
echo "<tr><td>Название</td><td> <input type=text name=NameP value='' size=15></td></tr>";
echo "<tr><td>Владелец </td><td>";
echo "<select name=idman>";    
while ($row2 = mysqli_fetch_array($result2)) 
{ echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
echo "</select></td></tr>";
echo "<tr><td>Сумма </td><td><input type=number name=SUMP value='' ></td></tr>";
echo "<tr><td>Процент</td><td> <input type=number name=PROC value='' ></td></tr>";
echo "<tr><td><input type=submit name=add value='Добавить'></td></tr><tr></tr>";

echo "<tr><td colspan=3 align=left><input type=submit name=del value='Удалить'>  выбранный продукт</td></tr>";
echo "<tr><td colspan=3 align=left><input type=submit name=upd value='Изменить'> выбранный продукт</td></tr>";		 
echo "</tr></table></div></div>";


if (($_POST['rad']) && ($_POST['upd']))			//изменение выбранной информации
{
   echo "<div class=ins><table border=0 align=center>";
   echo "<tr><td colspan=3><b>Введите новые</b></td></tr>";			  
 $q="select * from prod where IDP=".$_POST['rad'];
 $res=mysqli_query($id,$q);
 $result1=mysqli_query($id,"select * from bank ");
$result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));
   $row = mysqli_fetch_array($res);
   echo "<td>Банк </td><td><select name=idbank1>";
   while ($row1 = mysqli_fetch_array($result1)) 
   {  if ($row1['IDB']==$row['IDB']) echo "<option value=".$row1['IDB']." selected>".$row1['NameB']."</option>";
    else echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
   echo "</select></td>";
 echo "<tr><td>название</td><td> <input type=text name=NameP1 value='".$row['NameP']."' size=15></td></tr>";
 echo "<td>Владелец </td><td><select name=idman1>";
 while ($row2 = mysqli_fetch_array($result2))
 {if ($row2['IDM']==$row['IDM'])echo "<option value=".$row2['IDM']." selected>".$row2['NameF']."</option>";
    else echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
    echo "<tr><td>Сумма </td><td><input type=number name=SUMP1 value='".$row['SUMP']."' step='0.01'></td></tr>";
    echo "<tr><td>Процент</td><td> <input type=number name=PROC1 value='".$row['PROC']."' step='0.1'></td></tr>";


 echo "<tr><td><input type=submit name=sav value='Сохранить'>";
 echo "<input type=hidden name=IDP1 value='".$row['IDP']."'></td></tr>";

echo "</table></div>";
}


echo "</form></div>";


mysqli_close($id); 

function get_post($i,$var)
{
return mysqli_real_escape_string($i,$_POST[$var]);
}
?> 