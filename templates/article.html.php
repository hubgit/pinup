
<article itemscope>
	<header>
		<h1 itemprop="name"><?= $data['name'] ?></h1>
		<div><time itemprop="dateCreated"
			datetime="<?= date(DATE_ATOM, $data['dateCreated']) ?>"><?= date('F jS, Y', $data['dateCreated']) ?></time></div>
		<div><time itemprop="dateModified"
			datetime="<?= date(DATE_ATOM, $data['dateModified']) ?>"><?= date('F jS, Y', $data['dateModified']) ?></time></div>
	</header>

<? foreach ((array) $data['section'] as $section): ?>
	<section><?= $section ?></section>
<? endforeach ?>

<? if (isset($data['footer'])): ?>
	<footer><?= $data['footer'] ?></footer>
<? endif ?>
</article>