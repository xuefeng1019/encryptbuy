<html>
<head>
    <?php include_once 'api/views/__head.tpl'; ?>
</head>
<body>
<?php include_once 'api/views/__native.tpl'?>

<main class="Main">
    <div class="BrowsePage">
        <div class="BrowsePage-tabs">
            <div class="Container Container--lg">
                <div class="TabNav">
                    <a class="TabNav-tab TabNav-tab--active" aria-current="false" href="/market">全部商品</a>
                </div>
            </div>
        </div>

        <div class="BrowseKittyGallery">
            <div class="KittiesGallery">
                <div class="Container Container--lg">
                    <?php
                    if ($goods) {
                    ?>
                    <div class="KittiesGrid">
                        <?php
                        foreach ($goods as $g) {
                        ?>
                        <div class="KittiesGrid-item">
                            <a aria-current="false" href="/goods/<?=$g['cn']['id']?>">
                                <div class="KittyCard-wrapper" style="padding-bottom: 10px;">
                                    <div class="KittyCard KittyCard--bg-sapphire KittyCard--responsive KittyCard--shadow-sapphire">
                                        <img class="KittyCard-image" src="/<?=$g['cn']['goods_banner']?>" alt="<?=$g['cn']['main_title']?>" />
                                        <div class="KittyCard-status">
                                            <div class="KittyStatus">
                                                <div class="KittyStatus-item">
                                                    <span class="KittyStatus-itemIcon"><i class="Icon Icon--tag"></i></span>
                                                    <span class="KittyStatus-itemText"><span class="KittyStatus-label">等待领养</span><span class="KittyStatus-note"><small>Ξ</small> 0.0029</span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="KittyCard-details" style="margin-top:20px;">
                                        <span class="KittyCard-details-item"><?=$g['cn']['main_title']?></span>
                                    </div>
                                    <div class="KittyCard-details">
                                        <span class="KittyCard-details-item" style="color:red;">
                                            <!--<img class="Ethereum_logo" src="/static/images/150px-Ethereum_logo_2014.svg.png">-->
                                            <small>Ξ</small>&nbsp;<?=$g['cn']['eth_price']?> ETH
                                        </span>
                                    </div>
                                    <!--<div class="KittyCard-details">
                                        <span class="KittyCard-details-item">总量:<?=$g['cn']['slice_count']?>份</span>
                                        <span class="KittyCard-details-item">剩余:<?=$g['cn']['slice_count'] - $g['cn']['sold_count']?>份</span>
                                    </div>
                                    -->
                                </div>
                            </a>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    <?php
                    } else {
                    ?>
                    <h2 class="Hero-h2">没有您要找的数据</h2>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            if ($all_page > 1) {
                $for_start = $page;
                if ($page > 5) {
                    $for_end   = $page + 5;
                    $for_start = $page - 4;
                } else {
                    $for_end   = 10;
                    $for_start = 1;
                }

                if ($for_end > $all_page) {
                    $for_end = $all_page;
                }
                if ($for_start < 1) {
                    $for_start = 1;
                }

            ?>
            <div class="KittiesGalleryPagination">
                <div class="Pagination">
                    <div class="Pagination-pages">
                        <?php
                        for ($i = $for_start; $i <= $for_end; $i++) {
                        ?>
                        <button class="Pagination-page <?=$i == $page ? "Pagination-page--active" : ""?>"><?=$i?></button>
                        <?php
                        }
                        ?>
                    </div>
                    <div>
                        <?php
                        if ($page > 1) {
                        ?>
                        <button class="Pagination-button">上一页</button>
                        <?php
                        }
                        ?>
                        <?php
                        if ($page < $all_page) {
                        ?>
                        <button class="Pagination-button">下一页</button>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </div>
</main>

<?php include_once 'api/views/__footer.tpl'?>
</body>
</html>