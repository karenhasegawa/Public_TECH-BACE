<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1</title>
        <!---------DB接続設定-----
        <link href="mission_5\database1.php"> ---------->
        <?php
            /*****************************************************
                        データベース
            ******************************************************/

            /****************************************************
                【自分用】
                * データベース名：********
                * ユーザー名：*********
                * パスワード：**********
            ***************************************************/

            /**********************　DB接続設定(毎回)*******************/
                $dsn = 'mysql:dbname=********;host=localhost';
                $user = '********';
                $password = '*********';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            /**************************************************** */

        ?>
    </head>
    <body>
        <form class="form1" action="" method="post">
            名前<br>
            <input id="form_name" type="text" name="name" value="名前" placeholder="名前を入力してください" width="300px"><br>
            コメント<br>
            <input id="form_comment" type="text" name="comment" value="コメント" placeholder="コメントを入力してください" width="300px"><br>
            パスワード<br>
            <input id="form_pass" type="password" name="pass_main" value="パスワード" placeholder="パスワードを入力してください" width="300px"><br>
            <input id="form_number" type="hidden" name="number" placeholder="投稿番号" width="300px">                        
            <br> 
            <input type="submit" name="new_submit" value="送信"><br>
        </form>
        <form action="" method="post">
            削除する投稿ナンバーを記入してください<br>
            <input type="number" name="delete" placeholder="削除番号" width="300px">
            <br>パスワード<br>
            <input type="password" name="pass_delete" value="パスワード" placeholder="パスワードを入力してください" width="300px"><br>
            <input type="submit" name="delete_submit" value="削除"><br>
        </form>
        <form action="" method="post">    
            編集する投稿ナンバーを記入してください<br>
            <input type="number" name="edit" placeholder="編集番号を入力して下さい" width="300px">
            <br>パスワード<br>
            <input type="password" name="pass_edit" value="パスワード" placeholder="パスワードを入力してください" width="300px"><br>
            <input type="submit" name="edit_submit" value="編集"><br>
        </form>
        <script>
            let element_1 = document.getElementById('form_name');
            let element_2 = document.getElementById('form_comment');
            let element_3 = document.getElementById('form_number');
            let element_4 = document.getElementById('form_pass');
        </script>
        <?php

            //定数設定
            /////////define("FILE","./database_2.txt");

            //初期値設定
            $delete_num = "";
            $delete_pass = "";
            $edit_num = "";
            $edit_pass = "";

            /***************************************************************** 
             *                      各入力による分岐                           *
            ******************************************************************/
            /*送信が押されたとき*/
            if(!empty($_POST["new_submit"])&&!empty($_POST["number"]))Edit_Submit2();
            else if(!empty($_POST["new_submit"]))New_Submit();
            /*削除が押されたとき*/
            else if(!empty($_POST["delete_submit"])){
                if(!empty($_POST["delete"])){
                    $delete_num = $_POST["delete"];
                    if(!empty($_POST["pass_delete"]))$delete_pass = $_POST["pass_delete"];
                }              
                $anser = PassWord($delete_num,$delete_pass);
                echo $anser."<br>";
                if($anser == "パスワードが一致しました。")Delete_Submit();
                else echo "もう一度記入してください。";
            }
            /*編集が押されたとき*/
            else if(!empty($_POST["edit_submit"])){
                if(!empty($_POST["edit"])){
                    $edit_num = $_POST["edit"];
                    if (!empty($_POST["pass_edit"]))$edit_pass = $_POST["pass_edit"];
                }
                $anser = PassWord($edit_num,$edit_pass);
                echo $anser."<br>";
                if($anser == "パスワードが一致しました。")Edit_Submit();
                else echo "もう一度記入してください。";
            }

            /***************************************************************
             *                      各機能の関数                            *
             ***************************************************************/                
            function New_Submit(){

                global $pdo;

                //ファイルの読み込み
                //$line_new = Read_db();
                //echo "最後の行:".$line[count($line)-1]."<br>";

                /* //投稿ナンバーの取得
                $line_last = $line_new[count($line_new)-1];
                        /////$line_last = explode("<>",$line_last,6);
                $id_number = $line_last["id"];
                //echo $id_number;*/
                
                /*************投稿する内容の保存***********/
                if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass_main"])){

                    //$id_number += 1;
                    //////$date =  date("Y-m-d H:i:s");             
                    //$id_number_date = $id_number."<>".$_POST["name"]."<>".$_POST["comment"]."<>".$date."<>".$_POST["pass_main"]."<>";
                    
                    /*$fp = fopen(FILE,"a");
                    fwrite($fp,$id_number_date.PHP_EOL);
                    fclose($fp);*/

                    /********************データ（レコード）を登録********************* */
                    $sql = $pdo -> prepare("INSERT INTO Massege (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                    $name = $_POST["name"];
                    $comment = $_POST["comment"]; //好きな名前、好きな言葉は自分で決めること
                    $date =  date("Y-m-d H:i:s");
                    $password = $_POST["pass_main"];
                    $sql -> execute();
                    //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう
                    /******************************************************* */

                }

                return 0;
            }

            function Delete_Submit(){

                if(!empty($_POST["delete"])){

                    global $pdo;

                    $delete = $_POST["delete"];

                    //ファイルの読み込み
                    $line_delete=Read_db();

                    /*//上書き
                    $fp = fopen(FILE,"w");*/
                    $count=0;
                    foreach($line_delete as $row){
                       // $lines = explode("<>",$i,6);
                    
                        if($row["id"]==$delete){

                            $id = $delete;
                            $sql = 'delete from Massege where id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->execute();
                            $count = 1;
                            
                        }
                        /*else {
                            //fwrite($fp,$i.PHP_EOL);
                           
                        }*/
                    }
                    if($count==0){
                        echo "新規を入力して下さい。";
                        return 0;
                    }
                                                                                               
                    //fclose($fp);



                }

                return 0;

            }
            
            function Edit_Submit(){

                $lines_edit=Read_db();
                
                //valueに代入
                foreach($lines_edit as $lines){
                    //$lines = explode("<>",$i,6);
                    if($lines["id"]==$_POST["edit"]){
                        $n="'";$c="'";$p="'";
                        $n.=$lines["name"];$c.=$lines["comment"];$nm=$lines["id"];$p.=$lines["password"];
                        $n.="'";$c.="'";$p.="'";
                        echo <<<EOM
                            <script type='text/javascript'>
                                element_1.value={$n};
                                element_2.value={$c};
                                element_3.value={$nm};
                                element_4.value={$p};
                                element_3.type="number";
                            </script>
                        EOM;
                    }
                }
                
                return 0;
            }

            function Edit_Submit2(){

                global $pdo;
                $line_edit=Read_db();
                $edit_num=$_POST["number"];
                $edit_name=$_POST["name"];
                $edit_comment=$_POST["comment"];
                $edit_pass=$_POST["pass_main"];
                //$date =  date("Y-m-d H:i:s");

                //上書き
                //$fp = fopen(FILE,"w");
                $count=0;
                foreach($line_edit as $lines){
                    //$lines = explode("<>",$i,5);
                    if($lines["id"]==$edit_num){ 
                        //$i=$edit_num."<>".$edit_name."<>".$edit_comment."<>".$date."<>".$edit_pass;
                       
                        $id = $edit_num; //変更する投稿番号
                        $name = $edit_name;
                        $comment = $edit_comment; //変更したい名前、変更したいコメントは自分で決めること
                        $date = date("Y-m-d H:i:s");
                        $password = $edit_pass;
                        $sql = 'UPDATE Massege SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->bindParam(':date', $date, PDO::PARAM_INT);
                        $stmt->bindParam(':password', $password, PDO::PARAM_INT);
                        $stmt->execute();

                        $count += 1;
                    }
                    //fwrite($fp,$i.PHP_EOL);
                }
                if($count==0){
                    echo "編集する番号がないので新規登録します";
                    New_Submit();
                }

                return 0;
                
            }

            function PassWord($num,$pass){

                $lines = Read_db();
                foreach($lines as $line){
                    //$i = explode("<>",$line,6);
                    if($line["id"]==$num){                        
                        switch ($line["password"]){
                            case $pass:
                                echo "<script type='text/javascript'>alert('パスワードは一致しています');</script>";
                                return "パスワードが一致しました。";
                            default:
                                echo "<script type='text/javascript'>alert('パスワードが合っていません');</script>";
                                return "パスワードが合っていません";
                        }
                    }                   
                }
                echo "その投稿番号はありません";
            }

            function Read_db(){

                global $pdo;

                /**テーブルに登録されたデータを取得し、表示*/
                $sql = 'SELECT * FROM Massege';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                        /*foreach ($results as $row){
                            //$rowの中にはテーブルのカラム名が入る
                            echo $row['id'].',';
                            echo $row['name'].',';
                            echo $row['comment'].'<br>';
                            echo "<hr>";
                        }*/
                return $results;
                        
            }

            /**************************************************************** 
            function Read_File(){

                //ファイルエラーチェック
                if(!file_exists(FILE)){
                    echo "<script type='text/javascript'>alert('データファイルがありません');</script>";
                }

                //ファイルの読み込み
                $line = file(FILE,FILE_IGNORE_NEW_LINES);
                
                return $line;

            }
            ********************************************************************/
            
            function view(){

                echo "<hr>";

                $lines_view=Read_db();
                
                //ファイル内容表示
                foreach($lines_view as $row){
                    //$line_view = explode("<>",$i,6);
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['comment'].'<br>';
                    echo "<hr>";
                }

                return 0;
            }

            echo "<br><br>";
            view();
           
        ?>

    </body>
</html>