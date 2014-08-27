function loadGames($) {
    var table = $('#gamesListContainer table');
    $('.game-count').html(games.length);
    if(games.length == 0) {
        table.append('<tr><td colspan="11" class="no-games">No games in DB.</td></tr>');
    }
    else {
        for(var a = 0; a < games.length; a++) {
            var game = games[a];
            table.append(generateRowFromGame(game, false, true));
        }
    }
}

function loadRankedTable($) {
    var u = [];
    var table = $('#rankingTable');
    for(var user in users) {
        u.push({
            'username': user,
            'kda' : users[user][0],
            'numGames' : users[user][1]
        });
    }
    users = u;
    users.sort(function(a, b){ return b.kda - a.kda; });
  
    for(var a = 0; a < users.length; a++) {
        var user = users[a];
        if(user.numGames > 0) {
            var newRow = '<tr>';
            newRow += '<td>' + (a+1) + '</td>';
            newRow += '<td>' + getUserLink(user.username) + '</td>';
            newRow += '<td>' + user.kda.toFixed(4) + '</td>';
            newRow += '<td>' + user.numGames + '</td>';
            newRow += '</tr>';
              
        table.append(newRow);
        }
    }
}
  
function getUserLink(userid) {
    return '<a href="./view.php?userid=' + userid + '">' + userid + '</a>';
}
  
function loadAwardTable($) {
    var table = $('#awardTable');
    table.append('<tr><td>Champion</td><td>' + getUserLink(awardObj['wins']['username'])  + '</td><td>' + numeral(awardObj['wins']['total']).format('0,0.00') + '</td><td>Wins (Top Total)</td></tr>');
    table.append('<tr><td>"That" Guy</td><td>' + getUserLink(awardObj['highest_kda']['username'])  + '</td><td>' + numeral(awardObj['highest_kda']['total']).format('0,0.00') + '</td><td>KDA (Top single game total)</td></tr>');
    table.append('<tr><td>Assassin</td><td>' + getUserLink(awardObj['kills']['username'])  + '</td><td>' + numeral(awardObj['kills']['total']).format('0,0.00') + '</td><td>Kills (Avg per game)</td></tr>');
    table.append('<tr><td>Mass Murderer</td><td>' + getUserLink(awardObj['most_kills']['username'])  + '</td><td>' + numeral(awardObj['most_kills']['total']).format('0,0.00') + '</td><td>Kills (Top single game total)</td></tr>');
    table.append('<tr><td>Casual Feeder</td><td>' + getUserLink(awardObj['deaths']['username'])  + '</td><td>' + numeral(awardObj['deaths']['total']).format('0,0.00') + '</td><td>Deaths (Avg per game)</td></tr>');
    table.append('<tr><td>Cannon Fodder</td><td>' + getUserLink(awardObj['most_deaths']['username'])  + '</td><td>' + numeral(awardObj['most_deaths']['total']).format('0,0.00') + '</td><td>Deaths (Top single game total)</td></tr>');
    table.append('<tr><td>Wingman</td><td>' + getUserLink(awardObj['assists']['username'])  + '</td><td>' + numeral(awardObj['assists']['total']).format('0,0.00') + '</td><td>Assists (Avg per game)</td></tr>');
    table.append('<tr><td>Masochist</td><td>' + getUserLink(awardObj['most_assists']['username'])  + '</td><td>' + numeral(awardObj['most_assists']['total']).format('0,0.00') + '</td><td>Assists (Top single game total)</td></tr>');
    table.append('<tr><td>Mogul</td><td>' + getUserLink(awardObj['gold']['username'])  + '</td><td>' + numeral(awardObj['gold']['total']).format('0,0.00') + '</td><td>Gold earned (Avg per game)</td></tr>');
    table.append('<tr><td>Merciless</td><td>' + getUserLink(awardObj['minions']['username'])  + '</td><td>' + numeral(awardObj['minions']['total']).format('0,0.00') + '</td><td>Minions slain (Top single game total)</td></tr>');
}
  
function getRandomHex(){
    var result = Math.round(Math.random()*255);
    while (result < 128) {
        result = Math.round(Math.random()*255);
    }
      
    return result.toString(16);
}

