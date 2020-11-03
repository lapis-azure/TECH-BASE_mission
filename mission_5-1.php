<!-- GitHub アップロード用 -->
<?php
//DBに接続
$dsn = 'mysql:dbname=********;host=localhost';
$user = '********';
$password = '********';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//データベース内にテーブルを作成する
$sql = "CREATE TABLE IF NOT EXISTS tb_5_1"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "PassWord TEXT"
	.");";
    $stmt = $pdo->query($sql);

$flag=0;//念のためflagの初期化

//編集ボタンが押された時のみ動作
if(!empty($_POST["submit_write"])){
    //投稿番号、パスワードどちらもあれば
    if(!empty($_POST["write"]) && !empty($_POST["PW_write"])){
    //echo "【編集機能1】";
    $write=$_POST["write"];
    $PW_write=$_POST["PW_write"];

        $sql = 'SELECT * FROM tb_5_1';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
            //編集対象番号とパスワードが一致したら 編集対象番号のnameとcommentを取得する
            if($row['id']==$write && $row['PassWord']==$PW_write){
                $re_write=$write; 
                $re_name=$row['name'];
                $re_comment=$row['comment'];
            //編集対象番号とパスワードが一致しない場合、invalidと表示
            }else if($row['id']==$write && $row['PassWord']!=$PW_write){
                $flag=1;
                /*echo "<br>!------------------------!<br><br>";
                echo "Error: PassWord is invalid<br><br>";
                echo "!------------------------!<br>";*/
            }
         }

    }
}
?>

<!--投稿フォームの作成-->
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8"/>
        <title>mission_5-1</title>
        <style>
             label {
                margin-right: 5px;
                width:100px;
                float: left;
            }
        </style>

    </head>
    <body>
        <form action="" method="post">
           【 投稿フォーム 】<br>
            <label for="name">名前：</label>
            <input type="text" name="name" value="<?php if(!empty($re_name)){ echo $re_name;}?>" placeholder="名前"><br>
            
            <label for="comment">コメント：</label>
            <input type="text" name="comment" value="<?php if(!empty($re_comment)){ echo $re_comment;}?>" placeholder="コメント"><br>
            
            <label for="PW">パスワード：</label>
            <input type="text" name="PW" value="" placeholder="パスワード">  
            <input type="submit" name="submit"><br>

            <!--編集したい投稿番号をhiddenで表示-->
            <input type="hidden" name="PostNumber" value="<?php if(!empty($re_write)){ echo $re_write;}?>"><br>  <!-- type="hidden" 確認する時はnumberにすれば良い-->
            
            【 削除フォーム 】<br>
            <label for="delete">投稿番号：</label>    
            <input type="number" name="delete" value="" placeholder="削除対象番号"><br> 
            
             <label for="PW_delete">パスワード：</label> 
             <input type="text" name="PW_delete" value="" placeholder="パスワード">
             <input type="submit" name="submit_delete" value="削除"><br>
             <br>
             
            【 編集フォーム 】<br>
            <label for="write">投稿番号：</label>  
            <input type="number" name="write" value="" placeholder="編集対象番号"><br> 
            
             <label for="PW_write">パスワード：</label>  
             <input type="text" name="PW_write" value="" placeholder="パスワード">
             <input type="submit" name="submit_write" value="編集"><br>
        </form>
       
    </body>
</html>

<?php
//【投稿機能】
//submitのボタンが押された時のみ動作
if(!empty($_POST["submit"])&&empty($_POST["PostNumber"])){
    //echo "【 投稿機能 】<br>";
    $name = $_POST['name'];
    $comment=$_POST['comment'];
    $PassWord=$_POST['PW']; 
    
    if (empty($name)){
            echo "<br>!------------------------!<br><br>";
            echo "Error: Name is empty<br><br>";
            echo "!------------------------!<br>";
     }else if(empty($comment)){
            echo "<br>!------------------------!<br><br>";
            echo "Error: Comment is empty<br><br>";
            echo "!------------------------!<br>";        
         } else if(empty($PassWord)){
            echo "<br>!------------------------!<br><br>";
            echo "Error: PassWord is empty<br><br>";
            echo "!------------------------!<br>"; 
            //名前、コメント、パスワードの3つが入力された時のみDBに書き込む      
         }else if(!empty($name) && !empty($comment) && !empty($PassWord)){

         $sql = $pdo -> prepare("INSERT INTO tb_5_1(name, comment, date, PassWord) VALUES (:name, :comment, :date, :PassWord)");
         $sql -> bindParam(':name', $name, PDO::PARAM_STR);
         $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
         $sql -> bindParam(':date', $date, PDO::PARAM_STR);
         $sql -> bindParam(':PassWord', $PassWord, PDO::PARAM_STR);
         $date=date("Y/m/d H:i");
         $sql -> execute();
         }
}

