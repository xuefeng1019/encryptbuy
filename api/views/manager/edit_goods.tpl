<html>
<head>
    <?php include_once 'api/views/__manager_head.tpl'; ?>
</head>
<body>
<?php include_once 'api/views/__native.tpl'?>
<main class="Main">
    <div class="ActivityPage">
        <div class="Section">
            <div class="Container">
                <div class="ActivityPage-header">
                    <h1 class="Hero-h2">商品管理--编辑商品</h1>
                </div>
            </div>
        </div>
        <div class="Section">
            <div class="Container">
                <div class="ActivityPage-activity">
                    <p class="InputButtons-group">
                        <label for="email">商品名称</label>
                        <input type="email" id="main_title" name="main_title" class="InputButtons InputButtons-input" placeholder="" value="" maxlength="100" autocapitalize="off" autocorrect="off">
                    </p>
                    <p class="InputButtons-group">
                        <label for="email">副标题</label>
                        <input type="email" id="sub_title" name="sub_title" class="InputButtons InputButtons-input" placeholder="" value="" maxlength="100" autocapitalize="off" autocorrect="off">
                    </p>
                    <p class="InputButtons-group">
                        <label for="email">商品主图</label>
                        <input type="email" id="sub_title" name="sub_title" class="InputButtons InputButtons-input" placeholder="" value="" maxlength="100" autocapitalize="off" autocorrect="off">
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include_once 'api/views/__footer.tpl'?>
</body>
</html>