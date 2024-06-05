<?php 
session_start();
require_once("conf.php");
require_once("header.php");

echo "<title>SQL</title>";

if ($_SESSION['access'] == 1) {
    echo "<div>";
    echo "<h1>Свои запросы</h1>";

    // Форма для ввода запроса
    echo "<form method='post' action=''>";
    echo "<div class='height' style='max-height:70vh;'>";
    echo "<textarea name='qu' class='inpu'  style='width:100%; height:20%'></textarea>";
    echo "<input type='submit' value='Выполнить запрос' class='inpuB small inpuBB'>";
    echo "<div>";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $query = $_POST['qu'];
        
        // Экранирование запроса
        $safe_query = mysqli_real_escape_string($id, $query);

        // Выполнение запроса
        $result = mysqli_query($id, $safe_query);

        if ($result) {
            echo "<h2>Результаты запроса:$query;</h2>";
            echo "<div class='height' style='max-height:40vh;'>";
            echo "<table class='tab1'>";
            echo "<thead><tr>";

            // Получение названий столбцов
            $fields = mysqli_fetch_fields($result);
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr></thead>";

            // Вывод данных
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table></div>";
        } else {
            echo "<div class='alert alert-danger'> $query <br> Ошибка выполнения запроса: " . mysqli_error($id) . "</div>";
        }
    }
    echo "</div>";
    echo "</div>";
    
    echo "</form>";

    echo "<div>";
    echo "bank (IDB, NameB)<br>";
    echo "prod (IDP, NameP, SUMP, PROC, IDM, IDB, upd)<br>";
    echo "man (IDM, NameL, NameF, NameO, BDay, Gender, login, password, access)<br>";
    echo "oper (IDO, Dat, SUMO, TYPE, IDP, IDPP, Place)<br>";
    echo "tovar (IDT, NameT, Cost, IDO)<br>";
    echo "</div>";

    echo "</div>";

    // Обработка и выполнение запроса
    

} else {
    echo "<h1>Доступ на эту страницу закрыт.</h1>";
}

mysqli_close($id);
?>