function generateRowFromGame(game, editable, makeNewRow) {
    var newRow = '';
    if(!editable) {
        if(makeNewRow) {
            newRow = '<tr class="game" data-gameid="' + game['id'] + '">';
        }
            newRow += '<td>' + convertChamp(game['champ']) + getChampImg(game['champ']) + '</td>';
            newRow += '<td class="' + convertVictory(game['victory']).toLowerCase() + '">' + convertVictory(game['victory']) + '</td>';
            newRow += '<td class="">'
                    + '<span class="kills">' + numeral(game['kills']).format('0,0') + '</span>'
                    + ' / '
                    + '<span class="deaths">' + numeral(game['deaths']).format('0,0') + '</span>'
                    + ' / '
                    + '<span class="assists">' + numeral(game['assists']).format('0,0') + '</span>'
                + '</td>';
            newRow += '<td class="gold">' + numeral(game['gold']).format('0,0') + '</td>';
            newRow += '<td>' + numeral(game['minions']).format('0,0') + '</td>';
            newRow += '<td>' + numeral(calculateKDA(game['kills'], game['assists'], game['deaths'])).format('0,0.0000') + '</td>';
            newRow += '<td>' + convertGameType(game['game_type']) + '</td>';
            newRow += '<td>' + convertGameLevel(game['game_level']) + '</td>';
            newRow += '<td>' + convertDate(game['date']) + '</td>';
            newRow += '<td>' + convertSummonerRole(game['summoner_role']) + '</td>';
        if(makeNewRow) {
            newRow += '</tr>';
        }
    }
    else {
        newRow += '<td>' + convertChamp(game['champ']) + getChampImg(game['champ']) + '</td>';
        newRow += '<td>' + getVictoryDropdown(game['victory']) +'</td>';
        newRow += '<td>'
                + '<input type="hidden" name="gameid" id="gameid" value="' + game['id'] + '" />'
                + '<input type="text" name="edit-kills" id="edit-kills" value="' + game['kills'] + '" />'
                + ' / '
                + '<input type="text" name="edit-deaths" id="edit-deaths" value="' + game['deaths'] + '" />'
                + ' / '
                + '<input type="text" name="edit-assists" id="edit-assists" value="' + game['assists'] + '" />'
            + '</td>';
        newRow += '<td><input type="text" name="edit-gold" id="edit-gold" value="' + game['gold'] + '" /></td>';
        newRow += '<td><input type="text" name="edit-minions" id="edit-minions" value="' + game['minions'] + '" /></td>';
        newRow += '<td>' + numeral(calculateKDA(game['kills'], game['assists'], game['deaths'])).format('0,0.0000') + '</td>';
        newRow += '<td>' + getGameTypeDropdown(game['game_type']) + '</td>';
        newRow += '<td>' + getGameLevelDropdown(game['game_level']) + '</td>';
        newRow += '<td><input type="text" name="edit-date" id="edit-date" value="' + convertDate(game['date']) + '" /></td>';
        newRow += '<td>' + getSummonerRoleDropdown(game['summoner_role']) + '</td>';
    }
    
    return newRow;
}

function getSummonerRoleDropdown(selectedVal){
    var result =
        '<select name="edit-summ_role" id="edit-summ_role">'
            + '<option value="-1"' + ((selectedVal == -1) ? ' selected="selected"' : '') + '>N/A</option>'
            + '<option value="0"' + ((selectedVal == 0) ? ' selected="selected"' : '') + '>AP Carry</option>'
            + '<option value="5"' + ((selectedVal == 5) ? ' selected="selected"' : '') + '>AP Bruiser</option>'
            + '<option value="1"' + ((selectedVal == 1) ? ' selected="selected"' : '') + '>AD Carry</option>'
            + '<option value="6"' + ((selectedVal == 6) ? ' selected="selected"' : '') + '>AD Bruiser</option>'
            + '<option value="2"' + ((selectedVal == 2) ? ' selected="selected"' : '') + '>Jungle</option>'
            + '<option value="3"' + ((selectedVal == 3) ? ' selected="selected"' : '') + '>Tank</option>'
            + '<option value="4"' + ((selectedVal == 4) ? ' selected="selected"' : '') + '>Support</option>'
        + '</select>';
    return result;
}

function getGameLevelDropdown(selectedVal){
    var result =
        '<select name="edit-game_level" id="edit-game_level">'
            + '<option value="1"' + ((selectedVal == 1) ? ' selected="selected"' : '') + '>Normal</option>'
            + '<option value="2"' + ((selectedVal == 2) ? ' selected="selected"' : '') + '>Ranked</option>'
            + '<option value="3"' + ((selectedVal == 3) ? ' selected="selected"' : '') + '>Custom</option>'
        + '</select>';
    
    return result;
}

