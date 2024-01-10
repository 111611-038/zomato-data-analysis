<!DOCTYPE html> 
<html lang="zh-Hant-TW">

<form method="get">
    <select name="choose_condition" required onchange="this.form.submit()">
        <option selected>請選擇</option>
        <optgroup label="搜尋餐廳">
            <option value="user_age">顧客年齡</option>
            <option value="sales_amount">價格</option>
            <option value="rating">評價</option>
            <option value="cuisine">餐點種類</option>
            <option value="city">所在城市</option>
        </optgroup>
        <optgroup label="修改訂單">
            <option value="insert">增加新訂單</option>
            <option value="update">更新訂單</option>
            <option value="delete">刪除訂單</option>
        </optgroup>
    </select>
</form>

<form method="get">
    <!--<input type="text" name="field" placeholder="請輸入欄位名稱"/>
    <input type="text" name="value" placeholder="請輸入欄位的值"/>-->
    <?php
    if ($_GET['choose_condition'] === 'user_age'){
        echo '<p>顧客年齡</p><input type="hidden" name="condition" value="user_age"/>
            <input type="number" name="min" min="0" max="100" step="1" placeholder="最小值" required/>
            <input type="number" name="max" min="0" max="100" step="1" placeholder="最大值" required/>';
    } else if ($_GET['choose_condition'] === 'sales_amount'){
        echo '<p>價格</p><input name="condition" value="sales_amount" style="display: none"/>
            <input type="number" name="min" min="0" step="1" placeholder="最小值" required/>
            <input type="number" name="max" min="0" step="1" placeholder="最大值" required/>';
    } else if ($_GET['choose_condition'] === 'rating'){
        echo '<p>評價</p><input name="condition" value="rating" style="display: none"/>
            <input type="number" name="min" min="0" max="5" step="0.1" placeholder="最小值" required/>
            <input type="number" name="max" min="0" max="5" step="0.1" placeholder="最大值" required/>';
    } else if ($_GET['choose_condition'] === 'cuisine'){
        echo '<p>餐點種類</p><input name="condition" value="cuisine" style="display: none"/>
            <input type="text" name="type" placeholder="請輸入種類" required/>';
    } else if ($_GET['choose_condition'] === 'city'){
        echo '<p>所在城市</p><input name="condition" value="city" style="display: none"/>
            <input type="text" name="city" placeholder="請輸入城市" required/>';
    } else if ($_GET['choose_condition'] === 'insert'){
        echo '<p>增加新訂單</p><input name="condition" value="insert" style="display: none"/>
            <input type="number" name="id" min="0" placeholder="請輸入訂單ID" required/>
            <input type="date" name="date" placeholder="請輸入日期" required/>
            <input type="number" name="user_id" min="0" placeholder="請輸入使用者ID" required/>
            <input type="number" name="r_id" min="0" placeholder="請輸入餐廳ID" required/>';
    } else if ($_GET['choose_condition'] === 'update'){
        echo '<p>更新訂單</p><input name="condition" value="update" style="display: none"/>
            <input type="number" name="id" min="0" placeholder="請輸入訂單ID" required/>
            <input type="date" name="date" placeholder="請輸入更新的日期" required/>';
    } else if ($_GET['choose_condition'] === 'delete'){
        echo '<p>刪除訂單</p><input name="condition" value="delete" style="display: none"/>
            <input type="number" name="id" min="0" placeholder="請輸入訂單ID" required/>';
    }
    ?>
    <button type="submit">確認</button>
</form>

<style>
  table{
    border:2px solid #000;
  }
  table tr:first-child {
    background-color: #ccc;
  }
  td{
    border:2px solid #000;
    padding:10px;
  }
</style>

