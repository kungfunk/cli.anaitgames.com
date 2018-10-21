<?php
namespace Comands\Migrate;

use Database\Repositories\CommentRepository;
use Database\Repositories\TagRepository;
use Models\Factories\CommentFactory;
use Models\Factories\TagFactory;
use Symfony\Component\Console\Helper\ProgressBar;
use PDO;

use Models\Factories\UserFactory;
use Models\Factories\PostFactory;
use Database\Repositories\UserRepository;
use Database\Repositories\PostRepository;

class Migrate
{
    private $connectionFrom;
    private $connectionTo;

    private $temp_users_map = [];
    private $temp_posts_map = [];

    private const JPG_MIME = 'image/jpeg';
    private const PNG_MIME = 'image/png';
    private const GIF_MIME = 'image/gif';
    private const JPG_EXTENSION = '.jpg';
    private const PNG_EXTENSION = '.png';
    private const GIF_EXTENSION = '.gif';
    private const OLD_NO_PATREON = 0;
    private const OLD_PATREON_BRONCE = 16;
    private const OLD_PATREON_PLATA = 17;
    private const OLD_PATREON_ORO = 18;
    private const NEW_NO_PATREON = 0;
    private const NEW_PATREON_BRONCE = 1;
    private const NEW_PATREON_PLATA = 2;
    private const NEW_PATREON_ORO = 3;
    private const NEW_ROLE_UNCONFIRMED = 0;
    private const NEW_ROLE_USER = 1;
    private const NEW_ROLE_MOD = 2;
    private const NEW_ROLE_EDITOR = 3;
    private const NEW_ROLE_ADMIN = 4;

    private const NEW_STATUS_DRAFT = 'draft';
    private const NEW_STATUS_PUBLISHED = 'published';
    private const NEW_STATUS_DELETED = 'trash';
    private const OLD_STATUS_BORRADOR = 'borrador';
    private const OLD_STATUS_REVISAR = 'revisar';
    private const OLD_STATUS_PUBLICADO = 'publicado';
    private const OLD_STATUS_PRIVADO = 'privado';
    private const OLD_STATUS_PAPELERA = 'papelera';
    private const NEW_TYPE_NEWS = 1;
    private const NEW_TYPE_ARTICLE = 2;
    private const NEW_TYPE_ANALYSIS = 3;
    private const NEW_TYPE_STREAMING = 4;
    private const NEW_TYPE_PODCAST = 5;
    private const OLD_TYPE_ARTICULO = 'articulo';
    private const OLD_TYPE_PRIMERAS_IMPRESIONES = 'primeras impresiones';
    private const OLD_TYPE_AVANCE = 'avance';
    private const OLD_TYPE_ANALISIS = 'analisis';
    private const OLD_TYPE_NOTICIA = 'noticia';
    private const OLD_TYPE_OPINION = 'opinion';
    private const OLD_TYPE_BLOG_EDITOR = 'blog de editor';
    private const OLD_TYPE_BLOG_USUARIO = 'blog de usuario';
    private const OLD_TYPE_EDITIORIAL = 'editorial';
    private const OLD_TYPE_GALERIA = 'galeria de imagenes';
    private const OLD_TYPE_VIDEO = 'video';
    private const OLD_TYPE_ENTREVISTA = 'entrevista';
    private const OLD_TYPE_ENCUESTA = 'encuesta';
    private const OLD_TYPE_CONCURSO = 'concurso';
    private const OLD_TYPE_SORTEO = 'sorteo';
    private const OLD_TYPE_COBERTURA = 'cobertura en directo';
    private const OLD_TYPE_PODCAST = 'podcast';
    private const DEFAULT_AUTHOR_ID = 4;

    private const SQL_GET_ALL_USERS = 'SELECT * FROM usuario';
    private const SQL_COUNT_ALL_USERS = 'SELECT count(*) FROM usuario';
    private const SQL_GET_LOGROS_FORM_USER = 'SELECT * FROM usuario_logros WHERE usuario_id = ? AND logro_id in (?, ?, ?) ORDER BY logro_id DESC';

    private const SQL_GET_POSTS = "SELECT * FROM articulo WHERE estado NOT IN ('papelera', 'borrador')";
    private const SQL_COUNT_POSTS = "SELECT count(*) FROM articulo WHERE estado NOT IN ('papelera', 'borrador')";
    private const SQL_GET_COMMENTS = "SELECT * FROM foro_mensajes WHERE id_hilo = ?";

