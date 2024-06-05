<?php
   require_once('conf.php');
   require_once("header.php");
if (($_POST['rad']) && ($_POST['do']))
{
           $query=get_post($id,'query'.$_POST['rad']);
           $par=get_post($id,'par'.$_POST['rad']);
		   $val=get_post($id,'val'.$_POST['rad']);

           if ($val)
           {
             if($_POST['rad']==3) $query=$query.$par."'%".$val."%'";
			                 else $query=$query.$par."".$val."";
           }
           $res=mysqli_query($id,$query);
           $n=mysqli_num_rows($res);
           $m=mysqli_num_fields($res);
           if ($_POST['rad']==2) $m1=100/$m; 		   
		    echo "<table border='2' align='center' width='50%'>";
            echo "<tr><td colspan='".$m."'><b><i>".$query."</i></b></td></tr>";
			if ($n<>0) 
		   {
           for($i=0; $i<$n; $i++)
            {
             $row=mysqli_fetch_row($res);
             echo "<tr>";
             for ($j=0; $j<$m; $j++)
               echo "<td>".$row[$j]."</td>";
             echo "</tr>";
             }
			 echo "<tr><td colspan='".$m."'>запрос вернул ".$n." запись(и,ей)</td></tr>";
		    }
		    else echo "<tr><td colspan='".$m."'>запрос вернул 0 записей</td></tr>";
			echo "</table>";
}

     echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
        echo "<br><br>";
        echo "<table border=0 width=50% align=center>";
              echo "<tr><th colspan='3' >Запросы</th></tr>";
              echo "<tr>";
              echo "<td width=10% align=right><input type=radio name='rad' value='1'>";
              echo "<td width=80%>1. Вывести продукты, на которых больше чем</td>";
              echo "<td width=10%><input type=text name='val1' size='10' value=''></td>";   
              echo "<input type=hidden name='query1'  value='select NameB,NameP, SUMP, NameF from bank join prod on bank.IDB=prod.IDB join man on prod.IDM=man.IDM'>";
              echo "<input type=hidden name='par1'  value=' where SUMP > '>";
              echo "</tr>";

              echo "<tr>";
              echo "<td width=10% align=right><input type=radio name='rad' value='2'>";
              echo "<td width=80%>2. Вывести полную сумму</td>";
              echo "<input type=hidden name='query2'  value='select sum(SUMP) from prod'>";
              echo "</tr>";

              echo "<tr>";
              echo "<td width=10% align=right><input type=radio name='rad' value='3'>";
              echo "<td width=80%>3. Вывести продукты, название которых содержит  </td>";
              echo "<td width=10%><input type=text name='val3' size='10' value=''></td>";   
              echo "<input type=hidden name='query3'  value='select NameB,NameP, SUMP, NameF from bank join prod on bank.IDB=prod.IDB join man on prod.IDM=man.IDM '>";
              echo "<input type=hidden name='par3'  value=' where NameP like '>";
              echo "</tr>";

              echo "<tr>";
              echo "<td colspan=3 align='center'><input type=submit name='do' value='Выполнить'>";
              echo "</tr>";
        echo "</table>";

			  echo "<br><br>";
			  echo "<table border=0 width=40% align=center><tr>";
              echo "<td>Вывести продукты банка </td>";
			  $result1=mysqli_query($id,"select * from bank ") or die ("Ошибка при выполнении запроса: ".mysqli_error($id)); 
              echo "<td><select name=idb>";
                while ($row1 = mysqli_fetch_array($result1)) 
                { echo "<option value=".$row1['IDB'].">".$row1['NameB']."</option>";}
              echo "</select></td>";   
              echo "<td><input type=submit name='do4' value='Выполнить'></td></tr>";			  
            if ($_POST['do4'])
			  {	  
                $qu="select NameB, NameP, SUMP, NameF from bank join prod on bank.IDB=prod.IDB join man on prod.IDM=man.IDM where bank.IDB =".$_POST['idb'].';';
			    echo "<tr><td colspan=3 align=left><b>".$qu."</b></td></tr>";
			    $res=mysqli_query($id,$qu) or die ("Ошибка при выполнении запроса: ".mysqli_error($id)); ;
				while ($row = mysqli_fetch_array($res)) {
                    echo "<tr>";
                    echo "<td>".$row["NameB"]."</td> <td>".$row["NameP"]."</td><td>".$row["NameF"]."</td> <td>".$row["SUMP"]."</td>";
                    echo "</tr>";
                }
			//   echo "<tr><td colspan=3 align=left>".$row['named']."</td></tr>";
            }
			  echo "</table>";

   echo "</form>";

         mysqli_close($id); 
function get_post($i,$var)
{
return mysqli_real_escape_string($i,$_POST[$var]);
}
?> 

<a href='page.php' target='_self'> Назад</a>

