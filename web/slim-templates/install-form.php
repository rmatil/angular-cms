<!doctype html>

<html lang="de">
<head>
    <meta charset="utf-8">

    <title>Installation | Angular CMS</title>
    <meta name="description" content="Angular CMS">

    <style>
        html, body {
            background: #f8f8f8;
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 15px;
            font-weight: 200;
        }

        @font-face {
            font-family: 'Source Sans Pro';
            src: url('../cms/css/sourcesanspro-extralight-webfont.eot');
            src: url('../cms/css/sourcesanspro-extralight-webfont.eot?#iefix') format('embedded-opentype'),
                 url('../cms/css/sourcesanspro-extralight-webfont.woff') format('woff'),
                 url('../cms/css/sourcesanspro-extralight-webfont.ttf') format('truetype'),
                 url('../cms/css/sourcesanspro-extralight-webfont.svg#source_sans_proextralight') format('svg');
            font-weight: 200;
            font-style: normal;
        }

        @font-face {
            font-family: 'Source Sans Pro';
            src: url('../cms/css/sourcesanspro-regular-webfont.eot');
            src: url('../cms/css/sourcesanspro-regular-webfont.eot?#iefix') format('embedded-opentype'),
                 url('../cms/css/sourcesanspro-regular-webfont.woff') format('woff'),
                 url('../cms/css/sourcesanspro-regular-webfont.ttf') format('truetype'),
                 url('../cms/css/sourcesanspro-regular-webfont.svg#source_sans_proregular') format('svg');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Source Sans Pro';
            src: url('../cms/css/sourcesanspro-bold-webfont.eot');
            src: url('../cms/css/sourcesanspro-bold-webfont.eot?#iefix') format('embedded-opentype'),
                 url('../cms/css/sourcesanspro-bold-webfont.woff') format('woff'),
                 url('../cms/css/sourcesanspro-bold-webfont.ttf') format('truetype'),
                 url('../cms/css/sourcesanspro-bold-webfont.svg#source_sans_probold') format('svg');
            font-weight: 800;
            font-style: normal;
        }

        ul {
            padding: 0;
        }

        ul li {
            display: inline-block;
        }

        @media all and (max-width: 800px) {
            ul li {
                display: block;
                padding: 5px 0;
                text-decoration: none;
            }
        }

        input {
            width: 200px;
            font-size: 0.8em;
            text-align: center;
        }

        input:focus {
            outline: 0;
        }

        input[type=text],
        input[type=email],
        input[type=number] {
            padding: 10px;
        }

        input[type=password] {
            padding: 10px;
        }

        input[type=submit] {
            color: #fff;
            padding: 10px;
            background: #e7494a;
        }

        input[type=submit]:hover {
            cursor: pointer;
            -webkit-box-shadow: 0 1px 20px rgba(0,0,0,.1);
            -moz-box-shadow: 0 1px 20px rgba(0,0,0,.1);
            box-shadow: inset 0 1px 20px rgba(0,0,0,.1);
        }
        
        input[type=checkbox] {
            width: auto;
        }

        .lightyellow-border {
            border: 2px solid #efcf4d;
        }

        .lightred-border {
            border: 2px solid #e7494a;
        }

        .lightpurple-border {
            border: 2px solid #9b59b6;
        }

        .lightblue-border {
            border: 2px solid #3498db;
        }

        .login-form {
            border: 1px solid #e5e5e5;
            max-width: 800px;
            margin: 10% auto;
            background: #fff;
            text-align: center;
        }

        @media all and (max-width: 800px) {
            .login-form {
                max-width: 90%;
            }
        }

        .error {
            font-weight: 400;
            padding: 10px;
        }

        table {
            width: auto;
        }

        td {
            padding: 0 10px 0 0;
        }
    </style>
