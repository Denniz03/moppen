<?php
    $user = $_GET['user'];

    if ($user == 'max') {
        $userImage = 'MaxiTaxi2014';
        $userName = 'Max Korghout';
        $userMail = 'max.korthout2014@gmail.com';
        $userColor = '#FF6347';
        $userColorLight = '#FF7F5F';
        $userColorDark = '#DF472F';
    } else if ($user == 'noa') {
        $userImage = 'NoaPoa2017';
        $userName = 'Noa Korghout';
        $userMail = 'noa.korthout2017@gmail.com';
        $userColor = '#EE82EE';
        $userColorLight = '#FF9EFF';
        $userColorDark = '#D167D2';
    } else if ($user == 'danny') {
        $userImage = 'Denniz03';
        $userName = 'Danny Korghout';
        $userMail = 'danny.korthout2gmail.com';
        $userColor = '#FFA500';
        $userColorLight = '#FFC031';
        $userColorDark = '#DF8B00';
    } else {
        $userImage = 'anonymous';
        $userName = 'Onbekend';
        $userMail = '';
        $userColor = '#3CB371';
        $userColorLight = '#5BCF8B';
        $userColorDark = '#139858';
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                $.ajax({
                    url: "get_joke.php",
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        var joke = data.joke;
                        var isNSFW = data.nsfw;

                        if (!isNSFW) {
                            $('#jokeText').text(joke); // Vul de mop in
                        } else {
                            $('#jokeText').text("Deze mop kan niet worden weergegeven vanwege NSFW-inhoud.");
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
                document.cookie = 'favorites=' + encodeURIComponent(favoritesJSON) + ';path=/'; // Update de favorieten cookie
            }

            // Functie voor ophalen favorieten
            function loadFavoritesFromCookie() {
                var decodedFavorites = decodeURIComponent(document.cookie)
                    .split(';')
                    .find(cookie => cookie.trim().startsWith('favorites='));
                
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

            // Functie om de tellers op te slaan in cookies
            function saveCountersToCookie() {
                document.cookie = 'totalSeen=' + encodeURIComponent(totalSeen) + ';path=/';
                document.cookie = 'totalApproved=' + encodeURIComponent(totalApproved) + ';path=/';
                document.cookie = 'totalRejected=' + encodeURIComponent(totalRejected) + ';path=/';
            }

            // Functie om de tellers uit cookies op te halen
            function loadCountersFromCookie() {
                var counters = {};
                document.cookie.split(';').forEach(cookie => {
                    var [key, value] = cookie.trim().split('=');
                    counters[key] = parseInt(value) || 0;
                });
                return counters;
            }

            // Actie wanneer voor toevoegen aan favorieten
            favoriteButton.addEventListener('click', function() {
                if (!isAddedToFavorites) {
                    favoriteButton.innerHTML = '<i class="fas fa-star"></i> Toegevoegd aan Favorieten';
                    isAddedToFavorites = true;
                    totalFavorites++;
                    updateStatus();

                    var currentJoke = $('#jokeText').text();
                    favorites.push(currentJoke);
                    saveFavoritesToCookie(favorites);
                }
            });

            // Actie wanneer erop wordt geklikt om te goedkeuren
            $('.approve-button').click(function() {
                totalApproved++;
                totalSeen++;
                updateStatus();
                saveCountersToCookie();
                fetchNewJoke();
            });

            // Actie wanneer erop wordt geklikt om af te keuren
            $('.reject-button').click(function() {
                totalRejected++;
                totalSeen++;
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
            <button class="button account-edit-button"><i class="fas fa-user-edit"></i><span>Account gegevens</span></button>
            <button class="button sign-out-button"><i class="fas fa-sign-out"></i><span>Afmelden</span></button>
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
