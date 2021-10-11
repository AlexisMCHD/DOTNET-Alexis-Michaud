<?php 

class Controller 
{
    public function home()
    {
        //créer une instance du manager
        $manager = new Manager();

        //appeler la méthode findPosts et stocker le résultat dans une variable
        //$posts = $manager->findPosts();

        //dans home.php, on va afficher en boucle la variable
        include("templates/home.php");
    }

    public function detail()
    {
        $id = $_GET['id'];
        $manager = new Manager();
        //$post = $manager->findPostById($id);
        
        include("templates/detail.php");
    }

    public function createPost()
    {
        //si on n'a pas d'utilisateur connecté... 
        //on veut le dégager d'ici, il n'est pas autorisé
        if (empty($_SESSION['user'])){
            //on invite gentiment le user à se connecter...
            header("Location: index.php?page=connexion");
            //on s'assure que le reste du code n'exécute pas
            die();
        }

        //traitement du formulaire s'il est soumis
        $errors = [];
        if (!empty($_POST)) {
            $title      = strip_tags($_POST['title']);
            $content    = strip_tags($_POST['content']);

            //seulement si on a un upload, on valide ! 
            //erreur 4 : veut dire qu'aucun fichier n'a été envoyé
            if($_FILES['pic']['error'] != 4){
                //le fichier temporaire, uploadé sur le serveur
                $file = $_FILES['pic']['tmp_name'];

                //on s'assure que le type du fichier est safe
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file);

                //les types mime que j'accepte
                $acceptedTypes = ["image/jpeg", "image/jpg", "image/png"];

                //je cherche le mime du fichier parmi ceux que j'accepte
                if (!in_array($mime, $acceptedTypes)) {
                    $errors[] = "Type de fichier non accepté !";
                }

                // renseigner correctement les tailles dans le php.ini !!!
                // post_max_size 
                // upload_max_filesize 
                $size = $_FILES['pic']['size'];
                if ($size > 20000000) {
                    $errors[] = "Fichier trrop gros. 20 mb max svp.";
                }

                //génère une chaîne toujours unique

                //on devine l'extension du fichier
                $extension = str_replace("image/", ".", $_FILES['pic']['type']);
                    
                $newFilename = uniqid() . $extension;
                       

            //validation des valeurs
            if (empty($title)){
                $errors[] = "Le titre est requis !";
            }
            elseif (strlen($title) > 191){
                $errors[] = "191 caractères max pour le titre svp !";
            }
            if (empty($content)){
                $errors[] = "Le contenu de l'article est requis !";
            }
            elseif (strlen($content) > 10000){
                $errors[] = "10000 caractères max pour le contenu svp !";
            }
        }
   
        //si je n'ai pas trouvé d'erreur... 
        if (empty($errors)){
            $manager = new Manager();
            $newPostId = $manager->saveNewPost($title, $content, $newFilename);     
            //redirige vers la page de détail de ce nouvel article 
            header("Location: index.php?page=detail&id=$newPostId");
            die();
        }
            }
            include("templates/404.php");
    }
}          
    
