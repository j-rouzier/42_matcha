<div id="unlogscreen">
	<div id="leftscreen">
		<center><h1>MATCHA</h1>
		<p>Inscrivez-vous maintenant !</p></center>
			<?php 
			include("view/register/registerform.view.php"); ?>
		<div id="connectionform">
			<h2>Connection</h2>
		<?php
			if (isset($error) && $error['module'] == "login") { echo "<p id=\"errormsg\">" . $error['msg'] . "</p>"; }
?>
			<form method="POST" action="index.php?action=login">
				<label for="nickname">Nom d'utilisateur : </label>
				<input type="text" name="nickname" id="nickname" maxlength="15" required <?php if (isset($_POST['nickname'])) { echo "value=\"" . $_POST['nickname'] . "\"" ; } ?> />
				<br />

				<label for="password">Mot de passe : </label>
				<input type="password" name="password" id="password" maxlength="255" required <?php if (isset($_POST['password'])) { echo "value=\"" . $_POST['password'] . "\"" ; } ?> />
				<br />
				<input type="submit" value="Connexion">
			</form>
		</div>
		<div id="forgettenpassword">
		<h2>Mot de passe oublié ?</h2>
			<?php 
			include("view/index/resetpassword.view.php"); ?>
		</div>
	</div>
</div>