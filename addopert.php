<?php 
session_start();
require_once("conf.php");
require_once("header.php");
echo "<title>".(isset($_GET['change'])?'Изменить':'Добавить')."</title>";
echo "<div>";

if (($_POST['add'])){
    $ido=get_post($id,'ido');
    $idp=get_post($id,'idp');
    $typ=get_post($id,'typ');
    $plac=get_post($id,'plac');
    $sum=$_POST['sum']*100;
    $dat=$_POST['dat'];
    $idpp=get_post($id,'idp2');
    if ($ido){
        $q="update oper set Dat='".$dat."', SUMO= ".$sum." , TYPE= '".$typ."',
         IDP= ".$idp." , IDPP= ".$idpp." , Place= '".$plac."' where IDO=".$ido."";
         $raw=mysqli_query($id,"select IDP, upd, SUMP from prod where IDP=".$idp);
         $wa = mysqli_fetch_array($raw);
         if ($wa['upd']<=$dat){
            $raw3=mysqli_query($id,"select SUMO from oper where IDO=".$ido);
            $wa3=mysqli_fetch_array($raw3);
            $qq="update prod set upd='".$dat."', SUMP=SUMP";
                if($typ=='пополнение'){
                    $qq=$qq."+".($sum-$wa3['SUMO']);
                }elseif($typ=='между' or $typ=='трата'){
                    $qq=$qq."-".($sum-$wa3['SUMO']);
                }
                $qq=$qq." where IDP=".$idp;
                $raw2=mysqli_query($id,$qq);
            }

    }else{
        $raw=mysqli_query($id,"select IDP, upd from prod where IDP=".$idp);
        $wa = mysqli_fetch_array($raw);
        if ($wa['upd']<=$dat){
                $qq="update prod set upd='".$dat."', SUMP=SUMP";
                if($typ=='пополнение'){
                    $qq=$qq."+".$sum;
                }elseif($typ=='между' or $typ=='трата'){
                    $qq=$qq."-".$sum;
                    
                }
                $qq=$qq." where IDP=".$idp;
                $raw2=mysqli_query($id,$qq);
        }
        
        if($idpp!='null'){
                $raw=mysqli_query($id,"select IDP, upd from prod where IDP=".$idpp);
                $wa = mysqli_fetch_array($raw);
                if ($wa['upd']<=$dat){
                    mysqli_query($id,"update prod set upd='".$dat."', SUMP=SUMP+".$sum." where IDP=".$idpp);
                }
            }
        $q="insert into oper value (null,'".$dat."',".$sum.",'".$typ."',".$idp.",".$idpp.",'".$plac."')";
    }
   //echo $q;
   $a=mysqli_query($id,$q) or die ("Ошибка при выполнении запроса: " .mysqli_error ($id));
   

    echo "<script> window.location.replace('opers.php'); </script>";
} 

if ($_POST['add2'] and $_POST['tname']!=''){
    $nam=get_post($id,'tname');
    $cost=$_POST['cost']*100;
    $ido=$_POST['ido'];
    $q="insert into tovar value(null,'".$nam."',".$cost.",".$ido.")";
    // echo $q;
    mysqli_query($id,$q) or die('Error:'.mysqli_error($id));
    echo "<script> window.location.replace('".$_SERVER['PHP_SELF']."?change=".$ido."'); </script>";
}


if ($_POST['sav'])                            //сохранение изменений
{
    $idt=$_POST['idt2'];
    $nam=get_post($id,'tname2');
    $co=$_POST['cost2']*100;

    $q1="update tovar set NameT='".$nam."', Cost='".$co."' where IDT='".$idt."'";
    //echo $q1;
    mysqli_query($id, $q1);
}



