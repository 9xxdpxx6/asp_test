<template>
    <div class="mt-5">
        <h2 class="text-center">Последние посты</h2>
        <div class="row">
            <div class="col-md-3" v-for="post in posts" :key="post.id">
                <div class="card mb-4">
                    <img :src="post.image" class="card-img-top" alt="post image">
                    <div class="card-body">
                        <h5 class="card-title">{{ post.title }}</h5>
                        <p class="card-text">{{ post.excerpt }}</p>
                        <a :href="'/blog/' + post.id" class="btn btn-outline-primary">Читать больше</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <a href="/blog" class="btn btn-primary">Посмотреть все посты</a>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Posts',
    data() {
        return {
            posts: [
                { id: 1, title: 'Post 1', excerpt: 'Brief summary...', image: 'https://via.placeholder.com/400x300.png/e83e8c/333?text=Blog+Post+1' },
                { id: 2, title: 'Post 2', excerpt: 'Brief summary...', image: 'https://via.placeholder.com/400x300.png/3498db/333?text=Blog+Post+2' },
                { id: 3, title: 'Post 3', excerpt: 'Brief summary...', image: 'https://via.placeholder.com/400x300.png/f39c12/333?text=Blog+Post+3' },
                { id: 4, title: 'Post 4', excerpt: 'Brief summary...', image: 'https://via.placeholder.com/400x300.png/9b59b6/333?text=Blog+Post+4' },
            ],
        };
    },
};
</script>
