<?php

/**
 * Classe qui gère les articles.
 */
class ArticleManager extends AbstractEntityManager 
{
    /**
     * Récupère tous les articles.
     * @return array : un tableau d'objets Article.
     */
    public function getAllArticles() : array
    {
        $sql = "SELECT * FROM article";
        $result = $this->db->query($sql);
        $articles = [];

        while ($article = $result->fetch()) {
            $articles[] = new Article($article);
        }
        return $articles;
    }
    
    /**
     * Récupère un article par son id.
     * @param int $id : l'id de l'article.
     * @return Article|null : un objet Article ou null si l'article n'existe pas.
     */
    public function getArticleById(int $id) : ?Article
    {
        $sql = "SELECT * FROM article WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        $article = $result->fetch();
        if ($article) {
            return new Article($article);
        }
        return null;
    }

    /**
     * Ajoute ou modifie un article.
     * On sait si l'article est un nouvel article car son id sera -1.
     * @param Article $article : l'article à ajouter ou modifier.
     * @return void
     */
    public function addOrUpdateArticle(Article $article) : void 
    {
        if ($article->getId() == -1) {
            $this->addArticle($article);
        } else {
            $this->updateArticle($article);
        }
    }

    /**
     * Ajoute un article.
     * @param Article $article : l'article à ajouter.
     * @return void
     */
    public function addArticle(Article $article) : void
    {
        $sql = "INSERT INTO article (id_user, title, content, date_creation) VALUES (:id_user, :title, :content, NOW())";
        $this->db->query($sql, [
            'id_user' => $article->getIdUser(),
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ]);
    }

    /**
     * Modifie un article.
     * @param Article $article : l'article à modifier.
     * @return void
     */
    public function updateArticle(Article $article) : void
    {
        $sql = "UPDATE article SET title = :title, content = :content, date_update = NOW() WHERE id = :id";
        $this->db->query($sql, [
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'id' => $article->getId()
        ]);
    }

    /**
     * Supprime un article.
     * @param int $id : l'id de l'article à supprimer.
     * @return void
     */
    public function deleteArticle(int $id) : void
    {
        $sql = "DELETE FROM article WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

    /**
     * Incrémente le nombre de vues d'un article.
     * @param int $id : l'id de l'article dont on veut incrémenter les vues.
     * @return void
     */
    public function incrementViews(int $id) : void
    {
        $sql = "UPDATE article SET views = views + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

    /**
     * Récupère le nombre total d'articles.
     * @return int : le nombre total d'articles.
     */
    public function getTotalArticlesCount() : int
    {
        $sql = "SELECT COUNT(*) as total FROM article";
        $result = $this->db->query($sql);
        $data = $result->fetch();
        return (int) $data['total'];
    }

    /**
     * Récupère le nombre total de vues de tous les articles.
     * @return int : le nombre total de vues.
     */
    public function getTotalViewsCount() : int
    {
        $sql = "SELECT SUM(views) as total FROM article";
        $result = $this->db->query($sql);
        $data = $result->fetch();
        return (int) ($data['total'] ?? 0);
    }

    /**
     * Récupère les articles les plus populaires (par nombre de vues).
     * @param int $limit : le nombre d'articles à récupérer.
     * @return array : un tableau d'objets Article.
     */
    public function getMostPopularArticles(int $limit = 5) : array
    {
        // On sécurise la valeur limit (doit être un entier positif)
        $limit = max(1, (int)$limit);
        $sql = "SELECT * FROM article ORDER BY views DESC LIMIT " . $limit;
        $result = $this->db->query($sql);
        $articles = [];

        while ($article = $result->fetch()) {
            $articles[] = new Article($article);
        }
        return $articles;
    }

    /**
     * Récupère les statistiques par mois (nombre d'articles créés).
     * @return array : un tableau associatif avec les statistiques par mois.
     */
    public function getArticleStatsByMonth() : array
    {
        $sql = "SELECT 
                    YEAR(date_creation) as year, 
                    MONTH(date_creation) as month, 
                    COUNT(*) as count 
                FROM article 
                GROUP BY YEAR(date_creation), MONTH(date_creation) 
                ORDER BY YEAR(date_creation) DESC, MONTH(date_creation) DESC 
                LIMIT 12";
        $result = $this->db->query($sql);
        $stats = [];

        while ($row = $result->fetch()) {
            $stats[] = $row;
        }
        return $stats;
    }

    /**
     * Récupère tous les articles avec le nombre de commentaires.
     * @return array : un tableau associatif avec 'article' et 'comment_count'
     */
    public function getAllArticlesWithCommentCount() : array
    {
        $sql = "SELECT a.*, COUNT(c.id) as comment_count 
                FROM article a 
                LEFT JOIN comment c ON a.id = c.id_article 
                GROUP BY a.id, a.id_user, a.title, a.content, a.date_creation, a.date_update, a.views
                ORDER BY a.date_creation DESC";
        
        $result = $this->db->query($sql);
        $articles = [];

        while ($row = $result->fetch()) {
            $article = new Article($row);
            $articles[] = [
                'article' => $article,
                'comment_count' => (int)$row['comment_count']
            ];
        }
        return $articles;
    }
}