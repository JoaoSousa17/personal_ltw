<?php
  $db = new PDO('sqlite:news.db');

  $stmt = $db->prepare('SELECT news.*, users.*, COUNT(comments.id) AS comments
  FROM news 
  JOIN users USING (username) 
  LEFT JOIN comments ON comments.news_id = news.id
  GROUP BY news.id, users.username
  ORDER BY published DESC');

  $stmt->execute();
  $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Online Newspaper</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><a href="all.html">Online Newspaper</a></h1>
        <a href="all.html"><img class="logo" src="logo.png" alt="ON Logo"></a>
    </header>

    <nav>
        <ul>
            <li><a href="section.html">Local</a></li>
            <li><a href="section.html">Politics</a></li>
            <li><a href="section.html">Sports</a></li>
            <li><a href="section.html">Business</a></li>
        </ul>
    </nav>

    <main>
        <?php foreach ($articles as $article): ?>
            <?php 
                $date = date('F j', strtotime($article['published']));
                $tags = explode(',', $article['tags']);
            ?>
            <article class="news">
                <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                <p><?php echo htmlspecialchars($article['introduction']); ?></p>

                <footer>
                    <span class="author"><?php echo htmlspecialchars($article['username']); ?></span>
                    <time datetime="<?php echo htmlspecialchars($article['published']); ?>"><?php echo $date; ?></time>
                    <a href="article.html">Read More</a>
                </footer>
            </article>
        <?php endforeach; ?>
    </main>

    <footer>
        <p>Copyright &copy; Fake News, 2022</p>
    </footer>
</body>
</html>
