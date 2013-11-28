<!doctype html>
<meta charset="utf-8">
<title><?= h(strip_tags($index['title'])) ?></title>

<link rel="stylesheet" href="/static/css/index.css">
<link rel="stylesheet" href="/static/css/item.css">

<? if (isset($index['description'])): ?>
<meta name="description" content="<?= h($index['description']) ?>">
<? endif ?>

<? foreach ($index['_items'] as $data): ?>
<? require __DIR__ . '/article.html.php'; ?>
<? endforeach; ?>