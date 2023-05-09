<?php
if(isset($_POST['felhasznaloNev']) && isset($_POST['jelszo']) && isset($_POST['vezeteknev']) && isset($_POST['keresztnev'])) {
    try {
        // Kapcsolódás
        $dbh = new PDO('mysql:host=localhost;dbname=paneldb', 'root', '',
                        array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
        $dbh->query('SET NAMES utf8 COLLATE utf8_hungarian_ci');
        
        // Létezik már a felhasználói név?
        $sqlSelect = "select id from felhasznalok where felhasznaloNev = :felhasznaloNev";
        $sth = $dbh->prepare($sqlSelect);
        $sth->execute(array(':felhasznaloNev' => $_POST['felhasznalo']));
        if($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $uzenet = "A felhasználói név már foglalt!";
            $ujra = "true";
        }
        else {
            // Ha nem létezik, akkor regisztráljuk
            $sqlInsert = "insert into felhasznalok(id, vezeteknev, keresztnev, felhasznaloNev, jelszo)
                          values(0, :vezeteknev, :vezeteknev, :felhasznaloNev, :jelszo)";
            $stmt = $dbh->prepare($sqlInsert); 
            $stmt->execute(array(':vezeteknev' => $_POST['vezeteknev'], ':keresztnev' => $_POST['keresztnev'],
                                 ':felhasznaloNev' => $_POST['felhasznaloNev'], ':jelszo' => sha1($_POST['jelszo']))); 
            if($count = $stmt->rowCount()) {
                $newid = $dbh->lastInsertId();
                $uzenet = "A regisztrációja sikeres.<br>Azonosítója: {$newid}";                     
                $ujra = false;
            }
            else {
                $uzenet = "A regisztráció nem sikerült.";
                $ujra = true;
            }
        }
    }
    catch (PDOException $e) {
        $uzenet = "Hiba: ".$e->getMessage();
        $ujra = true;
    }      
}
else {
    header("Location: .");
}
?>