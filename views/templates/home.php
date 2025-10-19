<?php
    /**
     * Affichage de Liste des articles. 
     */
?>

<div class="articleList">
    <?php foreach($articles as $article) { ?>
        <article class="article">
            <h2><?= $article->getTitle() ?></h2>
            <span class="quotation">«</span>
            <p><?= $article->getContent(400) ?></p>
            
            <div class="footer">
                <span class="info"> <?= ucfirst(DateFormatter::convertDateToFrenchFormat($article->getDateCreation())) ?></span>
                <span class="info"> <?= $article->getViews() ?> vue<?= $article->getViews() > 1 ? 's' : '' ?></span>
                <a class="info" href="index.php?action=showArticle&id=<?= $article->getId() ?>">Lire +</a>
            </div>
        </article>
    <?php } ?>
</div>