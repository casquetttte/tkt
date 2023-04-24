<!DOCTYPE html>
<html>
<head>
	<title>Jeux en commun Steam</title>
	<meta charset="UTF-8">
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
							"name" => $game1['name'],
							"logo" => "http://media.steampowered.com/steamcommunity/public/images/apps/{$game1['appid']}/{$game1['img_logo_url']}.jpg"
						);
					}
				}
			}

			// Affiche les jeux en commun
			if(count($commonGames) > 0) {
				echo "<h2>Jeux en commun entre les profils Steam :</h2>";
				echo "<ul>";
				foreach($commonGames as $game) {
					echo "<li><img src='{$game['logo']}' /> {$game['name']}</li>";
				}
				echo "</ul>";
			} else {
				echo "<p>Aucun jeu en commun trouvé.</p>";
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