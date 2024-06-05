<?php 
session_start();
require_once("conf.php");
require_once("header.php");

echo "<title>Запросы</title>";

if (isset($_SESSION['access']) == 1) {
    echo "<div>";
    echo "<h1>Запросы</h1>";
    echo "<div style='display:flex; justify-content:space-evenly;'>";

    // Формируем таблицу с запросами
    echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
    echo "<table border=0 style='width:35vw' align=center>";
    
    // Массив с запросами
    $queries = array(
        1 => array(
            "label" => "Вывести продукты, на которых больше чем ",
            "query" => "SELECT NameB, NameP, SUMP, NameF FROM bank ,prod, man where bank.IDB=prod.IDB and prod.IDM=man.IDM",
            "param" => " and SUMP > ",
            "input" => "<input type=text name='val1' size='10' value=''>"
        ),
        2 => array(
            "label" => "Вывести полную сумму",
            "query" => "SELECT SUM(SUMP) FROM prod",
            "input" => ""
        ),
        3 => array(
            "label" => "Вывести продукты, название которых содержит",
            "query" => "SELECT NameB, NameP, SUMP, NameF FROM  bank ,prod, man where bank.IDB=prod.IDB and prod.IDM=man.IDM",
            "param" => " and NameP LIKE ",
            "input" => "<input type=text name='val3' size='10' value=''>"
        ),
        4 => array(
            "label" => "Вывести доходы по месяцам года",
            "query" => "SELECT MONTH(Dat), YEAR(Dat), SUM(SUMO) AS Sum FROM oper , prod, man where oper.IDP=prod.IDP and man.IDM=prod.IDM and TYPE='пополнение'",
            "param" => " and YEAR(Dat)=",
            "input" => "<input type=number min=1900 max=2100 name='val4' size='10' value=''>",
            "group" => " GROUP BY YEAR(Dat), MONTH(Dat)"
        ),
        5 => array(
            "label" => "Вывести траты по месяцам года",
            "query" => "SELECT MONTH(Dat), YEAR(Dat), SUM(SUMO) AS Sum FROM oper, prod where oper.IDP=prod.IDP and TYPE='трата'",
            "param" => " and YEAR(Dat)=",
            "input" => "<input type=number min=1900 max=2100 name='val5' size='10' value=''>",
            "group" => " GROUP BY YEAR(Dat), MONTH(Dat)"
        ),
        6 => array(
            "label" => "Вывести переводы между счетов по месяцам года",
            "query" => "SELECT MONTH(Dat), YEAR(Dat), SUM(SUMO) AS Sum FROM oper, prod where oper.IDP=prod.IDP and TYPE='между'",
            "param" => " and YEAR(Dat)=",
            "input" => "<input type=number min=1900 max=2100 name='val6' size='10' value=''>",
            "group" => " GROUP BY YEAR(Dat), MONTH(Dat)"
        ),
        7 => array(
            "label" => "Вывести максимальный расход по месяцам года",
            "query" => "SELECT MAX(SUMO), Dat, Place FROM oper, prod where oper.IDP=prod.IDP  and TYPE='трата'",
            "param" => " and YEAR(Dat)=",
            "input" => "<input type=number min=1900 max=2100 name='val7' size='10' value=''>",
            "group" => " GROUP BY YEAR(Dat), MONTH(Dat)"
        ),
        8 => array(
            "label" => "Вывести средний размер расхода по месяцам года",
            "query" => "SELECT MONTH(Dat), YEAR(Dat), AVG(SUMO) AS avg FROM oper, prod where oper.IDP=prod.IDP and TYPE='трата'",
            "param" => " and YEAR(Dat)= ",
            "input" => "<input type=number min=1900 max=2100 name='val8' size='10' value=''>",
            "group" => " GROUP BY YEAR(Dat), MONTH(Dat)"
        ),
        9 => array(
            "label" => "Количество операций по месту",
            "query" => "SELECT Place, COUNT(*), SUM(SUMO) AS sum FROM oper, prod where oper.IDP=prod.IDP",
            "param" => " and Place like ",
            "input" => "<input type=text name='val9' size='10' value=''>",
            "group" => " GROUP BY Place"
        ),
        10 => array(
            "label" => "Количество и сумма операций между счетов по получателям",
            "query" => "SELECT NameF, COUNT(*), SUM(SUMO) AS sum FROM oper, prod, man, prod as prod2 where oper.IDPP=prod2.IDP and oper.IDP=prod.IDP and man.IDM=prod2.IDM ",
            "param" => " and Place like ", 
            "group" => " GROUP BY NameF"
        ),
        11 => array(
            "label" => "Вывести человека с максимальным доходом по месяцам года",
            "query" => "SELECT y, m, summ, NameF FROM (select YEAR(Dat) as y, MONTH(Dat) as m, NameF, SUM(SUMO) as summ  from oper, prod, man where oper.IDP=prod.IDP and man.IDM=prod.IDM and TYPE='пополнение' GROUP BY NameF, YEAR(Dat), MONTH(Dat)) as a where summ = (
                SELECT MAX(summ)
                FROM (
                    SELECT SUM(SUMO) AS summ
                    FROM oper 
                    JOIN prod ON oper.IDP = prod.IDP 
                    JOIN man ON man.IDM = prod.IDM 
                    WHERE TYPE = 'пополнение' 
                    AND YEAR(Dat) =", 
                    "query2"=>" AND MONTH(Dat) =", 
                    "query3" =>" GROUP BY NameF
                ) AS b
            )  ",
            "param" => " and y = ",
            "2param" => " and m = ",
            "input" => "<select name='val11' >
            <option value=2024>2024</option>
            <option value=2023>2023</option>
            </select>
            <select name='2val11' >
            <option value=1>январь</option>
            <option value=2>Февраль</option>
            <option value=3>Март</option>
            <option value=4>Апрель</option>
            <option value=5>Май</option>
            <option value=6>Июнь</option>
            <option value=7>Июль</option>
            <option value=8>Август</option>
            <option value=9>Сентябрь</option>
            <option value=10>Октябрь</option>
            <option value=11>Ноябрь</option>
            <option value=12>Декабрь</option>
            </select>",
            "def"=>" where y = YEAR(NOW()) and m = MONTH(NOW())"
        ),
        12 => array(
            "label" => "Вывести человека с максимальным расходом по месяцам года",
            "query" => "SELECT y, m, summ, NameF FROM (select YEAR(Dat) as y, MONTH(Dat) as m, NameF, SUM(SUMO) as summ  from oper, prod, man where oper.IDP=prod.IDP and man.IDM=prod.IDM and TYPE='трата' GROUP BY NameF, YEAR(Dat), MONTH(Dat)) as a where summ = (
                SELECT MAX(summ)
                FROM (
                    SELECT SUM(SUMO) AS summ
                    FROM oper 
                    JOIN prod ON oper.IDP = prod.IDP 
                    JOIN man ON man.IDM = prod.IDM 
                    WHERE TYPE = 'трата' 
                    AND YEAR(Dat) =", 
                    "query2"=>" AND MONTH(Dat) =", 
                    "query3" =>" GROUP BY NameF
                ) AS b
            )  ",
            "param" => " and y = ",
            "2param" => " and m = ",
            "input" => "<select name='val12' >
            <option value=2024>2024</option>
            <option value=2023>2023</option>
            </select>
            <select name='2val12' >
            <option value=1>январь</option>
            <option value=2>Февраль</option>
            <option value=3>Март</option>
            <option value=4>Апрель</option>
            <option value=5>Май</option>
            <option value=6>Июнь</option>
            <option value=7>Июль</option>
            <option value=8>Август</option>
            <option value=9>Сентябрь</option>
            <option value=10>Октябрь</option>
            <option value=11>Ноябрь</option>
            <option value=12>Декабрь</option>
            </select>",
            "def"=>" where y = YEAR(NOW()) and m = MONTH(NOW())"
        )

    );

    foreach ($queries as $key => $query) {
        echo "<tr>";
        echo "<td width=10% align=right><input type=radio name='rad' value='{$key}'></td>";
        echo "<td width=70%>{$query['label']}</td>";
        echo "<td width=20%>{$query['input']}</td>";   
        echo isset($query['param']) ? "<input type=hidden name='param{$key}' value='{$query['param']}'>" : "";
        echo isset($query['group']) ? "<input type=hidden name='group{$key}' value='{$query['group']}'>" : "";
        echo "</tr>";
    }

    echo "<tr>";
    echo "<td colspan=3 align='center'><input type=submit name='do' value='Выполнить' class='inpuB small inpuBB'></td>";
    echo "</tr>";
    echo "</table></form>";
    
    if (isset($_POST['rad']) && isset($_POST['do'])) {
        $rad = $_POST['rad'];
        $query = $queries[$rad]['query'];
        $param = get_post($id, 'param' . $rad);
        $val = get_post($id, 'val' . $rad);
        $group = get_post($id, 'group' . $rad);

        if ($param && $val) {
            if ($rad == 3 or $rad==9) {
                $query .= $queries[$rad]['param'] . "'%" . $val . "%'";
            } elseif($rad == 1){
                $query .= $queries[$rad]['param'] . ($val*100);
            }elseif($rad==11 or $rad==12){
                $val2 = get_post($id, '2val' . $rad);
                $query.= $val .$queries[$rad]['query2']. $val2. $queries[$rad]['query3']. $queries[$rad]['param'] . $val .$queries[$rad]['2param'].$val2;
            }
            else {
                $query .= $queries[$rad]['param'] . $val;
            }
        }

        if ($_SESSION['access'] == 2) {
                $query .= " AND prod.IDM=" . $_SESSION['id_user'];
        }

        if ($group) {
            $query .= $group;
        }

        echo "<div style='width:50%'>";
        echo "Результаты запроса: $query;<br>";

        $res = mysqli_query($id, $query) or die('Error: ' . mysqli_error($id));
        $n = mysqli_num_rows($res);

        if ($res) {
            echo "запрос вернул " . $n . " запись(и,ей)
            <div class='height'><table class='tab1 height' style='max-height:40vh; '>";
            echo "<thead><tr>";

            // Получение названий столбцов
            $fields = mysqli_fetch_fields($res);
            $sumFields = array();
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
                if (stripos($field->name, 'sum') !== false) {
                    $sumFields[] = $field->name;
                }
            }
            echo "</tr></thead>";

            // Вывод данных
            while ($row = mysqli_fetch_assoc($res)) {
                echo "<tr>";
                foreach ($row as $key => $cell) {
                    if (in_array($key, $sumFields)) {
                        echo "<td>" . htmlspecialchars($cell / 100) . "</td>";
                    } else {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table></div></div></div>";
        }
    }

    echo "</div></div>";
} else {
    echo "<h1>Доступ на эту страницу закрыт.</h1>";
}

function get_post($i, $var) {
    return isset($_POST[$var]) ? mysqli_real_escape_string($i, $_POST[$var]) : '';
}

mysqli_close($id); 
?>
