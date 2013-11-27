<!doctype html>
<meta charset="utf-8">
<title><?= h(strip_tags($data['title'])) ?></title>

<link rel="stylesheet" href="/static/css/body.css">
<script src="/static/js/script.js" async></script>

<meta name="dc.created" content="<?= date($data['created'], 'J i, Y') ?>">
<meta name="description" content="<?= h($data['description']) ?>">

<? if (isset($data['prev-version'])): ?>
<link rel="prev-version" href="<?= h($data['prev-version']) ?>">
<? endif ?>

<? if (isset($data['next-version'])): ?>
<link rel="next-version" href="<?= h($data['next-version']) ?>">
<? endif ?>

<article>
	<header>
		<h1><?= $data['title'] ?></h1>
		<div><time class="created"><?= date($data['created'], 'J i, Y') ?></time></div>
	</header>

<? foreach ((array) $data['sections'] as $section): ?>
	<section><?= $section ?></section>
<? endforeach ?>

	<footer><?= $data['footer'] ?></footer>
</article>

