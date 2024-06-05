<?php
session_start ();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='./styles/test.css'>
    <!-- ИКОНКИ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
<div class='head'>
    <div>
        <a href='banks.php'>Банки</a>
        <?php 
        echo "<a href='profpag.php'>".$_SESSION['login']."</a>";
        ?>
    </div>
    <div >
        <a href='index.php?d=2'>
        <?php 
        if (isset($_SESSION['access'])==1)
        {
        echo "Выйти";
        }else{
            echo "Войти";
        }
        ?>  
        </a>
    </div>
</div>
<?php  

if (isset($_SESSION['access'])==1)
{
echo "<div class='sidebar'>";
    if ($_SESSION['access']==1){
        echo "<div>
            <a href='mans.php'>Пользователи</a>
        </div>
        ";
    }

echo "<div>
        <a href='banks.php'>Банки</a>
    </div>";
echo "<div>
        <a href='prods.php'>Продукты</a>
    </div>";
    
echo "<div>
        <a href='opers.php'>Краткая история</a>
    </div> "; 
echo "<div>
        <a href='tovars.php'>Детализация</a>
    </div> "; 
echo "<div>
        <a href='addopert.php'>Добавить операцию</a>
    </div> "; 
echo "<div>
        <a href='zaps.php'>Запросы</a>
    </div> "; 
    if ($_SESSION['access']==1){
        echo "<div>
            <a href='ques.php'>SQL</a>
        </div>
        ";
    }

    


// echo " <div>_____</div>";
 
// echo "<div>
//         <a href='page.php'>Сайт</a>
//     </div>";
// echo "   <div>
//         <a href='que.php'>Запросы</a>
//     </div>" ;
//     echo "   <div>
//         <a href='test.php'>тест</a>
//     </div>" ;
    

    echo "</div>";

} 
?>  


</body>