    private const SQL_GET_PLATAFORMA = "SELECT * FROM plataforma";
    private const SQL_GET_FICHA = "SELECT * FROM fichas";
    private const SQL_GET_JUEGO = "SELECT * FROM juego";
    private const SQL_COUNT_PLATAFORMA = "SELECT count(*) FROM plataforma";
    private const SQL_COUNT_FICHA = "SELECT count(*) FROM fichas";
    private const SQL_COUNT_JUEGO = "SELECT count(*) FROM juego";
    private const SQL_GET_PLATAFORMA_RELATIONSHIP = "SELECT * FROM articulo_plataforma WHERE id_plataforma = ?";
    private const SQL_GET_FICHA_RELATIONSHIP = "SELECT * FROM articulo_fichas_relacionados WHERE id_ficha = ?";
    private const SQL_GET_JUEGO_RELATIONSHIP = "SELECT * FROM articulo_juegos_relacionados WHERE id_juego = ?";

    private const CLEAN_UP_ACTIONS = [
        "DELETE FROM `usuario` WHERE `lastseen` = '0000-00-00 00:00:00' AND `num_logins` = '0'",
        "AND (SELECT Count(*) FROM `foro_mensajes` WHERE `foro_mensajes.id_usuario` = `usuario.id`) < 1",
        "AND (SELECT Count(*) FROM `usuario_actividad` WHERE `usuario_actividad.id_usuario` = `usuario.id`) < 1",
        "UPDATE posts SET slug = REPLACE(slug, '%25c2%25bf', '') WHERE slug like '%%25c2%25bf%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25c2%25a1', '') WHERE slug like '%%25c2%25a1%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25c2%25bb', '') WHERE slug like '%%25c2%25bb%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25e3%2581%25e3%2581%25be', '') WHERE slug like '%%25e3%2581%25e3%2581%25be%'",
        "UPDATE posts SET slug = REPLACE(slug, '%2599', '') WHERE slug like '%%2599%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25e2', '') WHERE slug like '%%25e2%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25aa', '') WHERE slug like '%%25aa%'",
        "UPDATE posts SET slug = REPLACE(slug, '%259f', '') WHERE slug like '%%259f%'",
        "UPDATE posts SET slug = REPLACE(slug, '%259c', '') WHERE slug like '%%259c%'",
        "UPDATE posts SET slug = REPLACE(slug, '%2580', '') WHERE slug like '%%2580%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25c2', '') WHERE slug like '%%25c2%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25a5', '') WHERE slug like '%%25a5%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25ba', '') WHERE slug like '%%25ba%'",
        "UPDATE posts SET slug = REPLACE(slug, '%259d', '') WHERE slug like '%%259d%'",
        "UPDATE posts SET slug = REPLACE(slug, '%2593', '') WHERE slug like '%%2593%'",
        "UPDATE posts SET slug = REPLACE(slug, '%2594', '') WHERE slug like '%%2594%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25b4', '') WHERE slug like '%%25b4%'",
        "UPDATE posts SET slug = REPLACE(slug, '%25a6', '') WHERE slug like '%%25a6%'",
        "UPDATE posts SET slug = REPLACE(slug, '%2584%25a2', '') WHERE slug like '%%2584%25a2%'",
        "UPDATE posts SET slug = TRIM(LEADING '-' FROM slug)",
        "UPDATE posts SET slug = TRIM(TRAILING '-' FROM slug)",
        "UPDATE posts SET slug = REPLACE(slug, '%2', '') WHERE slug like '%%2%'",
        "UPDATE posts SET slug = REPLACE(slug, '---', '-') WHERE slug like '%---%'",
        "UPDATE posts SET slug = REPLACE(slug, '--', '-') WHERE slug like '%--%'",
    ];

    public function __construct(PDO $connectionFrom, PDO $connectionTo, ProgressBar $progressBar)
    {
        $this->connectionFrom = $connectionFrom;
        $this->connectionTo = $connectionTo;
        $this->progressBar = $progressBar;

        $this->userRepository = new UserRepository($this->connectionTo);
        $this->postRepository = new PostRepository($this->connectionTo);
        $this->commentRepository = new CommentRepository($this->connectionTo);
        $this->tagRepository = new TagRepository($this->connectionTo);
    }

    public function cleanUp()
    {
        $this->progressBar->setMaxSteps(count(self::CLEAN_UP_ACTIONS));
        $this->progressBar->start();

        foreach (self::CLEAN_UP_ACTIONS as $action) {
            $this->connectionFrom->query($action);
            $this->progressBar->advance();
        }

        $this->progressBar->finish();
    }

