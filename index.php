<?php
    $user = $_GET['user'];

    if ($user == 'max') {
        $userImage = 'MaxiTaxi2014';
        $userName = 'Max Korghout';
        $userMail = 'max.korthout2014@gmail.com';
        $userColor = '#FF6347';
        $userColorLight = '#FF7F5F';
        $userColorDark = '#DF472F';
        $userFavorites = 'maxFavorites';
        $userHistory = "maxHistory";
    } else if ($user == 'noa') {
        $userImage = 'NoaPoa2017';
        $userName = 'Noa Korghout';
        $userMail = 'noa.korthout2017@gmail.com';
        $userColor = '#EE82EE';
        $userColorLight = '#FF9EFF';
        $userColorDark = '#D167D2';
        $userFavorites = 'noaFavorites';
        $userHistory = "noaHistory";
    } else if ($user == 'danny') {
        $userImage = 'Denniz03';
        $userName = 'Danny Korghout';
        $userMail = 'danny.korthout2gmail.com';
        $userColor = '#FFA500';
        $userColorLight = '#FFC031';
        $userColorDark = '#DF8B00';
        $userFavorites = 'dannyFavorites';
        $userHistory = "dannyHistory";
    } else {
        $userImage = 'anonymous';
        $userName = 'Onbekend';
        $userMail = '';
        $userColor = '#3CB371';
        $userColorLight = '#5BCF8B';
        $userColorDark = '#139858';
        $userFavorites = 'anonymousFavorites';
        $userHistory = "anonymousHistory";
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="<?php echo $userColor ?>">
    <meta name="msapplication-navbutton-color" content="<?php echo $userColor ?>">
    <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo $userColor ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Moppen Pagina</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --userColor: <?php echo $userColor ?>;
            --userColorLight: <?php echo $userColorLight ?>;
            --userColorDark: <?php echo $userColorDark ?>;
        }
    </style>
    <script>
        $(document).ready(function() {
            var totalSeen = 0;
            var totalApproved = 0;
            var totalRejected = 0;
            var totalFavorites = 0;
            var favoriteButton = document.querySelector('.add-to-favorites');
            var isAddedToFavorites = false;
            var favorites = loadFavoritesFromCookie();
            var counters = loadCountersFromCookie();
            totalSeen = counters.totalSeen || totalSeen;
            totalApproved = counters.totalApproved || totalApproved;
            totalRejected = counters.totalRejected || totalRejected;
            totalFavorites = favorites.length || 0; // Gebaseerd op het aantal favorieten in de cookie
            updateStatus();
            displayFavoritesInPopup();

            // Functie om de statusbalk bij te werken
            function updateStatus() {
                document.getElementById('totalSeen').innerHTML = '<i class="fas fa-eye"></i> ' + totalSeen;
                document.getElementById('totalApproved').innerHTML = '<i class="fas fa-check"></i> ' + totalApproved;
                document.getElementById('totalRejected').innerHTML = '<i class="fas fa-times"></i> ' + totalRejected;
                document.getElementById('totalFavorites').innerHTML = '<i class="fas fa-star"></i> ' + totalFavorites;
            }

            // Functie om een nieuwe mop op te halen via PHP
            function fetchNewJoke() {
                totalSeen++; // Increment totalSeen counter
                updateStatus(); // Update status bar

                $.ajax({
                    url: "get_joke.php",
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        var joke = data.joke;
                        var isNSFW = data.nsfw;
                        var jokeId = data.id;

                        if (!isNSFW && !isMopGezien(jokeId, '<?php echo $userHistory ?>')) {
                            $('#jokeText').text(joke);
                            voegMopToeAanGeschiedenis(jokeId, '<?php echo $userHistory ?>');
                        } else {
                            fetchNewJoke();
                        }
                    },
                    error: function(xhr, status, error) {
                        // Verwerk fouten hier
                    }
                });
            }

            // Functie om favorieten op te slaan
            function saveFavoritesToCookie(favorites) {
                var favoritesJSON = JSON.stringify(favorites);
                document.cookie = '<?php echo $userFavorites ?>' + '=' + encodeURIComponent(favoritesJSON) + ';path=/'; // Update de favorieten cookie
            }

            // Functie voor ophalen favorieten
            function loadFavoritesFromCookie() {
                var decodedFavorites = decodeURIComponent(document.cookie)
                    .split(';')
                    .find(cookie => cookie.trim().startsWith('<?php echo $userFavorites ?>='));
                
                if (decodedFavorites) {
                    var favoritesJSON = decodedFavorites.split('=')[1];
                    return JSON.parse(favoritesJSON);
                }

                return [];
            }

            // Functie voor het tonen van de favorieten
            function displayFavoritesInPopup() {
                var popupItems = document.querySelector('.popup-items');
                popupItems.innerHTML = '';

                if (favorites.length > 0) {
                    favorites.forEach(function(favorite, index) {
                        var favoriteItem = document.createElement('div');
                        favoriteItem.classList.add('popup-item');

                        var jokeText = document.createElement('div');
                        jokeText.classList.add('article-text');
                        jokeText.textContent = favorite;
                        favoriteItem.appendChild(jokeText);

                        var removeButton = document.createElement('button');
                        removeButton.classList.add('button', 'favorite-remove');
                        removeButton.innerHTML = '<i class="fas fa-trash-alt"></i><span>Verwijder</span>';
                        removeButton.addEventListener('click', function() {
                            favorites.splice(index, 1);
                            saveFavoritesToCookie(favorites);
                            displayFavoritesInPopup(); // Update de weergave in de popup
                        });
                        favoriteItem.appendChild(removeButton);

                        popupItems.appendChild(favoriteItem);

                        if (index < favorites.length - 1) {
                            var hr = document.createElement('hr');
                            popupItems.appendChild(hr);
                        }
                    });
                } else {
                    var noFavoritesText = document.createElement('p');
                    noFavoritesText.textContent = 'Geen favorieten opgeslagen.';
                    popupItems.appendChild(noFavoritesText);
                }
            }

            // Functie om mop-ID toe te voegen aan geschiedenis-cookie
            function voegMopToeAanGeschiedenis($mopId) {
                var userHistory = '<?php echo $userHistory ?>';
                var history = loadHistoryFromCookie(userHistory);
                history.push($mopId);
                saveHistoryToCookie(history, userHistory);
            }

            // Functie om te controleren of een mop al gezien is
            function isMopGezien($mopId) {
                var userHistory = '<?php echo $userHistory ?>';
                var history = loadHistoryFromCookie(userHistory);
                return history.indexOf($mopId) !== -1;
            }

            // Functie om geschiedenis uit cookies te laden, met een lege array als het niet is ingesteld
            function loadHistoryFromCookie(userHistory) {
                var decodedHistory = decodeURIComponent(document.cookie)
                    .split(';')
                    .find(cookie => cookie.trim().startsWith(userHistory + '='));
                
                if (decodedHistory) {
                    var historyJSON = decodedHistory.split('=')[1];
                    return JSON.parse(historyJSON);
                }

                return [];
            }

            // Functie om geschiedenis op te slaan in cookies
            function saveHistoryToCookie(history, userHistory) {
                var historyJSON = JSON.stringify(history);
                document.cookie = userHistory + '=' + encodeURIComponent(historyJSON) + ';path=/'; // Update de geschiedenis cookie
            }

            // Functie om de tellers op te slaan in cookies
            function saveCountersToCookie() {
                document.cookie = '<?php echo $user ?>_totalSeen=' + encodeURIComponent(totalSeen) + ';path=/';
                document.cookie = '<?php echo $user ?>_totalApproved=' + encodeURIComponent(totalApproved) + ';path=/';
                document.cookie = '<?php echo $user ?>_totalRejected=' + encodeURIComponent(totalRejected) + ';path=/';
            }

            // Functie om de tellers uit cookies op te halen
            function loadCountersFromCookie() {
                var counters = {};
                var cookies = document.cookie.split(';');
                cookies.forEach(cookie => {
                    var [key, value] = cookie.trim().split('=');
                    counters[key] = parseInt(value) || 0;
                });
                
                return {
                    totalSeen: counters['<?php echo $user ?>_totalSeen'] || 0,
                    totalApproved: counters['<?php echo $user ?>_totalApproved'] || 0,
                    totalRejected: counters['<?php echo $user ?>_totalRejected'] || 0
                };
            }

            // Actie voor toevoegen aan favorieten
            favoriteButton.addEventListener('click', function() {
                if (!isAddedToFavorites) {
                    favoriteButton.innerHTML = '<i class="fas fa-star"></i> Toegevoegd aan Favorieten';
                    isAddedToFavorites = true;
                    totalFavorites++;
                    updateStatus();

                    var currentJoke = $('#jokeText').text();
                    favorites.push(currentJoke);
                    saveFavoritesToCookie(favorites);

                    displayFavoritesInPopup();
                }
            });

            // Actie wanneer erop wordt geklikt om te goedkeuren
            $('.approve-button').click(function() {
                totalApproved++;
                updateStatus();
                saveCountersToCookie();
                fetchNewJoke();
            });

            // Actie wanneer erop wordt geklikt om af te keuren
            $('.reject-button').click(function() {
                totalRejected++;
                updateStatus();
                saveCountersToCookie();
                fetchNewJoke();
            });

            // Eerste mop laden bij het laden van de pagina
            fetchNewJoke();

            // Overlay en sluit knop
            $("#overlay, .popup-close-button").click(function(){
                $("#overlay").toggle();
                $("body").toggleClass("fixed");
                $(".popup").hide();
            });

            // Menu favorieten of user klik
            $("#favoritesButton, #userButton").click(function(){
                $(this).next().fadeToggle(); 
                $('#overlay').fadeToggle(); 
                $('body').toggleClass('fixed');
            });
        });
    </script>
