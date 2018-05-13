<html>
<head>
    <?php include_once 'api/views/__head.tpl'; ?>
</head>
<body>
<?php include_once 'api/views/__native.tpl'?>
<script src="/static/js/node_modules/distpicker/dist/distpicker.js"></script>
<script src="/static/js/jquery.validate.js"></script>
<script type="text/javascript">
$(function() {

    var can_submit = false;

    var nick_name = "";
    var phone     = "";
    var province  = "";
    var city      = "";
    var district  = "";
    var address   = "";
    var zip       = "";

    function validate() {
        nick_name = $("#nickname").val();
        phone     = $("#phone").val();
        province  = $("#province").val();
        city      = $("#city").val();
        district  = $("#district").val();
        address   = $("#address").val();
        zip       = $("#zip").val();

        if (nick_name) {
            can_submit = true;
        } else {
            can_submit = false;
        }
        if (phone && can_submit) {
            can_submit = true;
        } else {
            can_submit = false;
        }
        if (province && can_submit) {
            can_submit = true;
        } else {
            can_submit = false;
        }
        if (city && can_submit) {
            can_submit = true;
        } else {
            can_submit = false;
        }
        if (district && can_submit) {
            can_submit = true;
        } else {
            can_submit = false;
        }
        if (address && can_submit) {
            can_submit = true;
        } else {
            can_submit = false;
        }
        if (zip && can_submit) {
            can_submit = true;
        } else {
            can_submit = false;
        }

        if (can_submit == true) {
            $('#save-botton').removeAttr('disabled');
        } else {
            $('#save-botton').attr('disabled', 'on');
        }
    }

    $("#nickname").bind("input blur", function() {
        validate();
    });
    $("#phone").bind("input blur", function() {
        validate();
    });
    $("#province").bind("input blur", function() {
        validate();
    });

    $("#city").bind("input blur", function() {
        validate();
    });

    $("#district").bind("input blur", function() {
        validate();
    });

    $("#address").bind("input blur", function() {
        validate();
    });
    //$("input[type='text']").change(function() {
    $("#zip").bind("input blur", function() {
        validate();
    });

    $("#save-botton").click(function () {
        validate();
        if (can_submit == true) {
            $.ajax({
                type: "POST",
                url: "/save-address",
                data: {nickname:nick_name, phone:phone, province:province, city:city, district:district,address:address,zip:zip},
                success: function(data) {
                    if (data.code == '200') {
                        window.location.reload();
                    }
                },
                dataType: "json"
            });
        }
        return false;
        //$("#save-address").submit();
    });

    $(".buy-product").click(function() {

        if (typeof account !== "undefined") {
            var address_id = $(this).attr('rel');
            var gas_price = 0;
            web3js.eth.getGasPrice(function(error, result) {
                gas_price = result.c[0];

                var goods_price = web3js.toWei("<?=$goods['cn']['eth_price']?>", 'ether');

                var gas_limit = 1000000;
                web3js.eth.getBlock("latest", function(error, _result) {
                    gas_limit = _result.gasLimit;
                    console.log(gas_limit);
                    $.ajax({
                        type: "POST",
                        url: "/create-order",
                        data: {account:account, goods_id:<?=$goods['cn']['id']?>, address_id:address_id},
                        success: function(data) {
                            if (data.code == '200') {
                                web3js.eth.sendTransaction({data: data.order_id, from:account, to:'0x7931D918Cec4BD0b255d19590BD1878233149EB9',value: goods_price, gasPrice: gas_price, gas: gas_limit}, function(err, transactionHash) {
                                    if (!err) {
                                        alert(transactionHash);
                                    } else {
                                        alert(err);
                                    }
                                })
                            }
                        },
                        dataType: "json"
                    });
                })
            });
        }
    });
})
</script>
<main class="Main">
    <div class="Hero">
        <div class="Transaction">
            <h1 class="Transaction-title"><?=$goods['cn']['main_title']?></h1>
            <div class="Transaction-flow">
                <span class="Transaction-item Transaction-item--from">选择配送地址</span>
                <svg width="32" height="1" viewbox="0 0 32 1" xmlns="http://www.w3.org/2000/svg">
                    <path d="M.98.5h30.07" stroke="#DCDBD9" fill="none" fill-rule="evenodd" stroke-linecap="square"></path>
                </svg>
                <span class="Transaction-item Transaction-item--transfer">支付 <strong>Ξ<?=$goods['cn']['eth_price']?></strong>&nbsp;ETH</span>
            </div>
        </div>
        <div class="Container Container--sm Container--center" style="max-width:66rem;">
            <?php
            if ($address && count(address) > 0) {
            ?>
            <div class="ActivityPage-activity">
                <div>
                    <?php
                    foreach ($address as $_add) {
                        $slice = explode("|", $_add['area']);
                    ?>
                    <div class="Activity Activity--breed" role="button">
                        <div class="Activity-details">
                            <div class="Activity-details-header">
                                <div class="Activity-details-date">
                                    <span style="font-size:3.0rem;"><?=$_add['name']?></span>  <?=$_add['phone_number']?>
                                </div>
                            </div>
                            <div class="Activity-details-text">
                                <span><?=$slice[0]?>&nbsp;<?=$slice[1]?>&nbsp;<?=$slice[2]?></span>
                            </div>
                            <div class="Activity-details-text">
                                <span><?=$_add['detail_address']?></span>
                            </div>
                            <div class="Activity-details-text">
                                <span><?=$_add['zip']?></span>
                            </div>
                        </div>
                        <div class="Activity-action">
                            <a class="Button Button--cta buy-product" href="javascript:void(0);" rel="<?=$_add['id']?>">配送到此地址</a>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            }
            ?>
            <div class="SettingsForm">
                <form id="save-address">
                    <div class="Section">
                        <p class="InputButtons-group"><label for="nickname">姓名</label><input type="text" id="nickname" name="nickname" class="InputButtons InputButtons-input" placeholder="收货人姓名(非常重要)" maxlength="100"/></p>
                        <p class="InputButtons-group"><label for="phone">联系电话</label><input type="text" id="phone" name="phone" class="InputButtons InputButtons-input" placeholder="联系电话(非常重要)" value="" maxlength="40" autocomplete="off"  /></p>
                        <div class="KittiesToolbar-includeAndSort-container">
                            <div class="KittiesToolbar-sort">
                                <div class="KittiesToolbar-sort-options">
                                    <div class="SelectionGroup SelectionGroup--display-inlineFlex" data-toggle="distpicker">
                                        <div class="SelectionGroup-item">
                                            <div role="menu" class="Select-container">
                                                <select id="province" class="Select" name="province" data-province="选择省">

                                                </select>
                                                <svg class="IconV2 IconV2--position-default IconV2--display-inlineBlock Select-icon" width="16" height="16" viewBox="0 0 16 16">
                                                    <path d="M3.619 3.729h8.762a.75.75 0 0 1 .637 1.146l-4.381 7.042a.75.75 0 0 1-1.274 0L2.982 4.875a.75.75 0 0 1 .637-1.146z" fill="#c4c3c0" fill-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="SelectionGroup-item">
                                            <div role="menu" class="Select-container">
                                                <select id="city" class="Select" name="city" data-city="选择市">

                                                </select>
                                                <svg class="IconV2 IconV2--position-default IconV2--display-inlineBlock Select-icon" width="16" height="16" viewBox="0 0 16 16">
                                                    <path d="M3.619 3.729h8.762a.75.75 0 0 1 .637 1.146l-4.381 7.042a.75.75 0 0 1-1.274 0L2.982 4.875a.75.75 0 0 1 .637-1.146z" fill="#c4c3c0" fill-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="SelectionGroup-item">
                                            <div role="menu" class="Select-container">
                                                <select id="district" class="Select" name="district" data-district="选择区">

                                                </select>
                                                <svg class="IconV2 IconV2--position-default IconV2--display-inlineBlock Select-icon" width="16" height="16" viewBox="0 0 16 16">
                                                    <path d="M3.619 3.729h8.762a.75.75 0 0 1 .637 1.146l-4.381 7.042a.75.75 0 0 1-1.274 0L2.982 4.875a.75.75 0 0 1 .637-1.146z" fill="#c4c3c0" fill-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="InputButtons-group"><label for="address">街道地址</label><input type="text" id="address" name="address" class="InputButtons InputButtons-input" placeholder="请填写详细地址和房间号" value="" maxlength="100"/></p>
                        <p class="InputButtons-group"><label for="zip">邮政编码</label><input type="text" id="zip" name="zip" class="InputButtons InputButtons-input" placeholder="" value="" maxlength="10" autocomplete="off"  /></p>

                    </div>
                    <div class="Section">
                        <button class="Button Button--love Button--larger" id="save-botton" disabled>保存</button><!--disabled-->
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include_once 'api/views/__footer.tpl'?>
</body>
</html>