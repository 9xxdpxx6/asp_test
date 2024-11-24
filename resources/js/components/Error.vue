<template>
    <div class="error-container my-5">
        <div class="text-center">
            <h1 class="error-title display-1 fw-bold">404</h1>
            <p class="error-message fs-4">Кажется, вы потерялись в просторах интернета...</p>
<!--            <img src="https://via.placeholder.com/400x300.png/f39c12/333?text=Lost+in+Space" alt="Lost in Space" class="error-image my-4">-->
            <div>
                <router-link class="btn btn-primary btn-lg shadow-sm" :to="{ name: 'home' }">Вернуться на главную</router-link>

            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Error404',
}
</script>

<style scoped>
.error-container {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    color: #2d3748;
}

.error-title {
    color: #e53e3e;
    font-size: 8rem;
}

.error-message {
    font-size: 1.5rem;
    color: #4a5568;
}

.error-image {
    max-width: 100%;
    height: auto;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.btn-primary {
    background: #3182ce;
    border: none;
    transition: transform 0.3s ease;
}

.btn-primary:hover {
    background: #2b6cb0;
    transform: scale(1.1);
}
</style>