//【削除機能】
if(!empty($_POST["submit_delete"])){
    $delete=$_POST["delete"];    //削除
    $PW_delete=$_POST["PW_delete"];  //削除パスワード
           
    if(empty($delete)){
        echo "<br>!------------------------!<br><br>";
        echo "Error: Delete-Numeber is empty<br><br>";
        echo "!------------------------!<br><br>";
    //削除パスワードがなければ
    } else if(empty($PW_delete)){
            echo "<br>!------------------------!<br><br>";
            echo "Error: PassWord is empty<br><br>";
            echo "!------------------------!<br><br>";
    //投稿番号、パスワードどちらもあれば
    }else if(!empty($delete) && !empty($PW_delete)){
        $sql = 'SELECT * FROM tb_5_1';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
            //削除対象番号とパスワードが一致したら delete
            if($row['id']==$delete && $row['PassWord']==$PW_delete){
                $id = $delete;
                $sql = 'delete from tb_5_1 where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            //削除対象番号とパスワードが一致しない場合 invalid と表示
            }else if($row['id']==$delete && $row['PassWord']!=$PW_delete){
                echo "<br>!------------------------!<br><br>";
                echo "Error: PassWord is invalid<br><br>";
                echo "!------------------------!<br>";
            }
         }
    }
}

/*【編集機能2】：編集対象番号、パスワードの入力  の不足をブラウザに表示*/  
//編集ボタンが押された時のみ動作
if(!empty($_POST["submit_write"])){
    $write=$_POST["write"];
    $PW_write=$_POST["PW_write"];
    //編集対象番号がなければ
    if(empty($write)){
        echo "!------------------------!<br><br>";
        echo "Error: Edit-Numeber is empty<br><br>";
        echo "!------------------------!<br>";
    //編集パスワードがなければ
    } else if(empty($PW_write)){
        echo "!------------------------!<br><br>";
        echo "Error: PassWord is empty<br><br>";
        echo "!------------------------!<br>";
    //編集対象番号とパスワードが一致しない場合、invalidと表示
    } else if($flag==1){  
         echo "!------------------------!<br><br>";
         echo "Error: PassWord is invalid<br><br>";
         echo "!------------------------!<br>"; 
    }
}

/*【編集機能3】
投稿番号を表示するhiddenの欄にデータが入っていて、フォームから送信がある場合に編集する */
            
// 送信ボタンを押した時のみ、投稿番号を表示する欄にデータがある時のみ動作 
if(!empty($_POST["submit"]) && !empty($_POST["PostNumber"])){
    $new_name = $_POST['name'];
    $new_comment=$_POST['comment'];
    $PassWord=$_POST['PW']; 
    $PostNumber=$_POST["PostNumber"];

    //名前がなければ
    if (empty($new_name)){
        echo "<br>!------------------------!<br><br>";
        echo "Error: Name is empty<br><br>";
        echo "!------------------------!<br>";
    //コメントがなければ
    }else if(empty($new_comment)){
        echo "<br>!------------------------!<br><br>";
        echo "Error: Comment is empty<br><br>";
        echo "!------------------------!<br>";
     //パスワードがなければ  
     } else if(empty($PassWord)){
        echo "<br>!------------------------!<br><br>";
        echo "Error: PassWord is empty<br><br>";
        echo "!------------------------!<br>";
    } else if(!empty($new_name) && !empty($new_comment) && !empty($PassWord)){
        //echo "機能1<br>";
        $sql = 'SELECT * FROM tb_5_1';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
            //編集番号と一致し、パスワードと一致したら nameとcommentを新しい内容に編集
            if($row['id']==$PostNumber && $row['PassWord']==$PassWord){
                //echo "機能2<br>";
                $id = $PostNumber;
                $name = $new_name;     //変更したい名前
                $comment = $new_comment;    //変更したいコメント
                $sql = 'update tb_5_1 set name=:name,comment=:comment where id=:id';;
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            //編集番号とパスワードが一致しなければ invalidと表示
            }else if($row['id']==$PostNumber && $row['PassWord']!=$PassWord){
                echo "<br>!------------------------!<br><br>";
                echo "Error: PassWord is invalid<br><br>";
                echo "!------------------------!<br>";
            }
         }

    }
}

 //【ブラウザに表示】 DBに保存された内容をselectによって表示する
    if(!empty($_POST["submit"])|| !empty($_POST["submit_delete"])|| !empty($_POST["submit_write"])){ 
        echo "----------------------------<br>";
        echo "【 投稿一覧 】<br>";
         $sql = 'SELECT * FROM tb_5_1';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
	    	//$rowの中にはテーブルのカラム名
	    	echo $row['id'].' ';
	    	echo $row['name'].' ';
	    	echo $row['comment'].' ';
	    	echo $row['date'].'<br>';
		    //echo $row['PassWord'].'<br>'; //パスワードは表示しない
             echo "<hr>";
         }
    }
?>