</head>
<body>

    <h1>Installation - Angular CMS</h1>
    <?php
        if (isset($errors)) {
            echo '<section>';
            echo 'Leider ist bei der Installation ein Fehler aufgetreten:';
            echo '<ul>';
            foreach ($errors as $error) {
                echo '<li>'.$error->getMessage().'</li>';
            }
            echo '</ul>';
            echo '</section>';
        }
    
    ?>
    
    <form action="/install/do-install" method="post">
        <table>
            <col style="width:150px">
            <col style="width:auto">
            <h2>Datenbank</h2>
            <tr>
                <td>
                    <label for="database-type">Datenbank Type</label>
                </td>
                <td>
                    <select id="database-type" name="database-type" required>
                        <option value="pdo_mysql">MySQL</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="db-host">Datenbank Host</label>
                </td>
                <td>
                    <input type="text" id="db-host" name="db-host" value="localhost" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="db-port">Datenbank Port</label>
                </td>
                <td>
                    <input type="number" id="db-port" name="db-port" value="3306" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="db-name">Datenbank Name</label>
                </td>
                <td>
                    <input type="text" id="db-name" name="db-name" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="db-user">Benutzername</label>
                </td>
                <td>
                    <input type="text" id="db-user" name="db-user" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="db-password">Datenbank Passwort</label>
                </td>
                <td>
                    <input type="password" id="db-password" name="db-password" required/>
                </td>
            </tr>
        </table>
        
        <h2>Email</h2>
        <table>
            <col style="width:150px">
            <col style="width:auto">
            <tr>
                <td>
                    <label for="mail-host">Host</label>
                </td>
                <td>
                    <input type="text" id="mail-host" name="mail-host" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="mail-port">Port</label>
                </td>
                <td>
                    <input type="number" id="mail-port" name="mail-port" value="587" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="mail-smtp-auth">SMTP Authentication</label>
                </td>
                <td>
                    <input type="checkbox" id="mail-smtp-auth" name="mail-smtp-auth" value="true"/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="mail-username">Benutzername</label>
                </td>
                <td>
                    <input type="text" id="mail-username" name="mail-username" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="mail-password">Passwort</label>
                </td>
                <td>
                    <input type="password" id="mail-password" name="mail-password" required/>
                </td>
            </tr>
        </table>
        
        <h2>Website</h2>
        <table>
            <col style="width:150px">
            <col style="width:auto">
            <tr>
                <td>
                    <label for="website-name">Website Name</label>
                </td>
                <td>
                    <input type="text" id="website-name" name="website-name" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="website-name">Website Email Address</label>
                </td>
                <td>
                    <input type="email" id="website-email" name="website-email" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="website-name">Website Reply To Email</label>
                </td>
                <td>
                    <input type="email" id="website-reply-to-email" name="website-reply-to-email" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="website-url">Website URL</label>
                </td>
                <td>
                    <input type="text" id="website-url" name="website-url" value="<?php echo $_SERVER['SERVER_NAME']; ?>" required/>
                </td>
            </tr>
        </table>

        <h2>Security</h2>
        <table>
            <col style="width:150px">
            <col style="width:auto">
            <tr>
                <td>
                    <label for="encryption-code">Verschlüsselungs Code</label>
                </td>
                <td>
                    <input type="text" id="encryption-code" name="encryption-code" value="ThisIsNotSafeChangeIt"/>
                </td>
            </tr>
        </table>

        <h2>Administrator Account</h2>
        <table>
            <col style="width:150px">
            <col style="width:auto">
            <tr>
                <td>
                    <label for="admin-user">Benutzername</label>
                </td>
                <td>
                    <input type="text" id="admin-user" name="admin-user" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="admin-password">Passwort</label>
                </td>
                <td>
                    <input type="password" id="admin-password" name="admin-password" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="admin-email">Email</label>
                </td>
                <td>
                    <input type="email" id="admin-email" name="admin-email" required/>
                </td>
            </tr>
            
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Submit"></td>
            </tr>
        </table>
    </form>
</body>
</html>