function getGameTypeDropdown(selectedVal){
    var result =
        '<select name="edit-game_type" id="edit-game_type">'
            + '<option value="1"' + ((selectedVal == 1) ? ' selected="selected"' : '') + '>Summoner\'s Rift</option>'
            + '<option value="2"' + ((selectedVal == 2) ? ' selected="selected"' : '') + '>Twisted Treeline</option>'
            + '<option value="3"' + ((selectedVal == 3) ? ' selected="selected"' : '') + '>ARAM</option>'
            + '<option value="4"' + ((selectedVal == 4) ? ' selected="selected"' : '') + '>Dominion</option>'
        + '</select>';
        
    return result;
}

function getVictoryDropdown(selectedVal){
    var result =
        '<select name="edit-victory" id="edit-victory">'
            + '<option value="-1"' + ((selectedVal == -1) ? ' selected="selected"' : '') + '>N/A</option>'
            + '<option value="0"' + ((selectedVal == 0) ? ' selected="selected"' : '') + '>Loss</option>'
            + '<option value="1"' + ((selectedVal == 1) ? ' selected="selected"' : '') + '>Win</option>'
        + '</select>';
        
    return result;
}

function getChampImg(champNum){
    champNum *= 1.0;
    var imgSrc = './question_mark.png';
    
    if(champNum > 0 && champNum < champs.length) {
        imgSrc = 'http://ddragon.leagueoflegends.com/cdn/3.15.5/img/champion/' + champs[champNum]['secret_name'] + '.png';
    }
    
    return '<img src="' + imgSrc + '" alt="Champion Image" />';
}

function convertVictory(num){
    num *= 1.0;
    var result = '';
    switch(num){
        case 1:
            result = 'Win';
        break;
        case 0:
            result = 'Loss';
        break;
        default:
            result = 'N/A';
        break;
    }
    
    return result;
}

function convertChamp(id){
    for(var a = 0; a < champs.length; a++) {
        if(champs[a]['id'] == id){
            return champs[a]['name'];
        }
    }
    return '';
}

function convertDate(date) {
    var firstHyphen = date.indexOf('-');
    var secondHyphen = date.indexOf('-', firstHyphen + 1);
    var day = date.substr(secondHyphen+1);
    var month = date.substr(firstHyphen+1, 2);
    var year = date.substr(0, firstHyphen);
    return month + '/' + day + '/' + year;
}

function convertSummonerRole(roleNum) {
    var result = '';
    roleNum *= 1;
    switch(roleNum) {
        case 0:
            result = 'AP Carry';
        break;
        case 1:
            result = 'AD Carry';
        break;
        case 2:
            result = 'Jungle';
        break;
        case 3:
            result = 'Tank';
        break;
        case 4:
            result = 'Support';
        break;
        case 5:
            result = 'AP Bruiser';
        break;
        case 6:
            result = 'AD Bruiser';
        break;
        default:
            result = 'N/A';
        break;
    }
    
    return result;
}

function convertGameLevel(gameLevelNum) {
    var result = '';
    gameLevelNum *= 1;
    switch(gameLevelNum) {
        case 1:
            result = 'Normal';
        break;
        case 2:
            result = 'Ranked';
        break;
        case 3:
            result = 'Custom';
        break;
    }
    
    return result;
}

function convertGameType(gameTypeNum) {
    var result = '';
    gameTypeNum *= 1;
    switch(gameTypeNum) {
        case 1:
            result = 'Summoner\'s Rift';
        break;
        case 2:
            result = 'Twisted Treeline';
        break;
        case 3:
            result = 'ARAM';
        break;
        case 4:
            result = 'Dominion';
        break;
    }
    
    return result;
}

function generateStatRow(statTitle, cellClass, val) {
    return '<tr><td>' + statTitle + '</td><td class="' + cellClass + '">' + val + '</td></tr>';
}

function generateEmptyRow(colspan) {
    return '<tr><td colspan="' + colspan + '">&nbsp;</td></tr>';
}

function multiStatRow(title, majorstat, formatter) {
    var result = '<tr><td>' + title + '</td><td>';
    
    if(userStats[majorstat].length > 0) {
        result += '<ul>';
        for(var a = 0; a < userStats[majorstat].length; a++) {
            result += formatter(userStats[majorstat][a]);
            //result +='<li>' + userStats[majorstat][a]['name'] + ' with ' + userStats[][a]['wins'] + ' win' + ((userStats[majorstat][a]['wins'] > 1) ? 's' : '') + '</li>';
        }
        result += '</ul>'
    }
    else {
        result += 'N/A';
    }
            
    result += '</td></tr>';
    
    return result;
}

