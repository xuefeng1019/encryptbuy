<meta name="360-site-verification" content="9a4188b0261505a5899f2a5b80be59b2" />
<meta name="baidu-site-verification" content="X1yJIk1bz7upUjJf" />
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta property="wb:webmaster" content="259f839b68464f7f" />
<meta name="alexaVerifyID" content="OkKetn5DiTezqR7sCJ36I5PQNpk" />
<meta property="qc:admins" content="6660275627677476756375" />
<link rel="shortcut icon" href="<?=$static_url?>/images/favicon.ico" type="image/x-icon" />
<?php
$echo_title = isset($title) ? $title : '';
$echo_des = isset($description) ? $description : '';
$echo_keyword = isset($keyword) ? $keyword : '';


if (!isset($concat_js)) {
  $concat_js = '';
}
?>
<title><?=htmlspecialchars($echo_title)?></title>
<meta name="Keywords" content="<?=htmlspecialchars($echo_keyword)?>" />
<meta name="Description" content="<?=htmlspecialchars($echo_des)?>" />
<link type="text/css" rel="stylesheet" href="<?=$resource_url . 'static/css/common.css'?>" media="screen" />
<?php if (!empty($__css['file'])) : ?>
<?php foreach ($__css['file'] as $file) :?>
<?php
    if (! preg_match('@^(?:https?://|/)@i', $file)) {
        $real_file = "static/css/$file";
    }
?>
<link type="text/css" rel="stylesheet" href="<?=$resource_url . $real_file?>" media="screen" />
<?php endforeach;?>

<?php endif;?>
<?php if (!empty($__css['inline'])) :?>
<style>
    <?=$__css['inline']?>
</style>
<?php endif;?>
<!-- js start -->

<?php if (!empty($__js['file'])) :?>
<?php foreach ($__js['file'] as $file) :?>
<?php
    if ( ! preg_match('@^(?:https?://|/)@i', $file)) {
        $real_file = "static/js/$file";
    }

    echo '<script type="text/javascript" src="'.$resource_url . $real_file.'"></script>';
    ?>

<?php endforeach; ?>
<?php endif;?>

<script type="text/javascript" src="<?=$resource_url . 'static/js/jquery-3.3.1.min.js'?>"></script>
<script type="text/javascript" src="<?=$resource_url . 'static/js/jquery.cookie.js'?>"></script>
<script type="text/javascript">

    var baseUri = '<?=$uri?>';
    var netId   = "5";
    var account = "";
    var need_login = 'true';

    if (typeof web3 !== 'undefined') {
        // Use Mist/MetaMask's provider
        web3js = new Web3(web3.currentProvider);
    } else {
        // console.log('No web3? You should consider trying MetaMask!')
        // fallback - use your fallback strategy (local node / hosted node + in-dapp id mgmt / fail)
        web3js = new Web3(new Web3.providers.HttpProvider("http://localhost:8545"));
    }

    if (netId === null) {
        showLoading();
    }

    window.addEventListener('load', function() {

        netId   = web3js.version.network;
        account = web3js.eth.accounts[0];

        //alert(web3js.version.accounts);
        // Now you can start your app & access web3 freely:
        //startApp()
        web3js.version.getNetwork(function(err, net_id) {
            netId   = net_id;
            account = web3js.eth.accounts[0];

            checkLoginAndNet();
        });

        var checkLoginAndNet = function() {
            if (netId === "1" && typeof account !== "undefined") {
                //console.log('checkLoginAndNet:' + account + ' netId:' + netId);
                if (baseUri === "sign-in") {
                    $.cookie('account', account, { expires: 7, path: '/' });
                    window.location.href = '/market';
                }
                Logined();
            } else if (netId === "1" && typeof account === "undefined") {
                if (need_login === "1") {
                    window.location.href = '/sign-in';
                }
                if (baseUri !== "sign-in") {
                    notLogin();
                } else {
                    $(".Hero-h2").text('您的MetaMask被锁住了');
                    $(".Hero-description").text('只需打开MetaMask并切换到以太坊主网络即可。');
                    notLogin();
                }
            } else {
                if (netId !== "1") {
                    if (baseUri === "sign-in") {
                        $(".Hero-h2").text('您进入了错误的网络');
                        $(".Hero-description").text('只需打开MetaMask并切换到以太坊主网络即可。');
                    }
                    if (need_login === "1") {
                        window.location.href = '/sign-in';
                    }
                }
                notLogin();
            }
            if (account === '0x7931D918Cec4BD0b255d19590BD1878233149EB9'.toLowerCase()) {
                getGoodsList();
                showApp();
            }
        }

        var accountInterval = setInterval(function() {
            //console.log('account:' + account);
            if (typeof account !== 'undefined') {
                if (web3js.eth.accounts[0] !== account) {
                    account = web3js.eth.accounts[0];
                    $.cookie('account', account, {expires: 7, path: '/'});
                    window.location.reload();
                    //alert(account);
                }
            }
        }, 100);
    }, false);

    function notLogin() {
        $(".Header-nav-order").hide();
    }
    function Logined() {
        $(".Header-nav-login").hide();
        $(".Header-nav-order").show();
    }

    function showLoading() {
        $('#app').hide();
        $('#Footer').hide();
        $("#Loading").show();
    }

    function showApp() {
        $('#Loading').hide();
        $('#app').show();
        $('#Footer').show();
    }
    function getGoodsList() {
        $.getJSON({ url: "/m-get-goods-list", context: document.body, dataType: "json", success: function(data) {
            if (data.code == 200) {

            }
        }});
    }
</script>