</head>
<body>
	<div class="overlay" id="overlay"></div>
    <div class="menu-bar">
        <button class="button" id="menuButton"><i class="fas fa-bars"></i></button>
        <button class="button" id="searchButton"><i class="fas fa-search"></i></button>
        <button class="button" id="favoritesButton"><i class="fas fa-star"></i></button>
        <div class="popup" id="favoritesPopup">
            <button class="popup-close-button button"><i class="fas fa-times"></i></button>
            <div class="article-title"><i class="far fa-"></i><h1>Favorieten</h1></div>
            <hr>
            <div class="popup-items"></div>
        </div>
        <button class="button" id="userButton"><i class="fas fa-user"></i></button>
        <div class="popup" id="userPopup">
            <button class="popup-close-button button"><i class="fas fa-times"></i></button>
            <div class="article-media"><i style="background-image: url('images/<?php echo $userImage ?>.jpg')" class="far fa-"></i>&nbsp;</div>
            <div class="article-title"><i class="far fa-"></i><h1><?php echo $userName ?></h1></div>
            <div class="article-subtitle"><i class="far fa-"></i><?php echo $userMail ?></div>
            <hr>
            <?php if ($userImage == "anonymous") { ?>
                <button class="button sign-in-button"><i class="fas fa-sign-in"></i><span>Aanmelden</span></button>
            <?php } else { ?>
                <button class="button account-edit-button"><i class="fas fa-user-edit"></i><span>Account gegevens</span></button>
                <button class="button sign-out-button"><i class="fas fa-sign-out"></i><span>Afmelden</span></button>
            <?php } ?>}
       </div>
    </div>
    
    <div class="article">
        <h1>Leuke Mop</h1>
		<p id="jokeText"></p>
        
        <div class="buttons">
    		<button class="button approve-button"><i class="fas fa-check"></i><span>Goedkeuren</span></button>
    		<button class="button reject-button"><i class="fas fa-times"></i><span>Afkeuren</span></button>
    		<button class="button add-to-favorites"><i class="far fa-star"></i><span>Toevoegen aan Favorieten</span></button>
		</div>
    </div>
	<div class="status-bar">
        <span id="totalSeen"><i class="fas fa-eye"></i><span>0</span></span>
        <span id="totalApproved"><i class="fas fa-check"></i><span>0</span></span>
        <span id="totalRejected"><i class="fas fa-times"></i><span>0</span></span>
        <span id="totalFavorites"><i class="fas fa-star"></i><span>0</span></span>
    </div>
</body>
</html>
