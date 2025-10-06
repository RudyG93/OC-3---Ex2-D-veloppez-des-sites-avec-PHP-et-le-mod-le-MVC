<?php
    /**
     * Template de monitoring/statistiques admin
     * Affiche les statistiques complètes du blog
     */
?>

<style>
/* Styles spécifiques au monitoring - optimisé pour écrans >= 1366px */
.monitoring-container {
    min-width: 1200px;
    padding: 20px;
}

.monitoring-header {
    text-align: center;
    margin-bottom: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.stat-number {
    font-size: 2.5em;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1.1em;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.monitoring-section {
    background: white;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.monitoring-section h2 {
    color: #333;
    border-bottom: 3px solid #667eea;
    padding-bottom: 15px;
    margin-bottom: 25px;
}

.popular-articles-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.popular-articles-table th,
.popular-articles-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.popular-articles-table th {
    background-color: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.views-badge {
    background: #667eea;
    color: white;
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 0.9em;
    font-weight: bold;
}

.recent-comments {
    max-height: 400px;
    overflow-y: auto;
}

.comment-item {
    border-left: 4px solid #667eea;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.comment-meta {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 8px;
}

.comment-content {
    color: #333;
    line-height: 1.5;
}

.stats-charts {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.chart-container {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.month-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.month-stat {
    background: white;
    padding: 10px 15px;
    border-radius: 5px;
    border-left: 4px solid #667eea;
    min-width: 120px;
}

.admin-nav {
    text-align: center;
    margin-bottom: 30px;
}

.admin-nav a {
    display: inline-block;
    padding: 12px 25px;
    margin: 0 10px;
    background: #667eea;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.admin-nav a:hover {
    background: #5a67d8;
}
</style>

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
        <h2>🔥 Articles les Plus Populaires</h2>
        <table class="popular-articles-table">
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>Titre</th>
                    <th>Date de création</th>
                    <th>Vues</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                foreach ($popularArticles as $article): ?>
                <tr>
                    <td><strong>#<?= $rank++ ?></strong></td>
                    <td>
                        <a href="index.php?action=showArticle&id=<?= $article->getId() ?>" target="_blank">
                            <?= Utils::format($article->getTitle()) ?>
                        </a>
                    </td>
                    <td><?= Utils::convertDateToFrenchFormat($article->getDateCreation()) ?></td>
                    <td><span class="views-badge"><?= $article->getViews() ?> vues</span></td>
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