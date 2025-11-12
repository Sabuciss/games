<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Laipni lūdzam šaja lapā</title>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>
    

<h1>Sveiki, ko vēlies spēlēt!</h1>
<div class="cards-wrapper">
    <div class="card">
        <img src="img/cards.png" alt="game" >
        <div class="container">
            <h4><b>Atmiņas spēle</b></h4>
            <p><a href="{{ url('/play') }}">Sākt atmiņas kartīšu spēli</a></p>
        </div>
    </div>

    <div class="card">
        <img src="img/typing.jpg" alt="game" >
        <div class="container">
            <h4><b>Typing-game </b></h4>
            <p><a href="{{ url('/typing-game') }}">Typing-game</a></p>
        </div>
    </div>
</div>
   
</body>
</html>
