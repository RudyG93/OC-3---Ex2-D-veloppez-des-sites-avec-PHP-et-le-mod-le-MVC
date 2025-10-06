<?php

/**
 * Cette classe sert à gérer les commentaires. 
 */
class CommentManager extends AbstractEntityManager
{
    /**
     * Récupère tous les commentaires d'un article.
     * @param int $idArticle : l'id de l'article.
     * @return array : un tableau d'objets Comment.
     */
    public function getAllCommentsByArticleId(int $idArticle) : array
    {
        $sql = "SELECT * FROM comment WHERE id_article = :idArticle";
        $result = $this->db->query($sql, ['idArticle' => $idArticle]);
        $comments = [];

        while ($comment = $result->fetch()) {
            $comments[] = new Comment($comment);
        }
        return $comments;
    }

    /**
     * Récupère un commentaire par son id.
     * @param int $id : l'id du commentaire.
     * @return Comment|null : un objet Comment ou null si le commentaire n'existe pas.
     */
    public function getCommentById(int $id) : ?Comment
    {
        $sql = "SELECT * FROM comment WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        $comment = $result->fetch();
        if ($comment) {
            return new Comment($comment);
        }
        return null;
    }

    /**
     * Ajoute un commentaire.
     * @param Comment $comment : l'objet Comment à ajouter.
     * @return bool : true si l'ajout a réussi, false sinon.
     */
    public function addComment(Comment $comment) : bool
    {
        $sql = "INSERT INTO comment (pseudo, content, id_article, date_creation) VALUES (:pseudo, :content, :idArticle, NOW())";
        $result = $this->db->query($sql, [
            'pseudo' => $comment->getPseudo(),
            'content' => $comment->getContent(),
            'idArticle' => $comment->getIdArticle()
        ]);
        return $result->rowCount() > 0;
    }

    /**
     * Supprime un commentaire.
     * @param Comment $comment : l'objet Comment à supprimer.
     * @return bool : true si la suppression a réussi, false sinon.
     */
    public function deleteComment(Comment $comment) : bool
    {
        $sql = "DELETE FROM comment WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $comment->getId()]);
        return $result->rowCount() > 0;
    }

    /**
     * Supprime un commentaire par son ID.
     * @param int $id : l'ID du commentaire à supprimer.
     * @return bool : true si la suppression a réussi, false sinon.  
     */
    public function deleteCommentById(int $id) : bool
    {
        $sql = "DELETE FROM comment WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        return $result->rowCount() > 0;
    }

    /**
     * Récupère tous les commentaires avec les titres d'articles, avec pagination.
     * @param int $limit : nombre de commentaires par page
     * @param int $offset : décalage pour la pagination
     * @param string $sortBy : critère de tri ('date_creation', 'pseudo', 'article_title')
     * @param string $sortOrder : ordre de tri ('asc', 'desc')
     * @return array : tableau associatif avec commentaires et titres d'articles
     */
    public function getAllCommentsWithArticleTitle(int $limit = 20, int $offset = 0, string $sortBy = 'date_creation', string $sortOrder = 'desc') : array
    {
        // Sécurisation des paramètres
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        
        $allowedSortColumns = ['date_creation', 'pseudo', 'article_title'];
        $allowedSortOrders = ['asc', 'desc'];
        
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'date_creation';
        }
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $sql = "SELECT c.*, a.title as article_title 
                FROM comment c 
                LEFT JOIN article a ON c.id_article = a.id 
                ORDER BY " . $sortBy . " " . strtoupper($sortOrder) . " 
                LIMIT " . $limit . " OFFSET " . $offset;
        
        $result = $this->db->query($sql);
        $comments = [];

        while ($row = $result->fetch()) {
            $comment = new Comment($row);
            $comments[] = [
                'comment' => $comment,
                'article_title' => $row['article_title']
            ];
        }
        return $comments;
    }

    /**
     * Récupère le nombre total de commentaires.
     * @return int : le nombre total de commentaires.
     */
    public function getTotalCommentsCount() : int
    {
        $sql = "SELECT COUNT(*) as total FROM comment";
        $result = $this->db->query($sql);
        $data = $result->fetch();
        return (int) $data['total'];
    }

    /**
     * Récupère les commentaires récents avec le titre de l'article.
     * @param int $limit : le nombre de commentaires à récupérer.
     * @return array : un tableau associatif avec les données des commentaires et titres d'articles.
     */
    public function getRecentCommentsWithArticleTitle(int $limit = 10) : array
    {
        // On sécurise la valeur limit (doit être un entier positif)
        $limit = max(1, (int)$limit);
        $sql = "SELECT c.*, a.title as article_title 
                FROM comment c 
                LEFT JOIN article a ON c.id_article = a.id 
                ORDER BY c.date_creation DESC 
                LIMIT " . $limit;
        $result = $this->db->query($sql);
        $comments = [];

        while ($row = $result->fetch()) {
            $comment = new Comment($row);
            $comments[] = [
                'comment' => $comment,
                'article_title' => $row['article_title']
            ];
        }
        return $comments;
    }

    /**
     * Récupère les statistiques par mois (nombre de commentaires créés).
     * @return array : un tableau associatif avec les statistiques par mois.
     */
    public function getCommentStatsByMonth() : array
    {
        $sql = "SELECT 
                    YEAR(date_creation) as year, 
                    MONTH(date_creation) as month, 
                    COUNT(*) as count 
                FROM comment 
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

}
