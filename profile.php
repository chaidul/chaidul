<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
        exit;
    }

    if (isset($_GET['id'])) {

        $profile_id = isset($_GET['id']) ? $_GET['id'] : 0;

        $profile_id = helper::clearInt($profile_id);

        $profile = new profile($dbo, $profile_id);

        $profile->setRequestFrom(auth::getCurrentUserId());
        $profileInfo = $profile->get();

    } else {

        header("Location: /");
        exit;
    }

    if ($profileInfo['error'] === true) {

        header("Location: /");
        exit;
    }

    if ($profileInfo['id'] == auth::getCurrentUserId()) {

        $page_id = "my-profile";

        $account = new account($dbo, $profileInfo['id']);
        $account->setLastActive();
        unset($account);

    } else {

        $page_id = "profile";
    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

            include_once("stubs/profile.php");
            exit;
    }
    $userRating = new userRating($dbo, auth::getCurrentUserId());
    $rating = $userRating->getUserRating($profileInfo['id']);

    $css_files = array("my.css", "account.css");
    $page_title = $profileInfo['fullname']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");
    $account = new account($dbo, auth::getCurrentUserId());
    $account->setLastActive();
    $vip = $account->getVip();

?>

<body>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>
<style>
.rate {
    float: left;
    height: 46px;
}
.rate:not(:checked) > input {
    position:absolute;
    top:-9999px;
}
.rate:not(:checked) > label {
    float:right;
    width:1em;
    overflow:hidden;
    white-space:nowrap;
    cursor:pointer;
    font-size:30px;
    color:#ccc;
}
.rate:not(:checked) > label:before {
    content: '★ ';
}
.rate > input:checked ~ label {
    color: #ffc700;    
}
.rate:not(:checked) > label:hover,
.rate:not(:checked) > label:hover ~ label {
    color: #deb217;  
}
.rate > input:checked + label:hover,
.rate > input:checked + label:hover ~ label,
.rate > input:checked ~ label:hover,
.rate > input:checked ~ label:hover ~ label,
.rate > label:hover ~ input:checked ~ label {
    color: #c59b08;
}
[type="radio"]:not(:checked) + label::before, [type="radio"]:checked + label::before {
  border-radius: 50%;
  border: 0px solid #5a5a5a;
}
[type="radio"]:checked + label::after {
  border-radius: 50%;
  border: 0px solid #26a69a;
  background-color: none;
  z-index: -1;
  -webkit-transform: scale(1.02);
  transform: scale(1.02);
}
</style>
<main class="content">
    <div class="container">
        <div class="row">
            <div class="col s12 m12 l12">
                <div class="row">
                    <div class="col s12">
                        <div class="card white">

                            <ul class="collection" style="border: none; border-bottom: 1px solid #e0e0e0">

                                <li class="collection-item avatar" style="padding-left: 94px">
                                    <img style="height: 64px; width: 64px;" src="<?php if ( strlen($profileInfo['bigPhotoUrl']) != 0 ) { echo $profileInfo['bigPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="" class="circle profile-img">
                                    <span style="font-size: 1.44rem;" class="title"><?php echo $profileInfo['fullname']; ?></span>
                                    <p>
                                        <span>@<?php echo $profileInfo['username']; ?></span>
                                        <br>
                                        <?php

                                            if ($profileInfo['online']) {

                                                echo "<span class=\"teal-text\">Online</span>";

                                            } else {

                                                if ($profileInfo['lastAuthorize'] == 0) {

                                                    echo "Offline";

                                                } else {

                                                    echo $profileInfo['lastAuthorizeTimeAgo'];
                                                }
                                            }
                                            if($profileInfo['vip'] == 1){
                                            	echo '<img src="../img/important.svg" width="21px">';
                                            }
                                        ?>
                                 
                                    <?php
                                    if (strlen($profileInfo['friendsCount']) > 0) {?>
                                        <p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#238FFB" class="bi bi-people" viewBox="0 0 16 16">
                                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                        </svg> Friends: <?php echo $profileInfo['friendsCount']; ?></p>
                                        
                                    <?php } ?>
                                    </p>
                                    <?php if ($profileInfo['id'] != auth::getCurrentUserId()) {?>
                                    <label for="" style="float: left;clear: both;color: #000;font-size: 15px;font-weight: 500;">User Rating</label><br/>
                                    <input type="hidden" name="profile_id" id="profile_id" value="<?=$profileInfo['id']?>">
                                    <div class="rate">
                                        <input type="radio" id="star5" name="rate" value="5" <?=(($rating['rating'] == 5)?'checked':'')?>/>
                                        <label for="star5" title="text">5 stars</label>
                                        <input type="radio" id="star4" name="rate" value="4" <?=(($rating['rating'] == 4)?'checked':'')?>/>
                                        <label for="star4" title="text">4 stars</label>
                                        <input type="radio" id="star3" name="rate" value="3" <?=(($rating['rating'] == 3)?'checked':'')?>/>
                                        <label for="star3" title="text">3 stars</label>
                                        <input type="radio" id="star2" name="rate" value="2" <?=(($rating['rating'] == 2)?'checked':'')?>/>
                                        <label for="star2" title="text">2 stars</label>
                                        <input type="radio" id="star1" name="rate" value="1" <?=(($rating['rating'] == 1)?'checked':'')?>/>
                                        <label for="star1" title="text">1 star</label>
                                    </div>
                                    <?php } ?>
                                    <?php

                                        if ($profileInfo['id'] == auth::getCurrentUserId()) {

                                            ?>
                                                <a href="/settings.php" class="secondary-content"><i class="material-icons">mode edit</i></a>
                                            <?php
                                        }

                                    ?>

                                </li>
          
                            </ul>

                            <div class="row">

                                   
                                <div class="col s12 m12 l12 left">

                                <div class="col s12  m7">
                            
                                    <div class="card white" style="box-shadow: none">


                                    <ul class="collection" style="border: none;">
                             <li class="collection-item">
                                            <h5 class="title"><i class="fa fa-handshake-o" style="font-size:25px;color:orange"></i><?php echo $LANG['label-join-date']; ?></h5>
                                            <p><?php echo $profileInfo['createDate'];; ?></p>
                                
                                        </li>

                                        <?php

                                            if (strlen($profileInfo['sex']) < 2) {

                                                ?>

                                                <li class="collection-item">
                                                    <h5 class="title"><i class="fa fa-id-badge" style="font-size:25px;color:orange"></i><?php echo $LANG['label-gender']; ?></i></h5>
                                                    <p><?php if ($profileInfo['sex'] == 0) { echo $LANG['gender-male']; } else echo $LANG['gender-female']; ?></p>
                                                </li>

                                                <?php
                                            }

                                        ?>

                                        <?php

                                            if (strlen($profileInfo['location']) > 0) {

                                                ?>

                                                    <li class="collection-item">
                                                        <h5 class="title"><i class="fa fa-map" style="font-size:25px;color:orange"></i><?php echo $LANG['label-location']; ?></h5>
                                                        <p><?php echo $profileInfo['location']; ?></p>
                                                    </li>

                                                <?php
                                            }

                                        ?>
                                        
                                        

                                            

                                                <li class="collection-item">
                                                    <h5 class="title"><i class="fa fa-whatsapp" style="font-size:25px;color:#3dc101;margin-right: 10px;"></i><?php echo 'Whatsapp no.'; ?></h5>
                                                    <a href="<?php echo $profileInfo['fb_page']; ?>">
                                                    <?php 
                                                    if (strlen($profileInfo['fb_page']) > 0) {
                                                        if($vip == 1)
                                                        {
                                                            echo $profileInfo['fb_page']; 
                                                        }else{
                                                            $length = strlen($profileInfo['fb_page']);
                                                            echo substr($profileInfo['fb_page'], 0, 4) . str_repeat("*",$length-6) . substr($profileInfo['fb_page'], ($length-2), $length);
                                                        }
                                                    }else{
                                                        echo '**********';
                                                    }
                                                    ?>
                                                    </a>
                                               
                                                </li>

                                        <?php

                                            if (strlen($profileInfo['instagram_page']) > 0) {

                                                ?>

                                                <li class="collection-item">
                                                    <h5 class="title"><?php echo $LANG['label-instagram-link']; ?></h5>
                                                    <a href="<?php echo $profileInfo['instagram_page']; ?>"><?php echo $profileInfo['instagram_page']; ?></a>
                                                </li>

                                                <?php
                                            }

                                        ?>

                                        <?php

                                            if (strlen($profileInfo['status']) > 0) {

                                                ?>

                                                    <li class="collection-item">
                                                        <h5 class="title"><i class="fa fa-creative-commons" style="font-size:25px;color:orange"></i><?php echo $LANG['label-status']; ?></h5>
                                                        <p><?php echo $profileInfo['status']; ?></p>
                                                    </li>

                                                <?php
                                            }

                                        ?>

                                    </ul>
                                    </div>
                                </div>

                                <?php

                                    if ($profileInfo['id'] != auth::getCurrentUserId()) {

                                        ?>

                                        <div class="col s12  m5">
                                            <div class="card white" style="box-shadow: none">

                                                <div class="row">
                                                    <div class="input-field col s12 friends_button_container" style="margin-top: 0">

                                                        <?php

                                                            if ($profileInfo['friend']) {

                                                                ?>
                                                                    <a onclick="Friends.remove('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" style="width: 100%; min-height: 36px; height: auto" class="btn waves-effect waves-light teal"><?php echo $LANG['action-remove-from-friends']; ?></a>
                                                                <?php

                                                            } else {

                                                                if ($profileInfo['follow']) {

                                                                    ?>
                                                                        <a onclick="Profile.sendRequest('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" style="width: 100%; min-height: 36px; height: auto" class="btn waves-effect waves-light teal"><?php echo $LANG['action-cancel-friend-request']; ?></a>
                                                                    <?php

                                                                } else {

                                                                    ?>
                                                                        <a onclick="Profile.sendRequest('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" style="width: 100%; min-height: 36px; height: auto" class="btn waves-effect waves-light teal" ><?php echo $LANG['action-add-to-friends']; ?></a>
                                                                    <?php
                                                                }
                                                            }
                                                        ?>

                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="input-field col s12 report_button_container" style="margin-top: 0">

                                                        <a onclick="Profile.getReportBox(); return false;" style="width: 100%" class="btn waves-effect waves-light teal"><?php echo $LANG['action-report']; ?></a>

                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="input-field col s12 block_button_container" style="margin-top: 0">

                                                        <?php

                                                        if ($profileInfo['blocked']) {

                                                            ?>
                                                                <a onclick="Profile.unblock('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" style="width: 100%" class="btn waves-effect waves-light teal"><?php echo $LANG['action-unblock']; ?></a>
                                                            <?php

                                                        } else {

                                                            ?>
                                                                <a onclick="Profile.block('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" style="width: 100%" class="btn waves-effect waves-light teal"><?php echo $LANG['action-block']; ?></a>
                                                            <?php
                                                        }
                                                        ?>

                                                    </div>
                                                </div>

                                                <?php

                                                    if ($profileInfo['allowMessages'] == 1 || ($profileInfo['allowMessages'] == 0 && $profileInfo['friend'])) {

                                                        ?>

                                                            <div class="row">
                                                                <div class="input-field col s12" style="margin-top: 0">
                                                                    <a href="/chat.php/?chat_id=0&user_id=<?php echo $profileInfo['id']; ?>" style="width: 100%; min-height: 36px; height: auto" class="btn waves-effect waves-light teal"><?php echo $LANG['action-send-message']; ?></a>
                                                                </div>
                                                            </div>

                                                        <?php
                                                    }
                                                ?>

                                            </div>
                                        </div>

                                        <?php

                                    } else {

                                        ?>

                                            <div class="col s12  m5">
                                                <div class="card white" style="box-shadow: none">

                                                    <div class="row">
                                                        <div class="input-field col s12" style="margin-top: 0">
                                                            <a onclick="Profile.changePhoto(); return false;" style="width: 100%; min-height: 36px; height: auto" class="btn waves-effect waves-light teal"><?php echo $LANG['action-change-photo']; ?></a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        <?php
                                    }
                                ?>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

	        </div>
        </div>
    </div>
