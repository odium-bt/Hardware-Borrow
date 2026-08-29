<?php

namespace CoteInfo\Controller;

use CoteInfo\Model\UserModel;
use CoteInfo\Model\CommentsModel;
use CoteInfo\Model\NotesModel;
use CoteInfo\Model\NewsModel;
use CoteInfo\Model\MediaModel;
use CoteInfo\Model\ReportsModel;
use PDOException;
/*
 * Classe Profile
 * Gère la page du profil utilisateur
 * Requête à la base de données :
 */

class Profile
{
    protected array $errors = [];

    protected int $userID;
    protected bool $isAdmin;

    protected array $user;

    protected array $reports;
    protected array $reportedComments;
    protected bool $reportDeleteStatus;

    public function __construct()
    {
        $userModel = new UserModel;
        $this->userID = $_SESSION['user_id'];
        $this->isAdmin = $userModel->isAdmin($this->userID) ?? false;


        // Changement d'avatar
        if (isset($_FILES['new-avatar'])) {
            $this->replaceAvatar($_FILES['new-avatar']);
        }

        if (isset($_GET['tab']) && $_GET['tab'] === 'reports') {
            $this->reportedComments = $this->getReports();
        }


        if (isset($_GET['delete'])) {
            $this->reportDeleteStatus = $this->setReportAsDeleted($_GET['delete']);
        }

        $this->user = $userModel->getById($this->userID);

        require ROOT . "/app/View/user_view.php";
    }

    /*
     * Fonction getComments
     * paramètre : /
     * résultat : commentaires de l'utilisateur
     */
    protected function getComments()
    {
        if (!isset($commentsModel)) {
            $commentsModel = new CommentsModel;
        }

        $comments = $commentsModel->getCommentsByUser($this->userID) ?? [];

        // Ajoute les notes associées aux commentaires
        if (!isset($notesModel)) {
            $notesModel = new NotesModel();
        }
        $notes = $notesModel->getNoteForComments($comments);
        $comments = $notesModel->linkNotes($comments, $notes);

        return $comments;
    }

    /*
     * Fonction getReports
     * paramètre : /
     * résultat : commentaires signalés
     */
    protected function getReports()
    {
        if (!isset($reportsModel)) {
            $reportsModel = new ReportsModel;
        }

        $this->reports = $reportsModel->getAll() ?? [];

        if (!isset($commentsModel)) {
            $commentsModel = new CommentsModel;
        }
        $commentIDs = array_column($this->reports, 'id_comment'); // créé un array avec les ID des commentaires signalés
        $comments = $commentsModel->getAllByIDExceptDeleted($commentIDs); // récupère les commentaires signalés

        // Ajout des notes associées aux commentaires
        if (!isset($notesModel)) {
            $notesModel = new NotesModel();
        }
        $notes = $notesModel->getNoteForComments($comments); // récupère les notes associées
        $comments = $notesModel->linkNotes($comments, $notes); // ajoute les notes à l'array $comments

        return $comments;
    }

    /*
     * Fonction setReportAsDeleted
     * paramètre : id commentaire
     * résultat : marque un commentaire signalé comme supprimé
     */
    protected function setReportAsDeleted(int $ID)
    {
        // Est-ce que le commentaire est signalé
        if (!in_array($ID, array_column($this->reports, 'id_comment'))) {
            return false;
        }
        // Appelle fonction qui marque comme supprimé
        $commentsModel = new CommentsModel;
        return $commentsModel->setDeleted($ID);
    }

    /*
     * Fonction getArticles
     * paramètre : /
     * résultat : commentaires de l'utilisateur
     */
    protected function getArticles()
    {
        $newsModel = new NewsModel;
        $articleIDs = $newsModel->getArticleIDsByUser($this->userID) ?? [];
        if (!empty($articleIDs)) {
            $idList = array_column($articleIDs, "id_news");
            // Va chercher les articles (sans thumbnail) dans la table news
            $articles = $newsModel->getPreviews($idList);
            $mediaModel = new MediaModel;
            return $mediaModel->getThumbnails($articles);
        } else {
            return [];
        }
    }


    /*
     * Fonction replaceAvatar
     * paramètre : fichier envoyé par l'utilisateur
     * résultat : sauvegarde le nouvel avatar ou renvoie une erreur
     *            si sauvegardé = true, sinon = false
     */
    private function replaceAvatar($avatar)
    {
        if (empty($avatar['name'])) {
            $this->errors['avatar'] = "Aucun fichier détecté.";
            return false;
        }
        if ($avatar['error'] !== UPLOAD_ERR_OK) {
            $this->errors['avatar'] = "Une erreur s'est produite lors de l'envoi de l'image.";
            return false;
        }

        $fileTmp = $avatar['tmp_name'];

        // Vérifie que c'est bien une image
        $imageInfo = getimagesize($fileTmp);
        if ($imageInfo === false) {
            $this->errors['avatar'] = "Ce fichier n'est pas une image.";
            return false;
        }

        $mime = $imageInfo['mime'];

        // Types autorisés
        $allowed = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp'
        ];

        if (!isset($allowed[$mime])) {
            $this->errors['avatar'] = "L'image doit être en format png, jpg ou webp.";
            return false;
        }

        // Création image source
        switch ($mime) {
            case 'image/png':
                $src = imagecreatefrompng($fileTmp);
                break;
            case 'image/jpeg':
                $src = imagecreatefromjpeg($fileTmp);
                break;
            case 'image/webp':
                $src = imagecreatefromwebp($fileTmp);
                break;
            default:
                $this->errors['avatar'] = "L'image doit être en format png, jpg ou webp.";
                return false;
        }

        // Redimensionne 
        $newWidth = 100;
        $newHeight = 100;

        $destination = imagecreatetruecolor($newWidth, $newHeight);

        // Contrôle transparence
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
        }

        // Copie et redimensionne l'image source vers la destination
        imagecopyresampled($destination, $src, 0, 0, 0, 0, $newWidth, $newHeight, $imageInfo[0], $imageInfo[1]);

        // Définis le dossier de sauvegarde
        $uploadDir = ROOT . "/public/images/avatars/";

        // Nom sécurisé
        $fileName = uniqid("avatar_", true) . "." . $allowed[$mime];
        $path = $uploadDir . $fileName;

        // Sauvegarde
        $saved = false;
        switch ($mime) {
            case 'image/png':
                $saved = imagepng($destination, $path);
                break;
            case 'image/jpeg':
                $saved = imagejpeg($destination, $path, 90);
                break;
            case 'image/webp':
                $saved = imagewebp($destination, $path);
                break;
        }

        if (!$saved) {
            $this->errors['avatar'] = "Erreur lors de la sauvegarde de l'avatar.";
            return false;
        }

        $userModel = new UserModel();
        $oldAvatarName = $userModel->getAvatar($this->userID);


        // Mise à jour de la base de données
        if (!$userModel->updateAvatar($this->userID, $fileName)) {
            // Supprime le fichier si la mise à jour échoue
            unlink($path);
            $this->errors['avatar'] = "Erreur lors de la mise à jour du profil.";
            return false;
        }

        // Si le code arrive jusqu'ici sans erreurs, supprime l'avatar précédent
        if ($oldAvatarName["avatar"] !== null) {
            $oldAvatar = $uploadDir . $oldAvatarName["avatar"];
            unlink($oldAvatar);
        }

        return true;
    }
}
