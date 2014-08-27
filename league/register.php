<?php
    require_once('./util.php');
    if(isUserLoggedIn()) {
        header('Location: http://www.wolven531.com/league/view.php');
    }
    else if(getReqType() == 'POST' && !isUserLoggedIn()) {
        $data = array('username' => $_POST['username'], 'password' => $_POST['password']);
        $dao = getDao();
        $result = $dao->checkRegisterData($data);

        if($result) {
            $dao->registerUser( $data );
            setUserSession(strtolower($data['username']));
            header('Location: http://www.wolven531.com/league/view.php');
        }
        else {
            header('Location: http://www.wolven531.com/league/register.php');
        }
        destroyDao();
    }
    else {
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $SITE_TITLE; ?> Register</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
        <style>
            #register-confirm-modal {
                display: none;
            }
            
            .emphasized {
                font-weight: bold;
                font-style: italic;
            }
        </style>
        <?php require('./basicSiteNeeds.php'); ?>
        <script src="./mainScript.js"></script>
        <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#registerForm input[type="submit"]').click(function(e){
                    e.preventDefault();
                    $('#register-confirm-modal').show().dialog({
                        modal: true,
                        closeText: 'hide',
                        draggable: false,
                        buttons: [
                            {
                                text: 'Absolutely', 
                                click: function() {
                                    var valid = validateForm($, $('#registerForm'), 
                                        [
                                            {'name' : 'username', 'type':'input', 'valType' : 'alphanumeric'},
                                            {'name' : 'password', 'type':'input', 'valType' : 'password'}
                                        ]);
                                    $( this ).dialog( 'close' );
                                    if(valid) {
                                        $('#registerForm').submit();
                                    }
                                }
                            },
                            {
                                text: 'Let Me Make Sure',
                                click: function() {
                                    $( this ).dialog( 'close' );
                                }
                            }
                        ]
                    });
                    
                });
            });
        </script>
    </head>
    <body class="metro">
        <?php include('./navigation.php'); ?>
        <div class="page">
            <div class="page-region">
                <div class="page-region-content">
                    <div class="container">
                        <?php if(!isUserLoggedIn()) { include('./forms/loginForm.html'); } ?>
                        <h2>Register New User</h2>
                        <div id="register-confirm-modal" title="Confirm Summoner Name">
                            <p>Are you sure you're using your <span class="emphasized">League Summoner Name</span>? This is
                                required to be accurate for the API.</p>
                        </div>
                        <div class="content">
                            <?php include('./forms/registerForm.html'); ?>
                        </div>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
        <?php include('./footer.php'); ?>
    </body>
</html>
<?php
    }
?>