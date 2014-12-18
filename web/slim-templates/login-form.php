<h1>Login</h1>
<?php
if (isset($error)) {
    echo '<h4>'.$error.'</h4>';
}

?>
<form action="/login/do-login" method="post">
    <input type="text" id="username" name="username" placeholder="Benutzername" />
    <input type="password" id="password" name="password" />
    <input type="submit" value="Login" />
</form>