<div id="registerform">
<?php 
	if (isset($error) && $error['module'] == "register") { echo "<p id=\"errormsg\">" . $error['msg'] . "</p><br />"; }
?>
	<form method="post" action="index.php?action=register">
		<fieldset>
			<legend>Informations de connexion</legend>
			<label for="nickname">Nom d'utilisateur : </label>
			<input type="text" name="nickname" id="nickname" maxlength="15" required <?php if (isset($_POST['nickname'])) { echo "value=\"" . $_POST['nickname'] . "\"" ; } ?> />
			<br />

			<label for="email">Email : </label>
			<input type="email" name="email" id="email" maxlength="255" required <?php if (isset($_POST['email'])) { echo "value=\"" . $_POST['email'] . "\"" ; } ?> />
			<br />

			<label for="password">Mot de passe : </label>
			<input type="password" name="password" id="password" maxlength="16" required />
			<br />

			<label for="password2">Confirmation du mot de passe : </label>
			<input type="password" name="password2" id="password2" maxlength="16" required />
		</fieldset>
		<fieldset>
			<legend>A propos de vous</legend>
			<label for="lastname">Nom : </label>
			<input type="text" name="lastname" id="lastname" maxlength="255" <?php if (isset($_POST['lastname'])) { echo "value=\"" . $_POST['lastname'] . "\"" ; } ?> />
			<br />

			<label for="firstname">Prénom : </label>
			<input type="text" name="firstname" id="firstname" maxlength="255" <?php if (isset($_POST['firstname'])) { echo "value=\"" . $_POST['firstname'] . "\"" ; } ?> />
			<br />

			<label for="birthdate">Date de naissance : </label>
			<input type="date" name="birthdate" id="birthdate" required <?php if (isset($_POST['birthdate'])) { echo "value=\"" . $_POST['birthdate'] . "\"" ; } ?> />
		</fieldset>
		<center><input type="submit" name="submit" value="S'inscrire">
	</form>
</div>