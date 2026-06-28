<div class="chatbot-widget" data-chatbot data-endpoint="{{ route('chatbot.reply') }}">
    <button class="chatbot-toggle" type="button" data-chatbot-toggle aria-label="Mở trợ lý tư vấn">
        <i class="fas fa-comments"></i>
    </button>

    <section class="chatbot-panel" data-chatbot-panel aria-label="Trợ lý tư vấn HLA">
        <header class="chatbot-header">
            <div>
                <strong>Trợ lý HLA</strong>
                <span>Tư vấn WiFi, đơn hàng, bảo hành</span>
            </div>
            <div class="chatbot-header-actions">
                <button type="button" data-chatbot-reset aria-label="Làm mới hội thoại">
                    <i class="fas fa-redo-alt"></i>
                </button>
                <button type="button" data-chatbot-close aria-label="Đóng trợ lý">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </header>

        <div class="chatbot-messages" data-chatbot-messages>
            <div class="chatbot-message is-bot">
                Chào bạn, mình có thể tư vấn router, mesh WiFi, bảo hành, giao hàng và thanh toán.
            </div>
        </div>

        <div class="chatbot-suggestions" data-chatbot-suggestions>
            <button type="button" data-message="Tư vấn router cho gia đình">Router gia đình</button>
            <button type="button" data-message="Tư vấn mesh WiFi phủ sóng toàn nhà">Mesh WiFi</button>
            <button type="button" data-message="Chính sách bảo hành như thế nào?">Bảo hành</button>
        </div>

        <form class="chatbot-form" data-chatbot-form>
            <input type="text" name="message" data-chatbot-input placeholder="Nhập câu hỏi của bạn..."
                autocomplete="off">
            <button type="submit" aria-label="Gửi câu hỏi">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </section>
</div>
