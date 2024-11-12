<template>
    <div class="container-fluid mt-5">
        <h2 class="text-center display-4 mb-4">Новости</h2>
        <div class="row">
            <div class="col-md-3" v-for="post in posts" :key="post.id">
                <div class="card h-100">
                    <img :src="post.image" class="card-img-top post-image" alt="post image">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title display-6">{{ post.title }}</h5>
                        <p class="card-text lead">{{ post.excerpt }}</p>
                        <a :href="'/blog/' + post.id" class="btn btn-outline-primary mt-auto">Читать больше</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center my-4">
            <a href="/blog" class="btn btn-primary">Посмотреть все новости</a>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Posts',
    data() {
        return {
            posts: [
                { id: 1, title: 'Наш автопарк пополнился новым автомобилем!', excerpt: 'Теперь у наших учеников еще больше возможностей для практики на современных автомобилях. Узнайте больше о нашем обновлении!', image: '/images/posts/loyalty.jpg' },
                { id: 2, title: 'Награда за высокие достижения', excerpt: 'Наша автошкола получила награду за вклад в безопасность дорожного движения! Мы гордимся этим достижением и стремимся сделать обучение еще качественнее.', image: '/images/posts/MC_00634.JPG' },
                { id: 3, title: 'Новый инструктор в нашей команде', excerpt: 'Представляем вам нашего нового инструктора, опытного водителя с многолетним стажем. Благодаря его методике, ученики смогут быстрее освоить азы безопасного вождения.', image: '/images/posts/MC_00375.JPG' },
                { id: 4, title: 'Автошкола снова в числе лучших!', excerpt: 'Наша команда получила почетный диплом за высокие результаты в подготовке водителей! Мы благодарим всех учеников за доверие и обещаем совершенствовать наши программы.', image: '/images/posts/MC_00638.JPG' },
            ],
        };
    },
};
</script>

<style scoped>
.post-image {
    height: 250px; /* Задайте фиксированную высоту */
    object-fit: cover; /* Сохраняет пропорции изображения */
    width: 100%; /* Растягивает изображение по ширине карточки */
}
.card {
    transition: transform 0.2s; /* Плавный эффект при наведении */
}
.card:hover {
    transform: scale(1.05); /* Увеличение карты при наведении */
}
</style>
