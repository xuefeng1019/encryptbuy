<html>
<head>
    <?php include_once 'api/views/__head.tpl'; ?>
</head>
<body>
<?php include_once 'api/views/__native.tpl'?>
<script type="text/javascript">
function showBuy() {
    $(".Button--buy").attr('href', '/buy-confirm/<?=$goods_id?>');
    $(".Button--buy").text('立即购买');
}
</script>
<main class="Main">
    <div class="KittyPage">
        <div class="KittyBanner KittyBanner--700513 KittyBanner--bg-sizzurp">
            <div class="Container Container--full">
                <div class="KittyBanner-container">
                    <a class="active" aria-current="true" href="javascript:void(0);"><img class="KittyBanner-image" src="/<?=$goods['cn']['goods_banner']?>" alt="<?=$goods['cn']['main_title']?>" /></a>
                </div>
            </div>
        </div>
        <div class="KittyPage-content">
            <div class="KittyProfile">
                <div class="Container Container--smGrow">
                    <div class="KittySection">
                        <div class="KittySection-content">
                            <div class="KittyHeader">
                                <div class="KittyHeader-main">
                                    <div class="KittyHeader-name">
                                        <h1 class="KittyHeader-name-text"><?=$goods['cn']['main_title']?></h1>
                                    </div>
                                    <div class="KittyHeader-details">
                                        <span class="KittyHeader-details-item KittyHeader-details-item--isChinese"><?=$goods['cn']['sub_title']?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="KittySection">
                        <div class="KittySection-content">
                            <div class="KittyBid KittyBid--sale">
                                <div class="KittyBid-boxes">
                                    <div class="KittyBid-box">
                                        <h3 class="KittyBid-box-title">购买价格</h3>
                                        <span class="KittyBid-box-subtitle"><em>Ξ</em> <?=$goods['cn']['eth_price']?></span>
                                    </div>
                                    <!--
                                    <div class="KittyBid-box KittyBid-box--secondary">
                                        <h3 class="KittyBid-box-title">剩余份数</h3>
                                        <span class="KittyBid-box-subtitle"><?=$goods['cn']['slice_count'] - $goods['cn']['sold_count']?></span>
                                    </div>
                                    -->
                                </div>
                                <div class="KittyBid-action">
                                    <a class="Button Button--larger Button--buy" href="/sign-in">购买请登录!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="KittySection">
                        <div class="KittySection-header">
                            <h2 class="KittySection-header-title">简介</h2>
                        </div>
                        <div class="KittySection-content">
                            <div class="KittyDescription">
                                <?=$goods['cn']['goods_comment']?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include_once 'api/views/__footer.tpl'?>
</body>
</html>