if (isset($_SESSION['access'])==1)
{
    
   
   echo "<h1 align=center>".(isset($_GET['change'])?'Изменить':'Добавить')." операцию и товары</h1>";

    // $q1="select man.IDM, NameF, bank.IDB, NameB, prod.IDP, NameP from man join prod on prod.IBM=man.IDM join bank on prod.IDB=bank.IDB ";
    //     $r1 = mysqli_query($id, $q1) or die("Ошибка выполнения запроса: " . mysqli_error($id));
    //     $arr=array();
    //     while ($ro1 = mysqli_fetch_array($r1)){
    //         $arr[$ro1['IDM']]
    //     }
    $ar=array();
    $r1=mysqli_query($id,"select IDM, NameF from man");
    while ($ro1 = mysqli_fetch_array($r1)){
        $ar[$ro1['IDM']]=array(
            'name'=>$ro1['NameF'],
            'banks'=>array()
        );
        $r2=mysqli_query($id,"select bank.IDB, NameB 
            from prod join bank on prod.IDB=bank.IDB 
            where prod.IDM=".$ro1['IDM']);
        while($ro2=mysqli_fetch_array($r2)){
            $ar[$ro1['IDM']]['banks'][$ro2['IDB']]=array(
                'name'=>$ro2['NameB'],
                'prods'=>array(),
                'id'=>$ro2['IDB']
            );
            $r3=mysqli_query($id,"select IDP, NameP, SUMP 
                from prod  
                where IDM=".$ro1['IDM']." and IDB=".$ro2['IDB']);
            while($ro3=mysqli_fetch_array($r3)){
                $ar[$ro1['IDM']]['banks'][$ro2['IDB']]['prods'][$ro3['IDP']]=array(
                    'name'=>$ro3['NameP'],
                    'sum'=>$ro3['SUMP'],
                    'id'=>$ro3['IDP']
                );
            }
        }
    }
    $json=json_encode($ar);





    if(isset($_GET['change'])==1){
        $ra=mysqli_query($id,"select IDM, IDB, prod.IDP, TYPE, Place, SUMO, Dat, IDPP from prod join oper on oper.IDP=prod.IDP where IDO=".$_GET['change']);
        $w = mysqli_fetch_array($ra);
        $ar2=array('IDB'=>$w['IDB'],'IDM'=>$w['IDM'],'IDP'=>$w['IDP']); 
        $plac=$w['Place'];
        $su=$w['SUMO'];
        $dat=$w['Dat'];
        
    }
    $json2=json_encode($ar2);
    if($w['IDPP']){
        $rrr=mysqli_query($id,"select IDM,IDB from prod where IDP=".$w['IDPP']);
        $w2 = mysqli_fetch_array($rrr);
        $ar3=array('IDB'=>$w2['IDB'],'IDM'=>$w2['IDM'],'IDP'=>$w['IDPP']);
    }
    $json3=json_encode($ar3);
    
    
    echo "<form action='addopert.php".(isset($_GET['change'])?'?change='.$_GET['change']:'')."' method=post>";
    // oper (IDO, Dat, SUMO, TYPE, IDP, IDPP)
    
    echo "<div style='display:flex; justify-content:space-evenly;'>";
    if ($_SESSION['access']==1){
        $result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));
        echo "<div>Владелец <select name=idman >";
        echo "<option value='---'>---</option>";
        while ($row2 = mysqli_fetch_array($result2))
        {if ($row2['IDM']==$idmf1)echo "<option value=".$row2['IDM']." selected>".$row2['NameF']."</option>";
        else echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
        echo "</select></div>";
    }else{
        echo "<input name=idman type=hidden value='".$_SESSION['id_user']."'>";
    }
    echo "<div>Банк <select name=idb><option value='---'>---</option></select></div>";
    echo "<div>Продукт <select name=idp><option value='---'>---</option></select></div>";
    echo "<div>Тип <select name=typ >";
        echo "<option value='---'>---</option>";
        echo "<option value='трата'";
        if($w[TYPE]=='трата'){
            echo " selected ";
        }
        echo ">трата</option>";
        echo "<option value='между'";
        if($w[TYPE]=='между'){
            echo " selected ";
        }
        echo ">между</option>";
        echo "<option value='пополнение'";
        if($w[TYPE]=='пополнение'){
            echo " selected ";
        }
        echo ">пополнение</option>";
    echo "</select></div>";

    echo "</div><br><div style='display:flex; justify-content:space-evenly;'>";
    echo "<div>Место <input type=text value='".$plac."' name=plac></div>";
    echo "<div>Сумма<input type=numder value='".($su/100)."' name=sum></div>";
    echo "<div>Дата <input type=date value='".$dat."' name=dat></div>";
    echo "</div>";
    echo "<br><div class='ins hid' id=mez>";
    $result2=mysqli_query($id,"select * from man ") or die ("Ошибка при выполнении запроса: " .mysql_error ($id));
    echo "<div>Получатель <select name=idman2 >";
        echo "<option value='---'>---</option>";
        while ($row2 = mysqli_fetch_array($result2))
        {echo "<option value=".$row2['IDM'].">".$row2['NameF']."</option>";}
        echo "</select></div>";
        echo "<div>Банк <select name=idb2><option value='---'>---</option></select></div>";
    echo "<div>Продукт <select name=idp2><option value='null'>---</option></select></div>";
    echo "</div><br>";
    echo "<input type='submit' value='Сохранить' name=add class='inpuB small inpuBB'>";
    
    
    
    if(isset($_GET['change'])==1){
        echo "<input type=hidden name=ido value='".$_GET['change']."'>";
        echo "<h2>Детали</h2>";
        $quee="select IDT,NameT,Cost from tovar where IDO=".$_GET['change'];
        $rese=mysqli_query($id,$quee)or die(" Ошибка выполнения запроса".mysqli_error($id));
    echo "<div class=height style='max-height:50vh'><table class=tab1>
    <thead>
    <th></th>
    <th>Название</th>
    <th>Цена</th>
    <th></th>
    </thead>";
   
    while ($row2 = mysqli_fetch_array($rese)) {
        echo "<tr>
        <td><input type=radio name='rad' value='".$row2['IDT']."'></td>
            <td>{$row2['NameT']}</td>
          <td>".($row2['Cost']/100)."</td>
          <td>
             <a href='?change={$_GET['change']}&delete_idT={$row2['IDT']}' class='inpuB small inpuBB'>
                      <i class='bi bi-trash'></i></a>
          </td>
        
        </tr>";
    }
    echo "</table></div>";
    
    echo "<br><div style='display:flex; justify-content:space-evenly;'>";
    echo "<div>Название <input type=text value='".$namt."' name=tname></div>";
    echo "<div>Цена <input type=numder value='".($co/100)."' name=cost></div>";
    echo "<input type='submit' value='Добавить' name=add2 class='inpuB small inpuBB'></div><br>";
    echo "<input type='submit' value='Изменить' name=chg class='inpuB small inpuBB'> ";
    if (($_POST['rad']) && ($_POST['chg']))	{
        $q="select * from tovar where IDT=".$_POST['rad'];
        $res=mysqli_query($id,$q);
        $row = mysqli_fetch_array($res);
        echo "<br><div style='display:flex; justify-content:space-evenly;'> <b>Изменить:</b>";
        echo "<div>Название <input type=text value='".$row['NameT']."' name=tname2></div>";
        echo "<div>Цена <input type=numder value='".($row['Cost']/100)."' name=cost2></div>";
        echo "<input name=idt2 value=".$row['IDT']." type=hidden>";
        echo "<input type='submit' value='Сохранить' name=sav class='inpuB small inpuBB'></div>";


    }





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

<script>
    

    let arr=<?php echo $json; ?>;
    let man=<?php echo $_SESSION['id_user']; ?>;
    let sel=<?php echo $json2; ?>;
    let sel2=<?php echo $json3; ?>;
    let mans=document.querySelector('select[name=idman]');
    let banks=document.querySelector('select[name=idb]');
    let pr=document.querySelector('select[name=idp]');
    let mans2=document.querySelector('select[name=idman2]');
    let banks2=document.querySelector('select[name=idb2]');
    let pr2=document.querySelector('select[name=idp2]');
    let t=document.querySelector('select[name=typ]');
    let mex=document.querySelector('#mez');
    let tval=t.value;
    let b;
    setT();
    if(sel){
        man=sel.IDM;
        if(mans){
            mans.querySelector('option[value="'+man+'"]').selected=true;
        }
        b=sel.IDB;
        setP();
    }
    if (mans){
        mans.addEventListener("change", function(){
            man =this.value;
            setB();
        })
    }
    function setB(){
        banks.innerHTML = '<option value="---">---</option>';
        if(arr[man]){
            Object.values(arr[man].banks).forEach(bank=>{
                const newOption = document.createElement('option');
                newOption.value = bank.id;
                newOption.textContent = bank.name;
                if(sel&&bank.id==sel.IDB){
                    newOption.selected=true;
                }
                banks.appendChild(newOption);
                
            })
        }
        
    }
    setB();
    banks.addEventListener("change", function(){
        b=this.value;
        setP();
    })
    function setP(){
        pr.innerHTML = '<option value="---">---</option>';
        if(arr[man].banks[b]){
            Object.values(arr[man].banks[b].prods).forEach(prod=>{
                const newOption = document.createElement('option');
                newOption.value = prod.id;
                newOption.textContent = prod.name;
                if(sel&&prod.id==sel.IDP){
                    newOption.selected=true;
                }
                pr.appendChild(newOption);
            })
        }
    }
    t.addEventListener("change", function(){
        tval=this.value;
        setT();
    })
    function setT(){
        if(tval=='между'){
            mez.classList.remove('hid');
        }
        else{
            mez.classList.add('hid');
        }
    }
    let man2;
    let b2;
    if(sel2){
        man2=sel2.IDM;
        b2=sel2.IDB;
        mans2.querySelector('option[value="'+man2+'"]').selected=true;
        setP2();
    }
    
    mans2.addEventListener("change", function(){
        man2 =this.value;
        setB2();
    })
    function setB2(){
        banks2.innerHTML = '<option value="---">---</option>';
        if(arr[man2]){
            Object.values(arr[man2].banks).forEach(bank=>{
                const newOption = document.createElement('option');
                newOption.value = bank.id;
                newOption.textContent = bank.name;
                if(sel2&&bank.id==sel2.IDB){
                    newOption.selected=true;
                }
                banks2.appendChild(newOption);
                
            })
        }
        
    }
    setB2();
    banks2.addEventListener("change", function(){
        b2=this.value;
        setP2();
    })
    function setP2(){
        pr2.innerHTML = '<option value="---">---</option>';
        if(arr[man2].banks[b2]){
            Object.values(arr[man2].banks[b2].prods).forEach(prod=>{
                const newOption = document.createElement('option');
                newOption.value = prod.id;
                newOption.textContent = prod.name;
                if(sel2&&prod.id==sel2.IDP){
                    newOption.selected=true;
                }
                pr2.appendChild(newOption);
            })
        }
    }

    

</script>
