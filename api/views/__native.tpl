<div id="Loading" class="Loading">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
</div>
<div id="app" style="display: none;">
    <div class="App">
        <div class="Header-container">
            <div class="Header">
                <div class="Header-group Header-group-home">
                    <a class="Header-home" aria-current="false" href="/">
                        <div class="Header-logoWrapper">
                            <div class="Header-logo"></div>
                        </div>
                        <h1 class="Header-wordmark"><?=$title?></h1>
                    </a>
                </div>
                <div class="Header-group">
                    <a class="Header-navigation-item Header-nav-login <?=$uri == 'sign-in' ? "Header-navigation-item--active" : "" ?>" aria-current="false" href="/sign-in"><?=$lang_site_login?></a>
                    <a class="Header-navigation-item <?=$uri == 'market' ? "Header-navigation-item--active" : "" ?>" aria-current="true" href="/market"><?=$lang_site_market?></a>
                    <a class="Header-navigation-item Header-nav-order <?=$uri == 'orders' ? "Header-navigation-item--active" : "" ?>" style="display: none;" aria-current="false" href="/orders"><?=$lang_site_record?></a>
                </div>
                <div class="Header-group">
                    <a class="Header-group-toggleIcon">
                        <img src="/static/images/icons/group.svg">
                    </a>
                    <div class="Header-dropdown Dropdown-content Header-navigation-dropdown">
                        <a class="Header-navigation-item <?=$uri == 'about' ? "Header-navigation-item--active" : "" ?>" aria-current="false" href="/about"><?=$lang_site_about?></a>
                        <a class="Header-navigation-item <?=$uri == 'faq' ? "Header-navigation-item--active" : "" ?>" aria-current="false" href="/faq"><?=$lang_site_qa?></a>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $('.Header-group-toggleIcon').click(function(){
                if ($('.Header-navigation-dropdown').css('display') == 'none') {
                    $('.Header-navigation-dropdown').addClass('show');
                } else {
                    $('.Header-navigation-dropdown').removeClass('show');
                }
            });
        </script>
