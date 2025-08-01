<?php

$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = 'root';

// idパラメータの値が存在すれば処理を行う
if (isset($_GET['id'])) {
    try {
        $pdo = new PDO($dsn, $user, $password);

        // idカラムの値をプレースホルダーに置き換えたDELETE文を作成
        $sql_delete = 'DELETE FROM books WHERE id = :id;';
        $stmt_delete = $pdo->prepare($sql_delete);

        // プレースホルダーに値をバインドする
        $stmt_delete->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

        // SQLを実行する
        $stmt_delete->execute();

        // 削除した件数を取得する
        $count = $stmt_delete->rowCount();

        $message = "書籍を{$count}件削除しました。";

        // 商品一覧ページにリダイレクトさせる（同時にmessageパラメータも渡す）
        header("Location: read.php?message={$message}");
    } catch (PDOException $e) {
        exit($e->getMessage());
    }
} else {
    // idパラメータが存在しない場合は、エラーメッセージを表示する
    $message = "削除する書籍が指定されていません。";
    header("Location: read.php?message={$message}");
}
?>
