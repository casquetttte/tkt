<!DOCTYPE html>
<html>
<head>
	<title>Jeux en commun Steam</title>
	<meta charset="UTF-8">
	<style>
		body {
      font-family: Arial, sans-serif;
      text-align: center;
    }

    form {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-right: 10px;
    }

    input[type="text"] {
      width: 300px;
      padding: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    input[type="submit"] {
      padding: 5px 20px;
      background-color: #4CAF50;
      border: none;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    h1 {
      margin-top: 50px;
    }

    h2 {
      margin-top: 20px;
    }

    ul {
      list-style: none;
      padding: 0;
    }

    li {
      margin-bottom: 10px;
      display: flex;
      align-items: center;
    }

    img {
      margin-right: 10px;
      width: 50px;
      height: 50px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

	</style>
</head>
<body>
	<h1>Jeux en commun Steam</h1>
	<form method="post">
		<label for="profile1">URL du profil Steam de l'utilisateur :</label>
		<input type="text" name="profile1" id="profile1">
		<br>
		<label for="profile2">URL du profil Steam de l'ami :</label>
		<input type="text" name="profile2" id="profile2">
		<br>
		<input type="submit" value="Trouver les jeux en commun">
	</form>
	<br>

<?php
// Steam API key
$apiKey = "6F8DA6E631ACA08FC0A895335157BCFD";

// Les ID de jeu en commun
$commonGames = array();

if(isset($_POST['profile1']) && isset($_POST['profile2'])) {
	$profile1 = $_POST['profile1'];
	$profile2 = $_POST['profile2'];

	// Récupère les jeux pour le profil 1
	$profile1Games = json_decode(file_get_contents("https://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=$apiKey&steamid=" . steamIDFromProfile($profile1) . "&include_appinfo=1"), true);

	// Récupère les jeux pour le profil 2
	$profile2Games = json_decode(file_get_contents("https://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=$apiKey&steamid=" . steamIDFromProfile($profile2) . "&include_appinfo=1"), true);

	// Boucle à travers les jeux du profil 1 et recherche les jeux en commun avec le profil 2
	foreach($profile1Games['response']['games'] as $game1) {
		foreach($profile2Games['response']['games'] as $game2) {
			if($game1['appid'] == $game2['appid']) {
				$commonGames[] = array(
					"appid" => $game1['appid'],
					"name" => $game1['name']
				);
			}
		}
	}

	// Affiche les jeux en commun
	if(count($commonGames) > 0) {
		echo "<h2>Jeux en commun entre les profils Steam :</h2>";
		echo "<ul>";
		foreach($commonGames as $game) {
			echo "<li>{$game['name']}</li>";
		}
		echo "</ul>";
	} else {
		echo "<p>Aucun jeu en commun trouvé.<br>Il est possible que l'un des 2 profils (ou les 2) soit en privé. Pensez à mettre votre profil ainsi que vos jeux en public.</p>";
	}
}

// Fonction pour récupérer l'ID Steam à partir de l'URL du profil Steam
function steamIDFromProfile($profile) {
    $content = file_get_contents($profile);
    $pattern = '/(https:\/\/steamcommunity.com\/profiles\/|https:\/\/steamcommunity.com\/id\/)([a-zA-Z0-9]+)/';
    preg_match($pattern, $content, $matches);
    $urlType = $matches[1];
    $id = $matches[2];
    if($urlType == "https://steamcommunity.com/id/") {
        $xml = simplexml_load_file("http://steamcommunity.com/id/{$id}/?xml=1");
        $json = json_encode($xml);
        $steamID64 = json_decode($json,TRUE)['steamID64'];
        return $steamID64;
    } else {
        return $id;
    }
}
?>
