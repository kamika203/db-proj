<?php 
session_start ();
require_once("conf.php");

if (isset($_GET["d"])){
    if ($_GET["d"]==1 and $_POST['login'])
		{
            $result=mysqli_query ($id,"SELECT * FROM `man` 
			                      WHERE `login`='".get_post($id,'login')."';");
                                  $row_user=mysqli_fetch_array($result);
            
            if ($row_user['login']<>$_POST['login'])
                {
                    $message="Такого пользователя не существует.";
                }
            elseif($row_user['password']<>md5($_POST['pass']))
                {
                    $message="Неверный пароль.";
                }
            else{
                $_SESSION['login']=$_POST['login'];
				$_SESSION['pass']=$_POST['pass'];
				$_SESSION['access']=$row_user['access'];
				$_SESSION['id_user']=$row_user['IDM'];

                if ($_SESSION['access']==1) 
				{
 					header("Location: http://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\')."/banks.php");
				}
                  elseif ($_SESSION['access']==2) 
				{
					header("Location: http://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\')."/profpag.php");				
				}

            }


            }
    elseif ($_GET["d"]==2)
		{
			session_destroy();
			header("Location: http://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\')."/index.php");
		}

	}


function get_post($i,$var)
{
return mysqli_real_escape_string($i,$_POST[$var]);
}
require_once("header.php");
?>

<title>Вход</title>
<div></div>
<div class="login_form">

<form action="index.php?d=1" method=POST>
<div class=logtxt>Вход</div>

<div>
<?php
echo " $message";
?>
</div>
<input class=inpu type=text name="login" placeholder="Логин" autocomplete="off" ><br>
<input class=inpu type=password name="pass" placeholder="пароль"><br>
<br>
<input class=inpuB type=submit  value="Войти">
</form>



</div>