    public function migrateUsers()
    {
        $count = $this->connectionFrom->query(self::SQL_COUNT_ALL_USERS);
        $userCount = $count->fetchColumn();

        $this->progressBar->setMaxSteps($userCount);
        $this->progressBar->start();

        $statement = $this->connectionFrom->prepare(self::SQL_GET_ALL_USERS);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $select_logros = $this->connectionFrom->prepare(self::SQL_GET_LOGROS_FORM_USER);
            $select_logros->execute([
                $row->id,
                self::OLD_PATREON_BRONCE,
                self::OLD_PATREON_PLATA,
                self::OLD_PATREON_ORO,
            ]);

            $logros_usuario = $select_logros->fetchAll();

            $user = UserFactory::create(
                $row->usuario,
                $row->email,
                password_hash($row->password, PASSWORD_DEFAULT),
                $row->usuario_url,
                $this->userGetRole($row->email_confirmado, $row->id_rol),
                $this->userGetPatreonLevel($row->patreon, $logros_usuario),
                $this->userGetAvatar($row->usuario_url, $row->avatar_mime_type),
                $row->rango,
                $row->twitter,
                $row->fecha_alta
            );

            $this->userRepository->save($user);
            $this->temp_users_map[$row->id] = $this->userRepository->getLastInsertedId();
            $this->progressBar->advance();
        }

