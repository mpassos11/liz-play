<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quem está assistindo?</title>
    <style>
        body {
            background-color: #141414;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        .profiles-container {
            text-align: center;
        }
        .profiles-container h1 {
            font-size: 3.5vw;
        }
        .profile {
            display: inline-block;
            margin: 0 2vw;
            text-align: center;
            cursor: pointer;
        }
        .profile img {
            width: 10vw;
            height: 10vw;
            border-radius: 4px;
            border: 2px solid transparent;
        }
        .profile:hover img {
            border: 2px solid #fff;
        }
        .profile-name {
            margin-top: 0.5em;
            font-size: 1.3vw;
        }
        a {
            text-decoration: none;
            color: #808080;
        }
        a:hover {
            color: #fff;
        }
    </style>
</head><body>
<div class="profiles-container">
    <h1>Quem está assistindo?</h1>
    <div class="profile-list">
        <a href="?perfil=1">
            <div class="profile">
                <img src="" alt="Matheus">
                <div class="profile-name">Matheus</div>
            </div>
        </a>
        <a href="?perfil=2">
            <div class="profile">
                <img src="" alt="Isa">
                <div class="profile-name">Isa</div>
            </div>
        </a>
    </div>
</div>
</body>
</html>