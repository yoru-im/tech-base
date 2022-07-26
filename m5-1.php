<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>mission_5-1</title>
    </head>
    <body>
        <?php
            $dsn = 'データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            #「 IF NOT EXISTS 」は「もしまだこのテーブルが存在しないなら」
            $sql = "CREATE TABLE IF NOT EXISTS tb5_1"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name char(32),"
            . "comment TEXT,"
            . "pass TEXT,"
            . "date DATETIME"
            .");";
            
            $stmt = $pdo->query($sql);
            
            if(!empty($_POST['name']) && !empty($_POST['comment'] && !empty($_POST['pass']))){
                $name = $_POST['name'];
                $comment = $_POST['comment'];
                $pass = $_POST['pass'];
                $date = date("Y/m/d H:i:s");
                
                
                #編集番号がhiddenに入力されていない時だけ動作
                if(empty($_POST['edit_num'])){
                    //execute 実行するメソッド　prepareでSQL文をリクエスト可能になる
                    //$sql = 'SELECT * FROM tb5_1';
                    
                    //$sqlから->bindParamメソッドを呼び出している
                    $sql = $pdo -> prepare("INSERT INTO tb5_1 (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> execute();
                    //$sql -> execute();
                    
                }else{
                    $edit_num = $_POST['edit_num'];

                    //SQL文中で変数を展開するには…　Lineのノートに投稿してあります
                    $sql = $pdo -> prepare("INSERT INTO tb5_1 (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
                    $sql = "UPDATE tb5_1 SET name = :name, comment = :comment, pass = :pass, date = :date WHERE id= '" . $edit_num . "' ";
                    $stmt = $pdo -> prepare($sql);
                    
                    $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
                    $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt -> execute();
                    
                }
            }
            
            /***編集内容の取得***/
            #編集対象番号が入力されている時
            if(!empty($_POST['edit']) && !empty($_POST['edit_pass'])){
    
                $edit = $_POST['edit'];
                $edit_pass = $_POST['edit_pass'];

                $sql = "SELECT * FROM tb5_1 WHERE id= '" . $edit . "' AND pass = '" . $edit_pass . "'";
                $stmt = $pdo -> query($sql);
                
                $post = $stmt ->fetch(PDO::FETCH_ASSOC);
                
            }
            
            /***削除機能***/
            if(!empty($_POST['del_num']) && !empty($_POST['del_pass'])){
                $del_num = $_POST['del_num'];
                $del_pass = $_POST['del_pass'];

                $sql = "delete from tb5_1 WHERE id= '" . $del_num . "' AND pass = '" . $del_pass . "'";
                $stmt = $pdo -> prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                
                
            }
            
            
        
        ?>
        <form action = "" method = "post">
            
            <!--
                value属性に変数を指定すると、フォーム内に表示することが出来ます。編集時以外には表示したくないので、「if(!empty(変数)）」で指定。
            -->
            <input type = "text" name = "name" placeholder = "名前" value = "<?php if (!empty($post['name'])) {echo $post['name'];}?>">
            <input type = "text" name = "comment" placeholder = "コメント" value = "<?php if (!empty($post['comment'])) {echo $post['comment'];}?>">
            
            <!--
                「type = hidden」と設定すると、ブラウザ上に表示しないように隠しながら、値を利用できる。
                この下の、「type = hidden」を、「typw = text」にすると、ブラウザでフォームが見えるようになります。 
            -->
            <input type = "hidden" name = "edit_num" placeholder = "hid" value = "<?php if (!empty($post['id'])) {echo $post['id'];}?>"><br/>
            
            <!-- インプットにパスワードを追加！ -->
            <input type = "text" name="pass" placeholder = "パスワード" value="<?php if (!empty($post['pass'])) {echo $post['pass'];}?>"/>
            <input type = "submit" name = "submit"><br><br>
            
            <input type = "number" name = "del_num" placeholder = "削除対象番号">
            <!-- 削除したい時にパスワードを入力させる -->
            <!-- type = "password" とすると、入力時に黒い点になる -->
            <input type = "password" name="del_pass" placeholder = "パスワード" value=""/>
            <input type = "submit" name = "delete" value = "削除"><br>
            
            <input type = "number" name = "edit" placeholder = "編集対象番号">
            <!-- 編集したい時にパスワードを入力させる -->
            <input type="password" name="edit_pass" placeholder = "パスワード" value=""/>
            <input type = "submit" value = "編集"><br/>
            
            
        </form>   
        
        <?php    
            /***ブラウザ出力***/
            $sql = 'SELECT * FROM tb5_1';
            $stmt = $pdo -> query($sql);
            //fetchは「抽出する」という意味
            $results = $stmt -> fetchAll();
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].' ';
                echo $row['name'].' ';
                echo $row['comment'].' ';
                echo $row['date'].'<br>';
                echo "<hr>";
            }
        ?>
    </body>
</html>