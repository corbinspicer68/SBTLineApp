<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'vendor/autoload.php';

$user     = 'root';
$password = 'root';
$db       = 'SBT';
$host     = 'localhost';
$port     = 8889;

$mysqli = new mysqli($host, $user, $password, $db);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

function sendText()
{
    return true;
}

function sendDetails($gameID, $message = null)
{
    
	global $mysqli;
    
    
    if ($stmt = $mysqli->prepare("SELECT gameID FROM games WHERE gameID = ?")) {
        
        $stmt->bind_param("s", $gameID);
        $stmt->execute();
        $stmt->store_result();
        echo $gameID;
        
        
        if ($stmt->num_rows > 0 && 1 != 1) {
            
            echo 'Record already exists';
        } else {
            
            
            
            $json = file_get_contents('https://api.totalcorner.com/v1/match/view/'.$gameID.'?token=a8672a746a987b25&columns=attacks,dangerousAttacks,shotOn,shotOff,possession');
			$obj  = json_decode($json);
			foreach ($obj->data as &$game) {
			echo "<br />";
			var_dump($game);
			$home = $game->h;
			$away = $game->a;
			$h_s = $game->hg;
			$a_s = $game->ag;
			$time = $game->status;
			
			echo "<br />";
			
         //   $query = "INSERT INTO games VALUES (NULL, ".$gameID.")";
		//	$mysqli->query($query);
            $json = file_get_contents('https://api.totalcorner.com/v1/match/odds/'.$gameID.'?token=a8672a746a987b25&columns=goalHalfList');
			$obj  = json_decode($json);
			foreach ($obj->data as &$game) {
			echo "<br />";
			$line = ($game->goal_half_list[0][1]);
			$odds = ($game->goal_half_list[0][2]);
			echo "<br />";
			}
			}


        }
        
        if(!empty($line)) {
        			$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('lkQnDIQJ4JOs9bsfxoKdIBcG4EAZWmqI0QHWXbtSJJW7NMdRo82VkEkENjn6bcPAcBCjziPrmkIFRLpw70qQejtKRUr052Gin0SDdBMjpu/OVG5CKm7wMrdmYAHLu6Vu6l6vdB/LEzvKjsz9Gx2ZpAdB04t89/1O/w1cDnyilFU=');
			$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '55941c006bead29305723e8f943200ef']);

$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($home . " ".$h_s." v  ". $a_s ." ". $away . " Time: ".$time."  Bet: Over " . $line. " FH Goals Odds: ". $odds);
$response = $bot->pushMessage('Uc79146897bc051af96b872a13656a304', $textMessageBuilder);

echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

}
        $stmt->close();
        
    } else {
        
        var_dump($mysqli->error_list);
        
    }
    
}


$json = file_get_contents('https://api.totalcorner.com/v1/match/today?token=a8672a746a987b25&type=inplay&columns=odds,goalLineHalf,attacks');
$obj  = json_decode($json);
foreach ($obj->data as &$game) {
    if (is_numeric($game->status) && floatval($game->status) < 90) {
        
        if (floatval($game->hg) == 2 && floatval($game->ag) == 0) {
            $send = 1;
            

            
            sendDetails($game->id, "Test");
            echo "Test1";
            
        }
        if (floatval($game->ag) == 0 && floatval($game->hg) == 0) {
            $send = 1;
        
            sendDetails($game->id, "Test");
            echo "Test2";
            
        }
        
        
    }
}
?>