function formatPlural(val, single, multi) {
    var result = single;
    if(val > 1) {
        result = multi;
    }
    return result;
}

function loadStatsTable($) {
    var statString = '';
    statString += generateStatRow('Running KDA', '', numeral(runningKDA).format('0,0.0000'));
    statString += generateEmptyRow(2);
    statString += multiStatRow('Top Winning Champs', 'topWinChamps', function(data){
        return '<li>' + data['name'] + ' with ' + data['wins'] + ' ' + formatPlural(data['wins'], 'win', 'wins') + '</li>';
    });
    statString += multiStatRow('Top Losing Champs', 'topLossChamps', function(data){
        return '<li>' + data['name'] + ' with ' + data['losses'] + ' ' + formatPlural(data['losses'], 'loss', 'losses') + '</li>';
    });
    statString += multiStatRow('Top Picked (ARAM) Champs', 'topPickedChamps', function(data){
        return '<li>' + data['name'] + ' with ' + data['timesPicked'] + ' ' + formatPlural(data['timesPicked'], 'time', 'times') + '</li>';
    });
    
    statString += generateEmptyRow(2);
    statString += generateStatRow('Total Kills', 'kills', numeral(ttlKills).format('0,0.00'));
    statString += generateStatRow('Total Deaths', 'deaths', numeral(ttlDeaths).format('0,0.00'));
    statString += generateStatRow('Total Assists', 'assists', numeral(ttlAssists).format('0,0.00'));
    statString += generateStatRow('Total Gold', 'gold', numeral(ttlGold).format('0,0.00'));
    statString += generateStatRow('Total Minions', '', numeral(ttlMin).format('0,0.00'));
    
    statString += generateEmptyRow(2);
    
    statString += generateStatRow('Avg Kills', 'kills', numeral(avgKills).format('0,0.00'));
    statString += generateStatRow('Avg Deaths', 'deaths', numeral(avgDeaths).format('0,0.00'));
    statString += generateStatRow('Avg Assists', 'assists', numeral(avgAssists).format('0,0.00'));
    statString += generateStatRow('Avg Gold', 'gold', numeral(avgGold).format('0,0.00'));
    statString += generateStatRow('Avg Minions', '', numeral(avgMin).format('0,0.00'));

    $('#statBox table').append(statString);
}

function calculateKDA(kills, assists, deaths) {
    if(deaths == 0) { deaths = 1; }
    var result = ((kills * 1.0) + (assists * 1.0)) / (deaths * 1.0);
    if(!isValid(result, 'numeric')) { result = 0; }
    return result;
}

function validateForm($, form, fieldArray) {
    var form = $(form),
        validForm = true;
    for(var a = 0; a < fieldArray.length; a++){
        var fieldName = fieldArray[a]['name'],
            type = fieldArray[a]['type'],
            valType = fieldArray[a]['valType'],
            field = form.find(type + '[name="' + fieldName + '"]').first();
        
        validForm &= checkField(field, valType);
    }
    return validForm;
}

function validateAndSubmitForm_($, form, fieldArray, url, callback){
    var validForm = validateForm($, form, fieldArray);
    if(validForm) {
       $.post(url,form.serialize(), callback);
    }
}

