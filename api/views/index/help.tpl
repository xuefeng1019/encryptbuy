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
                <h2 class="FaqGroup-title">EncryptBuy的帮助</h2>
                <div class="FaqGroup-questions">
                    <div class="Faq" role="button" id="faqs.g1.q1">
                        <h3 class="Faq-question">Etherscan如何处理我的交易??</h3>
                        <span class="Faq-answer">
                            <p>
                                每个有效的交易都会被纪录在全网，不可篡改，随时可以访问和查看。
                            </p>
                        </span>
                    </div>
                    <div class="Faq" role="button" id="faqs.g1.q2">
                        <h3 class="Faq-question">你能找回我丢失的交易么?我无法进入我的钱包...</h3>
                        <span class="Faq-answer">
                            <p>
                                不幸的是，不能....
                            </p>
                            <p>
                                如果你失去了对你MetaMask钱包的访问，我们不能恢复你的交易。这不是我们政策的反对或困难的问题，这根本不可能。
                            </p>
                            <p>
                                我们的智能合同确保了每个交易的安全, 从而使开发人员无法编辑、访问它们。这样可以保护你的交易免受黑客和其他窃取它们的人的攻击。不幸的是, 这也意味着我们不能简单地'恢复'任何你失去的交易。
                            </p>
                        </span>
                    </div>
                    <div class="Faq" role="button" id="faqs.g1.q3">
                        <h3 class="Faq-question">我不能注册/登录</h3>
                        <span class="Faq-answer">
                            <p>
                                登录故障通常可以通过以下操作进行修复:
                            </p>
                            <ol>
                                <li>
                                    请确保您使用的是桌面版Chrome或火狐浏览器。
                                </li>
                                <li>
                                    在尝试登录之前登录和退出MetaMask。
                                </li>
                                <li>
                                    如果使用的计算机与最初注册的不同, 则需要将MetaMask种子短语从旧计算机复制到新计算机, 以确保具有相同的安全密钥。
                                </li>
                            </ol>
                            <p>
                                这些步骤解决了大多数用户的登录问题, 但是如果您仍然存在问题, 您可以通过在Chrome中打开"后台日志"来帮助我们调试问题。
                            </p>
                            <p>
                                我们已经看到了一些用户的间歇登录问题, 但问题通常会在我们重现之前解决。您的EncryptBuy帐户链接到MetaMask, 因此日志可以帮助我们解决问题。
                            </p>
                        </span>
                    </div>
                    <div class="Faq" role="button" id="faqs.g1.q4">
                        <h3 class="Faq-question">我不能从手机版登录</h3>
                        <span class="Faq-answer">
                            <p>
                                我们不建议在移动设备上使用EncryptBuy。虽然火狐Android支持MetaMask加载项, 但许多用户在使用过程中会遇到性能问题。
                            </p>
                        </span>
                    </div>
                    <div class="Faq" role="button" id="faqs.g1.q5">
                        <h3 class="Faq-question">我不能在新电脑上登录</h3>
                        <span class="Faq-answer">
                            <p>
                                只要在两个计算机上都安装了MetaMask, 并且使用相同的安全密钥, 就可以从多台电脑登录EncryptBuy。当您登录到新计算机上的MetaMask时, 它将请求您的种子短语进行验证。将MetaMask种子短语从旧计算机复制到新计算机, 以确保具有相同的安全密钥。
                            </p>
                            <p>
                                不要担心您的电子邮件或用户名: 如果您有相同的密钥在不同的计算机上, EncryptBuy将知道您是同一用户, 并显示所有相同的信息和交易。
                            </p>
                        </span>
                    </div>
                </div>
            </div>
            <div class="FaqGroup">
                <h2 class="FaqGroup-title">以太币和交易的帮助</h2>
                <div class="FaqGroup-questions">
                    <div class="Faq" role="button" id="faqs.g2.q1">
                        <h3 class="Faq-question">我的交易一直失败</h3>
                        <span class="Faq-answer">
                            <p>
                                如果您的交易在很长一段时间内持续失败, 请与我们联系. 我们可以进一步调查问题的根源。
                            </p>
                        </span>
                    </div>
                    <div class="Faq" role="button" id="faqs.g2.q2">
                        <h3 class="Faq-question">我的交易超时但是成功了</h3>
                        <span class="Faq-answer">
                            <p>
                                当交易"超时"时, 这并不意味着交易已失败, 只是该网络非常繁忙。因此, 直到在网络中最终完成交易后,EncryptBuy才会更新。
                            </p>
                            <p>
                                这可能是由于手续费价格低或网络拥塞激增造成的。
                            </p>
                            <p>
                                这些问题大多与BlockChain的分布式性质有关, 我们正在努力在今后的版本中进行更好的优化。
                            </p>
                        </span>
                    </div>
                    <div class="Faq" role="button" id="faqs.g2.q2">
                        <h3 class="Faq-question">我的交易失败但是扣除了燃料费用</h3>
                        <span class="Faq-answer">
                            <p>
                                很抱歉听到您的交易失败。这就是BlockChain作为分布式平台的性质；这也是为什么它如此安全的原因。当交易失败时, 不幸的是你还得付燃料费用。
                            </p>
                        </span>
                    </div>
                    <div class="Faq" role="button" id="faqs.g2.q2">
                        <h3 class="Faq-question">什么是燃料?</h3>
                        <span class="Faq-answer">
                            <p>
                                "燃料"是以太网络用来测量行动的复杂程度的单位。需要大量燃料的交易比需要更少燃料的交易更复杂。你不可以选择你的交易使用多少燃料。
                            </p>
                            <p>
                                您可以选择 "燃料价格", 这会影响交易的总成本, 但会使它们更容易被快速处理。以太网络中的交易几乎总是首先处理最昂贵的。
                            </p>
                        </span>
                    </div>
                    <div class="Faq" role="button" id="faqs.g2.q2">
                        <h3 class="Faq-question">我想取消一个交易，但是无法取消</h3>
                        <span class="Faq-answer">
                            <p>
                                很遗憾听到你的交易有问题。有一个选项, 取消我们的网站上的交易, 但有时为时已晚, 因为这笔交易已经发送到以太坊区块链。
                            </p>
                            <p>
                                大多数交易的问题是, EncryptBuy已经变得如此流行, 以太坊网络用于管理EncryptBuy的交易正在处理所有额外的流量的麻烦。
                            </p>
                            <p>
                                我们正在研究如何解决这一拥塞问题, 但迄今为止唯一的办法是增加燃料价格, 以提高交易处理的可能性。你可以看更多的当前燃料定价。
                            </p>
                        </span>
                    </div>
                </div>
            </div>
            <div class="FaqGroup">
                <h2 class="FaqGroup-title">MetaMask的帮助</h2>
                <div class="FaqGroup-questions">
                    <div class="Faq" role="button" id="faqs.g3.q1">
                        <h3 class="Faq-question">为什么MetaMask变的很慢?</h3>
                        <span class="Faq-answer">
                            <p>
                                如果您有很多选项卡打开, MetaMask将可能遭受内存泄漏并慢下来。要解决此问题, 请关闭所有选项卡, 重新启动浏览器, 应该会改善。
                            </p>
                        </span>
                    </div>
                    <div class="Faq" role="button" id="faqs.g3.q2">
                        <h3 class="Faq-question">你能修改我的MetaMask地址和密码么?</h3>
                        <span class="Faq-answer">
                            <p>
                                很遗憾您无法访问您的Metamask帐户,但是,这是我们无法帮助的。您的EncryptBuy账号链接到您的钱包地址, 而不是您的电子邮件或用户名。那是因为你的交易在钱包里, 而不是我们的数据库里。
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