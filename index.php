<?php
//#1
//по стуктуре таблицы - хранить дни рождения в datetime
"SELECT u.name, count(*)
        FROM users AS u
        INNER JOIN phone_numbers AS pn
        ON u.id = pn.user_id
        WHERE u.gender = 2 
            AND TIMESTAMPDIFF(YEAR, FROM_UNIXTIME(u.birth_date, '%Y-%m-%d'), CURDATE()) BETWEEN 18 AND 22
            GROUP BY id.users";

//#2
/**
 * Выводит изменненый url.
 *
 * @param $url string старый url
 *
 * @return string|void новый url
 */
function getValidUrl($url) 
{
    
    if (!$url) {
        return null;
    }
    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $output);
    foreach ($output as $key => $value) {
        if ($value == 3) {
            unset($output[$key]);
        }
    }
    $path = parse_url($url, PHP_URL_PATH);
    asort($output);
    $output['url'] = $path;
    $query = http_build_query($output);
    return $new_url = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/?' . $query;
}

$new_url = getValidUrl('https://www.somehost.com/test/index.html?param1=4&param2=3&param3=2&param4=1&param5=3');


//#3
//использовался паттерн Строитель
class Article 
{
    
    protected $text;
    protected $autor;
    
    public function __construct(User $user, $text) 
    {
        $this->autor = $user;
        $this->text = $text;
    }
    
    public function changeAuthor(User $user) 
    {
        $this->autor = $user;
    }
    
    public function getAuthor() 
    {
        return $this->autor;
    }
    
    public function getAricle() 
    {
        return $this->text;
    }
}
class User 
{
    
    protected $name;
    protected $articles = array();
    
    public function __construct($name) 
    {
        $this->name = $name;
    }
    
    public function createArticle($text):Article 
    {
        return $this->articles[] = new Article($this,$text);
    }
    
    public function getArticles() 
    {
        return $this->articles;
    }
    
    public function getName() 
    {
        return $this->name;
    }
}
//#4
//вообще нужно вынести в отдельный файл соединение с БД
// SQL-инъекции (нужно использовать pdo и псевдонимы), отсутствует проверка передаваемых значений как в функцию, так и из нее
//
$host = 'localhost';
$db   = 'database';
$user = 'root';
$pass = '123123';
$charset = 'utf8';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);
/**
 * Выводит ассоциативный массив в формате id => имя пользователя.
 *
 * @param $user_ids string|int список idпользователей через запятую, либо единичное id
 *
 * @return array|void 
 */
function loadUsersData($user_ids) 
{
    global $pdo;
    $data = null;
    
    if (!$user_ids) {
        return null;
    }
    
    if (is_int($user_ids)) {
        $stmt = $pdo->prepare('SELECT  `name`  FROM `user` WHERE `id` = ?');
        $stmt->execute(array($user_ids));
        foreach ($row = $stmt->fetchAll() as $result) {
            if ($result['name']) {
                return $data[$user_ids] = $result['name'];
            }
        }
        $pdo = null;
    } else {
        $user_ids = explode(',', $user_ids);
        $stmt = $pdo->prepare('SELECT  `name`  FROM `user` WHERE `id` = ?');
        foreach ($user_ids as $user_id) {
            if (is_int($user_id)) {
                $stmt->execute(array($user_id));
                foreach ($row = $stmt->fetchAll() as $result) {
                    if ($result['name']) {
                        $data[$user_id] = $result['name'];
                    }
                }               
            }
        }
        $stmt = null;
        $pdo = null;
        return $data;
    }
}
$data = loadUsersData($_GET['user_ids']);

if ($data) {
    foreach ($data as $user_id => $name) {
        echo "<a href=\"/show_user.php?id=$user_id\">$name</a>";
    }
}




$url = 'https://www.somehost.com/test/index.html?param1=4&param2=3&param3=2&param4=1&param5=3';
$query = parse_url($url, PHP_URL_QUERY);
parse_str($query, $output);
foreach ($output as $key => $value) {
    if ($value == 3) {
        unset($output[$key]);
    }
}
$path = parse_url($url, PHP_URL_PATH);
asort($output);
$output['url'] = $path;
$query = http_build_query($output);
$new_url = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/?' . $query;