function validateAndSubmitForm($, edit, theRow) {
    var form = edit ? $('#editGameForm') : $('#addForm');
    var kEle = edit ? $('#edit-kills') : $('#kills');
    var dEle = edit ? $('#edit-deaths') : $('#deaths');
    var aEle = edit ? $('#edit-assists') : $('#assists');
    var gEle = edit ? $('#edit-gold') : $('#gold');
    var mEle = edit ? $('#edit-minions') : $('#minions');
    var gtEle = edit ? $('#edit-game_type') : $('#game_type');
    var glEle = edit ? $('#edit-game_level') : $('#game_level');
    var rEle = edit ? $('#edit-summ_role') : $('#summ_role');
    var dtEle = edit ? $('#edit-date') : $('#date');
    var vEle = edit ? $('#edit-victory') : $('#victory');
    var cEle = $('#champ_select');
    
    var validForm = checkField(kEle, 'numeric');
    validForm &= checkField(dEle, 'numeric');
    validForm &= checkField(aEle, 'numeric');
    validForm &= checkField(gEle, 'numeric');
    validForm &= checkField(mEle, 'numeric');
    validForm &= checkField(gtEle, 'numeric');
    validForm &= checkField(glEle, 'numeric');
    validForm &= checkField(rEle, 'numeric');
    validForm &= checkField(vEle, 'numeric');
    validForm &= checkField(dtEle, 'date');
    validForm &= checkField(cEle, 'numeric');
    
    if(gtEle.val() * 1.0 == 3 && rEle.val() * 1.0 == 2) { // There is no jungle on ARAM
        validForm = false;
        rEle.addClass('invalid');
    }
    else {
        rEle.removeClass('invalid');
    }

    if(validForm) {
       $.post(
           './addGame.php',
           form.serialize(),
           function(data, textStatus, jqXHR) {
               var timerLength = 750;
                   //console.log(data);
                   //console.log(textStatus);
               if(data.toLowerCase().indexOf('error') > -1) {
                   console.error(data);
                   $('.error-msg').fadeIn(timerLength, function(){ $('.error-msg').fadeOut(timerLength); });
               }
               else {
                   data = JSON.parse(data);
                   if(edit) {
                       gamesById[data['id']] = data;
                       theRow.html(generateRowFromGame(data, false, false));
                   }
                   else {
                       $('.success-msg').fadeIn(timerLength, function(){ $('.success-msg').fadeOut(timerLength); });
                       kEle.add(dEle).add(aEle).add(gEle).add(mEle).val('');
                       $('#gamesListContainer table tr:has(td)').add('.game-count').fadeOut(timerLength, function()
                       {
                           $('#gamesListContainer table tr:has(td)').remove();
                           for(var a = 0; a < data.length; a++) {
                               var newRow = generateRowFromGame(data[a], false, true);
                               $('#gamesListContainer table').append(newRow);
                           }
                           $('.game-count').html(data.length);
                           $('#gamesListContainer table').add('.game-count').fadeIn(timerLength);
                       });
                   }
               }
           }
       );
    }
}

function checkField(fieldEle, type) {
    var valid = isValid(fieldEle.val(), type);
    if(!valid) {
        fieldEle.addClass('invalid');
    } else {
        fieldEle.removeClass('invalid');
    }
    return valid;
}

function getFormattedDate() {
    var dt = new Date();
    var m = (dt.getMonth() + 1);
    var d = dt.getDate();
    var y = dt.getFullYear();
    if((m + '').length < 2) {
        m = '0' + m;
    }
    if((d + '').length < 2) {
        d = '0' + d;
    }
    return m + '/' + d + '/' + y;
}

function isValid(value, type) {
    var result = false;
    switch(type) {
        case 'alphabetic':
            result = /^[A-Za-z]+$/.test(value);
        break;
        case 'numeric':
            result = !isNaN(parseFloat(value)) && isFinite(value);
        break;
        case 'alphanumeric':
            result = /^[A-Za-z0-9 ]+$/.test(value);
        break;
        case 'password':
            result = /^[A-Za-z0-9]{8,}$/.test(value);
        break;
        case 'email':
            result = /^[A-Za-z0-9]+[A-Za-z0-9\.]*@[A-Za-z0-9]+\.[A-Za-z0-9\.]+$/.test(value);
        break;
        case 'date':
            result = /^\d{2}\/\d{2}\/\d{4}$/.test(value);
            if(result) {
                var d = new Date(value);
                var n = new Date();
                result &= d.getTime() <= n.getTime();
            }
        break;
    }
    return result;
}

function gameClicked($, gamerow) {
    var relevantGame = gamesById[gamerow.attr('data-gameid')*1.0],
        updateBtn = $('<div id="updateGameBtn">Update Game</div>'),
        cancelBtn = $('<div id="cancelUpdateBtn">Cancel</div>');
    $('tr.game').unbind('click');
    $('#gamesListContainer').prepend(cancelBtn).prepend(updateBtn);
    gamerow.html(generateRowFromGame(relevantGame, true, false));
    updateBtn.on('click', function(e){
        validateAndSubmitForm($, true, gamerow);
        updateBtn.remove();
        cancelBtn.remove();
        gamerow.html(generateRowFromGame(relevantGame, false, false));
        $('tr.game').on('click', function(e){ gameClicked($, $(this)); });
    });
    cancelBtn.on('click', function(e){
        cancelBtn.remove();
        updateBtn.remove();
        gamerow.html(generateRowFromGame(relevantGame, false, false));
        $('tr.game').on('click', function(e){ gameClicked($, $(this)); });
    });
}

function apiGameExpand($, gamerow)
{
    
}