<?php
    //ini_set("display_errors", "On");
    // 檢查變數有沒有傳進來
	// query必須用雙引號，字串記得加單引號

    // 根據不同的條件產生 query
    if ($_GET['condition'] === 'user_age'){
        $query = "SELECT DISTINCT r.name, r.city, r.rating, r.cuisine, r.address
                    FROM (SELECT name, city, rating, cuisine, id, address FROM restaurant ORDER BY rating DESC LIMIT 100) AS r
                    LEFT JOIN (SELECT user_id, r_id FROM orders) AS o ON r.id=o.r_id
                    LEFT JOIN (SELECT user_id FROM users WHERE age BETWEEN {$_GET['min']} and {$_GET['max']}) AS u ON o.user_id=u.user_id
                    ORDER BY r.rating DESC";
    } else if ($_GET['condition'] === 'sales_amount'){
        $query = "SELECT DISTINCT r.name, r.city, r.rating, r.cuisine, r.address
                    FROM (SELECT name, city, rating, cuisine, id, address FROM restaurant ORDER BY rating DESC LIMIT 100) AS r
                    LEFT JOIN (SELECT r_id FROM orders WHERE sales_amount BETWEEN {$_GET['min']} and {$_GET['max']}) AS o ON r.id=o.r_id
                    ORDER BY r.rating DESC";
    } else if ($_GET['condition'] === 'rating'){
        $query = "SELECT name, city, rating, cuisine, address
                    FROM restaurant
                    WHERE rating BETWEEN {$_GET['min']} and {$_GET['max']}
                    ORDER BY rating DESC
                    LIMIT 10000";
    } else if ($_GET['condition'] === 'cuisine'){
        $query = "SELECT name, city, rating, cuisine, address
                    FROM restaurant
                    WHERE cuisine LIKE '%{$_GET['type']}%'
                    ORDER BY rating DESC
                    LIMIT 10000";
    } else if ($_GET['condition'] === 'city'){
        $query = "SELECT name, city, rating, cuisine, address
                    FROM restaurant
                    WHERE city LIKE '%{$_GET['city']}%'
                    ORDER BY rating DESC
                    LIMIT 10000";
    } else if ($_GET['condition'] === 'insert'){
        $query = "INSERT INTO orders (id, order_date, user_id, r_id)
                    VALUE ( {$_GET['id']}, '{$_GET['date']}', {$_GET['user_id']}, {$_GET['r_id']} )";
    } else if ($_GET['condition'] === 'update'){
        $query = "UPDATE orders SET order_date = '{$_GET['date']}' WHERE id = {$_GET['id']};";
    }  else if ($_GET['condition'] === 'delete'){
        $query = "DELETE FROM orders WHERE id = {$_GET['id']};";
    } else {
        return;
    }
    //echo $query;

	//連線至資料庫
	$hostname= 'sql306.yabi.me';
	$username= 'yabi_35728249';
	$password= 's1116110';
	$dbname= 'yabi_35728249_project';
	
	$link = mysqli_connect($hostname, $username, $password) or die ("html>scr
        ipt language='JavaScript'>alert('無法連線至資料庫！請稍後再重試一次。'),
        history.go(-1)/script>/html>");
	mysqli_select_db($link, $dbname);
    
    mysqli_query($link, " SET SQL_BIG_SELECTS = 1 " );

    // 執行query
    $result = mysqli_query($link, $query);

    // 處理執行結果
    if ($_GET['condition'] === 'insert'){
        // 如果有異動到資料庫數量(更新資料庫)
        if (mysqli_affected_rows($link) > 0) {
            // 如果有一筆以上代表有更新
            echo "新增成功";
        } else if (mysqli_affected_rows($link) === 0) {
            echo "無資料新增";
        }else {
            echo "{$sql} 語法執行失敗，錯誤訊息: " . mysqli_error($link);
        }
        return;
    } else if ($_GET['condition'] === 'update'){
        // 如果有異動到資料庫數量(更新資料庫)
        if (mysqli_affected_rows($link) > 0) {
            echo "資料已更新";
        } else if(mysqli_affected_rows($link) === 0) {
            echo "無資料更新";
        } else {
            echo "{$sql} 語法執行失敗，錯誤訊息: " . mysqli_error($link);
        }
    } else if ($_GET['condition'] === 'delete'){
        // 如果有異動到資料庫數量(更新資料庫)
        if (mysqli_affected_rows($link) > 0) {
            echo "資料已刪除";
        } else if (mysqli_affected_rows($link) === 0) {
            echo "無資料刪除";
        } else {
            echo "{$sql} 語法執行失敗，錯誤訊息: " . mysqli_error($link);
        }
    } else {
        // 檢查是否有資料
        if($result){
            echo '執行成功<br>';
            // 檢查資料筆數>0，並印出結果表格
            if (mysqli_num_rows($result) > 0){
                echo '<table>';
                echo '<tr><td>name</td><td>city</td><td>rating</td><td>cuisine</td><td>address</td></tr>';//<td></td>
                while($row = mysqli_fetch_assoc($result)){
                    //$text = '<tr>'.'<td>'.$row['name'].'</td>';
                    //$text = $text.'<td>'.$row['age'].'</td>';
                    $text = '<tr>'.'<td>'.$row['name'].'</td>';
                    $text = $text.'<td>'.$row['city'].'</td>';
                    $text = $text.'<td>'.$row['rating'].'</td>';
                    $text = $text.'<td>'.$row['cuisine'].'</td>';
                    $text = $text.'<td>'.$row['address'].'</td>';
                    $text = $text.'</tr>';
                    echo $text;
                    //echo $row.'<br>';
                }
                echo '</table>';
            } else {
                echo '查無資料<br>';
            }

            // 釋放資料庫查到的記憶體
            mysqli_free_result($result);
        } else {
            echo '執行失敗<br>';
        }
    }
    
    // 結束連線
    mysqli_close($link);
?>
