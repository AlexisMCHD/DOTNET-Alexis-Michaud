<?php 

/**
 * Cette classe contient toutes les requêtes SQL !
 * Une requête par méthode ! 
 * SRP : single responsability principle
 */
class Manager
{
    //crée une nouvel utilisateur en bdd 
    public function saveNewUser($user)
    {
        $sql = "INSERT INTO 
                users (email, username, password, avatar, created_date) 
                VALUES 
                (:email, :username, :password, :avatar, NOW())";

        //on utilise la classe DbConnection pour récupérer 
        //notre connexion à la base de données ($pdo)
        //pamayim nekudotayim ou double : 
        //pour appeler les méthodes statiques ! 
        $pdo = DbConnection::getPdo();

        //si la méthode n'était pas statique...
        //$dbConnection = new DbConnection();
        //$pdo = $dbConnection->getPdo();

        //envoie la requête au serveur sql
        $stmt = $pdo->prepare($sql);

        //exécute la requête SQL avec les données du user
        //on appelle tous les getters de notre classe pour récupérer les données
        $stmt->execute([
            ":email" => $user->getEmail(),
            ":username" => $user->getUsername(),
            ":password" => $user->getPassword(),
            ":avatar" => $user->getAvatar(),
        ]);
    }

    //récupère une ligne depuis la table users
    //en fonction d'un username ou d'un email
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        $sql = "SELECT * FROM users 
                WHERE username = :username 
                OR email = :email";

        //récupère notre connexion à la bdd
        $pdo = DbConnection::getPdo();

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":username" => $usernameOrEmail,
            ":email" => $usernameOrEmail,
        ]);

        $foundUser = $stmt->fetch();
        return $foundUser;
    }

    public function saveNewPost($title, $content, $filename)
    {
        $sql = "INSERT INTO posts
                (title, content, picture, author_id, date_created) 
                VALUES 
                (:title, :content, :picture, :author_id, NOW())";
        
        $pdo = DbConnection::getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":title" => $title,
            ":content" => $content,
            ":picture" => $filename,
            ":author_id" => $_SESSION['user']['id'],
        ]);

        //cette fonction nous retourne le dernier id créé 
        //par CETTE CONNEXION
        $newPostId = $pdo->lastInsertId();
        return $newPostId;
    }

}