var apiStartGame = 'mock.php?api=startgame';
var apiGame      = 'mock.php?api=game';

var spriteWidth = 560; // width (px) of any image in sprites.png

/**
 * send post-request
 * @param api
 * @param data
 * @returns {Promise<any>}
 */
function postData(api, data) {
    return fetch(api, {
        'body': JSON.stringify(data),
        'headers': {
            'content-type': 'appication/json'
        },
        'credentials': 'include',
        'method': 'POST'
    })
        .then(function (response) {
            return response.json();
        })
}

/**
 * send playerName, difficulty to api and set new gameState
 * @param playerName
 * @param difficulty
 */
function startGame(playerName, difficulty) {
    postData(apiStartGame, {
        'playerName': playerName,
        'difficulty': difficulty
    }).then(function (gameState) {
        setGameState(gameState)
    });
}

/**
 * send character to api and set new gameState
 * @param character
 */
function sendCharacter(character) {
    postData(apiGame, character)
        .then(function (gameState) {
            setGameState(gameState)
        });
}

/**
 * render stateObject to screen
 * @param stateObject
 */
function setGameState(stateObject) {
    var image = document.querySelector('#status .image');

    // set body tag data attibute (used to hide/show elements in styles.css):
    document.getElementsByTagName('body')[0].dataset.gamestate = stateObject.status;

    // calculate css-sprite position for current stateObject:
    switch (stateObject.status) {
        case 'PLAYING':
            image.style.backgroundPositionX = stateObject.failedAttempts * spriteWidth * -1 + 'px';
            break;
        case 'WIN':
            image.style.backgroundPositionX = -11 * spriteWidth + 'px';
            break;
        case 'LOSE':
            image.style.backgroundPositionX = -10 * spriteWidth + 'px';
            break;
    }

    // display word from stateObject:
    document.getElementById('word').innerHTML = stateObject.word.map(function (character) {
        return '<div class="character">' + (character || '') + '</div>';
    }).join('');
}

/**
 * wait until dom is loaded and bind some event-listeners
 */
document.addEventListener("DOMContentLoaded", function () {
    // bind click handler to each .key in #keyboard:
    document
        .querySelectorAll('#keyboard .key')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                // call function "sendCharacter" for data-value attribute of .key
                sendCharacter(button.dataset.value);
            })
        });
    document
        .querySelector('#newgame form')
        .addEventListener('submit', function (e) {
            e.preventDefault(); // prevent event default behavior (in this case: "submit form")
            var playerName = document.getElementById('playerName').value.trim();
            var difficulty = document.getElementById('difficulty').value;
            if (playerName.length === 0) {
                // set red border if playerName is empty and display errormessage
                document.getElementById('playerName').style.borderColor = 'red';
                alert('Bitte geben Sie einen Spielernamen ein.');
            } else {
                // call function "startGame" for form inputs:
                startGame(playerName, difficulty);
            }
        });
});
