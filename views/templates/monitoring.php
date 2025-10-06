<?php
    /**
     * Template de monitoring/statistiques admin
     * Affiche les statistiques complètes du blog
     */
?>

<div class="monitoring-container">
    <div class="monitoring-header">
        <h1>📊 Tableau de Bord - Monitoring</h1>
        <p>Vue d'ensemble des statistiques du blog d'Emilie Forteroche</p>
    </div>

    <div class="admin-nav">
        <a href="index.php?action=admin">← Retour à l'administration</a>
        <a href="index.php">Voir le site</a>
    </div>

    <!-- Statistiques générales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $totalArticles ?></div>
            <div class="stat-label">Articles Publiés</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $totalComments ?></div>
            <div class="stat-label">Commentaires</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= number_format($totalViews) ?></div>
            <div class="stat-label">Vues Totales</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $averageViewsPerArticle ?></div>
            <div class="stat-label">Vues/Article</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $averageCommentsPerArticle ?></div>
            <div class="stat-label">Commentaires/Article</div>
        </div>
    </div>

    <!-- Articles les plus populaires -->
    <div class="monitoring-section">
        <h2>🔥 Tous les Articles (Triables)</h2>
        <p style="color: #666; margin-bottom: 15px;">Cliquez sur les en-têtes de colonnes pour trier les résultats</p>
        
        <?php
        // Affichage du tri actuel
        $sortLabels = [
            'title' => 'Titre',
            'date_creation' => 'Date de création', 
            'views' => 'Nombre de vues',
            'comment_count' => 'Nombre de commentaires'
        ];
        $orderLabels = [
            'asc' => 'croissant',
            'desc' => 'décroissant'
        ];
        ?>
        <div style="background: #f0f8ff; padding: 10px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #667eea;">
            <strong>Tri actuel :</strong> 
            <?php if ($currentSort === null): ?>
                Aucun tri appliqué (ordre naturel)
            <?php else: ?>
                <?= $sortLabels[$currentSort] ?> (ordre <?= $orderLabels[$currentOrder] ?>)
            <?php endif; ?>
            - <a href="index.php?action=monitoring" style="color: #667eea;">Réinitialiser le tri</a>
        </div>
        
        <?php
        // Fonction pour générer les liens de tri
        function generateSortLink($column, $currentSort, $currentOrder) {
            $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
            return "index.php?action=monitoring&sort=" . $column . "&order=" . $newOrder;
        }
        
        // Fonction pour générer l'indicateur de tri
        function getSortIndicator($column, $currentSort, $currentOrder) {
            if ($currentSort === $column) {
                return '<span class="sort-indicator ' . $currentOrder . '"></span>';
            } else {
                return '<span class="sort-indicator neutral"></span>';
            }
        }
        ?>
        
        <table class="popular-articles-table">
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>
                        <a href="<?= generateSortLink('title', $currentSort, $currentOrder) ?>" class="sortable-header">
                            Titre
                            <?= getSortIndicator('title', $currentSort, $currentOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= generateSortLink('date_creation', $currentSort, $currentOrder) ?>" class="sortable-header">
                            Date de création
                            <?= getSortIndicator('date_creation', $currentSort, $currentOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= generateSortLink('views', $currentSort, $currentOrder) ?>" class="sortable-header">
                            Vues
                            <?= getSortIndicator('views', $currentSort, $currentOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= generateSortLink('comment_count', $currentSort, $currentOrder) ?>" class="sortable-header">
                            Commentaires
                            <?= getSortIndicator('comment_count', $currentSort, $currentOrder) ?>
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                foreach ($popularArticles as $articleData): 
                    $article = $articleData['article'];
                    $commentCount = $articleData['comment_count'];
                ?>
                <tr>
                    <td><strong>#<?= $rank++ ?></strong></td>
                    <td>
                        <a href="index.php?action=showArticle&id=<?= $article->getId() ?>" target="_blank">
                            <?= Utils::format($article->getTitle()) ?>
                        </a>
                    </td>
                    <td><?= Utils::convertDateToFrenchFormat($article->getDateCreation()) ?></td>
                    <td><span class="views-badge"><?= $article->getViews() ?> vues</span></td>
                    <td><span class="comment-badge"><?= $commentCount ?> commentaire<?= $commentCount > 1 ? 's' : '' ?></span></td>
                    <td>
                        <a href="index.php?action=showUpdateArticleForm&id=<?= $article->getId() ?>">Modifier</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Commentaires récents -->
    <div class="monitoring-section">
        <h2>💬 Commentaires Récents</h2>
        <div class="recent-comments">
            <?php foreach ($recentComments as $commentData): ?>
            <div class="comment-item">
                <div class="comment-meta">
                    <strong><?= Utils::format($commentData['comment']->getPseudo()) ?></strong> 
                    - <?= Utils::convertDateToFrenchFormat($commentData['comment']->getDateCreation()) ?>
                    - Article: <em><?= Utils::format($commentData['article_title']) ?></em>
                </div>
                <div class="comment-content">
                    <?= Utils::format($commentData['comment']->getContent(200)) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modération des commentaires -->
    <div class="monitoring-section">
        <h2>🛡️ Modération des Commentaires</h2>
        <p style="color: #666; margin-bottom: 15px;">Gérez tous les commentaires du blog - Cliquez sur les en-têtes pour trier</p>
        
        <?php if (!empty($successMessage)): ?>
        <div class="success-message">
            ✅ <?= $successMessage ?>
        </div>
        <?php endif; ?>

        <?php
        // Fonctions pour les liens de tri des commentaires
        function generateCommentSortLink($column, $currentSort, $currentOrder) {
            $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
            return "index.php?action=monitoring&comment_sort=" . $column . "&comment_order=" . $newOrder;
        }
        
        function getCommentSortIndicator($column, $currentSort, $currentOrder) {
            if ($currentSort === $column) {
                return '<span class="sort-indicator ' . $currentOrder . '"></span>';
            } else {
                return '<span class="sort-indicator neutral"></span>';
            }
        }
        ?>

        <div style="background: #f0f8ff; padding: 10px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #667eea;">
            <strong>Tri actuel :</strong> 
            <?php
            $commentSortLabels = [
                'date_creation' => 'Date de création',
                'pseudo' => 'Auteur', 
                'article_title' => 'Article'
            ];
            $orderLabels = ['asc' => 'croissant', 'desc' => 'décroissant'];
            ?>
            <?= $commentSortLabels[$commentSort] ?> (ordre <?= $orderLabels[$commentOrder] ?>)
            - <a href="index.php?action=monitoring" style="color: #667eea;">Réinitialiser</a>
        </div>

        <table class="comment-moderation-table">
            <thead>
                <tr>
                    <th>
                        <a href="<?= generateCommentSortLink('date_creation', $commentSort, $commentOrder) ?>" class="sortable-header">
                            Date
                            <?= getCommentSortIndicator('date_creation', $commentSort, $commentOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= generateCommentSortLink('pseudo', $commentSort, $commentOrder) ?>" class="sortable-header">
                            Auteur
                            <?= getCommentSortIndicator('pseudo', $commentSort, $commentOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= generateCommentSortLink('article_title', $commentSort, $commentOrder) ?>" class="sortable-header">
                            Article
                            <?= getCommentSortIndicator('article_title', $commentSort, $commentOrder) ?>
                        </a>
                    </th>
                    <th>Commentaire</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allComments as $commentData): ?>
                <tr>
                    <td>
                        <?= Utils::convertDateToFrenchFormat($commentData['comment']->getDateCreation()) ?>
                    </td>
                    <td>
                        <strong><?= Utils::format($commentData['comment']->getPseudo()) ?></strong>
                    </td>
                    <td>
                        <a href="index.php?action=showArticle&id=<?= $commentData['comment']->getIdArticle() ?>" target="_blank">
                            <?= Utils::format($commentData['article_title']) ?>
                        </a>
                    </td>
                    <td class="comment-content-preview">
                        <?= Utils::format($commentData['comment']->getContent()) ?>
                    </td>
                    <td class="comment-actions">
                        <a href="index.php?action=deleteComment&id=<?= $commentData['comment']->getId() ?>&return=monitoring" 
                           class="delete-btn"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?\nCette action est irréversible.')">
                            🗑️ Supprimer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($allComments)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #666;">
                        Aucun commentaire trouvé.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination simple -->
        <div class="pagination">
            <?php if ($commentPage > 1): ?>
                <a href="index.php?action=monitoring&comment_page=<?= $commentPage - 1 ?>&comment_sort=<?= $commentSort ?>&comment_order=<?= $commentOrder ?>">
                    ← Précédent
                </a>
            <?php endif; ?>
            
            <span class="current">Page <?= $commentPage ?></span>
            
            <?php if (count($allComments) === $commentsPerPage): ?>
                <a href="index.php?action=monitoring&comment_page=<?= $commentPage + 1 ?>&comment_sort=<?= $commentSort ?>&comment_order=<?= $commentOrder ?>">
                    Suivant →
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistiques par mois -->
    <div class="stats-charts">
        <div class="monitoring-section">
            <h2>📈 Évolution des Articles</h2>
            <div class="chart-container">
                <div class="month-stats">
                    <?php foreach ($articleStatsByMonth as $stat): ?>
                    <div class="month-stat">
                        <strong><?= $stat['count'] ?></strong><br>
                        <small><?= DateTime::createFromFormat('!m', $stat['month'])->format('M') ?> <?= $stat['year'] ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="monitoring-section">
            <h2>💬 Évolution des Commentaires</h2>
            <div class="chart-container">
                <div class="month-stats">
                    <?php foreach ($commentStatsByMonth as $stat): ?>
                    <div class="month-stat">
                        <strong><?= $stat['count'] ?></strong><br>
                        <small><?= DateTime::createFromFormat('!m', $stat['month'])->format('M') ?> <?= $stat['year'] ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations système -->
    <div class="monitoring-section">
        <h2>⚙️ Informations Système</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= date('d/m/Y H:i') ?></div>
                <div class="stat-label">Dernière Mise à Jour</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= PHP_VERSION ?></div>
                <div class="stat-label">Version PHP</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= ini_get('memory_limit') ?></div>
                <div class="stat-label">Limite Mémoire</div>
            </div>
        </div>
    </div>
</div>