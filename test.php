<?php 
require_once("conf.php");
require_once("header.php");
$query="select IDP, NameB,NameP, SUMP, PROC,NameF from bank join prod on bank.IDB=prod.IDB join man on prod.IDM=man.IDM ";
//НАЗВАНИЕ БАНКА,продукт, сумма процент
$result=mysqli_query($id,$query)or die(" Ошибка выполнения запроса".mysqli_error($id));
echo "<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<link rel='stylesheet' href='./styles/test.css'>
<title>Тест</title>
</head>
";

echo "<div><form method='post' action='".$_SERVER['PHP_SELF']."'>";
echo "<div>";
echo "<table class=tab1 border=0 align=center>";
echo "<tr><th ></th><th>Банк</th><th>Продукт</th><th>Владелец</th><th>Сумма</th><th>%</th></tr>";
while($row=mysqli_fetch_array($result)){
    echo "<tr class=a".$row['IDP'].">";
    echo "<td align=right><input type=radio name='rad' value='".$row['IDP']."'>";
    echo "<td>".$row["NameB"]."</td> <td>".$row["NameP"]."</td><td>".$row["NameF"]."</td> <td>".$row["SUMP"]."</td> <td>".$row["PROC"]."</td>";
    echo "</tr>";
}
echo "</table></div>";

$q="select * from tovar";
$resut=mysqli_query($id,$q)or die(" Ошибка выполнения запроса".mysqli_error($id));
$arr=array();
$num=array();
while($st= mysqli_fetch_array($resut)){
    $num[]=$st['IDO'];
    $arr[$st['IDO']]=$arr[$st['IDO']]."<tr>
    <td>{$st['NameT']}</td>
    <td>".($st['Cost']/100)."</td>
    </tr>";
}
print_r($num);
echo "<table>";
print_r($arr);
echo "</table>";
if(in_array(3,$num)){
    echo "a";
}else{
    echo "b";
}
 $ar=array(3,1);
 $arr2=array();
$resut=mysqli_query($id,"select * from tovar where IDO in (".implode(",",$ar).")")or die(" Ошибка выполнения запроса".mysqli_error($id));
while($st= mysqli_fetch_array($resut)){
    $num[]=$st['IDO'];
    $arr2[$st['IDO']]=$arr[$st['IDO']]."<tr>
    <td>{$st['NameT']}</td>
    <td>".($st['Cost']/100)."</td>
    </tr>";
}
echo "<table>";
print_r($arr2);
echo "</table>";




?>
<div class=toTest>


<input type=text class=inpu placeholder="input" />
</div></div>
