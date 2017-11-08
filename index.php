<?php
//#1

"SELECT name.users, count(phone.phone_numbers)
        FROM users
        LEFT JOIN phone_numbers ON id.users = user_id.phone_numbers
        WHERE gender.users = 2 
            AND DATEDIF(year,birth_date,GETDATE()) BETWEEN (18, 22)
            GROUP BY id.users";

//#2
/**
 * Выводит изменненый url.
 *
 * @param $url string старый url
 *
 * @return string|void новый url
 */
function get_valid_url($url) {
    if (!$url){
        return null;
    }
    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $output);
    foreach ($output as $key => $value)
    {
        if ($value == 3)
        {
            unset($output[$key]);
        }
    }
    $path = parse_url($url, PHP_URL_PATH);
    asort($output);
    $output['url'] = $path;
    $query = http_build_query($output);
    return $new_url = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/?' . $query;
}

$new_url = get_valid_url('https://www.somehost.com/test/index.html?param1=4&param2=3&param3=2&param4=1&param5=3');


//#3
//использовался паттерн Строитель
class Article {
    
    protected $text;
    protected $autor;
    
    public function __construct(User $user, $text) {
        $this->autor = $user;
        $this->text = $text;
    }
    
    public function change_author(User $user) {
        $this->autor = $user;
    }
    
    public function get_author() {
        return $this->autor;
    }
    
    public function get_aricle() {
        return $this->text;
    }
}
class User {
    
    protected $name;
    protected $articles = array();
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function create_article($text):Article {
        return $this->articles[] = new Article($this,$text);
    }
    
    public function get_articles() {
        return $this->articles;
    }
    
    public function get_name() {
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
$load_users_data = function ($user_ids) use ($pdo) {

    $data = null;
    
    if (!$user_ids) {
        return null;
    }
    
    if (is_int($user_ids)) {
        $stmt = $pdo->prepare('SELECT  `name`  FROM `user` WHERE `id` = ?');
        $stmt->execute(array($user_ids));
        foreach ($row = $stmt->fetchAll() as $result){
            if($result['name']){
                return $data[$user_ids] = $result['name'];
            }
        }
        $pdo = null;
    } else {
        $user_ids = explode(',', $user_ids);
        foreach ($user_ids as $user_id) {
            if (is_int($user_id)) {
                $stmt = $pdo->prepare('SELECT  `name`  FROM `user` WHERE `id` = ?');
                $stmt->execute(array($user_id));
                foreach ($row = $stmt->fetchAll() as $result){
                    if($result['name']){
                        $data[$user_id] = $result['name'];
                    }
                }               
            }
        }
        $pdo = null;
        return $data;
    }
};
$data = $load_users_data($_GET['user_ids']);
if ($data) {
    foreach ($data as $user_id => $name){
        echo "<a href=\"/show_user.php?id=$user_id\">$name</a>";
    }
}