</main>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
        ?>

        <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

        <script type="text/javascript">

            window.Friends || ( window.Friends = {} );

            Friends.remove = function (friend_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/friends/method/remove.php',
                    data: 'friend_id=' + friend_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("div.friends_button_container").html(response.html);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            window.Profile || ( window.Profile = {} );

            Profile.changePhoto = function() {

                $('#img-box').openModal();
            };

            Profile.getReportBox = function() {

                $('#report-box').openModal();
            };

            Profile.sendReport = function (profile_id, reason, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/report.php',
                    data: 'profile_id=' + profile_id + "&reason=" + reason + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {

                        $('#report-box').closeModal();

                        if (response.hasOwnProperty('error')) {

                            Materialize.toast('<?php echo $LANG['label-profile-reported']; ?>', 3000);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Profile.sendRequest = function (profile_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/friends/method/sendRequest.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("div.friends_button_container").html(response.html);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Profile.block = function (profile_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/block.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("div.block_button_container").html(response.html);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Profile.unblock = function (profile_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/unblock.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("div.block_button_container").html(response.html);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

    <div id="img-box" class="modal">
        <div class="modal-content">
            <h4><?php echo $LANG['label-image-upload-description']; ?></h4>
            <div class="file_select_btn_container">
                <div class="file_select_btn btn" style="width: 220px"><?php echo $LANG['action-add-img']; ?></div>
            </div>

            <div class="file_select_btn_description" style="display: none">
                <?php echo $LANG['msg-loading']; ?>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat"><?php echo $LANG['action-close']; ?></a>
        </div>
    </div>

    <?php

        if (auth::getCurrentUserId() != $profileInfo['id']) {

            ?>

                <div id="report-box" class="modal">
                    <div class="modal-content">
                        <h5><?php echo $LANG['page-profile-report-sub-title']; ?></h5>
                        <a onclick="Profile.sendReport('<?php echo $profileInfo['id']; ?>', '0', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-teal btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-1']; ?></a>
                        <a onclick="Profile.sendReport('<?php echo $profileInfo['id']; ?>', '1', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-teal btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-2']; ?></a>
                        <a onclick="Profile.sendReport('<?php echo $profileInfo['id']; ?>', '2', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-teal btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-3']; ?></a>
                        <a onclick="Profile.sendReport('<?php echo $profileInfo['id']; ?>', '3', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-teal btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-4']; ?></a>
                    </div>
                    <div class="modal-footer">
                        <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat"><?php echo $LANG['action-cancel']; ?></a>
                    </div>
                </div>

            <?php
        }
    ?>

    <script type="text/javascript">

        $('.file_select_btn').upload({
            name: 'uploaded_file',
            method: 'post',
            enctype: 'multipart/form-data',
            action: '/ajax/profile/method/uploadPhoto.php',
            onComplete: function(text) {

                var response = JSON.parse(text);

                if (response.hasOwnProperty('error')) {

                    if (response.error === false) {

                        $('#img-box').closeModal();

                        if (response.hasOwnProperty('lowPhotoUrl')) {

                            $("img.profile-img").attr("src", response.lowPhotoUrl);
                        }
                    }
                }

                $("div.file_select_btn_description").hide();
                $("div.file_select_btn_container").show();
            },
            onSubmit: function() {

                $("div.file_select_btn_container").hide();
                $("div.file_select_btn_description").show();
            }
        });

        $('.rate input').on('click',function(){
            var profile_id = $('#profile_id').val();
            $.ajax({
                url:'/ajax/profile_user_rating.php',
                type:'POST',
                data:{'mode':'rating_user','profile_id':profile_id,'rating':$(this).val()},
                success:function(){
                    window.location.reload();
                }
            })
        });

    </script>

</body>
</html>