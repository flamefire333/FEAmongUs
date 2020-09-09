<?php
class Costume {
    public $name;
    public $primaryColor;
    public $secondaryColor;
    public $hat;
    function __construct($name_, $primaryColor_, $secondaryColor_, $hat_) {
        $this->name = $name_;
        $this->primaryColor = $primaryColor_;
        $this->secondaryColor = $secondaryColor_;
        $this->hat = $hat_;
    }
}
if($_GET["setup"]) {
    $costumes = array(
        new Costume("Edelgard", "red", "white", "NA"),
        new Costume("Hubert", "black", "brown", "NA"), 
        new Costume("Linhardt", "dark-green", "dark-blue", "NA"),
        new Costume("Petra", "pink", "purple", "NA"),
        new Costume("Ferdinand", "orange", "red", "NA"),
        new Costume("Bernadetta", "purple", "pink", "NA"),
        new Costume("Dorothea", "brown", "purple", "NA"),
        new Costume("Caspar", "cyan", "white", "NA"),
        new Costume("Dimitri", "dark-blue", "yellow", "NA"),
        new Costume("Dedue", "brown", "dark-blue", "NA"),
        new Costume("Felix", "dark-blue", "cyan", "NA"),
        new Costume("Annette", "orange", "cyan", "NA"),
        new Costume("Ingrid", "yellow", "light-green", "NA"),
        new Costume("Mercedes", "white", "brown", "NA"),
        new Costume("Ashe", "white", "dark-blue", "NA"),
        new Costume("Sylvain", "red", "orange", "NA"),
        new Costume("Claude", "yellow", "brown", "NA"),
        new Costume("Hilda", "pink", "black", "NA"),
        new Costume("Leonie", "orange", "red", "NA"),
        new Costume("Raphael", "yellow", "orange", "NA"),
        new Costume("Lysithea", "white", "purple", "NA"),
        new Costume("Lorenz", "purple", "white", "NA"),
        new Costume("Marianne", "cyan", "dark-blue", "NA"),
        new Costume("Ignatz", "light-green", "dark-green", "NA"), 
        new Costume("Byleth", "light-green", "dark-blue", "NA"),
        new Costume("Sothis", "dark-green", "pink", "NA"),
        new Costume("Rhea", "light-green", "white", "NA"),
        new Costume("Seteth", "dark-green", "dark-blue", "NA"),
        new Costume("Flayn", "dark-green", "light-green", "NA"),
        new Costume("Catherine", "yellow", "white", "NA"),
        new Costume("Shamir", "purple", "brown", "NA"),
        new Costume("Manuela", "brown", "pink", "NA"),
        new Costume("Jeralt", "brown", "yellow", "NA"),
    );
    shuffle($costumes);
    $gameID = $_GET["join"];
    $pc = count(explode(" ", file_get_contents(__DIR__ . "/game.txt"))) - 1;
    $pc = 10;
    $filePath = __DIR__ . "/gameP.txt";
    $gameID  = $_GET["join"];
    file_put_contents($filePath, $gameID, LOCK_EX);
    $curr = 1;
    $pdata = array();
    $usedColors = array();
    $colors = array("red", "black", "dark-green", "pink", "orange", "purple", "brown", "cyan", "dark-blue", "yellow", "white", "light-green");
    while($curr < count($costumes) && $curr <= 10 && $curr <= $pc) {
        $currCostume = $costumes[$curr - 1];
        $currName = $currCostume->name;
        $currColor = $currCostume->primaryColor;
        if(array_search($currColor, $usedColors) != FALSE) {
            $currColor = $currCostume->secondaryColor;
            if(array_search($currColor, $usedColors) != FALSE) {
                shuffle($colors);
                $cc = 0;
                while(array_search($colors[$cc], $usedColors) != FALSE) {
                    $cc = $cc + 1;
                }
                $currColor  = $colors[$cc];
            }
        }
        $usedColors[$curr] = $currColor;
        $currHat = $currCostume->hat;
        $pdata[$curr] = " " . $currName . " " . $currColor . " " . $currHat;
        $curr = $curr + 1;
    }
    shuffle($pdata);
    $curr = 1;
    while($curr < count($costumes) && $curr <= 10 && $curr <= $pc) {
        file_put_contents($filePath, $pdata[$curr], FILE_APPEND | LOCK_EX);
        $curr = $curr + 1;
    }
} else if($_GET["pinfo"]) {
    $name = $_GET["pinfo"];
    $filePath = __DIR__ . "/game.txt";
    $fileData = file_get_contents($filePath);
    $fileGameData = explode(" ", $fileData);
    $playerIndex = array_search($name, $fileGameData);
    $characterPath = __DIR__ . "/gameP.txt";
    $characterData = file_get_contents($characterPath);
    $characters = explode(" ", $characterData);
    $offset = $playerIndex * 3 + 1 - 3;
    echo "You are " . $characters[$offset] . " and your color is " . $characters[$offset + 1];
} else if($_GET["list"]) {
    $gameID = $_GET["join"];
    $filePath = __DIR__ . "/game.txt";
    $fileData = file_get_contents($filePath);
    $fileGameData = explode(" ", $fileData);
    $fileGameID = $fileGameData[0];
    if($fileGameID == $gameID) {
        $curr = 1;
        echo "<tr><th>Player List</th></tr>";
        while($curr < count($fileGameData)) {
            echo "<tr><td>" . $fileGameData[$curr] . "</td></tr>";
            $curr = $curr + 1;
        }
    } else {
        echo "<tr><td>No one</td></tr>";
    }
} else {
    echo "<html><body>";
    if($_GET["room"]) {
        echo "<h3 id='pinfo'>Waiting for refresh</h3><br /><br />";
        $name = $_GET["name"];
        $gameID = $_GET["room"];
        $filePath = __DIR__ . "/game.txt";
        $fileData = file_get_contents($filePath);
        $fileGameData = explode(" ", $fileData);
        $fileGameID = $fileGameData[0];
        if($fileGameID == $gameID) {
            if(!in_array($name, $fileGameData)) {
                file_put_contents($filePath, " ".$name, FILE_APPEND | LOCK_EX);
            }
        } else {
            file_put_contents($filePath, $gameID." ".$name, LOCK_EX);
        }
        echo "<script>
        function refreshPlayerList() {
            var xhttp = new XMLHttpRequest();
            var trash = Math.random();
            xhttp.onreadystatechange= function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById('PlayerList').innerHTML = this.responseText;
                }
            };
            xhttp.open('GET', 'amongus.php?list=1&trash=' + trash + '&join=" . strval($gameID) . "', true);
            xhttp.send();
        }
        function refreshPInfo() {
            var xhttp = new XMLHttpRequest();
            var trash = Math.random();
            xhttp.onreadystatechange = function() {
                if(this.readyState == 4 && this.status == 200) {
                    document.getElementById('pinfo').innerHTML = this.responseText;
                }
            };
            xhttp.open('GET', 'amongus.php?pinfo="  . strval($name) . "&trash=' + trash, true);
            xhttp.send();
        }
        function refresh() {
            refreshPlayerList();
            refreshPInfo();
        }
        refresh();
        var interval = setInterval(function(){refresh();}, 5000);
        </script>";
        echo "<table id='PlayerList'></table><br /><button type='button' onclick='refreshPlayerList();refreshPInfo()'>Refresh</button>";
        if($name == $fileGameData[1]) {    
            echo "<script>
                function setup() {
                var xhttp = new XMLHttpRequest();
                var trash = Math.random();
                xhttp.open('GET', 'amongus.php?setup=1&trash=' + trash + '&join=" . strval($gameID) . "', true);
                xhttp.send();
            }
            </script>";
            echo "<br /><button type='button' onclick='setup()'>Start Round</button>";
        }
    } else if($_GET["join"]) {
        $gameID = $_GET["join"];
        echo "<form action='amongus.php' method='get'>Name: <input type='text' name='name'></input><br/>Room: <input type='text' name='room' value='" . strval($gameID) . "'></input><br /><input type='submit'></input></form>";
    } else {
        $gameID = rand();
        echo "<form action='amongus.php' method='get'>Name: <input type='text' name='name'></input><br/>Room: <input type='text' name='room' value='" . strval($gameID) . "'></input><br /><input type='submit'></input></form>";
    }
    echo "</body></html>";
}
?>
