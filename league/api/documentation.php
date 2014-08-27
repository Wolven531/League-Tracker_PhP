<!DOCTYPE html>
<html>
    <head>
        <title>API Documentation</title>
    </head>
    <body>
        <h3>Documentation</h3>
        <div>
            <p>Note: Currently the API supports ONLY 'GET' requests</p>
            <p>Note: Currently the API supports ONLY JSON responses</p>
        </div>
        <h3>Users</h3>
        <div>
            <p>
                There are times when you may want to grab more information about a user (searching for them by similar name, loading stats, etc.). This service
                is very simple and there are no plans for expansion.
            </p>
            <p>
                Syntax: http://www.wolven531.com/league/api/?type=user
            </p>
            Examples:
            <ul>
                <li>
                    All Users: 
                    <a href="http://www.wolven531.com/league/api/?type=user&filter=all">http://www.wolven531.com/league/api/?type=user&filter=all</a>
                </li>
                <li>
                    Single User Close Name Match [Not an exact match] (Ja): 
                    <a href="http://www.wolven531.com/league/api/?type=user&filter=single&id=Ja">http://www.wolven531.com/league/api/?type=user&filter=single&id=Ja</a>
                </li>
                <li>
                    Single User Exact Name (JaxB): 
                    <a href="http://www.wolven531.com/league/api/?type=user&filter=single&id=PhreakJr">http://www.wolven531.com/league/api/?type=user&filter=single&id=PhreakJr</a>
                </li>
                <li>
                    Single User Exact ID (1): 
                    <a href="http://www.wolven531.com/league/api/?type=user&filter=single&id=1">http://www.wolven531.com/league/api/?type=user&filter=single&id=1</a>
                </li>
            </ul>
        </div>
        <h3>Games</h3>
        <div>
            <p>
                The games service is the most flexible one to date and supports a wide variety variables for querying the DB.
            </p>
            <p>
                Syntax: http://www.wolven531.com/league/api/?type=game
            </p>
            Examples:
            <ul>
                <li>
                    All Games: 
                    <a href="http://www.wolven531.com/league/api/?type=game&filter=all">http://www.wolven531.com/league/api/?type=game&filter=all</a>
                </li>
                <li>
                    Single Game Exact ID (50): 
                    <a href="http://www.wolven531.com/league/api/?type=game&filter=single&id=50">http://www.wolven531.com/league/api/?type=game&filter=single&id=50</a>
                </li>
                <li>
                    Games By User Exact Name (JaxB):
                    <a href="http://www.wolven531.com/league/api/?type=game&filter=search&user=JaxB">http://www.wolven531.com/league/api/?type=user&filter=search&user=JaxB</a>
                </li>
                <li>
                    Games By Minimum [inclusive] Stat (Kills >= 10):
                    <a href="http://www.wolven531.com/league/api/?type=game&filter=search&stat=kills&min=10">http://www.wolven531.com/league/api/?type=user&filter=search&stat=kills&min=10</a>
                </li>
                <li>
                    Games By Maximum [inclusive] Stat (Kills <= 5):
                    <a href="http://www.wolven531.com/league/api/?type=game&filter=search&stat=kills&max=5">http://www.wolven531.com/league/api/?type=user&filter=search&stat=kills&max=5</a>
                </li>
                <li>
                    Games By Exact Stat (Kills = 25):
                    <a href="http://www.wolven531.com/league/api/?type=game&filter=search&stat=kills&eq=25">http://www.wolven531.com/league/api/?type=user&filter=search&stat=kills&eq=25</a>
                </li>
            </ul>
            <p>Operation Types:</p>
            <ul>
                <li>&max=</li>
                <li>&min=</li>
                <li>&eq=</li>
            </ul>
            <p>Note, max and min can be used in combination, but if eq is present, they will not be considered.</p>
            <p>Stat Types: (&stat=)</p>
            <ul>
                <li>kills</li>
                <li>deaths</li>
                <li>assists</li>
                <li>minions</li>
                <li>gold</li>
                <li>date</li>
            </ul>
            <p>Note: Date is expected to be in the standard MySQL format of: YYYY-MM-DD. Failing to provide this format may yield unexpected results.</p>
            <p>You may also choose to only retrieve certain stats in your request by using the display parameter (Games with at least 10 kills, retrieving only deaths and assists):
                <a href="http://www.wolven531.com/league/api/?type=game&filter=search&stat=kills&min=10&display=deaths,assists">http://www.wolven531.com/league/api/?type=game&filter=search&stat=kills&min=10&display=deaths,assists</a>
            </p>
            <p>Display Types: (&display=)</p>
            <ul>
                <li>kills</li>
                <li>deaths</li>
                <li>assists</li>
                <li>minions</li>
                <li>gold</li>
                <li>date</li>
            </ul>
            <p>Note: you may select as many combinations of the display types as possible, but they must be comma-separated as in the example above.</p>
            <p>Note: Date is expected to be in the standard MySQL format of: YYYY-MM-DD. Failing to provide this format may yield unexpected results.</p>
        </div>
    </body>
</html>