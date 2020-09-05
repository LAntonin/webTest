<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
<!-- 
  Remarques relatives au jeu de caractères (charset)
  Par défaut, le jeu de caractères utilisés par les navigateurs est « iso-8859-1 » également appelé « latin1 »
  Personnellement, je suis plutôt favorable à l' « UTF-8 »
  Dans tous les cas, il faut arriver à maitriser cet aspect. Ainsi, choisir un encodage et faire en sorte
  de toujours utilisé le même est beaucoup plus simple !
  
  Sources :   http://www.w3.org/International/O-HTTP-charset.fr.php
        http://openweb.eu.org/articles/changer_pour_utf8
-->
    <link rel="stylesheet" href="style.css" />
    <title><?= $titre ?></title>
  </head>
  <body>
    <div id="global">
      <header>
        <h1><a title="Créé par Antonin Lyaët avec HTML5 et PHP"><?= $titre ?></a></h1>
      </header>
      <form>
        <?php if(isset($_SESSION['tailleY'], $_SESSION['tailleI'], $_SESSION['nbBombe'])) : ?>
          <p>
            Générer une grille de <input type="text" name="tailleI" value="<?= $_SESSION['tailleI'] ?>"/> par <input type="text" name="tailleY" value="<?= $_SESSION['tailleY'] ?>"/>
          </p>
          <p>
            Avec <input type="text" name="nbBombe" value="<?= $_SESSION['nbBombe'] ?>"/> bombes !
          </p>
        <?php else : ?>
          <p>
            Générer une grille de <input type="text" name="tailleY" value="10"/> par <input type="text" name="tailleI" value="10"/>
          </p>
          <p>
            Avec <input type="text" name="nbBombe" value="10"/> bombes !
          </p>
        <?php endif ?>
        <p>
          <input type="submit" name="generer" value="Générer"/>
          <a id="reset" href="index.php?reset&tailleI=<?= $_SESSION['tailleI'] ?>&tailleY=<?= $_SESSION['tailleY'] ?>&nbBombe=<?= $_SESSION['nbBombe'] ?>#drapeau">Reset</a>
          <input type="submit" name="regle" value="Régles" />
        </p>
      </form>
      <?php if(isset($_SESSION['erreur']) && !$_SESSION['erreur']) : ?>
        <div>
          <?php if($_SESSION['resultat'] === 'perdu') : ?>
            <p class="resultat" id="perdu">
              <?= $resultat ?>
            </p>
          <?php else : ?>
            <p class="resultat" id="gagne">
              <?= $resultat ?>
            </p>
          <?php endif ?>
          <form id="drapeau">
          <p>
            <?php if($_SESSION['switch'] === 'drapeauActif') : ?>
              <a href="index.php?switch=DrapeauOff#drapeau" accesskey="d"><img src="drapeau.jpg" alt="Drapeau"/></a>
            <?php else : ?>
              <a href="index.php?switch=DrapeauOn#drapeau" accesskey="d"><img src="pasdrapeau.jpg" alt="NoDrapeau"/></a>
            <?php endif ?>
        </form>
        </div>
        <div id="conteneurGrille">
          <div id="grille">
            <?php for($i = 0; $i < $_SESSION['tailleI']; $i++) : ?>
              <?php for($y = 0; $y < $_SESSION['tailleY']; $y++) : ?>
                <?php if(isset($_SESSION[$i . 'N' . $y])) : ?>
                  <?php if($_SESSION[$i . 'N' . $y] === 'bombe') : ?>
                    <?php if($_SESSION['resultat'] === 'perdu' || $_SESSION['resultat'] === 'en attente' || (isset($_SESSION[$i . 'N' . $y]) && $_SESSION[$i . 'N' . $y] !== 'drapeau') || (isset($_SESSION[$i . 'N' . $y]) && $_SESSION[$i . 'N' . $y] === 'drapeau' && $_SESSION['switch'] === 'drapeauInactif')) : ?>
                      <img id="<?= $i . 'N' . $y ?>" src="bombe.jpg" alt="bombe"/>
                    <?php else : ?>
                      <img id="<?= $i . 'N' . $y ?>" src="bombe.jpg" alt="bombe"/>
                    <?php endif ?>
                  <?php else : ?>
                    <?php if($_SESSION[$i . 'N' . $y] === 'drapeau') : ?>
                      <?php if($_SESSION['resultat'] === 'perdu' || $_SESSION['resultat'] === 'en attente' || (isset($_SESSION[$i . 'N' . $y]) && $_SESSION[$i . 'N' . $y] !== 'drapeau') || (isset($_SESSION[$i . 'N' . $y]) && $_SESSION[$i . 'N' . $y] === 'drapeau' && $_SESSION['switch'] === 'drapeauInactif')) : ?>
                        <img id="<?= $i . 'N' . $y ?>" src="drapeau.jpg" alt="drapeau"/>
                      <?php else : ?>
                        <a href="index.php?<?= $i ?>N<?= $y ?>">
                          <img id="<?= $i . 'N' . $y ?>" src="drapeau.jpg" alt="drapeau"/></a>
                      <?php endif ?>
                    <?php else : ?>
                      <?php if($_SESSION['resultat'] === 'perdu' || $_SESSION['resultat'] === 'en attente' || (isset($_SESSION[$i . 'N' . $y]) && $_SESSION[$i . 'N' . $y] !== 'drapeau') || (isset($_SESSION[$i . 'N' . $y]) && $_SESSION[$i . 'N' . $y] === 'drapeau' && $_SESSION['switch'] === 'drapeauInactif')) : ?>
                        <img id="<?= $i . 'N' . $y ?>" src="<?= $_SESSION[$i . 'N' . $y] ?>.jpg" alt="<?= $_SESSION[$i . 'N' . $y] ?>"/>
                      <?php else : ?>
                        <img id="<?= $i . 'N' . $y ?>" src="<?= $_SESSION[$i . 'N' . $y] ?>.jpg" alt="<?= $_SESSION[$i . 'N' . $y] ?>"/>
                      <?php endif ?>
                    <?php endif ?>
                  <?php endif ?>
                <?php else : ?>
                  <?php if($_SESSION['resultat'] === 'perdu' || $_SESSION['resultat'] === 'en attente' || (isset($_SESSION[$i . 'N' . $y]) && $_SESSION[$i . 'N' . $y] !== 'drapeau') || (isset($_SESSION[$i . 'N' . $y]) && $_SESSION[$i . 'N' . $y] === 'drapeau' && $_SESSION['switch'] === 'drapeauInactif')) : ?>
                    <img id="<?= $i . 'N' . $y ?>" src="vide.jpg" alt="vide"/>
                  <?php else : ?>
                    <a href="index.php?<?= $i ?>N<?= $y ?>#<?= $i . 'N' . $y ?>">
                      <img id="<?= $i . 'N' . $y ?>" src="vide.jpg" alt="vide"/></a>
                  <?php endif ?>
                <?php endif ?>
              <?php endfor ?>
              <br/>
            <?php endfor ?>
          </div>
        </div>
      <?php elseif($regle === true) : ?>
        <p>
          <h2>Les régles !</h2>

          Vous disposez d'une grille contenant des mines cachées.<br/>

          En cliquant sur une case vous connaissez le nombre de mines se trouvant dans les cases ( 8 au maximum) qui l'entourent.<br/>
          Le but du jeu est de détecter toutes les mines sans cliquer dessus.<br/>
          Si vous avez deviné la position d'une mine vous pouvez la faire apparaître grâce au drapeau en cliquant sur le drapeau puis sur la case voulue.<br/>
          
          <h2>Quelques astuces !</h2>
          Vous pouvez faire un raccourci clavier avec la touche d.<br/>
          <ul>
            <li>Sur Firefox : Alt + Shift + d</li>
            <li>Sur Opera15+ : Alt + d</li>
            <li>Sur les autres navigateurs : Alt + d</li>
          </ul>

        </p>
      <?php else : ?>
        <p>
          Veuillez rentrer des valeurs correctes !
          Il faut :
          <ul>
            <li>Que la largeur soit comprise entre 1 et 44;</li>
            <li>Que la longueur soit comprise entre 1 et 1500;</li>
            <li>Que le nombre de bombe ne soit pas supérieur ou égale à la taille de la grille (x*y);</li>
            <li>Ne pas être con !</li>
            <br/>
            <li class="warning"><p>Attention, plus le résultat de la longueur est grande, plus le programme risque d'être lent et long à générer</p></li>
          </ul>
        </p>
      <?php endif ?>
    </div> <!-- #global -->
  </body>
</html>