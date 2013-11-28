<!doctype html>
<meta charset="utf-8">
<title><?= h(strip_tags($data['title'])) ?></title>

<link rel="stylesheet" href="/static/css/body.css">

<script async src="../static/vendor/jquery/jquery.min.js"></script>
<script async src="../static/js/editor.js"></script>

<? if (isset($data['description'])): ?>
<meta name="description" content="<?= h($data['description']) ?>">
<? endif ?>

<? if (isset($data['prev-version'])): ?>
<link rel="prev-version" href="<?= h($data['prev-version']) ?>">
<? endif ?>

<? if (isset($data['next-version'])): ?>
<link rel="next-version" href="<?= h($data['next-version']) ?>">
<? endif ?>

<? require __DIR__ . '/article.html.php'; ?>
