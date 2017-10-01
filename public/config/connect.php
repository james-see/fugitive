<?php
$db = new SQLite3('mysqlitedb.db');

#$results = $db->query('CREATE TABLE chat(id INTEGER PRIMARY KEY ASC, content, user);');

#$results = $db->query('insert into chat(content,user) values("hello","ghostrider");');

$results = $db->query('select content,user from chat;');
while ($row = $results->fetchArray()) {
    foreach($row as $key => $value) {
        echo "$value posted $key<br/>";
    }
}
?>