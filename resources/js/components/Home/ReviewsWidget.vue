<template>
    <section class="section-spacing bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 section-title">Отзывы наших учеников</h2>
            </div>

            <div class="reviews-widget__frame-wrap mx-auto">
                <iframe
                    ref="widgetFrame"
                    frameborder="0"
                    sandbox="allow-modals allow-forms allow-scripts allow-same-origin allow-popups allow-top-navigation-by-user-activation"
                    class="reviews-widget__frame"
                    title="Отзывы 2GIS"
                ></iframe>
            </div>

            <div class="text-center mt-4">
                <a
                    :href="reviewsUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn btn-primary btn-lg rounded-pill px-4"
                >
                    <i class="fas fa-star me-2"></i>Оставить отзыв
                </a>
            </div>
        </div>
    </section>
</template>

<script>
const REVIEWS_URL = 'https://2gis.ru/krasnodar/branches/70000001046117787/firm/70000001046117788/39.003565%2C45.043388/tab/reviews?m=38.971526%2C45.024767%2F10.79';
const REVIEWS_WIDGET_PAYLOAD = 'PGhlYWQ+PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPgogICAgd2luZG93Ll9fc2l6ZV9fPSdiaWcnOwogICAgd2luZG93Ll9fdGhlbWVfXz0nbGlnaHQnOwogICAgd2luZG93Ll9fYnJhbmNoSWRfXz0nJwogICAgd2luZG93Ll9fb3JnSWRfXz0nNzAwMDAwMDEwNDYxMTc3ODcnCiAgIDwvc2NyaXB0PjxzY3JpcHQgY3Jvc3NvcmlnaW49ImFub255bW91cyIgdHlwZT0ibW9kdWxlIiBzcmM9Imh0dHBzOi8vZGlzay4yZ2lzLmNvbS93aWRnZXQtY29uc3RydWN0b3IvYXNzZXRzL2lmcmFtZS5qcyI+PC9zY3JpcHQ+PGxpbmsgcmVsPSJtb2R1bGVwcmVsb2FkIiBjcm9zc29yaWdpbj0iYW5vbnltb3VzIiBocmVmPSJodHRwczovL2Rpc2suMmdpcy5jb20vd2lkZ2V0LWNvbnN0cnVjdG9yL2Fzc2V0cy9kZWZhdWx0cy5qcyI+PGxpbmsgcmVsPSJzdHlsZXNoZWV0IiBjcm9zc29yaWdpbj0iYW5vbnltb3VzIiBocmVmPSJodHRwczovL2Rpc2suMmdpcy5jb20vd2lkZ2V0LWNvbnN0cnVjdG9yL2Fzc2V0cy9kZWZhdWx0cy5jc3MiPjwvaGVhZD48Ym9keT48ZGl2IGlkPSJpZnJhbWUiPjwvZGl2PjwvYm9keT4=';

export default {
    name: 'ReviewsWidget',
    data() {
        return {
            reviewsUrl: REVIEWS_URL,
        };
    },
    mounted() {
        this.mountReviewsWidget();
    },
    methods: {
        mountReviewsWidget() {
            const frame = this.$refs.widgetFrame;
            if (!frame || !frame.contentWindow || !frame.contentWindow.document) return;

            try {
                const decodedHtml = decodeURIComponent(escape(window.atob(REVIEWS_WIDGET_PAYLOAD)));

                const frameDocument = frame.contentWindow.document;
                frameDocument.open();
                frameDocument.write(decodedHtml);
                frameDocument.close();
            } catch (error) {
                console.error('Не удалось загрузить виджет отзывов:', error);
            }
        },
    },
};
</script>

<style scoped>
.reviews-widget__frame-wrap {
    width: 100%;
    max-width: 1100px;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.1);
}

.reviews-widget__frame {
    width: 100%;
    height: 824px;
    display: block;
    border: 0;
}

@media (max-width: 768px) {
    .reviews-widget__frame {
        height: 700px;
    }
}
</style>
