<?php 
session_start ();
require_once("conf.php");
require_once("header.php");


if ($_POST['sav'])                            //сохранение изменений
{
    $idm1=$_POST['IDM1'];
    $naml=get_post($id,'namL1');
    $namf=get_post($id,'namF1');
    $namo=get_post($id,'namO1');
    $bday=$_POST['bday1'];
    $g=$_POST['gen1'];
    $l=get_post($id,'log1');
    $pas=$_POST['pass1'];
    $access=$_POST['access1'];
    $lpas=$_POST['lpas'];

    if ($pas!=$lpas){
        $pas=md5($pas);
    }


    $q1="update man set NameL='".$naml."', NameF='".$namf."', 
    NameO='".$namo."', BDay='".$bday."', Gender='".$g."', 
    login='".$l."', password='".$pas."',
    access='".$access."' where IDM='".$idm1."'";
    //echo $q1;
    mysqli_query($id, $q1);
}

if (($_POST['add'])){
    $naml=get_post($id,'namL');
    $namf=get_post($id,'namF');
    $namo=get_post($id,'namO');
    $bday=$_POST['bday'];
    $g=$_POST['gen'];
    $l=get_post($id,'log');
    $pas=md5($_POST['pass']);
    $access=$_POST['access'];

   $q="insert into man value(null,'".$naml."','".$namf."','".$namo."','".$bday."',
   '".$g."','".$l."','".$pas."','".$access."');";
   //echo $q;
   mysqli_query($id,$q);
   echo "<script> window.location.replace('".$_SERVER['PHP_SELF']."'); </script>";
} 

if (($_POST['rad']) && ($_POST['del']))        //удаление выбранной информации
{
 $q="delete from man where IDM='".$_POST['rad']."'";
//echo $q;
 mysqli_query($id,$q) or die('Error:'.mysqli_error($id));
}


echo "<title>Пользователи</title>";
if($_SESSION['access']==1){
    echo "<div>";
    echo "<h1 align=center>Список пользователей</h1>";
    
    $query="select * from man";
    $result=mysqli_query($id,$query)or die(" Ошибка выполнения запроса".mysqli_error($id));
    echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
    echo "<div class=height style='max-height: 40vh'>";
    echo "<table class=tab1 border=0 align=center>";
    echo "<thead>
    <tr><th></th>
    <th>Фамилия</th><th>Имя</th><th>Отчество</th>
    <th>День Рождения</th><th>Пол</th>
    <th>Логин</th><th>Хэш-пароль</th><th>Доступ</th><th>Количество<br>счетов</th></tr></thead>";
    while($row=mysqli_fetch_array($result)){
        $q="select count(*) as c from prod where IDM=".$row['IDM'].";";
        $res=mysqli_query($id,$q)or die(" Ошибка выполнения запроса".mysqli_error($id));
        $c=mysqli_fetch_array($res);
        echo "<tr>";
        echo "<td align=right><input type=radio name='rad' value='".$row['IDM']."'>";
        echo "<td>".$row["NameL"]."</td> <td>".$row["NameF"]."</td><td>".$row["NameO"]."</td>
         <td>".$row["BDay"]."</td> <td>".$row["Gender"]."</td> 
         <td>".$row["login"]."</td> <td>".$row["password"]."</td><td>".$row['access']."</td>
         <td>".$c['c']."</td>";
        echo "</tr>";
    }
   
    echo "</table></div>";

    echo "<table class=tab1 border=0 align=center>";
    echo "<thead>
    <tr><th></th>
    <th>Фамилия</th><th>Имя</th><th>Отчество</th>
    <th>День Рождения</th><th>Пол</th>
    <th>Логин</th><th>Хэш-пароль</th><th>Доступ</th></tr></thead>";
    echo "<tr>";
    echo "<td><input type=submit name=add value='Добавить' class='inpuB small inpuBB'></td>";
    echo "<td><input type=text name=namL value=''></td>";
    echo "<td><input type=text name=namF value=''></td>";
    echo "<td><input type=text name=namO value=''></td>";
    echo "<td><input type=date name=bday value=''></td>";
    echo "<td><select name=gen>
    <option value='М'>М</option>
    <option  value='Ж' >Ж</option>
    </select></td>";
    echo "<td><input type=text name=log value=''></td>";
    echo "<td><input type=text name=pass value=''></td>";
    echo "<td><input type=text name=access value=''></td>";
    echo "</tr>";
    echo "</table><br>";

    echo "<div style='display:flex; justify-content:space-evenly;'>";
    echo "<div><input type=submit name=del value='Удалить' class='inpuB small inpuBB'>  выбранный аккаунт</div>";
    echo "<div><input type=submit name=upd value='Изменить' class='inpuB small inpuBB'> выбранный аккаунт</div>";		 
    echo "</div>";


    if (($_POST['rad']) && ($_POST['upd']))			//изменение выбранной информации
    {
       echo "<br><hr><b>Введите новые</b> (автоматическое хэширование)<div class=ins>";
       echo "";			  
     $q="select * from man where IDM=".$_POST['rad'];
     $res=mysqli_query($id,$q);
       $row = mysqli_fetch_array($res);
       echo "<table class=tab1 border=0 align=center>";
       echo "<thead>
       <tr><th></th>
       <th>Фамилия</th><th>Имя</th><th>Отчество</th>
       <th>День Рождения</th><th>Пол</th>
       <th>Логин</th><th>Хэш-пароль</th><th>Доступ</th></tr></thead>";
       echo "<tr>";
       echo "<td><input type=submit name=sav value='Сохранить' class='inpuB small inpuBB'></td>";
       echo "<td><input type=text name=namL1 value='".$row['NameL']."'></td>";
       echo "<td><input type=text name=namF1 value='".$row['NameF']."'></td>";
       echo "<td><input type=text name=namO1 value='".$row['NameO']."'></td>";
       echo "<td><input type=date name=bday1 value='".$row['BDay']."'></td>";
       echo "<td><select name=gen1>
       <option value='М' >М</option>";
       if ($row['Gender']=='Ж'){
        echo "<option  value='Ж' selected>Ж</option>";
       }else{
       echo "<option  value='Ж'>Ж</option>";}
       echo "</select></td>";
       echo "<td><input type=text name=log1 value='".$row['login']."'></td>";
       echo "<td><input type=text name=pass1 value='".$row['password']."'></td>";
       echo "<td><input type=text name=access1 value='".$row['access']."'></td>";
       echo "</tr>";
    echo "</table><br>";
     echo "<input type=hidden name=IDM1 value='".$row['IDM']."'>";
     echo "<input type=hidden name=lpas value='".$row['password']."'>";
    
    echo "</div>";
    }


    
    echo "</form>";
    echo "</div>";
}else{
    echo "<h1>Доступ на эту страницу закрыт.</h1>";
}

function get_post($i,$var)
{
return mysqli_real_escape_string($i,$_POST[$var]);
}
?>