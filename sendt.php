<?php 
	include "include/ronarazoro.php";

	if (isset($_SESSION["user"][1], $_POST["type"], $_POST["target"], $_GET["town"]))
	{
		$_GET["town"]=clean($_GET["town"]);
		$town = town($_GET["town"]); 
		if ($town[1]!=$_SESSION["user"][0]) {
			git('index.php'); die();
		}
		$target=town_(clean($_POST["target"]));
		if (isset($target[0]))
		{
			$t_towns=towns($target[1]); $pop=0;
			for ($i=0; $i<count($t_towns); $i++) {$pop+=$t_towns[$i][3];}
			$towner=user($target[1]);
			$faction=faction($_SESSION["user"][10]);
			$units=units($faction[0]);
			$pact=get_pact($_SESSION["user"][11], $towner[11]);
			$army=explode("-", $town[7]); $gen=explode("-", $town[15]);

			if (((($pact=="")||($pact))&&(($_SESSION["user"][11]!=$towner[11])||((!$_SESSION["user"][11])&&(!$towner[11]))))||(!clean($_POST["type"])))
			{
				if ($pop>120)
				{
					$ok_army=1; $qarmy=array(); $spd=0;
					for ($i=0; $i<13; $i++) {
						if(isset($_POST["q_".$i])) $qarmy[$i]=abs(clean($_POST["q_".$i])); else $qarmy[$i]=0;
						$spd+=$units[$i][8]*$qarmy[$i]; 
						if ($qarmy[$i]>$army[$i]) $ok_army=0; 
						$army[$i]-=$qarmy[$i];
					}
					if (array_sum($qarmy))
					{
						if ((count($t_towns)>2)||((!$qarmy[8])&&(!$qarmy[7])))
						{
							$ok_water=1; if (($town[16]!=$target[16])&&(($qarmy[9])||($qarmy[10]))) $ok_water=0;//if there are ships, but no water;
							$spd=$spd/array_sum($qarmy);
							if (($town[16]==$target[16])&&(($qarmy[9])||($qarmy[10]))) $spd=($units[9][8]*$qarmy[9]+$units[10][8]*$qarmy[10])/($qarmy[9]+$qarmy[10]);//water travel speed...
							if ($ok_army)
							{
								if ($ok_water)
								{
									$loc=town_xy($town[0]);
									$tloc=town_xy($target[0]);
									$date=sqrt(pow($loc[0]-$tloc[0], 2)+pow($loc[1]-$tloc[1], 2))/$spd;
									$time[0]=floor($date); $time[1]=floor(($date-floor($date))*60); $time=implode("-", $time);
									$date=strtotime("+".floor($date)." hours ".floor(($date-floor($date))*60)." minutes");
									$date=strftime("%y-%m-%d %H:%M:%S", $date);
									$gen[3]=clean($_POST["formation"]); if (!isset($_POST["general"])) $gen[0]=0; else $_POST["general"] = clean($_POST["general"]);
									dispatch($town, $target, clean($_POST["type"]), $date, $time, implode("-", $qarmy), implode("-", $army), implode("-", $gen));
									git_("town.php?town=".$_GET["town"], "olumlu", "Gönderildi!");
								}
								else git_("town.php?town=".$_GET["town"], "olumsuz",$lang["noWaterRoute"]);
							}
							else git_("town.php?town=".$_GET["town"], "olumsuz",$lang["notTroops"]);
						}
						else git_("town.php?town=".$_GET["town"], "olumsuz",$lang["siegeImmunity"]);
					}
					else git_("town.php?town=".$_GET["town"], "olumsuz",$lang["armyVoid"]);
				}
				else git_("town.php?town=".$_GET["town"], "olumsuz",$lang["attackImmunity"]);
			}
			else git_("town.php?town=".$_GET["town"], "olumsuz",$lang["cantAttAlly"]);
		}
		else git_("town.php?town=".$_GET["town"], "olumsuz",$lang["noTown"]);
	}
	else git_("town.php?town=".$_GET["town"], "olumsuz",$lang["insufData"]);
?>
