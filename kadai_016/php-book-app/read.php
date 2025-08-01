<?php
$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO($dsn, $user, $password);

    // orderパラメーターが存在すれば、その値を$orderに代入
    if (isset($_GET['order'])) {
        $order = $_GET['order'];
    } else {
        // 存在しなければ、NULLを設定
        $order = NULL;
    }

    // keywordパラメーターの値が存在した場合、その値を$keyword変数に代入
    if (isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
    } else {
        $keyword = NULL;
    }

    // orderパラメーターによって、SQLのORDER BY句を変更する
    if ($order === 'desc') {
        $sql_select = 'SELECT * FROM books WHERE book_name LIKE :keyword ORDER BY updated_at DESC';
    } else {
        $sql_select = 'SELECT * FROM books WHERE book_name LIKE :keyword ORDER BY updated_at ASC';
    }



    // SQL分を実行する
    $stmt_select = $pdo->prepare($sql_select);

    // SQLのLIKE句で使うため、変数$keywordの前後を%で囲む
    $partial_match = "%{$keyword}%";

    // SQLのプレースホルダーに値をバインドする
    $stmt_select->bindValue(':keyword', $partial_match, PDO::PARAM_STR);

    // SQLを実行する
    $stmt_select->execute();

    // SQLの実行結果を配列で取得する
    $books = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
}  catch (PDOException $e) {
        exit($e->getMessage());
    }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>書籍一覧</title>
    <link rel="stylesheet" href="css/style.css">

    <!-- Google Fontsの読み込み -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300&display=swap" href="stylesheet">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">書籍管理アプリ</a>
        </nav>
    </header>
    <main>
        <article class="books">
            <h1>商品一覧</h1>
            <?php
            // 商品の登録・更新・削除が完了した場合、メッセージを表示する
            if (isset($_GET['message'])) {
                echo "<p class='success'>{$_GET['message']}</p>";
            }
            ?>
            <div class="books-ui">
                <div>
                    <a href="read.php?order=desc&keyword=<?= $keyword ?>">
                        <img src="images/desc.png" alt="降順" class="sort-img">
                    </a>
                    <a href="read.php?order=asc&keyword=<?= $keyword ?>">
                        <img src="images/asc.png" alt="昇順" class="sort-img">
                    </a>
                    <form action="read.php" method="get" class="search-form">
                        <input type="hidden" name="order" value="<?= $order ?>">
                        <input type="text" class="search-box" placeholder="書籍名を入力" name="keyword" value="<?= $keyword ?>">
                    </form>
                </div>
                <a href="create.php" class="btn">書籍登録</a>
            </div>
            <table class="books-table">
                <tr>
                    <th>書籍コード</th>
                    <th>書籍名</th>
                    <th>単価</th>
                    <th>在庫数</th>
                    <th>ジャンルコード</th>
                    <th>編集</th>
                    <th>削除</th>
                </tr>
                <?php
                // 配列の要素を順番位取り出して表示する
                foreach ($books as $book) {
                    $table_row = "
                    <tr>
                    <td>{$book['book_code']}</td>
                    <td>{$book['book_name']}</td>
                    <td>{$book['price']}</td>
                    <td>{$book['stock_quantity']}</td>
                    <td>{$book['genre_code']}</td>
                    <td><a href='update.php?id={$book['id']}'><img src='images/edit.png' alt='編集' class='edit-icon'></a></td>
                    <td><a href='delete.php?id={$book['id']}'><img src='images/delete.png' alt='削除' class='delete-icon'></a></td>
                    </tr>
                    ";
                    echo $table_row;
                }
                ?>
            </table>
        </article>
    </main>
    <footer>
        <p class="copyright">&copy; 書籍管理アプリ All rights reserved.</p>
    </footer>
</body>
</html>