        $this->progressBar->finish();
    }

    public function migratePosts()
    {
        $count = $this->connectionFrom->query(self::SQL_COUNT_POSTS);
        $postCount = $count->fetchColumn();

        $this->progressBar->setMaxSteps($postCount);
        $this->progressBar->start();

        $statement = $this->connectionFrom->prepare(self::SQL_GET_POSTS);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            if($row->creador == 0) {
                $userId = self::DEFAULT_AUTHOR_ID;
            }
            else {
                $userId = $this->temp_users_map[$row->id] || self::DEFAULT_AUTHOR_ID;
            }

            $post = PostFactory::create(
                $userId,
                $this->getPostTypeId($row->tipo),
                $this->getStatus($row->estado),
                $row->fecha_publicacion,
                $row->titular,
                $row->subtitular,
                $row->url,
                $row->cuerpo, //TODO: format with markdown parser
                $row->extracto,
                $row->otro,
                $row->nota_anait,
                $row->numero_visitas,
                null
            );

            $this->postRepository->save($post);
            $this->temp_posts_map[$row->id] = $this->postRepository->getLastInsertedId();

            $this->migrateComments($row->id_foro_hilo, $this->temp_posts_map[$row->id]);

            $this->progressBar->advance();
        }

        $this->progressBar->finish();
    }

    public function migrateTags()
    {
        $platform = $this->connectionFrom->query(self::SQL_COUNT_PLATAFORMA);
        $platformCount = $platform->fetchColumn();

        $file = $this->connectionFrom->query(self::SQL_COUNT_FICHA);
        $fileCount = $file->fetchColumn();

        $game = $this->connectionFrom->query(self::SQL_COUNT_JUEGO);
        $gameCount = $game->fetchColumn();

        $this->progressBar->setMaxSteps($platformCount + $fileCount + $gameCount);
        $this->progressBar->start();

        $statement = $this->connectionFrom->prepare(self::SQL_GET_PLATAFORMA);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $tag = TagFactory::create($row->nombre, $row->url);
            $this->tagRepository->save($tag);
            $tag_id = $this->tagRepository->getLastInsertedId();

            $relationshipQuery = $this->connectionFrom->prepare(self::SQL_GET_PLATAFORMA_RELATIONSHIP);
            $relationshipQuery->execute([$row->id]);

            while ($relationship = $relationshipQuery->fetch(PDO::FETCH_OBJ)) {
                $post_new_id = $this->temp_posts_map[$relationship->id_articulo];
                $this->tagRepository->addRelationship($tag_id, $post_new_id);
            }

            $this->progressBar->advance();
        }

        $statement = $this->connectionFrom->prepare(self::SQL_GET_FICHA);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $tag = TagFactory::create($row->nombre, $row->url);
            $this->tagRepository->save($tag);
            $tag_id = $this->tagRepository->getLastInsertedId();

            $relationshipQuery = $this->connectionFrom->prepare(self::SQL_GET_FICHA_RELATIONSHIP);
            $relationshipQuery->execute([$row->id]);

            while ($relationship = $relationshipQuery->fetch(PDO::FETCH_OBJ)) {
                $post_new_id = $this->temp_posts_map[$relationship->id_articulo];
                $this->tagRepository->addRelationship($tag_id, $post_new_id);
            }

            $this->progressBar->advance();
        }

        $statement = $this->connectionFrom->prepare(self::SQL_GET_JUEGO);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $tag = TagFactory::create($row->nombre, $row->url);
            $this->tagRepository->save($tag);
            $tag_id = $this->tagRepository->getLastInsertedId();

            $relationshipQuery = $this->connectionFrom->prepare(self::SQL_GET_JUEGO_RELATIONSHIP);
            $relationshipQuery->execute([$row->id]);

            while ($relationship = $relationshipQuery->fetch(PDO::FETCH_OBJ)) {
                $post_new_id = $this->temp_posts_map[$relationship->id_articulo];
                $this->tagRepository->addRelationship($tag_id, $post_new_id);
            }

            $this->progressBar->advance();
        }

        $this->progressBar->finish();
    }

    private function migrateComments($hiloId, $postId)
    {
        $statement = $this->connectionFrom->prepare(self::SQL_GET_COMMENTS);
        $statement->execute([$hiloId]);

        while ($comment = $statement->fetch(PDO::FETCH_OBJ)) {
            $userId = $this->temp_users_map[$comment->id_usuario];

            if($userId) {
                $comment = CommentFactory::create(
                    $postId,
                    $userId,
                    $comment->texto,
                    $comment->fecha
                );

                $this->commentRepository->save($comment);
            }
        }
    }

    private function userGetAvatar($usuario_url, $old_avatar) {
        switch($old_avatar) {
            case self::JPG_MIME:
                return $usuario_url.self::JPG_EXTENSION;
                break;
            case self::PNG_MIME:
                return $usuario_url.self::PNG_EXTENSION;
                break;
            case self::GIF_MIME:
                return $usuario_url.self::GIF_EXTENSION;
                break;
            default:
                return null;
                break;
        }
    }

    private function userGetRole($email_confirmado, $old_role) {
        if($email_confirmado == 0)
            return self::NEW_ROLE_UNCONFIRMED;

        switch($old_role) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
                return self::NEW_ROLE_USER;
                break;
            case 8:
                return self::NEW_ROLE_MOD;
                break;
            case 6:
                return self::NEW_ROLE_EDITOR;
                break;
            case 7:
                return self::NEW_ROLE_ADMIN;
                break;
            default:
                return self::NEW_ROLE_UNCONFIRMED;
        }
    }

    function userGetPatreonLevel($is_patreon, $logros_usuario) {
        if($is_patreon == self::OLD_NO_PATREON)
            return self::NEW_NO_PATREON;
        switch($logros_usuario[0]->logro_id) {
            case self::OLD_PATREON_BRONCE:
                return self::NEW_PATREON_BRONCE;
                break;
            case self::OLD_PATREON_PLATA:
                return self::NEW_PATREON_PLATA;
                break;
            case self::OLD_PATREON_ORO:
                return self::NEW_PATREON_ORO;
                break;
        }
        return self::NEW_NO_PATREON;
    }

    function getPostTypeId($old_post_type) {
        switch($old_post_type) {
            case self::OLD_TYPE_NOTICIA:
            case self::OLD_TYPE_VIDEO:
            case self::OLD_TYPE_CONCURSO:
            case self::OLD_TYPE_ENCUESTA:
            case self::OLD_TYPE_GALERIA:
            case self::OLD_TYPE_SORTEO:
                return self::NEW_TYPE_NEWS;
                break;
            case self::OLD_TYPE_ARTICULO:
            case self::OLD_TYPE_PRIMERAS_IMPRESIONES:
            case self::OLD_TYPE_AVANCE:
            case self::OLD_TYPE_OPINION:
            case self::OLD_TYPE_BLOG_EDITOR:
            case self::OLD_TYPE_BLOG_USUARIO:
            case self::OLD_TYPE_EDITIORIAL:
            case self::OLD_TYPE_ENTREVISTA:
                return self::NEW_TYPE_ARTICLE;
                break;
            case self::OLD_TYPE_ANALISIS:
                return self::NEW_TYPE_ANALYSIS;
                break;
            case self::OLD_TYPE_COBERTURA:
                return self::NEW_TYPE_STREAMING;
                break;
            case self::OLD_TYPE_PODCAST:
                return self::NEW_TYPE_PODCAST;
                break;
        }
    }

    function getStatus($old_status) {
        switch($old_status) {
            case self::OLD_STATUS_BORRADOR:
            case self::OLD_STATUS_REVISAR:
            case self::OLD_STATUS_PRIVADO:
                return self::NEW_STATUS_DRAFT;
                break;
            case self::OLD_STATUS_PUBLICADO:
                return self::NEW_STATUS_PUBLISHED;
                break;
            case self::OLD_STATUS_PAPELERA:
                return self::NEW_STATUS_DELETED;
                break;
        }
    }
}
