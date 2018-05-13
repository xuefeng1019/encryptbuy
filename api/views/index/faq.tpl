<html>
<head>
    <?php include_once 'api/views/__head.tpl'; ?>
</head>
<body>
    <?php include_once 'api/views/__native.tpl'?>
        <main class="Main">
            <div class="FaqPage">
                <div class="Container Container--sm">
                    <div class="FaqGroup">
                        <h2 class="FaqGroup-title">新手上路</h2>
                        <div class="FaqGroup-questions">
                            <div class="Faq" role="button" id="faqs.g1.q1">
                                <h3 class="Faq-question">使用EncryptBuy都需要什么?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        您开始购物时所需要:
                                    </p>
                                    <ul>
                                        <li>运行Chrome或Firefox桌面版本的计算机或笔记本电脑</li>
                                        <li>MetaMask，专门与网页应用共同使用的数字钱包</li>
                                        <li>以太币，为EncryptBuy提供支持的数字支付形式</li>
                                    </ul>
                                </span>
                            </div>
                            <div class="Faq" role="button" id="faqs.g1.q2">
                                <h3 class="Faq-question">安装MetaMask，您的数字钱包</h3>
                                <span class="Faq-answer">
                                    <p>
                                        要想要使用EncryptBuy，您将需要安装数字钱包MetaMask。您将需要给MetaMask支付才能参与购物。
                                    </p>
                                    <p>
                                        <strong>注意:</strong>MetaMask这样的数字钱包的功能与银行账户是一致的——请谨慎操作并确保不要忘记您的密码或密码暗号(Seed Phrase)。
                                    </p>
                                </span>
                            </div>
                            <div class="Faq" role="button" id="faqs.g1.q3">
                                <h3 class="Faq-question">为什么MetaMask会被锁定?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        页面偶尔会显示锁屏。这种情况是因为MetaMask在经过一段时间之后会自动锁定您的账户。想要解锁，只需点击MetaMask插件并输入您的密码即可。
                                    </p>
                                </span>
                            </div>
                            <div class="Faq" role="button" id="faqs.g1.q4">
                                <h3 class="Faq-question">重新安装MetaMask</h3>
                                <span class="Faq-answer">
                                    <p>
                                        当用户遇到安装错误时，可能需要卸载并重新安装MetaMask。如果您保留好您的密码暗号（Seed Phrase）的话，只需要删除扩展包，重新安装它，并导入你的密码暗号（Seed Phrase）。然后，设置您要使用的密码（可以是您之前使用的密码或全新密码），然后在EncryptBuy网站上再次确认您的电子邮件地址和用户名。
                                    </p>
                                </span>
                            </div>
                            <div class="Faq" role="button" id="faqs.g1.q5">
                                <h3 class="Faq-question">获取以太币，您的数字货币</h3>
                                <span class="Faq-answer">
                                    <p>
                                        <strong>仅限美国公民:</strong>您可以在MetaMask购买以太币（ETH）。 ETH是一种作为此游戏运行基础的数字货币。
                                    </p>
                                    <p>
                                        <strong>其他用户:</strong>您将需要从交易所购买ETH, 然后将ETH从您的交易所钱包转入您的MetaMask钱包。
                                    </p>
                                </span>
                            </div>
                            <div class="Faq" role="button" id="faqs.g1.q6">
                                <h3 class="Faq-question">如何发送ETH至MetaMask</h3>
                                <span class="Faq-answer">
                                    <p>
                                        <strong>仅限美国公民:</strong>您能够直接从MetaMask使用Coinbase窗口购买ETH。这样购买ETH非常方便，不需要您创建两个账户。
                                    </p>
                                    <p>
                                        <strong>其他用户: </strong>您需要在普通法定货币从交易所购买ETH。复制您的MetaMask地址，点击‘…’然后‘复制地址到粘贴板’。进入Coinbase并点击‘账户’，然后选择您的ETH钱包并点击‘发送’。将MetaMask地址粘贴入文本框内并输入您想要转账的金额。
                                    </p>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="FaqGroup">
                        <h2 class="FaqGroup-title">了解EncryptBuy</h2>
                        <div class="FaqGroup-questions">
                            <div class="Faq" role="button" id="faqs.g2.q1">
                                <h3 class="Faq-question">什么是EncryptBuy?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        EncryptBuy是一种使用区块技术和智能合约的在线购物系统，您可以使用数字货币购买指定商品，比特币和以太币都是加密货币，所有的交易都被记录在区块上，无法被复制、篡改或删除。
                                    </p>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="FaqGroup">
                        <h2 class="FaqGroup-title">购买</h2>
                        <div class="FaqGroup-questions">
                            <div class="Faq" role="button" id="faqs.g3.q1">
                                <h3 class="Faq-question">我如何参与购物?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        前往“市场”，查看商品列表，点击立即购买并使用数字货币支付即可。
                                    </p>
                                </span>
                            </div>
                            <div class="Faq" role="button" id="faqs.g3.q2">
                                <h3 class="Faq-question">可以购买多个商品么?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        一次只能购买一个商品。
                                    </p>
                                </span>
                            </div>
                            <!--
                            <div class="Faq" role="button" id="faqs.g3.q2">
                                <h3 class="Faq-question">EncryptBuy是如何分配中奖?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        每一个夺宝机会都会获得一个随机数。
                                    </p>
                                    <p>
                                        当夺宝次数都销售完以后，会通过智能合约获得中奖随机数。
                                    </p>
                                    <p>
                                        与中奖随机数最近(大于、等于或小于)的随机数为中奖者，如有多位则取最先购买夺宝机会的用户。
                                    </p>
                                </span>
                            </div>
                            <div class="Faq" role="button" id="faqs.g3.q3">
                                <h3 class="Faq-question">如何知道是否中奖?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        点击"交易记录"，在购买的夺宝机会后面会显示中奖状态。
                                    </p>
                                </span>
                            </div>
                            -->
                            <div class="Faq" role="button" id="faqs.g3.q5">
                                <h3 class="Faq-question">为什么EncryptBuy的上手如此复杂?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        EncryptBuy是架构在区块链技术上的，这种技术相对而言还是新技术。它是安全无虞的，但是在这一潮流普及之前将难免显得有些复杂。
                                    </p>
                                </span>
                            </div>
                            <div class="Faq" role="button" id="faqs.g3.q6">
                                <h3 class="Faq-question">什么是以太币 (ETH)? 为什么我需要有了它才能使用EncryptBuy?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        以太币为以太坊网络提供支持，而以太坊网络正是架构EncryptBuy的载体。以太币的功能与任何其他货币一样﹔其价值随着市场状况的变化而波动。
                                    </p>
                                    <p>
                                        您需要将您的货币（例如RMB，USD，CAD等等）转换成以太币，只有这样才能在我们的网络上使用。
                                    </p>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="FaqGroup">
                        <h2 class="FaqGroup-title">其他问题</h2>
                        <div class="FaqGroup-questions">

                            <div class="Faq" role="button" id="faqs.g6.q2">
                                <h3 class="Faq-question">这是一个骗局吗?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        这不是骗局。所有的交易都被记录在区块上，无法被复制、篡改或删除。
                                    </p>
                                </span>
                            </div>

                            <div class="Faq" role="button" id="faqs.g6.q4">
                                <h3 class="Faq-question">我可以使用信用卡来使用EncryptBuy吗?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        现在还不能，因为此技术还暂未实现。
                                    </p>
                                    <p>
                                        但是，您可以用信用卡在大多数交易所购买以太币。
                                    </p>
                                </span>
                            </div>
                            <div class="Faq" role="button" id="faqs.g6.q5">
                                <h3 class="Faq-question">我可以用我的手机使用EncryptBuy吗?</h3>
                                <span class="Faq-answer">
                                    <p>
                                        暂时不行。支持EncryptBuy的技术（比如MetaMask）暂不能在移动设备上运行。
                                    </p>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    <script>

        $(".Faq").children(".Faq-answer").hide();
        $(".Faq").first().addClass("Faq--open");
        $(".Faq").first().children(".Faq-answer").show();
        $(".Faq").click(function () {
            if ($(this).hasClass("Faq--open")) {
                $(this).removeClass("Faq--open");
                $(this).children(".Faq-answer").hide();
            } else {
                $(this).addClass("Faq--open");
                $(this).children(".Faq-answer").show();
            }
        })

    </script>
    <?php include_once 'api/views/__footer.tpl'?>
</body>
</html>