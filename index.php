<?php

/**
 * Commencez par importer le fichier sql live.sql via PHPMyAdmin.
 * 1. Sélectionnez tous les utilisateurs.
 * 2. Sélectionnez tous les articles.
 * 3. Sélectionnez tous les utilisateurs qui parlent de poterie dans un article.
 * 4. Sélectionnez tous les utilisateurs ayant au moins écrit deux articles.
 * 5. Sélectionnez l'utilisateur Jane uniquement s'il elle a écris un article ( le résultat devrait être vide ! ).
 *
 * ( PS: Sélectionnez, mais affichez le résultat à chaque fois ! ).
 */

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'live';

try {
    $bdd = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 1
    $request = $bdd->prepare("
        SELECT * FROM user
");

    $request->execute();
    echo '<pre>';
    print_r($request->fetchAll());
    echo "</pre><br><br>";

    // 2
    $request = $bdd->prepare("
        SELECT * FROM article
");

    $request->execute();
    echo '<pre>';
    print_r($request->fetchAll());
    echo "</pre><br><br>";

    // 3
    $request = $bdd->prepare("
        SELECT username FROM user
            WHERE id = ANY (SELECT user_fk FROM article WHERE contenu LIKE '%poterie%')
");

    $request->execute();

    foreach ($request->fetchAll() as $user_data) {
        echo $user_data['username']. " parle de poterie dans un article <br>";
    }

    // 4 Sélectionnez tous les utilisateurs ayant au moins écrit deux articles.
    $request = $bdd->prepare("
        SELECT username FROM user
            WHERE id = ANY (SELECT user_fk FROM article HAVING count(user_fk) >= 2)
");

    $request->execute();
    echo '<pre>';
    print_r($request->fetchAll());
    echo "</pre><br><br>";

    // 5 Sélectionnez l'utilisateur Jane uniquement s'il elle a écris un article ( le résultat devrait être vide ! ).
    $request = $bdd->prepare("
        SELECT username FROM user WHERE username LIKE 'jane%'
            AND id = ALL (SELECT user_fk FROM article)
");

    $request->execute();
    echo '<pre>';
    print_r($request->fetchAll());
    echo "</pre><br><br>";


} catch (PDOException $exception) {
    echo $exception->getMessage();
}