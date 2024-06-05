<?php 
session_start ();
require_once("conf.php");
require_once("header.php");

echo "<title>Профиль</title>";
if (isset($_SESSION['access'])==1)
	{
        $result=mysqli_query ($id,"SELECT * FROM `man` 
        WHERE `login`='".$_SESSION['login']."';");
        $row_user=mysqli_fetch_array($result);
        if ($_SESSION['access']==2){
            $money=mysqli_query($id,"SELECT SUM(SUMP) AS sum FROM `prod`
            WHERE `IDM`=".$_SESSION['id_user'].";");
            $mon=mysqli_fetch_array($money);
            $prodsreq=mysqli_query($id,"SELECT COUNT(*) AS count FROM `prod`
            WHERE `IDM`=".$_SESSION['id_user'].";");
            $prod=mysqli_fetch_array($prodsreq);
            $banks=mysqli_query($id,"SELECT NameB, SUM(sump) as summ from prod join bank on bank.IDB=prod.IDB where prod.IDM=".$_SESSION['id_user']." GROUP BY NameB");
        }
echo "<div>
<h1 align=center>Профиль</h1><div>";

echo "<table class=tab1>";
echo "<tr><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>День рождения</th><th>пол</th></tr>";
echo "<tr><td>".$row_user['NameL']."</td>";
echo "<td>  ".$row_user['NameF']."</td>";
echo "<td>  ".$row_user['NameO']."</td>";
echo "<td>  ".$row_user['BDay']."</td>";
echo "<td>  ".$row_user['Gender']."</td></tr></table></div><br><hr>";

if ($_SESSION['access']==2){
    echo "<div><div>
    <div> Количество счетов: ".$prod['count']."</div></div><hr>
    Банки и суммы средств на них
    <table class=tab1><tr><th>Банк</th><th>Сумма</th></tr>";
    while($row=mysqli_fetch_array($banks)){
        echo "<tr>";
        echo "<td>".$row['NameB']."</td>";
        echo "<td>".($row['summ']/100)."</td>";
        echo "</tr>";
    }
    echo "<tr><td><h3>Всего</h3></td><td><h3>".($mon['sum']/100)."</h3></td></tr>";
    echo "</table>";
    echo "</div>";
}
echo "<h3>Для редактирования обратитесь к администратору</h3></div>";

    }
    else{
        echo "<h1>Доступ на эту страницу закрыт.</h1>";
    }
?>

