<!DOCTYPE html>
<html>
    <head>
        <title>Wolven531 API v2 Tester</title>
        <style>
            .test-failure {
                color: #cc0000;
            }
            
            .test-success {
                color: #00cc00;
            }
            
            .test-container,
            .test-stats {
                margin: 10px;
                padding: 5px;
                border: 1px solid #000;
            }
            
            .test-container {
                overflow: scroll;
                height: 150px;
            }
        </style>
    </head>
    <body>
<?php
    $scenarios = array(
        array(
            'title' => 'user_games_for_user_name',
            'url' => 'http://wolven531.com/league/api/v2/?type=user_games&user_name=HeHimself',
            'test' => 'testCountResult',
            'expected' => 10
        ),
        array(
            'title' => 'user_games_for_id',
            'url' => 'http://wolven531.com/league/api/v2/?type=user_games&id=9',
            'test' => 'testCountResult',
            'expected' => 10
        ),
        array(
            'title' => 'user_game_for_id',
            'url' => 'http://wolven531.com/league/api/v2/?type=user_game&id=1244338613',
            'test' => 'testCountResult',
            'expected' => 1,
            'encodeResult' => true
        ),
        array(
            'title' => 'users',
            'url' => 'http://wolven531.com/league/api/v2/?type=users',
            'test' => 'testNotEmptyResult',
            'encodeResult' => true
        ),
        array(
            'title' => 'user_for_id',
            'url' => 'http://wolven531.com/league/api/v2/?type=user&id=1',
            'test' => 'testCountResult',
            'expected' => 1,
            'encodeResult' => true
        ),
        array(
            'title' => 'user_for_user_name',
            'url' => 'http://wolven531.com/league/api/v2/?type=user&user_name=PhreakJr',
            'test' => 'testCountResult',
            'expected' => 1,
            'encodeResult' => true
        ),
        array(
            'title' => 'items',
            'url' => 'http://wolven531.com/league/api/v2/?type=items',
            'test' => 'testCountResult',
            'expected' => 243,
            'encodeResult' => true,
            'entities' => true
        ),
        array(
            'title' => 'item_for_id',
            'url' => 'http://wolven531.com/league/api/v2/?type=item&id=1026',
            'test' => 'testCountResult',
            'expected' => 1,
            'encodeResult' => true
        ),
        array(
            'title' => 'champs',
            'url' => 'http://wolven531.com/league/api/v2/?type=champs',
            'test' => 'testCountResult',
            'expected' => 119
        ),
        array(
            'title' => 'champ_for_id',
            'url' => 'http://wolven531.com/league/api/v2/?type=champ&id=2',
            'test' => 'testCountResult',
            'expected' => 1,
            'encodeResult' => true
        ),
        array(
            'title' => 'check_user_login_success',
            'url' => 'http://wolven531.com/league/api/v2/?type=user_login&user_name=Jaxb&password=theultimatepass',
            'test' => 'testTrueResult',
            'hideUrl' => true,
            'encodeResult' => true
        ),
        array(
            'title' => 'check_user_login_failure',
            'url' => 'http://wolven531.com/league/api/v2/?type=user_login&user_name=Jaxb&password=',
            'test' => 'testFalseResult',
            'encodeResult' => true
        ),
        array(
            'title' => 'user_stats_for_user_name',
            'url' => 'http://wolven531.com/league/api/v2/?type=user_stats&user_name=PhreakJr',
            'test' => 'testNotNullResult',
            'encodeResult' => true
        ),
        array(
            'title' => 'user_stats_for_user_id',
            'url' => 'http://wolven531.com/league/api/v2/?type=user_stats&id=1',
            'test' => 'testNotNullResult',
            'encodeResult' => true
        )
    );
    
    $testDescriptions = '';
    $successes = 0;
    $failures = 0;
    for($index = 0; $index < count($scenarios); $index++) {
        $s = $scenarios[$index];
        //echo $s['title'];
        $testFunc = $s['test'];
        $resp = file_get_contents($s['url']);
        $resp = json_decode($resp);
        $testResult = $testFunc($resp, $s['expected']);
        if($testResult['result'] === true) { $successes++; }
        else { $failures++; }
        $actual = is_object($testResult['actual']) ? 'Object' : json_encode($testResult['actual']);
        if($s['entities']) { $actual = htmlentities($actual); }

        $result = '<div class="test-container">'
                . 'Testing scenario: "' . $s['title'] . '"...'
                . '<br/>'
                . 'URL: ' . ($s['hideUrl'] ? 'hidden' : '<a href="' . $s['url'] . '">' . $s['url'] . '</a>')
                . '<br />'
                . 'Test Result: ' . ($testResult['result'] === true ? '<span class="test-success">Success' : '<span class="test-failure">Failure') . '</span>'
                . '<br />'
                . 'Expected: ' . $testResult['expected']
                . '<br />'
                .  'Actual: ' . $actual
            . '</div>';
        $testDescriptions .= $result;
    }

    echo '<div class="test-stats">Successes: <span class="test-success">' . $successes . '</span>, Failures: <span class="test-failure">' . $failures . '</span></div>';
    echo $testDescriptions;
    
    function testCountResult($result, $expected)
    {
        $return = array();
        $return['actual'] = $result;
        $return['expected'] = intval($expected);
        $return['result'] = count($result) === $return['expected'];

        return $return;
    }
    
    function testNotEmptyResult($result)
    {
        $return = array();
        $return['actual'] = $result;
        $return['expected'] = 'Not empty';
        $return['result'] = !empty($result);

        return $return;
    }
    
    function testTrueResult($result)
    {
        $return = array();
        $return['actual'] = $result !== null ? (isset($result->success) ? $result->success : $result) : false;
        $return['expected'] = 'true';
        $return['result'] = $return['actual'] === true;
        $return['actual'] = $result;

        return $return;
    }
    
    function testFalseResult($result)
    {
        $return = array();
        $return['actual'] = $result !== null ? (isset($result->success) ? $result->success : $result) : false;
        $return['expected'] = 'false';
        $return['result'] = $return['actual'] === false;
        $return['actual'] = $result;

        return $return;
    }

    function testNotNullResult($result)
    {
        $return = array();
        $return['actual'] = $result;
        $return['expected'] = 'Not null';
        $return['result'] = $result !== null;

        return $return;
    }
    
?>
    </body>
</html>