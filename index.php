<?php
/* Objectif : Objectif : récupérer une valeur transmise par un formulaire…
 * P.P. 16/12/2017
 * Jeu de caractères : utf-8
*/
	session_start();
	$titre = 'Demineur';

	$resultat = '';
	$contenu = '';

	if(isset($_GET['regle']))
	{
		$regle = true;
		$_SESSION['erreur'] = true;
	}
	else
	{
		$regle = false;

		if(isset($_GET['tailleY'], $_GET['tailleI'], $_GET['nbBombe']) || isset($_GET['reset']))
		{
			if($_GET['tailleY'] <= 0 || $_GET['tailleI'] <= 0 || $_GET['tailleY'] >= 45 || $_GET['tailleI'] >= 1500 || $_GET['nbBombe'] >= ($_GET['tailleY'] * $_GET['tailleI']))
				$_SESSION['erreur'] = true;
			else
			{
				session_unset();

				$_SESSION['switch'] = 'drapeauInactif';

				$_SESSION['resultat'] = 'pas fini';

				$_SESSION['tailleI'] = $_GET['tailleI'];
				$_SESSION['tailleY'] = $_GET['tailleY'];
				$_SESSION['nbBombe'] = $_GET['nbBombe'];

				//Algorithme de génération

				$_SESSION['grille'] = generation();

				$_SESSION['erreur'] = false;
			}
		}

		if(isset($_SESSION['erreur']) && $_SESSION['erreur'] === false)
		{

			if($_SESSION['resultat'] != 'perdu' || $_SESSION['resultat'] != 'en attente')
			{
				//Définition du mode pour le clic

				if(isset($_GET['switch']))
				{
					if($_GET['switch'] === 'DrapeauOn')
						$_SESSION['switch'] = 'drapeauActif';
					else
						$_SESSION['switch'] = 'drapeauInactif';
				}

				//Détection du bouton cliqué

				$coordClic = detectionClic();

				if(isset($_SESSION['grille'][$coordClic]) && $_SESSION['grille'][$coordClic] === 0)
				{
					unset($_SESSION['cases0']);
					$_SESSION['cases0'][] = $coordClic;
					for ($i=0; $i < sizeof($_SESSION['cases0']); $i++)
						verifCase0($_SESSION['cases0'][$i]);
				}

				//Gagné, perdu ou rien du tout ?

				if(isset($_SESSION[$coordClic]) && $_SESSION[$coordClic] === 'bombe')
				{
					$_SESSION['resultat'] = 'perdu';
					$resultat = 'Vous avez perdu';
					afficherAllBombes();
				}
				else
				{
					$_SESSION['resultat'] = 'en attente';

					for($i = 0; $i < $_SESSION['tailleI']; $i++)
					{
						for($y = 0; $y < $_SESSION['tailleY']; $y++)
						{
							if(!isset($_SESSION[$i . 'N' . $y]))
							{
								if($_SESSION['grille'][$i . 'N' . $y] != 'bombe')
								{
									$_SESSION['resultat'] = 'pas fini';
									break;
								}
							}
						}

						if($_SESSION['resultat'] == 'pas fini')
							break;
					}

					if($_SESSION['resultat'] === 'en attente')
					{
						$resultat = 'gagné !';
					}
					else
						$resultat = '';
				}
			}
		}
	}

	require 'model.php';

	////////FONCTIONS////////

	//algorithme de génération

	function generation()
	{
		for($i = 0; $i < $_SESSION['nbBombe']; $i++)
		{
			$x = rand(0, ($_SESSION['tailleI'] - 1));
			$y = rand(0, ($_SESSION['tailleY'] - 1));

			if(isset($tabGrille[$x . 'N' . $y]))
				if($tabGrille[$x . 'N' . $y] === 'bombe')
					$i--;
				else
					$tabGrille[$x . 'N' . $y] = 'bombe';
			else
				$tabGrille[$x . 'N' . $y] = 'bombe';
		}

		for($i = 0; $i < $_SESSION['tailleI']; $i++)
		{
			for($y = 0; $y < $_SESSION['tailleY']; $y++)
			{
				if(!(isset($tabGrille[$i . 'N' . $y])))
				{
					$nbBombe = 0;

					if(isset($tabGrille[($i - 1) . 'N' . ($y - 1)]))				//En haut à gauche
						if($tabGrille[($i - 1) . 'N' . ($y - 1)] === 'bombe')
							$nbBombe+=1;

					if(isset($tabGrille[($i - 1) . 'N' . $y]))						//En haut
						if($tabGrille[($i - 1) . 'N' . $y] === 'bombe')
							$nbBombe+=1;

					if(isset($tabGrille[($i - 1) . 'N' . ($y + 1)]))				//En haut à droite
						if($tabGrille[($i - 1) . 'N' . ($y + 1)] === 'bombe')
							$nbBombe+=1;

					if(isset($tabGrille[$i . 'N' . ($y - 1)]))						//A gauche
						if($tabGrille[$i . 'N' . ($y - 1)] === 'bombe')
							$nbBombe+=1;

					if(isset($tabGrille[$i . 'N' . ($y + 1)]))						//A droite
						if($tabGrille[$i . 'N' . ($y + 1)] === 'bombe')
							$nbBombe+=1;

					if(isset($tabGrille[($i + 1) . 'N' . ($y - 1)]))				//En bas à gauche
						if($tabGrille[($i + 1) . 'N' . ($y - 1)] === 'bombe')
							$nbBombe+=1;

					if(isset($tabGrille[($i + 1) . 'N' . $y]))						//En bas
						if($tabGrille[($i + 1) . 'N' . $y] === 'bombe')
							$nbBombe+=1;

					if(isset($tabGrille[($i + 1) . 'N' . ($y + 1)]))				//En bas à droite
						if($tabGrille[($i + 1) . 'N' . ($y + 1)] === 'bombe')
							$nbBombe+=1;

					$tabGrille[$i . 'N' . $y] = $nbBombe;
				}
			}
		}

		return $tabGrille;
	}

	//Detection du clic

	function detectionClic()
	{
		for($i = 0; $i < $_SESSION['tailleI']; $i++)
		{
			$break_bool = false;

			for($y = 0; $y < $_SESSION['tailleY']; $y++)
			{
				if(isset($_GET[$i . 'N' . $y]))
				{
					if($_SESSION['switch'] === 'drapeauActif')
						if(isset($_SESSION[$i . 'N' . $y]))
						{
							if($_SESSION[$i . 'N' . $y] === 'drapeau')
								unset($_SESSION[$i . 'N' . $y]);
							else
								$_SESSION[$i . 'N' . $y] = 'drapeau';
						}
						else
							$_SESSION[$i . 'N' . $y] = 'drapeau';
					else
					{
						$contenu = $i . 'N' . $y;
						$_SESSION[$i . 'N' . $y] = $_SESSION['grille'][$i . 'N' . $y];
						
						$break_bool = true;
						break;
					}
				}
				else
					$contenu = "rien";
			}

			if($break_bool == true)
				break;
		}

		return $contenu;
	}

	//Verification des cases adjacentes des cases 0

	function verifCase0($coord)
	{
		list($i, $y) = explode('N', $coord);

		//var_dump($_SESSION['cheminVerif0']);

		$var = ($i - 1) . 'N' . ($y - 1);
		if(!(isset($_SESSION[$var])) && isset($_SESSION['grille'][$var]) || isset($_SESSION[$var]) && isset($_SESSION['grille'][$var]) && $_SESSION[$var] === 'drapeau')
		{
			$_SESSION[$var] = $_SESSION['grille'][$var];
			if($_SESSION['grille'][$var] === 0)
				$_SESSION['cases0'][] = $var;
		}

		$var = ($i - 1) . 'N' . $y;
		if(!(isset($_SESSION[$var])) && isset($_SESSION['grille'][$var]) || isset($_SESSION[$var]) && isset($_SESSION['grille'][$var]) && $_SESSION[$var] === 'drapeau')
		{
			$_SESSION[$var] = $_SESSION['grille'][$var];
			if($_SESSION['grille'][$var] === 0)
				$_SESSION['cases0'][] = $var;
		}

		$var = ($i - 1) . 'N' . ($y + 1);
		if(!(isset($_SESSION[$var])) && isset($_SESSION['grille'][$var]) || isset($_SESSION[$var]) && isset($_SESSION['grille'][$var]) && $_SESSION[$var] === 'drapeau')
		{
			$_SESSION[$var] = $_SESSION['grille'][$var];
			if($_SESSION['grille'][$var] === 0)
				$_SESSION['cases0'][] = $var;
		}

		$var = $i . 'N' . ($y - 1);
		if(!(isset($_SESSION[$var])) && isset($_SESSION['grille'][$var]) || isset($_SESSION[$var]) && isset($_SESSION['grille'][$var]) && $_SESSION[$var] === 'drapeau')
		{
			$_SESSION[$var] = $_SESSION['grille'][$var];
			if($_SESSION['grille'][$var] === 0)
				$_SESSION['cases0'][] = $var;
		}

		$var = $i . 'N' . ($y + 1);
		if(!(isset($_SESSION[$var])) && isset($_SESSION['grille'][$var]) || isset($_SESSION[$var]) && isset($_SESSION['grille'][$var]) && $_SESSION[$var] === 'drapeau')
		{
			$_SESSION[$var] = $_SESSION['grille'][$var];
			if($_SESSION['grille'][$var] === 0)
				$_SESSION['cases0'][] = $var;
		}

		$var = ($i + 1) . 'N' . ($y - 1);
		if(!(isset($_SESSION[$var])) && isset($_SESSION['grille'][$var]) || isset($_SESSION[$var]) && isset($_SESSION['grille'][$var]) && $_SESSION[$var] === 'drapeau')
		{
			$_SESSION[$var] = $_SESSION['grille'][$var];
			if($_SESSION['grille'][$var] === 0)
				$_SESSION['cases0'][] = $var;
		}

		$var = ($i + 1) . 'N' . $y;
		if(!(isset($_SESSION[$var])) && isset($_SESSION['grille'][$var]) || isset($_SESSION[$var]) && isset($_SESSION['grille'][$var]) && $_SESSION[$var] === 'drapeau')
		{
			$_SESSION[$var] = $_SESSION['grille'][$var];
			if($_SESSION['grille'][$var] === 0)
				$_SESSION['cases0'][] = $var;
		}

		$var = ($i + 1) . 'N' . ($y + 1);
		if(!(isset($_SESSION[$var])) && isset($_SESSION['grille'][$var]) || isset($_SESSION[$var]) && isset($_SESSION['grille'][$var]) && $_SESSION[$var] === 'drapeau')
		{
			$_SESSION[$var] = $_SESSION['grille'][$var];
			if($_SESSION['grille'][$var] === 0)
				$_SESSION['cases0'][] = $var;
		}
	}

	function afficherAllBombes()
	{
		for($i = 0; $i < $_SESSION['tailleI']; $i++)
		{
			for($y = 0; $y < $_SESSION['tailleY']; $y++)
			{
				if($_SESSION['grille'][$i . 'N' . $y] === 'bombe')
					$_SESSION[$i . 'N' . $y] = 'bombe';
			}
		}
	}
?>